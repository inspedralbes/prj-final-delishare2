<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Live extends Model
{
    use HasFactory;

    protected $table = 'live'; // Nombre de la tabla en singular

    protected $fillable = [
        'user_id',
        'recipe_id',
        'hora',
        'dia'
    ];

    protected $casts = [
        'hora' => 'datetime:H:i',
        'dia' => 'datetime:Y-m-d',
    ];

    public function chef()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}