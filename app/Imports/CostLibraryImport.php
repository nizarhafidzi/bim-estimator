<?php

namespace App\Imports;

use App\Models\AhspCoefficient;
use App\Models\AhspMaster;
use App\Models\Resource;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class CostLibraryImport implements WithMultipleSheets
{
    protected $libraryId;

    public function __construct($libraryId)
    {
        $this->libraryId = $libraryId;
    }

    public function sheets(): array
    {
        return [
            // Sheet 1 harus bernama 'Resources' di file Excel
            'Resources' => new ResourcesImport($this->libraryId),
            
            // Sheet 2 harus bernama 'Analysis' di file Excel
            'Analysis' => new AnalysisImport($this->libraryId),
        ];
    }
}

// --- SUB-CLASS 1: IMPORT BAHAN (RESOURCES) ---
class ResourcesImport implements ToCollection, WithHeadingRow
{
    protected $libraryId;
    public function __construct($libraryId) { $this->libraryId = $libraryId; }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validasi baris kosong
            if(!isset($row['code']) || !isset($row['price'])) continue;

            Resource::updateOrCreate(
                [
                    'cost_library_id' => $this->libraryId, 
                    'resource_code' => $row['code']
                ],
                [
                    'type' => strtolower($row['type'] ?? 'material'),
                    'name' => $row['name'],
                    'unit' => $row['unit'],
                    'price' => $row['price']
                ]
            );
        }
    }
}

// --- SUB-CLASS 2: IMPORT ANALISA (AHSP) ---
class AnalysisImport implements ToCollection, WithHeadingRow
{
    protected $libraryId;
    public function __construct($libraryId) { $this->libraryId = $libraryId; }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validasi data wajib
            if(!isset($row['ahsp_code']) || !isset($row['resource_code'])) continue;

            // 1. Buat Judul Analisa (Header) - Jika belum ada
            $ahsp = AhspMaster::firstOrCreate(
                [
                    'cost_library_id' => $this->libraryId, 
                    'code' => $row['ahsp_code'] // Kunci utama (Assembly Code)
                ],
                [
                    'name' => $row['ahsp_name'],
                    'division' => $row['division'] ?? 'General',
                    'sub_division' => $row['sub_division'] ?? null,
                    'unit' => $row['ahsp_unit']
                ]
            );

            // 2. Cari Resource ID berdasarkan Kode yang diinput di Excel
            $resource = Resource::where('cost_library_id', $this->libraryId)
                        ->where('resource_code', $row['resource_code'])
                        ->first();

            if ($resource) {
                // 3. Masukkan ke Tabel Rumus (Coefficient)
                AhspCoefficient::updateOrCreate(
                    [
                        'ahsp_master_id' => $ahsp->id, 
                        'resource_id' => $resource->id
                    ],
                    [
                        'coefficient' => $row['coefficient']
                    ]
                );
            } else {
                Log::warning("Resource Code tidak ditemukan: " . $row['resource_code']);
            }
        }
    }
}