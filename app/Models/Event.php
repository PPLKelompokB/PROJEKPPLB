<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
    use HasFactory;
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
    
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $query->when($keyword, function ($q) use ($keyword) {
            $q->where('title', 'LIKE', "%{$keyword}%")
              ->orWhere('location', 'LIKE', "%{$keyword}%")
              ->orWhere('description', 'LIKE', "%{$keyword}%");
        });
    }
}
