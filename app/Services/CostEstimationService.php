<?php

namespace App\Services;

use App\Models\AhspMaster;
use App\Models\CostResult;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class CostEstimationService
{
    public function calculateProject(Project $project)
    {
        // 1. Cek apakah Project sudah punya Cost Library?
        if (!$project->cost_library_id) {
            return [
                'status' => 'error', 
                'message' => 'Pilih "Cost Library" (Buku Harga) terlebih dahulu sebelum menghitung!'
            ];
        }

        // 2. Ambil Semua Elemen Model
        $elements = $project->elements()->where('volume', '>', 0)->get();

        // 3. Ambil Resep AHSP dari Library yang dipilih
        // Kita ambil code, total_price (computed), division, sub_division
        $ahspList = AhspMaster::with('coefficients.resource')
                        ->where('cost_library_id', $project->cost_library_id)
                        ->get()
                        ->keyBy('code'); // Indexing biar pencarian cepat

        $resultsData = [];
        $matchedCount = 0;
        $totalProjectCost = 0;

        DB::beginTransaction();
        try {
            // Bersihkan hasil lama
            CostResult::where('project_id', $project->id)->delete();

            foreach ($elements as $el) {
                $code = $el->assembly_code; // Contoh: C2010
                
                // Cari Resep
                $ahspItem = isset($code) ? $ahspList->get($code) : null;

                if ($ahspItem) {
                    // HITUNG HARGA (Rumus: Total Resep)
                    $unitPrice = $ahspItem->total_price; // Ini otomatis menghitung (Koef x Harga Resource)
                    
                    $totalCost = $el->volume * $unitPrice;
                    $status = 'matched';
                    $matchedCode = $ahspItem->code;
                    
                    $matchedCount++;
                    $totalProjectCost += $totalCost;
                } else {
                    $unitPrice = 0;
                    $totalCost = 0;
                    $status = 'unassigned';
                    $matchedCode = null;
                }

                $resultsData[] = [
                    'project_id' => $project->id,
                    'model_element_id' => $el->id,
                    'matched_work_code' => $matchedCode,
                    'unit_price_applied' => $unitPrice,
                    'total_cost' => $totalCost,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Batch Insert (Pecah per 500 agar database tidak tersedak)
            foreach (array_chunk($resultsData, 500) as $chunk) {
                CostResult::insert($chunk);
            }

            DB::commit();
            return [
                'status' => 'success',
                'total_cost' => $totalProjectCost,
                'matched' => $matchedCount
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}