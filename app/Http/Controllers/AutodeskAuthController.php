<?php

namespace App\Http\Controllers;

use App\Services\AutodeskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AutodeskAuthController extends Controller
{
    protected $autodeskService;

    public function __construct(AutodeskService $autodeskService)
    {
        $this->autodeskService = $autodeskService;
    }

    public function redirect()
    {
        return redirect()->away($this->autodeskService->getAuthorizationUrl());
    }

    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('dashboard')->with('error', 'Autodesk login failed.');
        }

        // 1. Tukar Code dengan Token
        $tokenData = $this->autodeskService->getUserToken($request->code);

        if (!isset($tokenData['access_token'])) {
            return redirect()->route('dashboard')->with('error', 'Failed to get access token.');
        }

        // 2. Ambil Info User dari Autodesk (DENGAN PENANGANAN ERROR)
        $accountName = 'Autodesk User'; // Default fallback name
        
        try {
            $profileResponse = Http::withToken($tokenData['access_token'])
                ->get('https://api.userprofile.autodesk.com/user/v1/users/@me');

            if ($profileResponse->successful()) {
                $userProfile = $profileResponse->json();
                // Cek apakah key ada sebelum mengakses
                $firstName = $userProfile['firstName'] ?? '';
                $lastName = $userProfile['lastName'] ?? '';
                $accountName = trim("$firstName $lastName") ?: 'Autodesk User';
            }
        } catch (\Exception $e) {
            // Jika gagal ambil nama, biarkan silent (pakai default name)
            // Log error jika perlu: \Log::error($e->getMessage());
        }

        // 3. Simpan ke Database User yang sedang login
        $user = Auth::user();
        $user->update([
            'autodesk_access_token' => $tokenData['access_token'],
            'autodesk_refresh_token' => $tokenData['refresh_token'],
            'token_expires_at' => now()->addSeconds($tokenData['expires_in']),
            'autodesk_account_name' => $accountName,
        ]);

        return redirect()->route('dashboard')->with('status', 'Connected to Autodesk ACC!');
    }

    public function disconnect()
    {
        $user = Auth::user();
        $user->update([
            'autodesk_access_token' => null,
            'autodesk_refresh_token' => null,
            'token_expires_at' => null,
            'autodesk_account_name' => null,
        ]);

        return redirect()->route('dashboard')->with('status', 'Disconnected from Autodesk.');
    }
}