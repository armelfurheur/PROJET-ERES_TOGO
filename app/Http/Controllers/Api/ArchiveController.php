<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anomalie;
use App\Models\Archive;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index()
    {
        $archives = Archive::orderBy('closed_at', 'desc')->get();
        
        $formatted = $archives->map(function($archive) {
            return [
                'id' => $archive->id,
                'anomalie_id' => $archive->anomalie_id,
                'rapporte_par' => $archive->rapporte_par,
                'departement' => $archive->departement,
                'localisation' => $archive->localisation,
                'statut' => $archive->statut,
                'description' => $archive->description,
                'action' => $archive->action,
                'preuve' => $archive->preuve,
                'datetime' => $archive->datetime->toISOString(),
                'status' => $archive->status,
                'closed_at' => $archive->closed_at ? $archive->closed_at->toISOString() : null,
                'closed_by' => $archive->closed_by,
                'proposals' => $archive->proposals,
                'created_at' => $archive->created_at->toISOString(),
                'updated_at' => $archive->updated_at->toISOString()
            ];
        });

        return response()->json(['archives' => $formatted]);
    }

    public function closeAnomaly(Request $request, $id)
    {
        $request->validate([
            'closed_by' => 'required|string'
        ]);

        $anomalie = Anomalie::with('propositions')->findOrFail($id);

        // Vérifier qu'il y a au moins une proposition
        if ($anomalie->propositions->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Une proposition d\'action est requise avant clôture'
            ], 400);
        }

        // Créer l'archive
        $archive = Archive::create([
            'anomalie_id' => $anomalie->id,
            'rapporte_par' => $anomalie->rapporte_par,
            'departement' => $anomalie->departement,
            'localisation' => $anomalie->localisation,
            'statut' => $anomalie->statut,
            'description' => $anomalie->description,
            'action' => $anomalie->action,
            'preuve' => $anomalie->preuve,
            'datetime' => $anomalie->datetime,
            'status' => 'Clos',
            'closed_at' => now(),
            'closed_by' => $request->closed_by,
            'proposals' => $anomalie->propositions->map(function($prop) {
                return [
                    'id' => $prop->id,
                    'action' => $prop->action,
                    'person' => $prop->person,
                    'date' => $prop->date->format('Y-m-d'),
                    'status' => $prop->status,
                    'received_at' => $prop->received_at ? $prop->received_at->format('d/m/Y H:i') : null
                ];
            })->toArray()
        ]);

        // Mettre à jour le statut de l'anomalie
        $anomalie->update(['status' => 'Clos']);

        return response()->json([
            'success' => true,
            'message' => 'Anomalie clôturée et archivée avec succès',
            'archive' => $archive
        ], 201);
    }

    public function restore($id)
    {
        $archive = Archive::findOrFail($id);
        
        // Vérifier si l'anomalie existe encore
        $anomalie = Anomalie::find($archive->anomalie_id);
        
        if ($anomalie) {
            // Restaurer le statut de l'anomalie
            $anomalie->update(['status' => 'Ouverte']);
            
            // Supprimer l'archive
            $archive->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Anomalie restaurée depuis les archives'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Anomalie associée non trouvée'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $archive = Archive::findOrFail($id);
        $archive->delete();

        return response()->json([
            'success' => true,
            'message' => 'Archive supprimée définitivement'
        ]);
    }
}