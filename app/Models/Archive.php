<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $guarded = [];

    protected $casts = [
        'datetime' => 'datetime',
        'closed_at' => 'datetime',
        'proposals' => 'array', // JSON → array PHP
    ];

    // Relations
    public function anomaly()
    {
        return $this->belongsTo(Anomalie::class);
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}