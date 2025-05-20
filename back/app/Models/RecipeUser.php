<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeUser extends Model
{
    use HasFactory;

    protected $table = 'recipe_user'; 

    protected $fillable = ['user_id', 'recipe_id', 'saved', 'liked', 'comment'];

    public $timestamps = true;


}
