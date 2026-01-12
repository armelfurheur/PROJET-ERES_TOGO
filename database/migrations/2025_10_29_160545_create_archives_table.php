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
                  ->constrained('anomalies')
                  ->onDelete('cascade');

            // Champs de l'anomalie
            $table->string('rapporte_par');
            $table->string('departement');
            $table->string('localisation')->nullable();
            $table->text('description');
            $table->text('action')->nullable();
            $table->string('preuve')->nullable();

            // Dates (corrigé)
            $table->timestamp('datetime')->nullable();     
            $table->timestamp('closed_at')->nullable();   

            // Clôture
            $table->foreignId('closed_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Statut
            $table->enum('status', ['Clos'])->default('Clos');

            // Propositions
            $table->json('proposals')->nullable();

            // Timestamps Laravel
            $table->timestamps();

            // Index
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
