<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'file_path',
    ];

    /**
     * Get the volunteer user associated with the certificate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event associated with the certificate.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Accessor to generate a clean serial number based on the ID.
     */
    public function getSerialNumberAttribute()
    {
        return 'OC-CERT-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
