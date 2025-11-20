<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\models\Archive;

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
        return view('statistics', compact('user'));
    }

    public function showStatisticsView()
    {
        return view('statistics');
    }

    public function showPropositionView()
    {
        return view('proposition');
    }

    public function showRapportView()
    {
        return view('rapport');
    }

    public function showConfigurationView()
    {
        return view('configuration');
    }

    public function showArchiveView()
    {
        return view('archive');
    }


    public function showCorbeilleView()
    {
        return view('corbeille');
    }

public function closed(Request $request)
{
    $query = Archive::query()->with('closer');

    if ($request->query('page') === 'all') {
        return response()->json([
            'anomalies' => $query->get()
        ]);
    }

    $perPage = 15;
    $archives = $query->paginate($perPage);

    return response()->json([
        'anomalies' => $archives->items(),
        'current_page' => $archives->currentPage(),
        'last_page' => $archives->lastPage(),
    ]);
}
}
