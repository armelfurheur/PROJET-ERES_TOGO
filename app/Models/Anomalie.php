<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anomalie extends Model
{
    use HasFactory;

    protected $fillable = [
        'rapporte_par',
        'departement',
        'localisation',
        'gravity',
        'status',
        'description',
        'action',
        'datetime',
        'preuve',
        'user_id',
    ];

    protected $casts = [
        'datetime' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

 // Ajouter cette relation
    public function propositions()
    {
        return $this->hasMany(Proposition::class);
    }


  
}