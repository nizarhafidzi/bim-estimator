<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ModelElement;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ModelInspector extends Component
{
    use WithPagination;

    public $projectId;
    
    public $search = ''; // Text Search (Pencarian bebas)
    public $filterTypeName = ''; // Dropdown Filter (Gantikan Category)

    public $selectedElement = null;
    public $showModal = false;

    public function mount()
    {
        $lastProject = Project::where('user_id', auth()->id())->latest()->first();
        if ($lastProject) {
            $this->projectId = $lastProject->id;
        }
    }

    // Reset halaman saat filter berubah
    public function updatedFilterTypeName() { $this->resetPage(); }
    public function updatedProjectId() { $this->resetPage(); $this->filterTypeName = ''; }

    public function inspect($elementId)
    {
        $this->selectedElement = ModelElement::find($elementId);
        $this->showModal = true;
    }

    public function render()
    {
        $projects = Project::where('user_id', auth()->id())->get();
        
        $uniqueTypes = [];
        $elements = [];

        if ($this->projectId) {
            // 1. SIAPKAN DROPDOWN TYPE NAME
            // Ambil semua nama mentah -> Bersihkan ID [...] -> Ambil Unik -> Urutkan
            // Ini agar dropdown tidak berisi ribuan item kembar
            $allNames = ModelElement::where('project_id', $this->projectId)->pluck('name');
            
            $uniqueTypes = $allNames->map(function ($name) {
                // Ambil teks sebelum tanda kurung siku '[' dan hapus spasi
                return trim(Str::beforeLast($name, '['));
            })->unique()->sort()->values();


            // 2. QUERY DATA UTAMA
            $query = ModelElement::where('project_id', $this->projectId);

            // Filter Dropdown Type Name
            if ($this->filterTypeName) {
                // Gunakan LIKE 'Nama Tipe%' agar mencocokkan nama asli yang ada ID-nya
                $query->where('name', 'LIKE', $this->filterTypeName . '%');
            }

            // Filter Text Search (Opsional, untuk pencarian lebih spesifik)
            if ($this->search) {
                $query->where('name', 'like', '%' . $this->search . '%');
            }

            $elements = $query->paginate(20);
        }

        return view('livewire.model-inspector', [
            'projects' => $projects,
            'uniqueTypes' => $uniqueTypes, // Kirim data tipe unik ke view
            'elements' => $elements
        ])->layout('layouts.app');
    }
}