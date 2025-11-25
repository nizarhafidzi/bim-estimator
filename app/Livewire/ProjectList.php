<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectList extends Component
{
    protected $listeners = ['projectImported' => '$refresh'];
    
    // Variable untuk Modal Log
    public $selectedProjectLogs = null;
    public $showLogModal = false;
    public $logProjectId = null;

    public function render()
    {
        return view('livewire.project-list', [
            'projects' => Project::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get()
        ]);
    }
    
    public function deleteProject($id)
    {
        Project::find($id)->delete();
    }

    // Action saat tombol "Show Log" diklik
    public function viewLogs($projectId)
    {
        $this->logProjectId = $projectId;
        $this->showLogModal = true;
        $this->refreshLogs();
    }

    // Action untuk update isi log (polling)
    public function refreshLogs()
    {
        if ($this->logProjectId) {
            $project = Project::find($this->logProjectId);
            $this->selectedProjectLogs = $project ? $project->debug_logs : [];
        }
    }

    public function closeLogs()
    {
        $this->showLogModal = false;
        $this->selectedProjectLogs = null;
        $this->logProjectId = null;
    }
}