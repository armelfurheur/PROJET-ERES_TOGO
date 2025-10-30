<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anomalie_id')->constrained('anomalies')->onDelete('cascade');
            $table->string('rapporte_par');
            $table->string('departement');
            $table->string('localisation');
            $table->string('statut');
            $table->text('description');
            $table->text('action');
            $table->string('preuve')->nullable();
            $table->timestamp('datetime');
            $table->string('status')->default('Clos');
            $table->timestamp('closed_at')->nullable();
            $table->string('closed_by')->nullable();
            $table->json('proposals')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};