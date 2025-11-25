<?php

use Illuminate\Support\Facades\Route;
use App\Services\AutodeskService;
use App\Http\Controllers\AutodeskAuthController;
use App\Livewire\ModelInspector;
use App\Livewire\ProjectCostCalculator;
use App\Livewire\CostDatabase;
use App\Livewire\ProjectDashboard;
use App\Livewire\CostLibraryManager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/inspector', ModelInspector::class)->middleware(['auth', 'verified'])->name('model-inspector');

Route::get('/estimasi', ProjectCostCalculator::class)->middleware(['auth', 'verified'])->name('cost-calculator');
Route::get('/master-harga', CostDatabase::class)->middleware(['auth', 'verified'])->name('cost-database');
Route::get('/dashboard/{id}', ProjectDashboard::class)->middleware(['auth', 'verified'])->name('project-dashboard');
Route::get('/libraries', CostLibraryManager::class)->middleware(['auth', 'verified'])->name('cost-libraries');

Route::get('/test-token', function () {
    $service = new AutodeskService();
    return $service->getAuthorizationUrl();
});

Route::middleware('auth')->group(function () {
    Route::get('/auth/autodesk/redirect', [AutodeskAuthController::class, 'redirect'])->name('autodesk.login');
    Route::get('/auth/autodesk/callback', [AutodeskAuthController::class, 'callback']); // URL ini harus sama persis dengan .env
    Route::post('/auth/autodesk/disconnect', [AutodeskAuthController::class, 'disconnect'])->name('autodesk.disconnect');
});

require __DIR__.'/auth.php';
