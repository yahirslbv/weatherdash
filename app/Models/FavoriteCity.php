<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteCity extends Model
{
    // Permitir el guardado masivo de estos campos
    protected $fillable = [
        'user_id',
        'city_name',
        'latitude',
        'longitude',
    ];

    // Relación: Una ciudad favorita pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}