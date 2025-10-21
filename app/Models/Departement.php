<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Departement
 * * @method static \Illuminate\Database\Eloquent\Builder|Departement whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement query()
 * @mixin \Eloquent
 */
class Departement extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];
    
    // Si vous n'avez pas de relation définie ici, la suite de la classe reste vide
}
