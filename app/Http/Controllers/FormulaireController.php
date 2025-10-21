<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FormulaireController extends Controller
{
    // Affiche le formulaire (GET)
    public function showForm(Request $request)
    {
        // Si on revient en mode "modifier", les anciennes valeurs sont déjà en session
        return view('formulaire');
    }

    // Traite la soumission du formulaire (POST)
    public function store(Request $request)
    {
        // Ici, ajoute la logique d'enregistrement si besoin
        // ...

        // On stocke les anciennes valeurs pour le bouton "Modifier"
        return redirect()->route('formulaire')
            ->with('success', 'Rapport soumis avec succès !')
            ->with('old', $request->except(['_token', 'preuve']));
    }
}