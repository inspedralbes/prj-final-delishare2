<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderRecipe extends Model
{
    use HasFactory;

    // Desactivar timestamps si no se usan
    public $timestamps = false;

    protected $fillable = ['folder_id', 'recipe_id'];
}
