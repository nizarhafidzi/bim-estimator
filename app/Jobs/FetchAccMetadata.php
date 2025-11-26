<?php

namespace App\Jobs;

use App\Models\ProjectFile; // <--- PENTING: Pakai Model Baru
use App\Models\User;
use App\Services\AutodeskService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchAccMetadata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file; // Variable ganti jadi $file
    protected $user;

    public $timeout = 1200; 
    public $tries = 3;

    // Constructor terima ProjectFile
    public function __construct(ProjectFile $file, User $user)
    {
        $this->file = $file;
        $this->user = $user;
    }

    // Helper Log ke kolom debug_logs di tabel project_files
    private function log($message, $type = 'INFO')
    {
        $timestamp = Carbon::now()->format('H:i:s');
        $newLine = "[{$timestamp}] [{$type}] {$message}";

        $logs = $this->file->debug_logs ?? [];
        $logs[] = $newLine;

        $this->file->debug_logs = $logs;
        $this->file->saveQuietly(); 
        
        echo "{$newLine}\n";
    }

    public function handle(AutodeskService $service): void
    {
        try {
            // Update status FILE jadi processing
            $this->file->update(['status' => 'processing', 'error_message' => null]);
            
            $this->log("🚀 Memulai import file: " . $this->file->name);

            // 1. Auth
            $token = $service->getValidUserToken($this->user);
            if(!$token) throw new \Exception("Gagal refresh token.");

            // 2. Decode URN (Ambil dari tabel project_files)
            $rawUrn = base64_decode($this->file->urn); 
            $safeUrn = rtrim(strtr(base64_encode($rawUrn), '+/', '-_'), '=');
            
            // 3. Cek Manifest (Sama seperti logic sebelumnya)
            $this->log("🔍 Cek Status Translation...");
            $manifest = $service->getManifest($safeUrn, $token);
            $status = $manifest['status'] ?? 'n/a';
            $progress = $manifest['progress'] ?? '0%';
            
            $this->log("📊 Status: {$status} ({$progress})");

            // Handle In Progress
            if ($status === 'inprogress' || $status === 'pending') {
                $this->log("⏳ Masih proses di server Autodesk. Menunggu 30 detik...");
                $this->release(30); 
                return;
            }

            // Handle Not Started / Failed
            if ($status === 'n/a' || $status === 'failed' || $status === 'timeout') {
                $this->log("⚠️ Trigger Translation baru...");
                $jobResult = $service->translateToSvf($safeUrn, $token);
                
                if (isset($jobResult['result']) && $jobResult['result'] === 'created') {
                    $this->log("✅ Translation Job dibuat. Menunggu 20 detik...");
                    $this->release(20);
                    return;
                } else {
                    throw new \Exception("Gagal trigger translasi.");
                }
            }

            // Handle Success -> Download Data
            if ($status === 'success') {
                $this->log("📥 Download Metadata...");
                $result = $service->fetchModelProperties($safeUrn, $token);

                if ($result['status'] === 'processing') {
                    $this->log("⏳ Properties indexing (202). Menunggu 15 detik...");
                    $this->release(15);
                    return;
                }

                if ($result['status'] === 'error') {
                    throw new \Exception($result['message']);
                }

                $data = $result['data'];
                $objects = $data['data']['collection'] ?? [];
                $count = count($objects);
                $this->log("📦 Metadata diterima. Memproses {$count} objek...");

                // --- PERUBAHAN PENTING DI SINI ---
                
                // 1. Hapus elemen lama HANYA DARI FILE INI (jangan hapus file lain di project yang sama)
                DB::table('model_elements')->where('project_file_id', $this->file->id)->delete();

                $elementsToInsert = [];
                foreach ($objects as $obj) {
                    if (!isset($obj['properties'])) continue;
                    $props = $obj['properties'];
                    
                    // Logic Parsing
                    $category = $this->findProperty($props, 'Category') ?? 'Uncategorized';
                    $assemblyCode = $this->findProperty($props, 'Assembly Code'); // atau 'Keynote'
                    $volumeRaw = $this->findProperty($props, 'Volume'); 
                    $volume = $this->parseVolume($volumeRaw);

                    if ($volume > 0 || $assemblyCode) {
                        $elementsToInsert[] = [
                            // KUNCI RELASI BARU:
                            'project_id' => $this->file->project_id, // Link ke Header Project
                            'project_file_id' => $this->file->id,    // Link ke File Spesifik
                            
                            'external_id' => $obj['externalId'], // GUID String (Wajib)
                            'name' => $obj['name'] ?? 'Unnamed',
                            'category' => $category,
                            'assembly_code' => $assemblyCode,
                            'volume' => $volume,
                            'raw_properties' => json_encode($props), // Simpan semua data
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                $this->log("💾 Menyimpan " . count($elementsToInsert) . " elemen...");
                
                // Batch Insert Kecil (50) agar aman
                foreach (array_chunk($elementsToInsert, 50) as $chunk) {
                    DB::table('model_elements')->insert($chunk);
                }

                $this->log("✨ Import File Sukses!");
                $this->file->update(['status' => 'ready']);
            }

        } catch (\Exception $e) {
            $msg = substr($e->getMessage(), 0, 500);
            $this->log("🔥 ERROR: {$msg}", 'ERROR');
            
            $this->file->update([
                'status' => 'error',
                'error_message' => $msg
            ]);
        }
    }

    // Helper methods tetap sama
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