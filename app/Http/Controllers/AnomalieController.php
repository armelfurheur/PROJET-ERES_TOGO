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
        'preuves.*' => 'nullable|image|max:2048', // Validation pour chaque image
    ]);

    $imagePaths = [];

    // Vérifie s’il y a au moins une image envoyée
    if ($request->hasFile('preuves')) {
        foreach ($request->file('preuves') as $image) {
            if ($image && $image->isValid()) {
                $path = $image->store('preuves', 'public');
                $imagePaths[] = $path;
            }
        }
    }

    // Stocker les chemins des images sous forme JSON (même si une seule)
    $validated['preuve'] = count($imagePaths) > 0 ? json_encode($imagePaths) : null;

    $anomalie = Anomalie::create($validated);

    // Réponse JSON pour AJAX
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
                'arret' => 'arret',
                'precaution' => 'precaution',
                'continuer' => 'continuer'
            ];
            $query->where('gravity', $priorityMap[$request->priority]);
        }

        if ($request->filled('department')) {
            $query->where('departement', 'like', '%' . $request->department . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
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

        $anomalies = Anomalie::whereBetween('created_at', [$today, $tomorrow])
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

        $anomalie->status = $nouveauStatut;
        $anomalie->save();

        return response()->json([
            'success' => true,
            'message' => "Le statut de l'anomalie a été mis à jour en '{$nouveauStatut}'.",
            'anomalie' => $anomalie
        ]);
    }


// ...

public function generateReport(Request $request)
{
    $type = $request->input('type');
    $month = $request->input('reportMonth');
    $year = $request->input('reportYear');

    if ($type === 'month' && $month) {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
    } elseif ($type === 'year' && $year) {
        $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $end = Carbon::createFromDate($year, 12, 31)->endOfYear();
    } else {
        return response()->json(['error' => 'Type ou période invalide.'], 400);
    }

    $anomalies = Anomalie::with('propositions')
        ->whereBetween('datetime', [$start, $end])
        ->get();

    if ($anomalies->isEmpty()) {
        return response()->json([
            'message' => 'Aucune anomalie trouvée.',
            'data' => [],
            'statistiques' => [
                'total' => 0, 'cloturees' => 0, 'ouvertes' => 0,
                'utilisateur_top' => ['nom' => 'Aucun', 'nombre' => 0],
                'par_gravite' => [], 'par_departement' => [], 'mensuel' => []
            ],
            'periode' => ['debut' => $start->toDateString(), 'fin' => $end->toDateString()]
        ]);
    }

    // Stats de base
    $totalAnomalies = $anomalies->count();
    $closed = $anomalies->where('status', 'Clôturée')->count();
    $open = $totalAnomalies - $closed;

    $topUser = $anomalies->groupBy('rapporte_par')->map->count()->sortDesc()->take(1);
    $topUserName = $topUser->keys()->first() ?? 'Aucun';
    $topUserCount = $topUser->first() ?? 0;

    $parGravite = $anomalies->groupBy('gravity')->map->count();
    $parDepartement = $anomalies->groupBy('departement')->map->count();

    // Compteur mensuel pour l'année
    $mensuel = [];
    if ($type === 'year') {
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = Carbon::createFromDate($year, $m, 1)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $mensuel[] = $anomalies->whereBetween('datetime', [$monthStart, $monthEnd])->count();
        }
    }

    $anomaliesData = $anomalies->map(fn($a) => [
        'id' => $a->id,
        'description' => $a->description,
        'localisation' => $a->localisation,
        'gravity' => $a->gravity,
        'departement' => $a->departement,
        'status' => $a->status,
        'propositions' => $a->propositions->pluck('description')->toArray()
    ]);

    return response()->json([
        'periode' => [
            'debut' => $start->format('Y-m-d'),
            'fin' => $end->format('Y-m-d'),
        ],
        'statistiques' => [
            'total' => $totalAnomalies,
            'cloturees' => $closed,
            'ouvertes' => $open,
            'utilisateur_top' => ['nom' => $topUserName, 'nombre' => $topUserCount],
            'par_gravite' => $parGravite->toArray(),
            'par_departement' => $parDepartement->toArray(),
            'mensuel' => $mensuel // <-- pour graphique annuel
        ],
        'data' => $anomaliesData
    ]);
}

}

