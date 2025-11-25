<?php

namespace App\Services;

use App\Models\CostResult;
use App\Models\MasterUnitPrice;
use App\Models\ModelElement;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class CostEstimationService
{
    public function calculateProject(Project $project)
    {
        // 1. Ambil semua elemen yang punya Volume > 0
        $elements = $project->elements()->where('volume', '>', 0)->get();

        // 2. Ambil semua Master Harga milik User ini
        // Kita jadikan Key-nya 'work_code' agar pencarian cepat (tanpa looping database berulang)
        $prices = MasterUnitPrice::where('user_id', $project->user_id)
                    ->get()
                    ->keyBy('work_code');

        $resultsData = [];
        $matchedCount = 0;
        $totalProjectCost = 0;

        DB::beginTransaction();
        try {
            // Hapus hasil hitungan lama (jika ada) biar bersih
            CostResult::where('project_id', $project->id)->delete();

            foreach ($elements as $el) {
                // LOGIKA UTAMA: MATCHING
                // Cek apakah assembly_code elemen ini ada di daftar harga?
                $code = $el->assembly_code;
                $priceData = isset($code) ? $prices->get($code) : null;

                if ($priceData) {
                    // JIKA COCOK (MATCHED)
                    $unitPrice = $priceData->price;
                    $totalCost = $el->volume * $unitPrice;
                    $status = 'matched';
                    $matchedWorkCode = $priceData->work_code;
                    $matchedCount++;
                    $totalProjectCost += $totalCost;
                } else {
                    // JIKA TIDAK COCOK (UNASSIGNED)
                    $unitPrice = 0;
                    $totalCost = 0;
                    $status = 'unassigned';
                    $matchedWorkCode = null;
                }

                // Siapkan data untuk insert batch
                $resultsData[] = [
                    'project_id' => $project->id,
                    'model_element_id' => $el->id,
                    'matched_work_code' => $matchedWorkCode,
                    'unit_price_applied' => $unitPrice,
                    'total_cost' => $totalCost,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Simpan ke database (Batch Insert per 500 data agar cepat)
            foreach (array_chunk($resultsData, 500) as $chunk) {
                CostResult::insert($chunk);
            }

            DB::commit();

            return [
                'status' => 'success',
                'elements_processed' => count($elements),
                'elements_matched' => $matchedCount,
                'total_cost' => $totalProjectCost
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}