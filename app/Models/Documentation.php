<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    protected $attributes = [
        'status' => 'pending'
    ];

    protected $fillable = [
        'event_id',
        'organizer_id',
        'file_path',
        'note',
        'status' 
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}