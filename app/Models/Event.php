<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang bisa diisi secara massal
    protected $fillable = [
        'title', 
        'description', 
        'location', 
        'event_date', 
        'status'
    ];

    /**
     * Scope untuk pencarian event berdasarkan keyword.
     * Logika ini akan mencari kata kunci di judul, lokasi, atau deskripsi.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $query->when($keyword, function ($q) use ($keyword) {
            $q->where('title', 'LIKE', "%{$keyword}%")
              ->orWhere('location', 'LIKE', "%{$keyword}%")
              ->orWhere('description', 'LIKE', "%{$keyword}%");
        });
    }
}