<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // ON REMPLACE DÉFINITIVEMENT 'name' PAR :
            $table->string('firstname', 100);   // Prénom(s)
            $table->string('lastname', 100);    // Nom de famille (en majuscules)

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // ERES-TOGO
            $table->string('department'); // Technique, Logistique, etc.
            $table->enum('role', ['user', 'admin'])->default('user');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};