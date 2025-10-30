<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropositionController;
use App\Http\Controllers\Api\ArchiveController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes existantes...

// Routes pour les archives
Route::get('/archives', [ArchiveController::class, 'index'])->name('api.archives');
Route::post('/anomalies/{id}/close', [ArchiveController::class, 'closeAnomaly'])->name('api.anomalies.close');
Route::post('/archives/{id}/restore', [ArchiveController::class, 'restore'])->name('api.archives.restore');
Route::delete('/archives/{id}', [ArchiveController::class, 'destroy'])->name('api.archives.destroy');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// routes/api.php

// Routes existantes...
Route::get('/anomalies', [AnomalieController::class, 'index'])->name('api.anomalies');
Route::get('/anomalies/{id}', [AnomalieController::class, 'show'])->name('api.anomalies.show');

// Nouvelles routes pour les propositions
Route::post('/anomalies/{id}/propositions', [PropositionController::class, 'store'])->name('api.propositions.store');
Route::put('/propositions/{id}', [PropositionController::class, 'update'])->name('api.propositions.update');
Route::delete('/propositions/{id}', [PropositionController::class, 'destroy'])->name('api.propositions.destroy');