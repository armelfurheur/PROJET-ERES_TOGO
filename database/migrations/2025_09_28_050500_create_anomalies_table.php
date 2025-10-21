// database/migrations/xxxx_create_anomalies_table.php
<?php

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
            $table->enum('statut_anomalie', ['arret', 'precaution', 'continuer'])->default('continuer');
            $table->text('description');
            $table->text('action');
            $table->string('preuve_url')->nullable();
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