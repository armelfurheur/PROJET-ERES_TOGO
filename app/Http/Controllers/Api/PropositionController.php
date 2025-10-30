<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anomalie;
use App\Models\Proposition;
use Illuminate\Http\Request;

class PropositionController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|string',
            'person' => 'required|string',
            'date' => 'required|date|after_or_equal:today'
        ]);

        $anomalie = Anomalie::findOrFail($id);

        $proposition = $anomalie->propositions()->create([
            'action' => $request->action,
            'person' => $request->person,
            'date' => $request->date,
            'status' => 'Proposée',
            'received_at' => $anomalie->datetime
        ]);

        // Mettre à jour le flag has_proposal de l'anomalie
        $anomalie->update(['has_proposal' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Proposition ajoutée avec succès',
            'proposition' => $proposition
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $proposition = Proposition::findOrFail($id);

        $request->validate([
            'action' => 'sometimes|required|string',
            'person' => 'sometimes|required|string',
            'date' => 'sometimes|required|date|after_or_equal:today',
            'status' => 'sometimes|required|string'
        ]);

        $proposition->update($request->only(['action', 'person', 'date', 'status']));

        return response()->json([
            'success' => true,
            'message' => 'Proposition mise à jour avec succès',
            'proposition' => $proposition
        ]);
    }

    public function destroy($id)
    {
        $proposition = Proposition::findOrFail($id);
        $anomalieId = $proposition->anomalie_id;
        
        $proposition->delete();

        // Vérifier s'il reste des propositions pour cette anomalie
        $anomalie = Anomalie::findOrFail($anomalieId);
        if ($anomalie->propositions()->count() === 0) {
            $anomalie->update(['has_proposal' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Proposition supprimée avec succès'
        ]);
    }
}