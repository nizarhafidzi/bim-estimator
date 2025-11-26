<?php

namespace App\Livewire;

use App\Models\AhspCoefficient;
use App\Models\AhspMaster;
use App\Models\CostLibrary;
use App\Models\Resource;
use Livewire\Component;

class AhspBuilder extends Component
{
    public $libraryId;
    public $library;
    
    // State Tampilan
    public $activeAhspId = null;
    public $searchAhsp = '';
    public $isCreating = false;
    public $showResourceModal = false; // State untuk Modal Tambah Bahan

    // Data Form AHSP (Header)
    public $ahspCode, $ahspName, $ahspDivision, $ahspUnit;

    // Data Form Tambah Bahan ke Resep
    public $searchResource = '';
    
    // --- BARU: Data Form Create New Resource (Modal) ---
    public $newResCode, $newResName, $newResType = 'material', $newResUnit, $newResPrice;

    public function mount($libraryId)
    {
        $this->libraryId = $libraryId;
        $this->library = CostLibrary::findOrFail($libraryId);
    }

    // --- A. MANAJEMEN AHSP (HEADER) ---
    
    public function selectAhsp($id)
    {
        $this->activeAhspId = $id;
        $this->isCreating = false;
        $ahsp = AhspMaster::find($id);
        $this->ahspCode = $ahsp->code;
        $this->ahspName = $ahsp->name;
        $this->ahspDivision = $ahsp->division;
        $this->ahspUnit = $ahsp->unit;
        $this->searchResource = ''; 
    }

    public function createNewMode()
    {
        $this->reset(['ahspCode', 'ahspName', 'ahspDivision', 'ahspUnit', 'activeAhspId']);
        $this->isCreating = true;
    }

    // FITUR BARU: HAPUS AHSP
    public function deleteAhsp($id)
    {
        AhspMaster::find($id)->delete();
        
        // Jika yang dihapus adalah yang sedang aktif, reset tampilan
        if ($this->activeAhspId == $id) {
            $this->activeAhspId = null;
            $this->isCreating = false;
        }
        session()->flash('message', 'Analisa berhasil dihapus.');
    }

    public function saveHeader()
    {
        $this->validate([
            'ahspCode' => 'required',
            'ahspName' => 'required',
            'ahspUnit' => 'required'
        ]);

        if ($this->isCreating) {
            $ahsp = AhspMaster::create([
                'cost_library_id' => $this->libraryId,
                'code' => $this->ahspCode,
                'name' => $this->ahspName,
                'division' => $this->ahspDivision ?? 'General',
                'unit' => $this->ahspUnit,
            ]);
            $this->activeAhspId = $ahsp->id;
            $this->isCreating = false;
            session()->flash('message', 'AHSP Baru dibuat.');
        } else {
            AhspMaster::find($this->activeAhspId)->update([
                'code' => $this->ahspCode,
                'name' => $this->ahspName,
                'division' => $this->ahspDivision,
                'unit' => $this->ahspUnit,
            ]);
            session()->flash('message', 'Header AHSP diupdate.');
        }
    }

    // --- B. MANAJEMEN BAHAN (INGREDIENTS) ---

    public function addIngredient($resourceId)
    {
        if (!$this->activeAhspId) return;

        // Cek duplikat bahan di resep yang sama
        $exists = AhspCoefficient::where('ahsp_master_id', $this->activeAhspId)
                    ->where('resource_id', $resourceId)->exists();

        if (!$exists) {
            AhspCoefficient::create([
                'ahsp_master_id' => $this->activeAhspId,
                'resource_id' => $resourceId,
                'coefficient' => 1.0
            ]);
        }
        $this->searchResource = ''; 
    }

    public function updateCoefficient($coefId, $value)
    {
        $coef = AhspCoefficient::find($coefId);
        if($coef) $coef->update(['coefficient' => $value]);
    }

    public function removeIngredient($coefId)
    {
        AhspCoefficient::find($coefId)->delete();
    }

    // --- C. FITUR BARU: CREATE NEW RESOURCE (MODAL) ---

    public function openResourceModal()
    {
        $this->reset(['newResCode', 'newResName', 'newResUnit', 'newResPrice']);
        $this->newResType = 'material';
        $this->showResourceModal = true;
    }

    public function saveNewResource()
    {
        $this->validate([
            'newResCode' => 'required',
            'newResName' => 'required',
            'newResUnit' => 'required',
            'newResPrice' => 'required|numeric',
        ]);

        // Simpan Resource Baru ke Library ini
        $res = Resource::create([
            'cost_library_id' => $this->libraryId,
            'resource_code' => $this->newResCode,
            'name' => $this->newResName,
            'type' => $this->newResType,
            'unit' => $this->newResUnit,
            'price' => $this->newResPrice,
        ]);

        // Otomatis masukkan ke resep yang sedang aktif (opsional, biar cepat)
        if ($this->activeAhspId) {
            $this->addIngredient($res->id);
        }

        $this->showResourceModal = false;
        session()->flash('message', 'Resource baru berhasil dibuat & ditambahkan!');
    }

    public function render()
    {
        $ahsps = AhspMaster::where('cost_library_id', $this->libraryId)
            ->where('name', 'like', '%' . $this->searchAhsp . '%')
            ->orderBy('code')
            ->get();

        $activeAhspDetails = null;
        if ($this->activeAhspId) {
            $activeAhspDetails = AhspMaster::with('coefficients.resource')->find($this->activeAhspId);
        }

        $availableResources = [];
        if (strlen($this->searchResource) > 1) {
            $availableResources = Resource::where('cost_library_id', $this->libraryId)
                ->where('name', 'like', '%' . $this->searchResource . '%')
                ->take(5)
                ->get();
        }

        return view('livewire.ahsp-builder', [
            'ahsps' => $ahsps,
            'activeData' => $activeAhspDetails,
            'availableResources' => $availableResources
        ])->layout('layouts.app');
    }
}