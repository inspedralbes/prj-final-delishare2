<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Cuisine extends Model
{
    use HasFactory;

    protected $fillable = ['country'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}