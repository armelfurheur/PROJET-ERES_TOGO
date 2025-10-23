<?php

// database/migrations/xxxx_create_anomalies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            $table->string('rapporte_par');
            $table->string('departement');
            $table->string('localisation');
            // 💡 Changé de 'statut_anomalie' à 'statut'
            $table->enum('statut', ['arret', 'precaution', 'continuer'])->default('continuer');
            $table->text('description');
            $table->text('action');
            // 💡 Changé de 'preuve_url' à 'preuve'
            $table->string('preuve')->nullable();
            $table->timestamp('datetime');
            $table->enum('status', ['Ouverte', 'Clos'])->default('Ouverte');
            $table->boolean('read')->default(false);
            $table->boolean('has_proposal')->default(false);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('anomalies');
    }
};