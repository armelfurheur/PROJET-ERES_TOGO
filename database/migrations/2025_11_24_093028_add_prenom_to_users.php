<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Ajoute les colonnes SANS utiliser after('name')
        if (!Schema::hasColumn('users', 'firstname')) {
            $table->string('firstname')->nullable();
        }

        if (!Schema::hasColumn('users', 'lastname')) {
            $table->string('lastname')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'firstname')) {
            $table->dropColumn('firstname');
        }

        if (Schema::hasColumn('users', 'lastname')) {
            $table->dropColumn('lastname');
        }
    });
}

};

