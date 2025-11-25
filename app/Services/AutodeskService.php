<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutodeskService
{
    // Base URL untuk APS V2
    protected $baseUrl = 'https://developer.api.autodesk.com';

    /**
     * ==========================================
     * BAGIAN 1: AUTHENTICATION (Login & Token)
     * ==========================================
     */

    /**
     * Mendapatkan URL Authorization untuk redirect user.
     */
    public function getAuthorizationUrl()
    {
        $params = [
            'response_type' => 'code',
            'client_id' => env('APS_CLIENT_ID'),
            'redirect_uri' => env('APS_CALLBACK_URL'),
            // Scope lengkap untuk membaca data, menulis (jika perlu), melihat viewer, dan profil user
            'scope' => 'data:read data:write viewables:read user:read',
            'prompt' => 'login' // Wajib ada agar user bisa switch account
        ];

        return $this->baseUrl . '/authentication/v2/authorize?' . http_build_query($params);
    }

    /**
     * Menukar Authorization Code dengan Access Token.
     */
    public function getUserToken($code)
    {
        $response = Http::asForm()->post($this->baseUrl . '/authentication/v2/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => env('APS_CLIENT_ID'),
            'client_secret' => env('APS_CLIENT_SECRET'),
            'redirect_uri' => env('APS_CALLBACK_URL'),
        ]);

        return $response->json();
    }

    /**
     * Mendapatkan token valid dari User.
     * Jika token akan expired < 5 menit, otomatis refresh.
     */
    public function getValidUserToken(User $user)
    {
        // Jika token belum ada, kembalikan null (perlu login)
        if (!$user->token_expires_at) {
            return null;
        }

        // Cek apakah token expired dalam 5 menit ke depan
        $expiresAt = Carbon::parse($user->token_expires_at);
        if (now()->addMinutes(5)->greaterThanOrEqualTo($expiresAt)) {
            return $this->refreshUserToken($user);
        }

        return $user->autodesk_access_token;
    }

    /**
     * Refresh Token Logic (API V2).
     * Update database user secara langsung di sini.
     */
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

        // Update User DB Secara Langsung
        $user->update([
            'autodesk_access_token' => $data['access_token'],
            'autodesk_refresh_token' => $data['refresh_token'],
            'token_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return $data['access_token'];
    }

    /**
     * ==========================================
     * BAGIAN 2: MODEL DERIVATIVE (Proses Data)
     * ==========================================
     */

    /**
     * Helper: Membuat URN menjadi URL-Safe Base64.
     * API Model Derivative mewajibkan format ini (tanpa padding '=').
     */
    private function makeSafeUrn($urn)
    {
        // Jika URN masuk masih ada prefix 'urn:', kita anggap belum di-encode sama sekali
        if (str_starts_with($urn, 'urn:')) {
            $urn = base64_encode($urn);
        }
        
        // Ganti karakter +/ dengan -_ dan hapus padding =
        return rtrim(strtr($urn, '+/', '-_'), '=');
    }

    /**
     * Cek Status Manifest (Apakah model sudah siap/translated?).
     */
    public function getManifest($urn, $token)
    {
        $safeUrn = $this->makeSafeUrn($urn);
        
        return Http::withToken($token)
            ->get($this->baseUrl . "/modelderivative/v2/designdata/{$safeUrn}/manifest")
            ->json();
    }

    /**
     * Trigger Translation (Memaksa Autodesk meng-generate SVF/Data).
     * Digunakan jika status manifest 'n/a' atau 'failed'.
     */
    public function translateToSvf($urn, $token)
    {
        $safeUrn = $this->makeSafeUrn($urn);

        $body = [
            'input' => [
                'urn' => $safeUrn
            ],
            'output' => [
                'formats' => [
                    [
                        'type' => 'svf', 
                        'views' => ['2d', '3d'] // Generate view 2D dan 3D
                    ]
                ],
                'destination' => [
                    'region' => 'us' // Region default US
                ]
            ]
        ];

        return Http::withToken($token)
            ->withHeaders(['x-ads-force' => 'true']) // Header wajib untuk memaksa job
            ->post($this->baseUrl . "/modelderivative/v2/designdata/job", $body)
            ->json();
    }

    /**
     * Helper: Ambil Properties lengkap dari Model
     * Return: ['status' => 'success|processing|error', 'data' => ..., 'message' => ...]
     */
    public function fetchModelProperties($urn, $token)
    {
        $safeUrn = $this->makeSafeUrn($urn);

        // 1. Ambil Metadata (List Views)
        $metaResponse = Http::withToken($token)
            ->get($this->baseUrl . "/modelderivative/v2/designdata/{$safeUrn}/metadata");

        if ($metaResponse->failed()) {
            return ['status' => 'error', 'message' => "Gagal list view: " . $metaResponse->status()];
        }

        $metaData = $metaResponse->json();
        $views = $metaData['data']['metadata'] ?? [];

        // DEBUG: Log daftar view yang ditemukan ke Laravel Log (bisa dicek jika masih error)
        \Log::info("Views found for URN {$safeUrn}: " . count($views));

        // 2. Cari View GUID untuk scene 3D utama
        $viewGuid = null;
        
        // Prioritas 1: Cari role '3d'
        foreach ($views as $view) {
            if (isset($view['role']) && $view['role'] === '3d') {
                $viewGuid = $view['guid'];
                break; 
            }
        }

        // Prioritas 2: Fallback ke view pertama jika tidak ada tag 3d
        if (!$viewGuid && count($views) > 0) {
            $viewGuid = $views[0]['guid'];
        }

        if (!$viewGuid) {
            return ['status' => 'error', 'message' => "Tidak ditemukan View 3D. Total views: " . count($views)];
        }

        // 3. Ambil Properties menggunakan GUID tersebut
        $propResponse = Http::withToken($token)
            ->get($this->baseUrl . "/modelderivative/v2/designdata/{$safeUrn}/metadata/{$viewGuid}/properties");

        // HANDLE 202 ACCEPTED (Masih Processing)
        if ($propResponse->status() == 202) {
            return ['status' => 'processing', 'message' => "Autodesk sedang indexing properties (202)."];
        }

        if ($propResponse->failed()) {
             // Payload terlalu besar (413) atau error lain
             $msg = "Gagal download properties. Code: " . $propResponse->status();
             if ($propResponse->status() == 413) $msg .= " (File terlalu besar)";
             
             return ['status' => 'error', 'message' => $msg];
        }

        return ['status' => 'success', 'data' => $propResponse->json()];
    }
}