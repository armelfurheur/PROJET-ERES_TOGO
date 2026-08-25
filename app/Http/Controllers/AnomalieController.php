<?php

namespace App\Http\Controllers;

use App\Mail\SendAnomalieMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Anomalie;
use Carbon\Carbon;
use App\Mail\AnomalyDetailsMail;

class AnomalieController extends Controller
{
    /* ======================================================
        VUES
    ====================================================== */

    public function index()
    {
        return view('layouts.formulaire');
    }

    public function showAnomaliesView()
    {
        return view('anomalie');
    }

 
    /**
     * Enregistrement d'une anomalie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rapporte_par' => 'required|string|max:255',
            'departement'  => 'required|string|max:255',
            'structure'    => 'required|string|in:ERES,RAST',
            'localisation' => 'required|string|max:255',
            'gravity'      => 'required|string',
            'description'  => 'required|string',
            'action'       => 'required|string',
            'datetime'     => 'required|date',
            'preuves'      => 'nullable|array',
            'preuves.*'    => 'nullable|image|max:5120',
        ]);

        /* ===============================
           Gestion des images
        =============================== */
        $imagePaths = [];

        if ($request->hasFile('preuves')) {
            foreach ($request->file('preuves') as $image) {
                if ($image && $image->isValid()) {
                    $imagePaths[] = $image->store('preuves', 'public');
                }
            }
        }

        $validated['preuve'] = !empty($imagePaths)
            ? json_encode($imagePaths)
            : null;

        $validated['status'] = 'Ouverte';

        $anomalie = Anomalie::create($validated);

        Mail::to('zahir@gmail.com')->send(
            new SendAnomalieMail($anomalie)
        );

        return response()->json([
            'success' => true,
            'message' => 'Anomalie enregistrée et notification envoyée avec succès.'
        ]);
    }



    /* ===================================================
        DASHBOARD
    ====================================================== */

    public function dashboard()
    {
        return view('statistics', [
            'eres' => [
                'total'     => Anomalie::where('structure', 'ERES')->count(),
                'ouvertes'  => Anomalie::where('structure', 'ERES')->where('status', 'Ouverte')->count(),
                'cloturees' => Anomalie::where('structure', 'ERES')->where('status', 'Clôturée')->count(),
            ],
            'rast' => [
                'total'     => Anomalie::where('structure', 'RAST')->count(),
                'ouvertes'  => Anomalie::where('structure', 'RAST')->where('status', 'Ouverte')->count(),
                'cloturees' => Anomalie::where('structure', 'RAST')->where('status', 'Clôturée')->count(),
            ]
        ]);
    }

    /* ======================================================
        LISTE DES ANOMALIES (FILTRES + PAGINATION)
    ====================================================== */

    public function getAnomalies(Request $request)
    {
        $query = Anomalie::query();

        if ($request->filled('structure')) {
            $query->where('structure', $request->structure);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('gravity', $request->priority);
        }

        if ($request->filled('department')) {
            $query->where('departement', 'like', '%' . $request->department . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
       if ($request->filled('start_date') && $request->filled('end_date')) {

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $query->whereBetween('created_at', [$start, $end]);
    }
      
        $countsQuery = clone $query;

        $anomalies = $query
            ->orderByDesc('created_at')
            ->paginate(20);

       
        $ouvertes  = (clone $countsQuery)->where('status', 'Ouverte')->count();
        $cloturees = (clone $countsQuery)->where('status', 'Clôturée')->count();

        $parGravite = (clone $countsQuery)
            ->select('gravity', DB::raw('count(*) as total'))
            ->groupBy('gravity')
            ->pluck('total', 'gravity');

        $parDepartement = (clone $countsQuery)
            ->select('departement', DB::raw('count(*) as total'))
            ->groupBy('departement')
            ->pluck('total', 'departement');

        return response()->json([
            'anomalies'     => $anomalies->items(),
            'current_page' => $anomalies->currentPage(),
            'last_page'    => $anomalies->lastPage(),
            'total'        => $anomalies->total(),
            'ouvertes'     => $ouvertes,
            'cloturees'    => $cloturees,
            'par_gravite'     => $parGravite,
            'par_departement' => $parDepartement,
        ]);
    }

    public function getTodayAnomalies()
    {
        return response()->json([
            'anomalies' => Anomalie::whereDate('created_at', Carbon::today())
                ->orderByDesc('created_at')
                ->get()
        ]);
    }

    /* ======================================================
        ANOMALIES CLÔTURÉES AVEC PROPOSITIONS
    ====================================================== */

    public function getClosedAnomaliesWithProposals()
    {
        $anomalies = Anomalie::with('propositions')
            ->where('status', 'Clôturée')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return response()->json([
            'anomalies'     => $anomalies->items(),
            'current_page' => $anomalies->currentPage(),
            'last_page'    => $anomalies->lastPage(),
            'total'        => $anomalies->total(),
        ]);
    }

    /* ======================================================
        DÉTAIL D'UNE ANOMALIE
    ====================================================== */

    public function getAnomalie($id)
    {
        return response()->json([
            'anomalie' => Anomalie::findOrFail($id)
        ]);
    }

    /* ======================================================
        MISE À JOUR DU STATUT
    ====================================================== */

    public function updateStatus(Request $request, $id)
    {
        $anomalie = Anomalie::with('propositions')->findOrFail($id);
        $status = $request->status;

        if ($status === 'Clôturée') {
            $nonCloturees = $anomalie->propositions()
                ->where('status', '!=', 'Clôturée')
                ->count();

            if ($nonCloturees > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certaines propositions ne sont pas clôturées.'
                ], 400);
            }
        }

        $anomalie->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => "Statut mis à jour : {$status}",
            'anomalie' => $anomalie
        ]);
    }


    /**
 * Supprimer une anomalie
 */
