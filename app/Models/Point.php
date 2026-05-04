<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'points'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}