<?php

namespace App\Jobs;

use App\Models\ProjectFile;
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

    protected $file;
    protected $user;

    public $timeout = 3600; // 30 Menit

    public function __construct(ProjectFile $file, User $user)
    {
        $this->file = $file;
        $this->user = $user;
    }

    // Helper Log ke Terminal & Database
    private function log($message, $type = 'INFO')
    {
        $timestamp = Carbon::now()->format('H:i:s');
        $newLine = "[{$timestamp}] [{$type}] {$message}";

        $logs = $this->file->debug_logs ?? [];
        $logs[] = $newLine;
        
        // Simpan log ke DB tanpa ganggu proses lain
        DB::table('project_files')->where('id', $this->file->id)->update(['debug_logs' => json_encode($logs)]);
        
        // TAMPILKAN DI LAYAR HITAM (CMD)
        echo "{$newLine}\n";
    }

    public function handle(AutodeskService $service): void
    {
        try {
            $this->file->update(['status' => 'processing', 'error_message' => null]);
            $this->log("🚀 Start Import: " . $this->file->name);

            $token = $service->getValidUserToken($this->user);
            
            // 1. Cek Status Translasi
            $this->log("🔍 Cek Status di Autodesk...");
            $manifest = $service->getManifest($this->file->urn, $token);
            $status = $manifest['status'] ?? 'n/a';
            
            if ($status === 'inprogress' || $status === 'pending') {
                $this->log("⏳ Masih proses translasi. Coba lagi 30 detik...");
                $this->release(30); return;
            }
            
            if ($status === 'n/a' || $status === 'failed' || $status === 'timeout') {
                $this->log("⚠️ File belum siap. Memicu translasi ulang...");
                $res = $service->translateToSvf($this->file->urn, $token);
                if(isset($res['result']) && ($res['result'] === 'created' || $res['result'] === 'success')) {
                    $this->log("✅ Translasi dimulai. Menunggu 20 detik...");
                    $this->release(20); return;
                }
                throw new \Exception("Gagal trigger translasi.");
            }

            if ($status === 'success') {
                // 2. Download Data
                $this->log("📥 Download Data Properti...");
                $res = $service->fetchModelProperties($this->file->urn, $token);

                if ($res['status'] === 'processing') {
                    $this->log("⏳ Sedang indexing properti. Menunggu 15 detik...");
                    $this->release(15); return;
                }
                
                if ($res['status'] === 'error') throw new \Exception($res['message']);

                $objects = $res['data']['data']['collection'] ?? [];
                $total = count($objects);
                $this->log("📦 Diterima {$total} objek dari Autodesk.");

                // --- FITUR DETEKTIF: TAMPILKAN CONTOH DATA DI TERMINAL ---
                if ($total > 0) {
                    // Ambil sampel objek pertama yang punya properti
                    $sample = null;
                    foreach($objects as $obj) {
                        if(isset($obj['properties']) && !empty($obj['properties'])) {
                            $sample = $obj['properties'];
                            break;
                        }
                    }

                    if ($sample) {
                        $this->log("--------------------------------------------------");
                        $this->log("🕵️  DATA INSPECTOR (Contoh Parameter yang Ditemukan)");
                        $this->log("--------------------------------------------------");
                        
                        foreach ($sample as $group => $props) {
                            if (is_array($props)) {
                                // Tampilkan Nama Group (misal: Identity Data)
                                // dan List Parameter di dalamnya (misal: Type Name, OmniClass)
                                $keys = implode(', ', array_keys($props));
                                $this->log("📂 GROUP: [$group]");
                                $this->log("   👉 Parameter: $keys");
                            }
                        }
                        $this->log("--------------------------------------------------");
                        $this->log("ℹ️  Gunakan nama parameter di atas untuk membuat Rules.");
                        $this->log("--------------------------------------------------");
                    }
                }
                // ---------------------------------------------------------

                // 3. Simpan ke Database (Logic Stabil)
                DB::table('model_elements')->where('project_file_id', $this->file->id)->delete();

                $batch = [];
                foreach ($objects as $obj) {
                    if (!isset($obj['properties'])) continue;
                    $rawProps = $obj['properties'];
                    
                    // Helper Pencari
                    $find = function($k) use ($rawProps) {
                        foreach ($rawProps as $g) {
                            if(is_array($g)) {
                                foreach ($g as $key => $val) { 
                                    if (strcasecmp($key, $k) == 0) return $val; 
                                }
                            }
                        }
                        return null;
                    };

                    // Ambil Data Penting
                    $category = $find('Category') ?? $find('OmniClass Title') ?? 'Uncategorized';
                    $assemblyCode = $find('Assembly Code') ?? $find('Keynote');
                    
                    $volStr = $find('Volume');
                    preg_match('/[\d\.]+/', str_replace(',', '', $volStr ?? ''), $matches);
                    $volume = isset($matches[0]) ? (float)$matches[0] : 0;

                    $batch[] = [
                        'project_id' => $this->file->project_id,
                        'project_file_id' => $this->file->id,
                        'external_id' => $obj['externalId'],
                        'name' => $obj['name'] ?? 'Unnamed',
                        'category' => $category,
                        'assembly_code' => $assemblyCode,
                        'volume' => $volume,
                        'raw_properties' => json_encode($rawProps), // Tetap simpan JSON lengkap
                        'created_at' => now(), 'updated_at' => now()
                    ];
                }

                // Batch Insert Aman (50 baris)
                foreach (array_chunk($batch, 50) as $chunk) {
                    DB::table('model_elements')->insert($chunk);
                }

                $this->log("✅ Import Selesai! Data tersimpan di database.");
                $this->file->update(['status' => 'ready']);
            }

        } catch (\Exception $e) {
            $this->log("🔥 Error: " . $e->getMessage(), 'ERROR');
            $this->file->update(['status' => 'error', 'error_message' => $e->getMessage()]);
        }
    }
}