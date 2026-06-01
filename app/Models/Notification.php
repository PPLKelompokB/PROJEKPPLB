<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $attributes = [
        'is_read' => false
    ];
    
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'action_url',
        'is_read'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}