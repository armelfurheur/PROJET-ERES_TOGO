<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            
            // Référence à l'anomalie originale
            $table->foreignId('anomaly_id')
                  ->unique()
                  ->constrained('anomalies')
                  ->onDelete('cascade');

            // Champs de l'anomalie
            $table->string('rapporte_par');
            $table->string('departement');
            $table->string('localisation')->nullable();
            $table->text('description');
            $table->text('action')->nullable();
            $table->string('preuve')->nullable(); // Chemin du fichier

            // Dates
            $table->timestamp('datetime');        // Date de signalement
            $table->timestamp('closed_at');       // Date de clôture

            // Clôture
            $table->foreignId('closed_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Statut (redondant avec closed_at, mais utile pour affichage rapide)
            $table->enum('status', ['Clos'])->default('Clos');

            // Propositions (stockées en JSON pour flexibilité)
            $table->json('proposals')->nullable();

            // Timestamps
            $table->timestamps();

            // Index pour performances
            $table->index('closed_at');
            $table->index('departement');
            $table->index('rapporte_par');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};