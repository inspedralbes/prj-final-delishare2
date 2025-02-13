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


    public function index(){
    $user = auth()->user();
    $folders = $user->folders;  

    return response()->json($folders);
}



    public function getRecipes(Folder $folder){

    if ($folder->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    $recipes = $folder->recipes;

    return response()->json([
        'folder' => $folder,
        'recipes' => $recipes,
    ]);
}

}
