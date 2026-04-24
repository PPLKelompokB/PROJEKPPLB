<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'status'
    ];

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}