<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anomalie;

class AnomalieController extends Controller
{
    /**
     * Affiche le formulaire d’enregistrement d’une anomalie.
     */
    public function index()
    {
        return view('layouts.formulaire');
    }

    /**
     * Enregistre une anomalie dans la base de données,
     * puis affiche le tableau de bord avec les données mises à jour.
     */
  public function store(Request $request)
{
    $validated = $request->validate([
        'rapporte_par' => 'required|string|max:255',
        'departement' => 'required|string|max:255',
        'localisation' => 'required|string|max:255',
        'statut' => 'required|string',
        'description' => 'required|string',
        'action' => 'required|string',
        'datetime' => 'required|date',
        'preuve' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('preuve')) {
        $path = $request->file('preuve')->store('preuves', 'public');
        $validated['preuve'] = $path;
    }

    Anomalie::create($validated);

    // Redirection vers le formulaire avec message de succès
    return redirect()->route('anomalie.index')
                     ->with('success', 'Anomalie enregistrée avec succès.');
}

    /**
     * Affiche le tableau de bord avec toutes les anomalies.
     */
    public function dashboard()
    {
        $anomalies = Anomalie::orderBy('created_at', 'desc')->get();
        return view('layouts.index', compact('anomalies'));
    }
}
