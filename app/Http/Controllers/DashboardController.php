<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord.
     */
    public function index()
    {
        // Vérifie si un utilisateur est connecté
        $user = Auth::user();

        // Passe les données de l'utilisateur à la vue 'dash'
        return view('layouts.index', compact('user'));
    }
}