<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anomalie;
use Carbon\Carbon;

class AnomalieController extends Controller
{
    /**
     * Affiche le formulaire d'enregistrement d'une anomalie.
     */
    public function index()
    {
        return view('layouts.formulaire');
    }

    public function showAnomaliesView()
    {
        return view('anomalie'); // ou 'anomalies' selon le nom réel de ta vue Blade
    }





    public function store(Request $request)
    {
        $validated = $request->validate([
            'rapporte_par' => 'required|string|max:255',
            'departement' => 'required|string|max:255',
            'localisation' => 'required|string|max:255',
            'gravity' => 'required|string',
            'description' => 'required|string',
            'action' => 'required|string',
            'datetime' => 'required|date',
            'preuve' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('preuve')) {
            $path = $request->file('preuve')->store('preuves', 'public');
            $validated['preuve'] = $path;
        }

        $anomalie = Anomalie::create($validated);

        // Réponse JSON pour les requêtes AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'anomalie' => $anomalie,
                'message' => 'Anomalie enregistrée avec succès.'
            ]);
        }

        // Redirection normale
        return redirect()->route('anomalie.index')
            ->with('success', 'Anomalie enregistrée avec succès.');
    }


    public function dashboard()
    {
        $anomalies = Anomalie::orderBy('created_at', 'desc')->get();
        return view('statistics', compact('anomalies'));
    }


    public function getAnomalies(Request $request)
    {
        $query = Anomalie::query();

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $priorityMap = [
                'arret' => '🚨 Arrêt Imminent',
                'precaution' => '⚠ Précaution',
                'continuer' => '🟢 Continuer'
            ];
            $query->where('gravity', $priorityMap[$request->priority]);
        }

        if ($request->filled('department')) {
            $query->where('departement', 'like', '%' . $request->department . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('datetime', $request->date);
        }

        $anomalies = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'anomalies' => $anomalies->items(),
            'current_page' => $anomalies->currentPage(),
            'last_page' => $anomalies->lastPage(),
            'total' => $anomalies->total(),
        ]);
    }



    public function getTodayAnomalies()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $anomalies = Anomalie::whereBetween('datetime', [$today, $tomorrow])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'anomalies' => $anomalies
        ]);
    }

    public function getClosedAnomaliesWithProposals(Request $request)
    {
        $anomalies = Anomalie::with('propositions')
            ->where('status', 'Clôturée')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'anomalies' => $anomalies->items(),
            'current_page' => $anomalies->currentPage(),
            'last_page' => $anomalies->lastPage(),
            'total' => $anomalies->total(),
            'per_page' => $anomalies->perPage(),
        ]);
    }


    public function getAnomalie($id)
    {
        $anomalie = Anomalie::findOrFail($id);

        return response()->json([
            'anomalie' => $anomalie
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $anomalie = Anomalie::with('propositions')->findOrFail($id);
        $nouveauStatut = $request->input('status');
        if ($nouveauStatut === 'Clôturée') {
            $propositionsNonCloturees = $anomalie->propositions()
                ->where('status', '!=', 'Clôturée')
                ->count();

            if ($propositionsNonCloturees > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de clôturer cette anomalie : certaines propositions associées ne sont pas encore clôturées.'
                ], 400);
            }
        }

        // Mise à jour du statut
        $anomalie->status = $nouveauStatut;
        $anomalie->save();

        return response()->json([
            'success' => true,
            'message' => "Le statut de l'anomalie a été mis à jour en '{$nouveauStatut}'.",
            'anomalie' => $anomalie
        ]);
    }
}
