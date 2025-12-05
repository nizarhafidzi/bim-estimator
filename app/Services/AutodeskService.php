<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutodeskService
{
    protected $baseUrl = 'https://developer.api.autodesk.com';

    // --- 1. AUTHENTICATION ---
    public function getAuthorizationUrl()
    {
        $params = [
            'response_type' => 'code',
            'client_id' => env('APS_CLIENT_ID'),
            'redirect_uri' => env('APS_CALLBACK_URL'),
            'scope' => 'data:read data:write viewables:read user:read',
            'prompt' => 'login'
        ];
        return $this->baseUrl . '/authentication/v2/authorize?' . http_build_query($params);
    }

    public function getUserToken($code)
    {
        return Http::asForm()->post($this->baseUrl . '/authentication/v2/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => env('APS_CLIENT_ID'),
            'client_secret' => env('APS_CLIENT_SECRET'),
            'redirect_uri' => env('APS_CALLBACK_URL'),
        ])->json();
    }

    public function getValidUserToken(User $user)
    {
        if (!$user->token_expires_at) return null;
        if (now()->addMinutes(5)->greaterThanOrEqualTo($user->token_expires_at)) {
            return $this->refreshUserToken($user);
        }
        return $user->autodesk_access_token;
    }

    public function refreshUserToken(User $user)
    {
        $response = Http::asForm()->post($this->baseUrl . '/authentication/v2/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $user->autodesk_refresh_token,
            'client_id' => env('APS_CLIENT_ID'),
            'client_secret' => env('APS_CLIENT_SECRET'),
        ]);

        if ($response->failed()) {
            Log::error('Gagal Refresh Token User ID ' . $user->id);
            return null;
        }

        $data = $response->json();

        $user->update([
            'autodesk_access_token' => $data['access_token'],
            'autodesk_refresh_token' => $data['refresh_token'],
            'token_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return $data['access_token'];
    }

    // --- 2. MODEL DERIVATIVE ---
    
    private function makeSafeUrn($urn)
    {
        if (str_starts_with($urn, 'urn:')) $urn = base64_encode($urn);
        return rtrim(strtr($urn, '+/', '-_'), '=');
    }

    public function getManifest($urn, $token)
    {
        $safeUrn = $this->makeSafeUrn($urn);
        return Http::withToken($token)
            ->get($this->baseUrl . "/modelderivative/v2/designdata/{$safeUrn}/manifest")
            ->json();
    }

    public function translateToSvf($urn, $token)
    {
        $safeUrn = $this->makeSafeUrn($urn);
        $body = [
            'input' => ['urn' => $safeUrn],
            'output' => [['type' => 'svf', 'views' => ['2d', '3d']]],
            'destination' => ['region' => 'us']
        ];
        return Http::withToken($token)->withHeaders(['x-ads-force' => 'true'])
            ->post($this->baseUrl . "/modelderivative/v2/designdata/job", $body)->json();
    }

    /**
     * HYBRID FETCH PROPERTIES: Direct Download + Auto-Chunking Fallback
     */
    public function fetchModelProperties($urn, $token)
    {
        $safeUrn = $this->makeSafeUrn($urn);

        // A. Cari View GUID (3D) - Logika Lama yang Terbukti Benar
        $metaResponse = Http::withToken($token)->get($this->baseUrl . "/modelderivative/v2/designdata/{$safeUrn}/metadata");
        if ($metaResponse->failed()) return ['status' => 'error', 'message' => 'Gagal ambil metadata view.'];

        $views = $metaResponse->json()['data']['metadata'] ?? [];
        $viewGuid = null;

        // Prioritas 1: Cari role '3d'
        foreach ($views as $view) {
            if (isset($view['role']) && $view['role'] === '3d') {
                $viewGuid = $view['guid'];
                break; 
            }
        }

        // Prioritas 2: Fallback ke view pertama
        if (!$viewGuid && count($views) > 0) {
            $viewGuid = $views[0]['guid'];
        }

        if (!$viewGuid) {
            return ['status' => 'error', 'message' => "Tidak ditemukan View 3D. Total views: " . count($views)];
        }

        Log::info("🎯 Selected View GUID: $viewGuid for URN: $safeUrn");

        // B. Coba Download Langsung (Cara Cepat)
        $url = $this->baseUrl . "/modelderivative/v2/designdata/{$safeUrn}/metadata/{$viewGuid}/properties";
        
        // PENTING: Tambahkan forceget=true untuk menghindari cache lama yang mungkin corrupt
        $propResponse = Http::withToken($token)
            ->timeout(600) 
            ->get($url . '?forceget=true');

        // C. JIKA ERROR 413 (Payload Too Large) -> PINDAH KE CHUNKING
        if ($propResponse->status() == 413) {
            Log::info("⚠️ Payload Too Large (413). Switching to Chunking Mode...");
            return $this->fetchPropertiesChunked($safeUrn, $viewGuid, $token);
        }

        if ($propResponse->status() == 202) {
            return ['status' => 'processing', 'message' => "Autodesk sedang indexing properties (202)."];
        }

        if ($propResponse->failed()) {
             return ['status' => 'error', 'message' => "Gagal download properties. Code: " . $propResponse->status()];
        }

        // D. Sukses Direct Download
        return ['status' => 'success', 'data' => $propResponse->json()];
    }

    /**
     * FUNGSI CHUNKING: Solusi untuk File Besar
     */
    public function fetchPropertiesChunked($urn, $viewGuid, $token)
    {
        // 1. Ambil Object Tree (Daftar ID) - Ini endpoint ringan yang jarang error 413
        $treeUrl = $this->baseUrl . "/modelderivative/v2/designdata/{$urn}/metadata/{$viewGuid}";
        $treeResp = Http::withToken($token)->get($treeUrl . '?forceget=true');
        
        if ($treeResp->failed()) {
            return ['status' => 'error', 'message' => 'Gagal ambil Object Tree untuk chunking: ' . $treeResp->status()];
        }
        
        // 2. Ratakan (Flatten) Tree untuk dapat semua Object ID
        $allIds = [];
        $objects = $treeResp->json()['data']['objects'] ?? [];
        $this->flattenObjectIds($objects, $allIds);
        
        Log::info("📦 Chunking Mode: Found " . count($allIds) . " object IDs.");
        
        if (count($allIds) == 0) {
             return ['status' => 'error', 'message' => 'Object Tree kosong. View mungkin tidak memiliki elemen.'];
        }

        // 3. Download Properti per Batch (200 ID per request cukup aman)
        $chunks = array_chunk($allIds, 200); 
        $combinedCollection = [];
        $successCount = 0;
        
        foreach ($chunks as $index => $chunkIds) {
            // Gunakan endpoint POST untuk minta ID spesifik
            $postUrl = $this->baseUrl . "/modelderivative/v2/designdata/{$urn}/metadata/{$viewGuid}/properties";
            
            try {
                $batchResp = Http::withToken($token)->post($postUrl, [
                    'objectids' => $chunkIds
                ]);

                if ($batchResp->successful()) {
                    $batchData = $batchResp->json()['data']['collection'] ?? [];
                    $combinedCollection = array_merge($combinedCollection, $batchData);
                    $successCount++;
                    
                    if ($index % 5 == 0) Log::info("⬇️ Batch " . ($index + 1) . "/" . count($chunks) . " downloaded.");
                } else {
                    Log::warning("⚠️ Batch $index failed: " . $batchResp->status());
                }
            } catch (\Exception $e) {
                Log::warning("⚠️ Exception on batch $index: " . $e->getMessage());
            }
        }
        
        Log::info("✅ Chunking Complete. Total Objects Retrieved: " . count($combinedCollection));

        // Kembalikan format yang SAMA PERSIS dengan respons API standar
        return [
            'status' => 'success',
            'data' => [
                'data' => [
                    'collection' => $combinedCollection
                ]
            ]
        ];
    }

    // Helper Rekursif
    private function flattenObjectIds($nodes, &$ids)
    {
        foreach ($nodes as $node) {
            if (isset($node['objectid'])) {
                $ids[] = $node['objectid'];
            }
            if (isset($node['objects'])) {
                $this->flattenObjectIds($node['objects'], $ids);
            }
        }
    }
}  
