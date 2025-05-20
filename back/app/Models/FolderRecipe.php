<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderRecipe extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['folder_id', 'recipe_id'];
}
