<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ERESAuthController;
use App\Http\Controllers\AnomalieController;
use App\Http\Controllers\FormulaireController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProposalController;

// ================= Page de Connexion =================
Route::get('/', function () {
    return view('auth.login');
});
// ================= Other Public Route ===============
Route::get('/anomalies/closed', [AnomalieController::class, 'getClosedAnomaliesWithProposals'])
    ->name('anomalies.closed');
Route::post('/proposals/{id}/close', [ProposalController::class, 'close'])->name('proposals.close');
Route::put('/anomalies/{id}/update-status', [AnomalieController::class, 'updateStatus'])->name('anomalies.updateStatus');
Route::post('/reports/generate', [AnomalieController::class, 'generateReport'])->name('generate.report');
 
// ================= QR Code =================
Route::get('/qrcode', function () {
    return view('qrcode');
});

// ================= Routes Invitées (guest) =================
Route::middleware('guest')->group(function () {
    // Connexion
    Route::get('/login', [ERESAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ERESAuthController::class, 'login']);

    // Inscription
    Route::get('/register', [ERESAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ERESAuthController::class, 'register']);

    // Mot de passe oublié
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('forgot-password', [ERESAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ERESAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ERESAuthController::class, 'reset'])->name('password.update');
});

// ================= Routes Authentifiées (auth) =================
Route::middleware('auth')->group(function () {

    // Déconnexion
    Route::post('/logout', [ERESAuthController::class, 'logout'])->name('logout');

    // API info utilisateur
    Route::get('/api/user-info', function () {
        return response()->json([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'initials' => strtoupper(substr(Auth::user()->name, 0, 2))
        ]);
    });

    // ================= Page du formulaire d’anomalie (role:user) =================
    Route::middleware('role:user')->group(function () {
        Route::get('/formulaire', [AnomalieController::class, 'index'])->name('anomalie.index');
        Route::post('/formulaire', [AnomalieController::class, 'store'])->name('anomalie.store');
    });

    // ================= Tableau de bord et gestion admin (role:admin) =================
  Route::middleware('role:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/anomalies/list', [AnomalieController::class, 'getAnomalies'])->name('anomalies.list');
    Route::get('/anomalies/today', [AnomalieController::class, 'getTodayAnomalies'])->name('anomalies.today'); // ← nouvelle route
    Route::get('/anomalies/{id}', [AnomalieController::class, 'getAnomalie'])->name('anomalies.show');   
   
    
});


    // ================= Sidebar Routes =================
    Route::get('/anomalies', [AnomalieController::class, 'showAnomaliesView'])->name('anomalies.view');
    Route::get('/statistics', [DashboardController::class, 'showStatisticsView'])->name('statistics.view');
    Route::get('/proposition', [DashboardController::class, 'showPropositionView'])->name('proposition.view');
    Route::get('/rapport', [DashboardController::class, 'showRapportView'])->name('rapport.view');
    Route::get('/configuration', [DashboardController::class, 'showConfigurationView'])->name('configuration.view');
    Route::get('/archive', [DashboardController::class, 'showArchiveView'])->name('archive.view');
    Route::get('/corbeille', [DashboardController::class, 'showCorbeilleView'])->name('corbeille.view');


    // ================= Routes Proposals =================
    Route::post('/proposals', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('/proposals/list/{anomalie}', [ProposalController::class, 'getProposalsByAnomalie'])->name('proposals.list');
   
});
