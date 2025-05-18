<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'cuisine_id', 'title', 'description', 
        'ingredients', 'steps', 'image', 'prep_time', 'cook_time', 
        'servings', 'nutrition', 'video'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'steps' => 'array',
        'nutrition' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    

    public function live()
    {
        return $this->hasMany(Live::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cuisine()
    {
        return $this->belongsTo(Cuisine::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'recipe_user')
                    ->withPivot('saved', 'liked')
                    ->withTimestamps();
    }

    public function usersWhoSaved()
    {
        return $this->belongsToMany(User::class, 'recipe_user')
                   ->withPivot('saved', 'liked')
                   ->wherePivot('saved', true);
    }

    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'folder_recipe');
    }
}