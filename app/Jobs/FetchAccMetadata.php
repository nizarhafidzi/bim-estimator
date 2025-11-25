<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\User;
use App\Services\AutodeskService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FetchAccMetadata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $project;
    protected $user;

    public $timeout = 1200; 
    public $tries = 3;

    public function __construct(Project $project, User $user)
    {
        $this->project = $project;
        $this->user = $user;
    }

    // Helper: Menulis Log ke Database agar muncul di UI User
    private function log($message, $type = 'INFO')
    {
        $timestamp = Carbon::now()->format('H:i:s');
        $newLine = "[{$timestamp}] [{$type}] {$message}";

        // Ambil log lama, tambah log baru
        $logs = $this->project->debug_logs ?? [];
        $logs[] = $newLine;

        // Update Project tanpa mengubah 'updated_at' agar tidak memicu refresh berlebihan
        $this->project->debug_logs = $logs;
        $this->project->saveQuietly(); 
        
        // Juga print ke terminal queue:listen
        echo "{$newLine}\n";
    }

    public function handle(AutodeskService $service): void
    {
        try {
            $this->project->update(['status' => 'processing', 'error_message' => null]);
            $this->log("🚀 Memulai proses import...");

            // 1. Auth
            $this->log("🔑 Meminta Token Valid...");
            $token = $service->getValidUserToken($this->user);
            if(!$token) throw new \Exception("Gagal refresh token.");
            $this->log("✅ Token didapatkan.");

            // 2. Decode URN
            $rawUrn = base64_decode($this->project->urn); 
            $safeUrn = rtrim(strtr(base64_encode($rawUrn), '+/', '-_'), '=');
            $this->log("📄 URN Decoded: " . substr($safeUrn, 0, 20) . "...");

            // 3. Cek Manifest
            $this->log("🔍 Mengecek Status Translation (Manifest)...");
            $manifest = $service->getManifest($safeUrn, $token);
            $status = $manifest['status'] ?? 'n/a';
            $progress = $manifest['progress'] ?? '0%';
            
            $this->log("📊 Status Autodesk: {$status} ({$progress})");

            if ($status === 'inprogress' || $status === 'pending') {
                $this->log("⏳ Masih proses di server Autodesk. Menunggu 30 detik...");
                $this->release(30); 
                return;
            }

            if ($status === 'n/a' || $status === 'failed' || $status === 'timeout') {
                $this->log("⚠️ Model belum siap. Memicu Translation Job baru...");
                $jobResult = $service->translateToSvf($safeUrn, $token);
                
                if (isset($jobResult['result']) && $jobResult['result'] === 'created') {
                    $this->log("✅ Translation Job dibuat. Menunggu 20 detik...");
                    $this->release(20);
                    return;
                } else {
                    throw new \Exception("Gagal trigger translasi: " . json_encode($jobResult));
                }
            }

            if ($status === 'success') {
                $this->log("📥 Download Metadata Properties...");
                
                // PANGGIL SERVICE (Format return sekarang array ['status'=>...])
                $result = $service->fetchModelProperties($safeUrn, $token);

                // 1. JIKA MASIH PROCESSING (202)
                if ($result['status'] === 'processing') {
                    $this->log("⏳ Properties sedang di-index (202). Menunggu 15 detik...");
                    $this->release(15); // Lepaskan job, coba lagi nanti
                    return;
                }

                // 2. JIKA ERROR
                if ($result['status'] === 'error') {
                    $this->log("❌ " . $result['message'], 'ERROR');
                    throw new \Exception($result['message']);
                }

                // 3. JIKA SUKSES
                $data = $result['data'];

                if (!isset($data['data']['collection'])) {
                    $this->log("❌ JSON Properties tidak valid/kosong.", 'ERROR');
                    throw new \Exception("Data collection kosong.");
                }

                $objects = $data['data']['collection'] ?? [];
                $count = count($objects);
                $this->log("📦 Metadata diterima. Memproses {$count} objek...");

                $elementsToInsert = [];
                
                // ... (Kode Parsing Loop di bawah ini SAMA PERSIS seperti sebelumnya) ...
                foreach ($objects as $obj) {
                    if (!isset($obj['properties'])) continue;
                    $props = $obj['properties'];
                    
                    $category = $this->findProperty($props, 'Category') ?? 'Uncategorized';
                    $assemblyCode = $this->findProperty($props, 'Assembly Code');
                    $volumeRaw = $this->findProperty($props, 'Volume'); 
                    $volume = $this->parseVolume($volumeRaw);

                    if ($volume > 0 || $assemblyCode) {
                        $elementsToInsert[] = [
                            'project_id' => $this->project->id,
                            'external_id' => $obj['externalId'],
                            'name' => $obj['name'] ?? 'Unnamed',
                            'category' => $category,
                            'assembly_code' => $assemblyCode,
                            'volume' => $volume,
                            'raw_properties' => json_encode($props),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                
                // ... (Kode Insert DB SAMA PERSIS seperti sebelumnya) ...
                $this->log("💾 Menyimpan " . count($elementsToInsert) . " elemen ke database...");
                DB::table('model_elements')->where('project_id', $this->project->id)->delete();
                
                foreach (array_chunk($elementsToInsert, 50) as $chunk) {
                    DB::table('model_elements')->insert($chunk);
                }

                $this->log("✨ Import Sukses! Status: READY.");
                $this->project->update(['status' => 'ready']);
            }

        } catch (\Exception $e) {
            $msg = substr($e->getMessage(), 0, 500);
            $this->log("🔥 ERROR: {$msg}", 'ERROR');
            
            $this->project->update([
                'status' => 'error',
                'error_message' => $msg
            ]);
            
            // Jangan throw exception lagi agar worker tidak menganggap ini failed job (biar tidak retry loop)
            // Kita anggap "Handled Failure"
        }
    }

    private function findProperty($properties, $keyName)
    {
        foreach ($properties as $group => $items) {
            if (is_array($items)) {
                foreach ($items as $k => $v) {
                    if (str_contains($k, $keyName)) return $v;
                }
            }
        }
        return null;
    }

    private function parseVolume($value)
    {
        if (!$value) return 0;
        preg_match('/[\d\.]+/', str_replace(',', '', $value), $matches);
        return isset($matches[0]) ? (float)$matches[0] : 0;
    }
}