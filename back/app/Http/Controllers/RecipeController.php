<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Models\Recipe;

use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index()
    {
        return Recipe::with(['user', 'category', 'cuisine'])->get();
    }

    public function show($id)
    {
        // Cargar la receta con las relaciones de usuario, categoría y cocina
        $recipe = Recipe::with(['user', 'category', 'cuisine'])->findOrFail($id);
    
        // Agregar el nombre del creador a los datos de la receta
        $recipe->creador = $recipe->user->name; 
    
        unset($recipe->user); 
    
        return response()->json($recipe);
    }
    

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'cuisine_id' => 'required|exists:cuisines,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'required|array',
            'steps' => 'required|array',
            'prep_time' => 'required|integer',
            'cook_time' => 'required|integer',
            'servings' => 'required|integer',
            'nutrition' => 'nullable|array',  
            'image' => 'nullable|string',
        ]);
    
        // Asignar automáticamente el usuario autenticado
        $data['user_id'] = auth()->id();
    
        // Crear la receta con los datos validados
        $recipe = Recipe::create($data);
    
        return response()->json($recipe, 201);
    }
    



   
public function update(Request $request, $id)
{
    $recipe = Recipe::findOrFail($id);

    $data = $request->validate([
        'user_id' => 'required|exists:users,id',
        'category_id' => 'required|exists:categories,id',
        'cuisine_id' => 'required|exists:cuisines,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'ingredients' => 'required|array',
        'steps' => 'required|array',
        'prep_time' => 'required|integer',
        'cook_time' => 'required|integer',
        'servings' => 'required|integer',
        'nutrition' => 'nullable|array',  // Handle nutrition field if available
        'image' => 'nullable|string', // Acepta texto o se puede dejar vacío

    ]);

    $recipe->update($data);

    return $recipe;
}

    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted successfully']);
    }
    public function likeRecipe(Request $request, $recipeId)
{
    $userId = $request->user()->id;
    
    // Verificar si ya existe un registro en la tabla pivot
    $recipeUser = DB::table('recipe_user')
        ->where('user_id', $userId)
        ->where('recipe_id', $recipeId)
        ->first();

    if ($recipeUser && $recipeUser->liked) {
        return response()->json(['message' => 'You already liked this recipe.'], 400);
    }

    if ($recipeUser) {
        // Si ya existe, solo actualizamos el campo 'liked'
        DB::table('recipe_user')
            ->where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->update(['liked' => true, 'updated_at' => now()]);
    } else {
        // Si no existe, creamos un nuevo registro
        DB::table('recipe_user')->insert([
            'user_id' => $userId,
            'recipe_id' => $recipeId,
            'liked' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Incrementamos el contador de likes de la receta
    Recipe::where('id', $recipeId)->increment('likes_count');

    return response()->json(['message' => 'Recipe liked successfully']);
}


public function unlikeRecipe(Request $request, $recipeId)
{
    $userId = $request->user()->id;

    // Verificar si el registro existe y está marcado como liked
    $recipeUser = DB::table('recipe_user')
        ->where('user_id', $userId)
        ->where('recipe_id', $recipeId)
        ->first();

    if (!$recipeUser || !$recipeUser->liked) {
        return response()->json(['message' => 'Recipe not liked yet.'], 400);
    }

    // Actualizar el registro para marcar como 'no liked'
    DB::table('recipe_user')
        ->where('user_id', $userId)
        ->where('recipe_id', $recipeId)
        ->update(['liked' => false, 'updated_at' => now()]);

    // Decrementamos el contador de likes
    Recipe::where('id', $recipeId)->decrement('likes_count');

    return response()->json(['message' => 'Recipe unliked successfully']);
}


    public function getAllRecipes()
    {
       $recipes = Recipe::all();
       return response()->json([
        'recipes' => $recipes,
    ], 200);
        }
        
        public function filterByCategory ($id)
        {
            $recipes = Recipe::where('category_id', $id)->get();
            return response()->json([
                'recipes' => $recipes,
            ], 200);
        }
    

public function filterByTime($time)
{
    $recipes = Recipe::whereRaw('cook_time + prep_time <= ?', [$time])->get();
    return response()->json([
        'recipes' => $recipes,
    ], 200);
}

public function getAllTimes()
{
    // Usar DB::table para la consulta, en lugar de la Eloquent de Recipe
    $times = DB::table('recipes')
        ->selectRaw('prep_time + cook_time as total_time')
        ->distinct()  // Asegura que los tiempos sean únicos
        ->pluck('total_time');  // Obtiene solo los tiempos

    // Devolver los tiempos únicos en formato JSON
    return response()->json([
        'times' => $times,
    ], 200);
}



public function filterByCuisine($id){
    $recipes = Recipe::where('cuisine_id', $id)->get();

    return response()->json([
        'recipes' => $recipes,
    ], 200);
}

public function filterByUser($userId)
{
    $recipes = Recipe::where('user_id', $userId)->get();
    return response()->json([
        'recipes' => $recipes,
    ], 200);
}


}