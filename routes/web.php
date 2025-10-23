<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ERESAuthController;
use App\Http\Controllers\AnomalieController;
use App\Http\Controllers\FormulaireController;
use App\Http\Controllers\DashboardController;

// ================= Page de Connexion =================
Route::get('/', function () {
    return view('auth.login');
});

// ================= QR Code =================
Route::get('/qrcode', function () {
    return view('qrcode');
});

// ================= Routes Invitées (non connectées) =================
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

// ================= Routes Authentifiées =================
Route::middleware('auth')->group(function () {
    // Déconnexion
    Route::post('/logout', [ERESAuthController::class, 'logout'])->name('logout');

    // Page du formulaire d’anomalie (accessible uniquement aux utilisateurs 'user')
    Route::middleware('role:user')->group(function () {
        Route::get('/formulaire', [AnomalieController::class, 'index'])->name('anomalie.index');
        Route::post('/formulaire', [AnomalieController::class, 'store'])->name('anomalie.store');
    });

    // Tableau de bord (accessible uniquement aux administrateurs 'admin')
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/anomalies/list', [AnomalieController::class, 'getAnomalies'])->name('anomalies.list');
    });
});