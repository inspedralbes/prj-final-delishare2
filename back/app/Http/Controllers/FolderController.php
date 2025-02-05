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
        // Verificar si el usuario está autenticado
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Crear la carpeta con el user_id
        $folder = new Folder();
        $folder->name = $request->name;
        $folder->user_id = $user->id; // Asignar el user_id del usuario autenticado
        $folder->save();
    
        return response()->json($folder, 201);
    }

    // Método para añadir una receta a la carpeta
    public function addRecipe(Request $request, $folderId, $recipeId)
    {
        $folder = Folder::find($folderId);
        $recipe = Recipe::find($recipeId);
    
        if (!$folder || !$recipe) {
            return response()->json(['message' => 'Carpeta o receta no encontrada'], 404);
        }
    
        // Verificar que la carpeta pertenece al usuario autenticado
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Relacionar la receta con la carpeta
        $folder->recipes()->attach($recipe->id);
    
        return response()->json(['message' => 'Receta guardada en la carpeta'], 200);
    }

    // Método para eliminar una receta de la carpeta
    public function removeRecipe($folderId, $recipeId)
    {
        $folder = Folder::find($folderId);  // Carga explícitamente el modelo Folder
        $recipe = Recipe::find($recipeId);  // Carga explícitamente la receta
    
        if (!$folder) {
            return response()->json(['error' => 'Carpeta no encontrada'], 404);
        }
    
        // Verificar si la receta existe en la carpeta
        $recipeInFolder = $folder->recipes()->where('recipe_id', $recipeId)->exists();
    
        if (!$recipeInFolder) {
            return response()->json(['error' => 'No hay recetas en esta carpeta'], 404);
        }

        // Verificar que la receta existe
        if (!$recipe) {
            return response()->json(['error' => 'Receta no encontrada en la base de datos'], 404);
        }
    
        // Verificar que la carpeta pertenece al usuario autenticado
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
    
        // Eliminar la receta de la carpeta
        $folder->recipes()->detach($recipe->id);
    
        return response()->json(['message' => 'Receta eliminada de la carpeta'], 200);
    }

    // Método para mostrar detalles de la carpeta
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
    public function removeFolder($folderId)
{
    // Buscar la carpeta en la base de datos
    $folder = Folder::find($folderId);

    // Verificar si la carpeta existe
    if (!$folder) {
        return response()->json(['error' => 'Carpeta no encontrada'], 404);
    }

    // Verificar si la carpeta pertenece al usuario autenticado
    if ($folder->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Eliminar la carpeta
    $folder->delete();

    return response()->json(['message' => 'Carpeta eliminada con éxito'], 200);
}
public function index()
{
    // Obtener las carpetas del usuario autenticado
    $user = auth()->user();
    $folders = $user->folders;  // Asegúrate de que la relación esté definida en el modelo User

    return response()->json($folders);
}
public function getRecipes(Folder $folder)
{
    // Verifica que la carpeta pertenece al usuario autenticado
    if ($folder->user_id !== auth()->id()) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Obtener las recetas asociadas a la carpeta
    $recipes = $folder->recipes;

    return response()->json([
        'folder' => $folder,
        'recipes' => $recipes,
    ]);
}

}
