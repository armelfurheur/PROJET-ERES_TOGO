<?php

namespace App\Http\Controllers;

use App\Mail\SendAnomalieMail;
use Illuminate\Support\Facades\Mail;
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
            'localisation' => 'required|string|max:255',
            'gravity'      => 'required|string',
            'description'  => 'required|string',
            'action'       => 'required|string',
            'datetime'     => 'required|date',
            'preuves.*'    => 'nullable|image|max:2048',
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

        Mail::to('rosine@erestogo.com')->send(
            new SendAnomalieMail($anomalie)
        );

        return response()->json([
            'success' => true,
            'message' => 'Anomalie enregistrée et notification envoyée avec succès.'
        ]);
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $anomalies = Anomalie::orderBy('created_at', 'desc')->get();
        return view('statistics', compact('anomalies'));
    }

    /**
     * Liste des anomalies avec filtres
     */
    public function getAnomalies(Request $request)
    {
        $query = Anomalie::query();

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

        $anomalies = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'anomalies'     => $anomalies->items(),
            'current_page' => $anomalies->currentPage(),
            'last_page'    => $anomalies->lastPage(),
            'total'        => $anomalies->total(),
        ]);
    }

    /**
     * Anomalies du jour
     */
    public function getTodayAnomalies()
    {
        $anomalies = Anomalie::whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['anomalies' => $anomalies]);
    }

    /**
     * Anomalies clôturées avec propositions
     */
    public function getClosedAnomaliesWithProposals()
    {
        $anomalies = Anomalie::with('propositions')
            ->where('status', 'Clôturée')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'anomalies'     => $anomalies->items(),
            'current_page' => $anomalies->currentPage(),
            'last_page'    => $anomalies->lastPage(),
            'total'        => $anomalies->total(),
            'per_page'     => $anomalies->perPage(),
        ]);
    }

    /**
     * Détails d'une anomalie
     */
    public function getAnomalie($id)
    {
        return response()->json([
            'anomalie' => Anomalie::findOrFail($id)
        ]);
    }

    /**
     * Mise à jour du statut
     */
    public function updateStatus(Request $request, $id)
    {
        $anomalie = Anomalie::with('propositions')->findOrFail($id);
        $nouveauStatut = $request->status;

        if ($nouveauStatut === 'Clôturée') {
            $restantes = $anomalie->propositions()
                ->where('status', '!=', 'Clôturée')
                ->count();

            if ($restantes > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certaines propositions ne sont pas clôturées.'
                ], 400);
            }
        }

        $anomalie->update(['status' => $nouveauStatut]);

        return response()->json([
            'success' => true,
            'message' => "Statut mis à jour : {$nouveauStatut}",
            'anomalie' => $anomalie
        ]);
    }

    /**
     * Génération de rapport
     */
   public function generateReport(Request $request)
{
    $type = $request->input('type');
    $month = $request->input('reportMonth');
    $year = $request->input('reportYear');

    // Déterminer la période en fonction du type
    if ($type === 'month' && $month) {
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
    } elseif ($type === 'year' && $year) {
        $start = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $end = Carbon::createFromDate($year, 12, 31)->endOfYear();
    } else {
        return response()->json(['error' => 'Type ou période invalide.'], 400);
    }

    // Récupérer les anomalies dans la période
    $anomalies = Anomalie::with('propositions')
        ->whereBetween('datetime', [$start, $end])
        ->get();

    // Si aucune anomalie n'est trouvée
    if ($anomalies->isEmpty()) {
        return response()->json([
            'message' => 'Aucune anomalie trouvée.',
            'data' => [],
            'statistiques' => [
                'total' => 0,
                'cloturees' => 0,
                'ouvertes' => 0,
                'utilisateur_top' => ['nom' => 'Aucun', 'nombre' => 0],
                'par_gravite' => [],
                'par_departement' => [],
                'mensuel' => [],
                'mensuel_cloturees' => [],
                'mensuel_ouvertes' => []
            ],
            'periode' => [
                'debut' => $start->toDateString(),
                'fin' => $end->toDateString()
            ]
        ]);
    }

    // Statistiques de base
    $totalAnomalies = $anomalies->count();
    $closed = $anomalies->where('status', 'Clôturée')->count();
    $open = $totalAnomalies - $closed;

    // Utilisateur le plus actif
    $topUser = $anomalies->groupBy('rapporte_par')->map->count()->sortDesc()->take(1);
    $topUserName = $topUser->keys()->first() ?? 'Aucun';
    $topUserCount = $topUser->first() ?? 0;

    // Répartition par gravité et par département
    $parGravite = $anomalies->groupBy('gravity')->map->count();
    $parDepartement = $anomalies->groupBy('departement')->map->count();

    // Initialisation des tableaux mensuels (12 mois)
    $mensuel = array_fill(0, 12, 0);
    $mensuelCloturees = array_fill(0, 12, 0);
    $mensuelOuvertes = array_fill(0, 12, 0);

    if ($type === 'year') {
        // Calcul des statistiques mensuelles pour l'année
        foreach ($anomalies as $anomalie) {
            $monthIndex = (int)$anomalie->datetime->format('m') - 1; // 0-11 pour les index du tableau
            $mensuel[$monthIndex]++;
            
            if ($anomalie->status === 'Clôturée') {
                $mensuelCloturees[$monthIndex]++;
            } else {
                $mensuelOuvertes[$monthIndex]++;
            }
        }
    } else {
        // Pour le mode mois, on met les totaux dans des tableaux à un élément
        $mensuel = [$totalAnomalies];
        $mensuelCloturees = [$closed];
        $mensuelOuvertes = [$open];
    }

    // Formatage des données des anomalies pour la réponse
    $anomaliesData = $anomalies->map(function($a) {
        return [
            'id' => $a->id,
            'description' => $a->description,
            'localisation' => $a->localisation,
            'gravity' => $a->gravity,
            'departement' => $a->departement,
            'status' => $a->status,
            'propositions' => $a->propositions->pluck('description')->toArray()
        ];
    });

    return response()->json([
        'periode' => [
            'debut' => $start->format('Y-m-d'),
            'fin' => $end->format('Y-m-d'),
        ],
        'statistiques' => [
            'total' => $totalAnomalies,
            'cloturees' => $closed,
            'ouvertes' => $open,
            'utilisateur_top' => [
                'nom' => $topUserName,
                'nombre' => $topUserCount
            ],
            'par_gravite' => $parGravite->toArray(),
            'par_departement' => $parDepartement->toArray(),
            'mensuel' => $mensuel, // Total anomalies par mois
            'mensuel_cloturees' => $mensuelCloturees, // Anomalies clôturées par mois
            'mensuel_ouvertes' => $mensuelOuvertes // Anomalies ouvertes par mois
        ],
        'data' => $anomaliesData
    ]);
}
}
