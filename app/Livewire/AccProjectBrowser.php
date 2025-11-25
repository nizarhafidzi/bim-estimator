<?php

namespace App\Livewire;

use App\Models\Project;
use App\Services\AutodeskService;
use App\Jobs\FetchAccMetadata;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AccProjectBrowser extends Component
{
    // State Utama
    public $viewState = 'hubs'; // hubs, projects, contents
    public $search = ''; // Fitur Search

    // Data Storage
    public $hubs = [];
    public $items = []; 
    
    // Navigation History (Breadcrumbs)
    public $breadcrumbs = [];
    public $currentHubId = null;
    public $currentProjectId = null;

    // Debug
    public $errorMsg = '';

    protected function getService()
    {
        return app(AutodeskService::class);
    }

    public function mount()
    {
        if (Auth::user()->autodesk_access_token) {
            $this->loadHubs();
        }
    }

    // --- 1. Load Hubs ---
    public function loadHubs()
    {
        $this->resetError(); // Method ini sekarang sudah ada :)
        $this->viewState = 'hubs';
        $this->breadcrumbs = [['label' => 'Hubs', 'action' => 'loadHubs']];
        
        $token = $this->getToken();
        if (!$token) return;

        $response = Http::withToken($token)->get('https://developer.api.autodesk.com/project/v1/hubs');
        if ($this->handleResponse($response)) {
            $this->hubs = $response->json()['data'];
        }
    }

    // --- 2. Load Projects (Inside a Hub) ---
    public function openHub($hubId, $hubName)
    {
        $this->resetError();
        $this->currentHubId = $hubId;
        $this->viewState = 'projects';
        $this->search = ''; 
        
        $this->breadcrumbs = [
            ['label' => 'Hubs', 'action' => 'loadHubs'],
            ['label' => $hubName, 'action' => null] 
        ];

        $token = $this->getToken();
        $response = Http::withToken($token)->get("https://developer.api.autodesk.com/project/v1/hubs/{$hubId}/projects");
        
        if ($this->handleResponse($response)) {
            $this->items = $response->json()['data'];
        }
    }

    // --- 3. Open Project (Load Top Folders) ---
    // --- 3. Open Project (FIXED: Menggunakan Root Folder Strategy) ---
    public function openProject($projectId, $projectName)
    {
        $this->resetError();
        $this->currentProjectId = $projectId;
        
        // Update Breadcrumb
        $this->breadcrumbs[] = ['label' => $projectName, 'id' => $projectId, 'type' => 'project'];

        $token = $this->getToken();
        
        // Pastikan kita punya Hub ID (dari state sebelumnya)
        $hubId = $this->currentHubId;

        // URL API yang Benar: Sertakan hubs/{hubId} sebelum projects
        // Ini wajib untuk sebagian besar akun BIM 360 / ACC
        $url = "https://developer.api.autodesk.com/project/v1/hubs/{$hubId}/projects/{$projectId}/topFolders";
        
        $response = Http::withToken($token)->get($url);
        
        if ($response->successful()) {
            $this->items = $response->json()['data'];
            $this->viewState = 'contents'; // Ubah tampilan jadi list file/folder
        } else {
            // Jika masih error, kita tampilkan pesan detail untuk debugging
            $errorBody = $response->json();
            $msg = $errorBody['errors'][0]['detail'] ?? $response->status();
            $this->errorMsg = "Gagal membuka Project. API Error: {$msg}. (URL: {$url})";
        }
    }

    // --- 4. Open Folder (Load Contents) ---
    public function openFolder($folderId, $folderName)
    {
        $this->resetError();
        $this->search = '';
        
        $this->breadcrumbs[] = ['label' => $folderName, 'id' => $folderId, 'type' => 'folder', 'action' => 'openFolder'];

        $token = $this->getToken();
        $response = Http::withToken($token)->get("https://developer.api.autodesk.com/data/v1/projects/{$this->currentProjectId}/folders/{$folderId}/contents");
        
        if ($this->handleResponse($response)) {
            $this->items = $response->json()['data'];
        }
    }

    // --- Breadcrumb Navigation ---
    public function navigateBreadcrumb($index)
    {
        if ($index === 0) {
            $this->loadHubs();
            return;
        }

        $target = $this->breadcrumbs[$index];
        $this->breadcrumbs = array_slice($this->breadcrumbs, 0, $index + 1);

        if (isset($target['type']) && $target['type'] == 'project') {
            $this->openProject($target['id'], $target['label']);
            array_pop($this->breadcrumbs); 
        } elseif (isset($target['action']) && $target['action'] == 'openFolder') {
            $this->openFolder($target['id'], $target['label']);
            array_pop($this->breadcrumbs);
        }
    }

    // --- Import Action ---
    public function importFile($itemId, $itemName)
    {
        $token = $this->getToken();
        $response = Http::withToken($token)->get("https://developer.api.autodesk.com/data/v1/projects/{$this->currentProjectId}/items/{$itemId}/tip");
        
        if ($response->successful()) {
            $versionData = $response->json();
            $urnVersion = $versionData['data']['id'];

            $project = Project::create([
                'user_id' => Auth::id(),
                'acc_project_id' => $this->currentProjectId,
                'urn' => base64_encode($urnVersion),
                'name' => $itemName,
                'status' => 'processing',
            ]);

            FetchAccMetadata::dispatch($project, Auth::user());

            session()->flash('status', "Importing '$itemName' started...");
            return redirect()->route('dashboard');
        } else {
            $this->errorMsg = "Failed to get file version.";
        }
    }

    // --- Helpers ---

    // INI METHOD YANG TADI HILANG
    public function resetError()
    {
        $this->errorMsg = '';
    }

    private function getToken() {
        $token = $this->getService()->getValidUserToken(Auth::user());
        if (!$token) $this->errorMsg = "Token Expired. Please reconnect.";
        return $token;
    }

    private function handleResponse($response) {
        if ($response->successful()) return true;
        $this->errorMsg = "API Error: " . $response->status();
        return false;
    }

    public function getFilteredItemsProperty()
    {
        if (empty($this->search)) return $this->items;

        return collect($this->items)->filter(function($item) {
            $name = $item['attributes']['name'] ?? $item['attributes']['displayName'] ?? '';
            return stripos($name, $this->search) !== false;
        })->all();
    }

    public function render()
    {
        return view('livewire.acc-project-browser', [
            'filteredItems' => $this->getFilteredItemsProperty()
        ]);
    }
}