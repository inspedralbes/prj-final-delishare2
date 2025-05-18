<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Recipe;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    // Método para crear una nueva carpeta asociada al usuario autenticado
    public function store(Request $request)
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Creo una nueva instancia de Folder con el nombre recibido
        $folder = new Folder();
        $folder->name = $request->name;
        $folder->user_id = $user->id; // Asocio la carpeta al usuario autenticado
        $folder->save();
    
        return response()->json($folder, 201); // Devuelvo la carpeta creada
    }

    // Añadir una receta a una carpeta específica
    public function addRecipe(Request $request, $folderId, $recipeId)
    {
        $folder = Folder::find($folderId);
        $recipe = Recipe::find($recipeId);
    
        // Verifico que existan tanto la carpeta como la receta
        if (!$folder || !$recipe) {
            return response()->json(['message' => 'Carpeta o receta no encontrada'], 404);
        }

        // Solo el dueño de la carpeta puede modificarla
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Relaciono la receta con la carpeta usando attach
        $folder->recipes()->attach($recipe->id);
    
        return response()->json(['message' => 'Receta guardada en la carpeta'], 200);
    }

    // Eliminar una receta específica de una carpeta
    public function removeRecipe($folderId, $recipeId)
    {
        $folder = Folder::find($folderId);  
        $recipe = Recipe::find($recipeId); 
    
        if (!$folder) {
            return response()->json(['error' => 'Carpeta no encontrada'], 404);
        }

        // Verifico si la receta está dentro de la carpeta
        $recipeInFolder = $folder->recipes()->where('recipe_id', $recipeId)->exists();
    
        if (!$recipeInFolder) {
            return response()->json(['error' => 'No hay recetas en esta carpeta'], 404);
        }

        if (!$recipe) {
            return response()->json(['error' => 'Receta no encontrada en la base de datos'], 404);
        }

        // Solo el dueño de la carpeta puede eliminar recetas de ella
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Elimino la relación entre receta y carpeta
        $folder->recipes()->detach($recipe->id);
    
        return response()->json(['message' => 'Receta eliminada de la carpeta'], 200);
    }

    // Mostrar detalles de una carpeta específica
    public function show(Folder $folder)
    {
        return response()->json([
            'id' => $folder->id,
            'name' => $folder->name,
            'created_at' => $folder->created_at,
            'user' => $folder->user, // Incluyo datos del usuario
            'recipes' => $folder->recipes, // Incluyo recetas
        ]);
    }

    // Eliminar una carpeta creada por el usuario
    public function removeFolder($folderId)
    {
        $folder = Folder::find($folderId);

        if (!$folder) {
            return response()->json(['error' => 'Carpeta no encontrada'], 404);
        }

        // Verifico si el usuario tiene permiso para eliminarla
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $folder->delete();

        return response()->json(['message' => 'Carpeta eliminada con éxito'], 200);
    }

    // Listar todas las carpetas del usuario con el conteo de recetas
    public function index()
    {
        $user = auth()->user();

        // Uso withCount para traer el número de recetas por carpeta
        $folders = $user->folders()->withCount('recipes')->get();
    
        // Devuelvo los datos en un formato simplificado
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

    // Obtener todas las recetas de una carpeta con detalles
    public function getRecipes($folderId)
    {
        // Cargar carpeta con relaciones (usuario, categoría y tipo de cocina de la receta)
        $folder = Folder::with('recipes.user', 'recipes.category', 'recipes.cuisine')->find($folderId);

        if (!$folder) {
            return response()->json(['error' => 'Carpeta no encontrada'], 404);
        }

        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Formateo los datos de cada receta para mostrar solo lo necesario
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
