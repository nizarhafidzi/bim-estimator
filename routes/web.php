<?php

use Illuminate\Support\Facades\Route;
use App\Services\AutodeskService;
use App\Http\Controllers\AutodeskAuthController;
use App\Livewire\ModelInspector;
use App\Livewire\ProjectCostCalculator;
use App\Livewire\CostDatabase;
use App\Livewire\ProjectDashboard;
use App\Livewire\CostLibraryManager;
use App\Livewire\Actions\Logout;
use App\Livewire\AhspBuilder;
use App\Livewire\ResourceManager;
use App\Livewire\ProjectFileManage;
use App\Livewire\ProjectReport;
use App\Livewire\Documentation;

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

Route::get('/docs', Documentation::class)->name('documentation');

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

Route::get('/file-dashboard/{fileId}', ProjectDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('file-dashboard');
    
Route::get('/libraries', CostLibraryManager::class)->middleware(['auth', 'verified'])->name('cost-libraries');
Route::get('/builder/{libraryId}', AhspBuilder::class)->middleware(['auth'])->name('ahsp-builder');
Route::get('/library/{libraryId}/resources', ResourceManager::class)->middleware(['auth', 'verified'])->name('resource-manager');
Route::get('/project/{id}/report', ProjectReport::class)->middleware(['auth'])->name('project-report');

Route::get('/test-token', function () {
    $service = new AutodeskService();
    return $service->getAuthorizationUrl();
});

Route::get('/project/{projectId}/files', ProjectFileManage::class)
    ->middleware(['auth'])
    ->name('project.files');

Route::middleware('auth')->group(function () {
    Route::get('/auth/autodesk/redirect', [AutodeskAuthController::class, 'redirect'])->name('autodesk.login');
    Route::get('/auth/autodesk/callback', [AutodeskAuthController::class, 'callback']); // URL ini harus sama persis dengan .env
    Route::post('/auth/autodesk/disconnect', [AutodeskAuthController::class, 'disconnect'])->name('autodesk.disconnect');
});

Route::post('logout', function (Logout $logout) {
    $logout();
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
