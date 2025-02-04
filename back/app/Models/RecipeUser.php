<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeUser extends Model
{
    use HasFactory;

    protected $table = 'recipe_user'; // Especifica la tabla porque no sigue la convención de nombres de Laravel

    protected $fillable = ['user_id', 'recipe_id', 'saved', 'liked', 'comment'];

    public $timestamps = true;
}
