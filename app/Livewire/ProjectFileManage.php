<?php

namespace App\Livewire;

use App\Jobs\FetchAccMetadata;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectFileManage extends Component
{
    public $projectId;
    public $project;
    
    // Listener untuk menangkap event dari Component AccProjectBrowser
    protected $listeners = ['file-selected-from-acc' => 'addFile'];

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->project = Project::findOrFail($projectId);
    }

    // Method ini dipanggil otomatis saat AccProjectBrowser emit event
    public function addFile($urn, $name)
    {
        // 1. Simpan ke tabel Project Files (Anak)
        $file = ProjectFile::create([
            'project_id' => $this->projectId,
            'name' => $name,
            'urn' => $urn,
            'status' => 'processing'
        ]);

        // 2. Jalankan Job Import untuk file ini
        FetchAccMetadata::dispatch($file, Auth::user());

        session()->flash('message', "File $name added & importing started!");
    }

    public function deleteFile($fileId)
    {
        ProjectFile::find($fileId)->delete();
    }

    public function render()
    {
        return view('livewire.project-file-manage', [
            'files' => $this->project->files()->orderBy('created_at', 'desc')->get()
        ])->layout('layouts.app');
    }
}