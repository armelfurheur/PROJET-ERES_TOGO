<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAccessController extends Controller
{

  /**
     * Redirection après connexion ou inscription
     */
    protected function redirectTo()
    {
        return Auth::user()->role === 'admin' ? '/dashboard' : '/formulaire';
    }
    // Vérifier mot de passe maître
    public function checkMasterPassword(Request $request)
    {
        // Récupère le mot de passe depuis .env ou fallback
        $masterPassword = env('ADMIN_MASTER_PASSWORD', '@lert*r!sk');

        // Comparaison exacte
        if ($request->password === $masterPassword) {
            // Retourne l’URL de redirection vers le dashboard
            return response()->json([
                'access' => true,
                'redirect' => route('admin.dashboard') // ← Vérifie que cette route existe
            ]);
        }

        return response()->json(['access' => false]);
    }

    // Page dashboard admin
    public function dashboard()
    {
        return view('admin.dashboardadminP', [
            'users' => \App\Models\User::all(),
        ]);
    }
}
