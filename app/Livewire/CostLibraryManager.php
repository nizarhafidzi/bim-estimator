<?php

namespace App\Livewire;

use App\Models\CostLibrary;
use App\Imports\CostLibraryImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class CostLibraryManager extends Component
{
    use WithFileUploads;

    public $libraries;
    
    // Form Create
    public $name, $description;
    
    // Form Import
    public $excelFile;
    public $importLibraryId = null;

    public function mount()
    {
        $this->loadLibraries();
    }

    public function loadLibraries()
    {
        // Ambil library beserta jumlah item di dalamnya
        $this->libraries = CostLibrary::where('user_id', Auth::id())
            ->withCount(['resources', 'ahsps'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createLibrary()
    {
        $this->validate(['name' => 'required|string|max:255']);

        CostLibrary::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->reset(['name', 'description']);
        $this->loadLibraries();
        session()->flash('success', 'Library created successfully.');
    }

    public function openImportModal($id)
    {
        $this->importLibraryId = $id;
    }

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new CostLibraryImport($this->importLibraryId), $this->excelFile);
            
            session()->flash('success', 'Data successfully imported!');
            $this->reset(['excelFile', 'importLibraryId']);
            $this->loadLibraries();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Import Failed: ' . $e->getMessage());
        }
    }
    
    public function deleteLibrary($id)
    {
        CostLibrary::find($id)->delete();
        $this->loadLibraries();
    }

    public function render()
    {
        return view('livewire.cost-library-manager')->layout('layouts.app');
    }
}