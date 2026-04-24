<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'organizer_id', 'title', 'description', 'location', 'date', 'duration', 'quota'
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }
    
    public function participants()
    {
        return $this->hasMany(EventRegistration::class)->with('user');
    }
    
    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
