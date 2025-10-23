<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propositions', function (Blueprint $table) {
            $table->id();

            // 1. Clé Étrangère : Lien vers la table 'anomalies'
            // Ceci est essentiel pour savoir à quelle anomalie la proposition est associée.
            $table->foreignId('anomalie_id')
                  ->constrained('anomalies')
                  ->onDelete('cascade'); // Supprime la proposition si l'anomalie est supprimée

            // 2. Champs de la table proposition (basés sur votre tableau de bord admin)

            // Qui propose l'action (Probablement un administrateur ou un responsable)
            $table->string('propose_par')->nullable(); 

            // L'action détaillée/la proposition de résolution
            $table->text('proposition_action'); 

            // Date & heure de réception/création de la proposition (déjà gérée par timestamps, mais ajoutée pour clarté)
            // L'en-tête de votre table utilise 'Date & heure réception', nous utilisons 'created_at' pour cela.

            // La date/heure prévue pour la résolution/clôture (correspond à 'Date prévue' dans votre en-tête)
            $table->timestamp('date_prevue')->nullable(); 

            // Statut de la proposition ('En cours', 'Approuvée', 'Rejetée', etc.)
            $table->enum('statut_proposition', ['En attente', 'En cours', 'Résolu', 'Annulé'])->default('En attente'); 

            $table->timestamps(); // Ajoute created_at (Date de réception/soumission de la proposition) et updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('propositions');
    }
};