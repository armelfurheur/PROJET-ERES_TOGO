<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'anomalie_id',
        'action',
        'person',
        'date',
        'status',
        'received_at'
    ];

    protected $casts = [
        'date' => 'date',
        'received_at' => 'datetime'
    ];

    public function anomalie()
    {
        return $this->belongsTo(Anomalie::class);
    }
}