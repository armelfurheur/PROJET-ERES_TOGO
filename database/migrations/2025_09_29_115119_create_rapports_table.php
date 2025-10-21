<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();  // ID auto-incrémenté
            $table->string('titre');  // Titre du rapport (ex. : 'Rapport HSE - Octobre 2025')
            $table->text('contenu')->nullable();  // Contenu détaillé (optionnel, pour notes ou JSON stats)
            $table->string('periode');  // Période (ex. : 'Octobre 2025' ou 'Toutes périodes')
            $table->integer('mois')->nullable();  // Mois filtré (1-12)
            $table->integer('annee')->nullable();  // Année filtrée (ex. : 2025)
            $table->integer('total_anomalies')->default(0);  // Total d'anomalies incluses
            $table->integer('anomalies_ouvertes')->default(0);  // Nombre d'anomalies ouvertes
            $table->integer('anomalies_fermees')->default(0);   // Nombre d'anomalies fermées
            $table->integer('gravite_arret')->default(0);       // Compteur gravité 'arret'
            $table->integer('gravite_precaution')->default(0);  // Compteur gravité 'precaution'
            $table->integer('gravite_continuer')->default(0);   // Compteur gravité 'continuer'
            $table->json('anomalies_ids')->nullable();  // JSON des IDs d'anomalies incluses (ex. : ["anom_xxx", "anom_yyy"])
            $table->string('export_csv_path')->nullable();  // Chemin vers fichier CSV généré
            $table->string('export_pdf_path')->nullable();  // Chemin vers fichier PDF généré
            $table->boolean('envoye_email')->default(false);  // Rapport envoyé par email ?
            $table->foreignId('genere_par')->constrained('users')->onDelete('cascade');  // Utilisateur HSE qui a généré
            $table->timestamps();

            // Index pour optimiser les recherches par période et créateur
            $table->index(['periode', 'annee', 'genere_par', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};