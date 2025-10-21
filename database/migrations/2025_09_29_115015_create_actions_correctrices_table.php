<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propositions', function (Blueprint $table) {
            $table->string('id')->primary();  // ID personnalisé (ex. : 'prop_20251008_123456_def')
            $table->string('anomaly_id');     // Lien vers l'anomalie (anomalies.id)
            $table->timestamp('received_at')->useCurrent();  // Date/heure de réception (copie de anomalies.datetime)
            $table->string('action_corrective', 500);  // Description de l'action corrective
            $table->string('personne_responsable');    // Personne/équipe responsable
            $table->date('date_prevue');               // Date prévue d'exécution
            $table->string('status', 100)->default('Proposée');  // Statut (Proposée, En cours, Terminée, etc.)
            $table->timestamps();

            // Clé étrangère pour lier à anomalies (suppression en cascade si anomalie supprimée)
            $table->foreign('anomaly_id')->references('id')->on('anomalies')->onDelete('cascade');

            // Index pour optimiser les requêtes (par anomalie, date, statut)
            $table->index(['anomaly_id', 'date_prevue', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propositions');
    }
};