<?php

namespace App\Exports;

use App\Models\Resource;
use App\Models\AhspMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LibraryExport implements WithMultipleSheets
{
    protected $libraryId;

    public function __construct($libraryId)
    {
        $this->libraryId = $libraryId;
    }

    public function sheets(): array
    {
        return [
            new ResourcesSheet($this->libraryId),
            new AnalysisSheet($this->libraryId),
        ];
    }
}

class ResourcesSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $libraryId;
    public function __construct($libraryId) { $this->libraryId = $libraryId; }

    public function collection()
    {
        return Resource::where('cost_library_id', $this->libraryId)
            ->select('resource_code', 'type', 'name', 'unit', 'price')
            ->get();
    }

    public function headings(): array { return ['code', 'type', 'name', 'unit', 'price']; }
    public function title(): string { return 'Resources'; }
}

class AnalysisSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $libraryId;
    public function __construct($libraryId) { $this->libraryId = $libraryId; }

    public function collection()
    {
        $data = [];
        $ahsps = AhspMaster::with('coefficients.resource')
            ->where('cost_library_id', $this->libraryId)->get();

        foreach ($ahsps as $ahsp) {
            foreach ($ahsp->coefficients as $coef) {
                if ($coef->resource) {
                    $data[] = [
                        'ahsp_code' => $ahsp->code,
                        'ahsp_name' => $ahsp->name,
                        'division' => $ahsp->division,
                        'sub_division' => $ahsp->sub_division,
                        'ahsp_unit' => $ahsp->unit,
                        'resource_code' => $coef->resource->resource_code,
                        'coefficient' => $coef->coefficient
                    ];
                }
            }
        }
        return collect($data);
    }

    public function headings(): array { return ['ahsp_code', 'ahsp_name', 'division', 'sub_division', 'ahsp_unit', 'resource_code', 'coefficient']; }
    public function title(): string { return 'Analysis'; }
}