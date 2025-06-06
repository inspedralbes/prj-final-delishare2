<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
public function recipes()
{
    return $this->belongsToMany(Recipe::class, 'folder_recipe', 'folder_id', 'recipe_id')
                ->withTimestamps();
}
}
