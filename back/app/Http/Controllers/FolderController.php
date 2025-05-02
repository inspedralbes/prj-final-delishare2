<?php
namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Recipe;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    // Método para crear una carpeta
    public function store(Request $request)
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Crear la carpeta con el user_id
        $folder = new Folder();
        $folder->name = $request->name;
        $folder->user_id = $user->id; 
        $folder->save();
    
        return response()->json($folder, 201);
    }

    // añadir una receta a la carpeta
    public function addRecipe(Request $request, $folderId, $recipeId)
    {
        $folder = Folder::find($folderId);
        $recipe = Recipe::find($recipeId);
    
        if (!$folder || !$recipe) {
            return response()->json(['message' => 'Carpeta o receta no encontrada'], 404);
        }
    
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $folder->recipes()->attach($recipe->id);
    
        return response()->json(['message' => 'Receta guardada en la carpeta'], 200);
    }

    
    //eliminar una receta de la carpeta
    public function removeRecipe($folderId, $recipeId)
    {
        $folder = Folder::find($folderId);  
        $recipe = Recipe::find($recipeId); 
    
        if (!$folder) {
            return response()->json(['error' => 'Carpeta no encontrada'], 404);
        }
    
        $recipeInFolder = $folder->recipes()->where('recipe_id', $recipeId)->exists();
    
        if (!$recipeInFolder) {
            return response()->json(['error' => 'No hay recetas en esta carpeta'], 404);
        }

        if (!$recipe) {
            return response()->json(['error' => 'Receta no encontrada en la base de datos'], 404);
        }
    
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
    
        $folder->recipes()->detach($recipe->id);
    
        return response()->json(['message' => 'Receta eliminada de la carpeta'], 200);
    }


    public function show(Folder $folder)
    {
        return response()->json([
            'id' => $folder->id,
            'name' => $folder->name,
            'created_at' => $folder->created_at,
            'user' => $folder->user,
            'recipes' => $folder->recipes,
        ]);
    }


    public function removeFolder($folderId) {
    $folder = Folder::find($folderId);

    if (!$folder) {
        return response()->json(['error' => 'Carpeta no encontrada'], 404);
    }

    if ($folder->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $folder->delete();

    return response()->json(['message' => 'Carpeta eliminada con éxito'], 200);
    }

    public function index()
    {
        $user = auth()->user();
        $folders = $user->folders()->withCount('recipes')->get();
    
        return response()->json($folders->map(function($folder) {
            return [
                'id' => $folder->id,
                'name' => $folder->name,
                'created_at' => $folder->created_at,
                'recipes_count' => $folder->recipes_count,
                'user_id' => $folder->user_id
            ];
        })); 
    }
public function getRecipes($folderId)
{
    $folder = Folder::with('recipes.user', 'recipes.category', 'recipes.cuisine')->find($folderId);

    if (!$folder) {
        return response()->json(['error' => 'Carpeta no encontrada'], 404);
    }

    if ($folder->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    return response()->json([
        'folder' => [
            'id' => $folder->id,
            'name' => $folder->name,
            'created_at' => $folder->created_at
        ],
        'recipes' => $folder->recipes->map(function($recipe) {
            return [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'description' => $recipe->description,
                'image_url' => $recipe->image,
                'video_url' => $recipe->video,
                'ingredients' => $recipe->ingredients,
                'steps' => $recipe->steps,
                'prep_time' => $recipe->prep_time,
                'cook_time' => $recipe->cook_time,
                'servings' => $recipe->servings,
                'nutrition' => $recipe->nutrition,
                'likes_count' => $recipe->likes_count,
                'created_at' => $recipe->created_at,
                'user' => $recipe->user ? [
                    'id' => $recipe->user->id,
                    'name' => $recipe->user->name
                ] : null,
                'category' => $recipe->category ? [
                    'id' => $recipe->category->id,
                    'name' => $recipe->category->name
                ] : null,
                'cuisine' => $recipe->cuisine ? [
                    'id' => $recipe->cuisine->id,
                    'country' => $recipe->cuisine->country
                ] : null
            ];
        })
    ]);
}
}
