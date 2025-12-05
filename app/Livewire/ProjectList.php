<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\CostLibrary; // Tambahkan ini
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectList extends Component
{
    // Properti Modal
    public $showCreateModal = false;
    public $newName, $newLibraryId;

    // Method Create Project
    public function createProject()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newLibraryId' => 'required|exists:cost_libraries,id',
        ]);

        Project::create([
            'user_id' => Auth::id(),
            'name' => $this->newName,
            'cost_library_id' => $this->newLibraryId,
            'description' => 'Project created via Dashboard' // Default description
        ]);

        $this->reset(['newName', 'newLibraryId', 'showCreateModal']);
        
        // Kirim event update jika diperlukan
        $this->dispatch('project-created');
    }

    public function deleteProject($id)
    {
        Project::find($id)->delete();
    }

    public function render()
    {
        return view('livewire.project-list', [
            'projects' => Project::where('user_id', Auth::id())
                            ->withCount('files')
                            ->with('costLibrary')
                            ->orderBy('created_at', 'desc')->get(),
            
            // Kirim data libraries untuk dropdown
            'libraries' => CostLibrary::where('user_id', Auth::id())->get() 
        ]);
    }
}