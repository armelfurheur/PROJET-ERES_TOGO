<?php

namespace App\Models;

// Assurez-vous d'utiliser toutes les façades nécessaires
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les attributs qui peuvent être assignés en masse.
     * Inclut 'department' que nous utilisons dans le contrôleur d'inscription.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department', // Ajouté pour l'inscription
    ];

    /**
     * Les attributs qui devraient être cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui devraient être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // ⭐ CORRECTION CRITIQUE : 
        // L'entrée 'password' => 'hashed' a été supprimée ou commentée.
        // La version Laravel 9.x que vous utilisez ne reconnaît pas ce cast.
        // Le hachage est géré par la façade Hash et le système d'authentification.
        // 'password' => 'hashed', 
    ];
}