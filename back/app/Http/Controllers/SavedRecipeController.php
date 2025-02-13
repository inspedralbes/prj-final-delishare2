<?php

namespace App\Http\Controllers;
use App\Models\RecipeUser;
use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class SavedRecipeController extends Controller
{
public function index()
{
    $user = Auth::user();
    $savedRecipes = $user->savedRecipes()->get();
    return response()->json($savedRecipes);
}


public function toggleSave(Request $request, $recipeId)
{
    $user = Auth::user();

    $savedRecipe = $user->savedRecipes()->wherePivot('recipe_id', $recipeId)->first();

    if ($savedRecipe) {
        $user->savedRecipes()->detach($recipeId);
        return response()->json(['message' => 'Receta eliminada de guardadas']);
    } else {
        $user->savedRecipes()->attach($recipeId, ['saved' => true]);
        return response()->json(['message' => 'Receta guardada']);
    }
}

    
}
