<?php

namespace App\Http\Controllers;

use App\Models\Proposition;
use App\Models\Anomalie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalController extends Controller
{
    /**
     * Display a listing of the proposals.
     */
    public function index()
    {
        $proposals = Proposition::with('anomalie')
            ->orderBy('received_at', 'desc')
            ->get();

        return view('proposals.index', compact('proposals'));
    }

   
 

    public function store(Request $request)
    {
        $request->validate([
            'anomalie_id' => 'required|exists:anomalies,id',
            'action' => 'required|string|max:255',
            'person' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $anomalie = Anomalie::findOrFail($request->anomalie_id);

        Proposition::create([
            'anomalie_id' => $request->anomalie_id,
            'action' => $request->action,
            'person' => $request->person,
            'date' => $request->date,
            'received_at' => $anomalie->datetime ?? now(),
            'status' => 'En attente',
        ]);

        return redirect()->route('anomalies.show', $anomalie->id)
            ->with('success', 'Proposition ajoutée avec succès !');
    }

    public function show(Proposition $proposal)
    {
        $proposal->load('anomalie');
        return view('proposals.show', compact('proposal'));
    }
}