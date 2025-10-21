<?php

namespace App\Http\Controllers;

use App\Models\Anomalie;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\PDF;

class DashboardController extends Controller
{
    /**
     * Affiche le dashboard avec les statistiques et les graphiques.
     */
    public function index(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Récupérer les filtres de la requête
        $status = $request->input('status', 'all');
        $priority = $request->input('priority', 'all');
        $department = $request->input('department');
        $date = $request->input('date');
        $month = $request->input('month', 'all');
        $year = $request->input('year', date('Y'));

        // Construire la requête pour les anomalies
        $query = Anomalie::query()->with(['user', 'proposals']);

        if ($status !== 'all') {
            $query->where('statut', $status);
        }

        if ($priority !== 'all') {
            $query->where('statut', $priority); // Utilise 'statut' pour la priorité (arrêt, précaution, continuer)
        }

        if ($department) {
            $query->where('departement', 'like', '%' . $department . '%');
        }

        if ($date) {
            $query->whereDate('datetime', $date);
        }

        if ($month !== 'all') {
            $query->whereMonth('datetime', $month);
        }

        $query->whereYear('datetime', $year);

        // Données pour les statistiques du dashboard
        $totalAnomalies = Anomalie::count();
        $openAnomalies = Anomalie::where('statut', 'Ouverte')->count();
        $closedAnomalies = Anomalie::where('statut', 'Clos')->count();
        $totalProposals = Proposal::count();

        // Données pour le graphique par gravité
        $gravityData = [
            'arret' => Anomalie::where('statut', 'arret')->count(),
            'precaution' => Anomalie::where('statut', 'precaution')->count(),
            'continuer' => Anomalie::where('statut', 'continuer')->count(),
        ];

        // Données pour le graphique par département
        $departmentData = [
            'technique' => Anomalie::where('departement', 'technique')->count(),
            'logistique' => Anomalie::where('departement', 'logistique')->count(),
            'commercial' => Anomalie::where('departement', 'commercial')->count(),
            'administratif' => Anomalie::where('departement', 'administratif')->count(),
        ];

        // Liste des anomalies pour le tableau
        $anomalies = $query->latest()->paginate(10);

        // Paramètres pour la configuration
        $params = [
            'email' => config('app.hse_email', 'hse@eres-togo.com'),
            'email_cc' => config('app.hse_email_cc', 'direction@eres-togo.com'),
            'notify_email' => true,
            'notify_sound' => true,
            'auto_archive' => true,
        ];

        return view('dashboard.index', [
            'user' => $user,
            'totalAnomalies' => $totalAnomalies,
            'openAnomalies' => $openAnomalies,
            'closedAnomalies' => $closedAnomalies,
            'totalProposals' => $totalProposals,
            'gravityData' => $gravityData,
            'departmentData' => $departmentData,
            'anomalies' => $anomalies,
            'params' => $params,
            'filters' => [
                'status' => $status,
                'priority' => $priority,
                'department' => $department,
                'date' => $date,
                'month' => $month,
                'year' => $year,
            ],
        ]);
    }

    /**
     * Exporter les anomalies en CSV.
     */
    public function exportAnomaliesCsv(Request $request)
    {
        $anomalies = Anomalie::query()
            ->when($request->status !== 'all', fn($q) => $q->where('statut', $request->status))
            ->when($request->priority !== 'all', fn($q) => $q->where('statut', $request->priority))
            ->when($request->department, fn($q) => $q->where('departement', 'like', '%' . $request->department . '%'))
            ->when($request->date, fn($q) => $q->whereDate('datetime', $request->date))
            ->with('user')
            ->get();

        $headers = ['ID', 'Date/Heure', 'Rapporté par', 'Département', 'Localisation', 'Gravité', 'Description', 'Action', 'Statut'];
        $rows = $anomalies->map(function ($anomaly) {
            return [
                $anomaly->id,
                $anomaly->datetime->format('d/m/Y H:i'),
                $anomaly->user ? $anomaly->user->name : $anomaly->rapporte_par,
                $anomaly->departement,
                $anomaly->localisation,
                $anomaly->statut,
                $anomaly->description,
                $anomaly->action,
                $anomaly->statut,
            ];
        })->toArray();

        $csv = implode(',', $headers) . "\n";
        $csv .= collect($rows)->map(function ($row) {
            return implode(',', array_map(function ($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row));
        })->implode("\n");

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="anomalies_eres_togo.csv"',
        ]);
    }

    /**
     * Exporter les anomalies en PDF.
     */
    public function exportAnomaliesPdf(Request $request)
    {
        $anomalies = Anomalie::query()
            ->when($request->status !== 'all', fn($q) => $q->where('statut', $request->status))
            ->when($request->priority !== 'all', fn($q) => $q->where('statut', $request->priority))
            ->when($request->department, fn($q) => $q->where('departement', 'like', '%' . $request->department . '%'))
            ->when($request->date, fn($q) => $q->whereDate('datetime', $request->date))
            ->with('user')
            ->get();

        $pdf = PDF::loadView('pdf.anomalies', [
            'anomalies' => $anomalies,
            'user' => Auth::user(),
            'date' => now()->format('d/m/Y'),
        ]);

        return $pdf->download('anomalies_eres_togo.pdf');
    }

    /**
     * Exporter les propositions en CSV.
     */
    public function exportProposalsCsv()
    {
        $proposals = Proposal::with('anomaly')->get();
        $headers = ['ID Proposition', 'Anomalie ID', 'Date Réception', 'Action Corrective', 'Personne Responsable', 'Date Prévue', 'Statut'];
        $rows = $proposals->map(function ($proposal) {
            return [
                $proposal->id,
                $proposal->anomaly_id,
                $proposal->received_at->format('d/m/Y H:i'),
                $proposal->action_corrective,
                $proposal->personne_responsable,
                $proposal->date_prevue->format('d/m/Y'),
                $proposal->status,
            ];
        })->toArray();

        $csv = implode(',', $headers) . "\n";
        $csv .= collect($rows)->map(function ($row) {
            return implode(',', array_map(function ($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row));
        })->implode("\n");

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="propositions_eres_togo.csv"',
        ]);
    }

    /**
     * Exporter les propositions en PDF.
     */
    public function exportProposalsPdf()
    {
        $proposals = Proposal::with('anomaly')->get();

        $pdf = PDF::loadView('pdf.proposals', [
            'proposals' => $proposals,
            'user' => Auth::user(),
            'date' => now()->format('d/m/Y'),
        ]);

        return $pdf->download('propositions_eres_togo.pdf');
    }

    /**
     * Marquer toutes les anomalies comme lues (si le champ read existe).
     */
    public function markAllAsRead()
    {
        // Vérifier si le champ 'read' existe dans la table anomalies
        if (Schema::hasColumn('anomalies', 'read')) {
            Anomalie::where('read', false)->update(['read' => true]);
            return redirect()->back()->with('success', 'Toutes les anomalies ont été marquées comme lues.');
        }

        return redirect()->back()->with('warning', 'Le champ read n\'existe pas dans la table anomalies.');
    }
}