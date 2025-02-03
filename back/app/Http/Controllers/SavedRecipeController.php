<?php

namespace App\Http\Controllers;
use App\Models\RecipeUser;
use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class SavedRecipeController extends Controller
{
    // Obtener recetas guardadas
// Guardar recetas
public function index()
{
    $user = Auth::user();
    $savedRecipes = $user->savedRecipes()->get();
    return response()->json($savedRecipes);
}


    // SavedRecipeController
public function toggleSave(Request $request, $recipeId)
{
    $user = Auth::user();

    // Buscar si la receta ya está guardada por este usuario
    $savedRecipe = $user->savedRecipes()->wherePivot('recipe_id', $recipeId)->first();

    if ($savedRecipe) {
        // Si la receta está guardada, la eliminamos
        $user->savedRecipes()->detach($recipeId);
        return response()->json(['message' => 'Receta eliminada de guardadas']);
    } else {
        // Si no está guardada, la añadimos
        $user->savedRecipes()->attach($recipeId, ['saved' => true]);
        return response()->json(['message' => 'Receta guardada']);
    }
}

    
}