public function destroy($id)
{
    $anomalie = Anomalie::findOrFail($id);

    try {
        $anomalie->delete();
        return response()->json([
            'success' => true,
            'message' => 'Anomalie supprimée avec succès.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Impossible de supprimer l\'anomalie.'
        ], 500);
    }
}

    /* ======================================================
        GÉNÉRATION DE RAPPORT
    ====================================================== */

   public function generateReport(Request $request)
{
    $type      = $request->input('type');
    $month     = $request->input('reportMonth');
    $year      = $request->input('reportYear');
    $structure = $request->input('structure');

    if (!in_array($structure, ['ERES', 'RAST', 'GLOBAL'])) {
        return response()->json(['error' => 'Structure invalide'], 400);
    }

    if ($type === 'month' && $month) {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
    } elseif ($type === 'year' && $year) {
        $start = Carbon::create($year, 1, 1)->startOfYear();
        $end   = Carbon::create($year, 12, 31)->endOfYear();
    } else {
        return response()->json(['error' => 'Période invalide'], 400);
    }

    $query = Anomalie::with('propositions')
        ->whereBetween('datetime', [$start, $end]);

    if ($structure !== 'GLOBAL') {
        $query->where('structure', $structure);
    }
    $anomalies = $query->get();

    $total = $anomalies->count();
    $closed = $anomalies->where('status', 'Clôturée')->count();
    $mensuel = [];
    $mensuelCloturees = [];

    for ($i = 1; $i <= 12; $i++) {
        $mensuel[$i] = $anomalies
            ->filter(fn($a) => Carbon::parse($a->datetime)->month == $i)
            ->count();

        $mensuelCloturees[$i] = $anomalies
            ->filter(fn($a) =>
                Carbon::parse($a->datetime)->month == $i &&
                $a->status == 'Clôturée'
            )
            ->count();
    }

    // Calculer les utilisateurs les plus actifs ERES
    // Grouper directement par 'rapporte_par' (le nom)
    $topUsersEres = $anomalies
        ->where('structure', 'ERES')
        ->filter(fn($a) => !empty($a->rapporte_par)) // Filtrer les anomalies sans nom
        ->groupBy('rapporte_par')
        ->map(function ($group) {
            return [
                'nom' => $group->first()->rapporte_par, // Utiliser directement rapporte_par
                'nombre' => $group->count()
            ];
        })
        ->sortByDesc('nombre')
        ->take(2)
        ->values()
        ->toArray();

    // Calculer les utilisateurs les plus actifs RAST
    $topUsersRast = $anomalies
        ->where('structure', 'RAST')
        ->filter(fn($a) => !empty($a->rapporte_par)) // Filtrer les anomalies sans nom
        ->groupBy('rapporte_par')
        ->map(function ($group) {
            return [
                'nom' => $group->first()->rapporte_par, // Utiliser directement rapporte_par
                'nombre' => $group->count()
            ];
        })
        ->sortByDesc('nombre')
        ->take(2)
        ->values()
        ->toArray();

    return response()->json([
        'periode' => [
            'debut' => $start->toDateString(),
            'fin'   => $end->toDateString(),
        ],
        'statistiques' => [
            'total'     => $total,
            'cloturees' => $closed,
            'ouvertes'  => $total - $closed,
            'par_gravite'     => $anomalies->groupBy('gravity')->map->count(),
            'par_departement' => $anomalies->groupBy('departement')->map->count(),
            'mensuel' => array_values($mensuel),
            'mensuel_cloturees' => array_values($mensuelCloturees),
            
            // Utilisateurs les plus actifs
            'utilisateurs_top_eres' => $topUsersEres,
            'utilisateurs_top_rast' => $topUsersRast,
        ],
        'data' => $anomalies
    ]);
}
}
