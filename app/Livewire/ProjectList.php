<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\CostLibrary;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectList extends Component
{
    // Form Create Project
    public $showCreateModal = false;
    public $newName, $newLibraryId;

    public function createProject()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newLibraryId' => 'required|exists:cost_libraries,id',
        ]);

        Project::create([
            'user_id' => Auth::id(),
            'name' => $this->newName,
            'cost_library_id' => $this->newLibraryId
        ]);

        $this->reset(['newName', 'newLibraryId', 'showCreateModal']);
        session()->flash('message', 'Project Header created! Now add files inside.');
    }

    public function deleteProject($id)
    {
        Project::find($id)->delete();
    }

    public function render()
    {
        return view('livewire.project-list', [
            'projects' => Project::where('user_id', Auth::id())
                            ->withCount('files') // Hitung jumlah file di dalamnya
                            ->with('costLibrary')
                            ->orderBy('created_at', 'desc')->get(),
            'libraries' => CostLibrary::where('user_id', Auth::id())->get()
        ]);
    }
}