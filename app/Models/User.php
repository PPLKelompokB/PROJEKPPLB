<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'role', 'photo', 'phone'
    ];

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function isVolunteer()
    {
        return $this->role === 'volunteer';
    }

    public function isOrganizer()
    {
        return $this->role === 'organizer';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
