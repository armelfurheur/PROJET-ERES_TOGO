<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'anomalie_id',
        'rapporte_par',
        'departement',
        'localisation',
        'statut',
        'description',
        'action',
        'preuve',
        'datetime',
        'status',
        'closed_at',
        'closed_by',
        'proposals'
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'closed_at' => 'datetime',
        'proposals' => 'array'
    ];

    public function anomalie()
    {
        return $this->belongsTo(Anomalie::class);
    }
}