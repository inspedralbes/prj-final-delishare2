<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Folder;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InfoUserController extends Controller
{
    // Devuelve todos los usuarios registrados
    public function index(){
        return User::all();
    }

    // Devuelve la información detallada de un usuario según su ID
    public function show($id){
        try {
            // Buscamos el usuario por su ID, lanza excepción si no lo encuentra
            $user = User::findOrFail($id);
            
            // Obtenemos todas las carpetas que ha creado el usuario
            $folders = Folder::where('user_id', $user->id)->get();
        
            // Obtenemos todas las recetas creadas por el usuario
            $recipes = Recipe::where('user_id', $user->id)->get();
        
            // Creamos un array para organizar recetas por carpeta
            $recipesInFolders = [];
            foreach ($folders as $folder) {
                $recipesInFolders[$folder->name] = $folder->recipes;
            }
        
            // Devolvemos todos los datos del usuario junto con sus carpetas y recetas
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'img' => $user->img,
                'password' => $user->password, // ← Este dato sensible está siendo devuelto
                'personal_access_token' => $user->personal_access_token,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'folders' => $folders,
                'recipes' => $recipes,
                'recipes_in_folders' => $recipesInFolders,
            ], 200);
        
        } catch (ModelNotFoundException $e) {
            // Si el usuario no existe, devolvemos un error 404
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            // Cualquier otro error se responde como error interno del servidor
            return response()->json(['message' => 'Error del servidor', 'error' => $e->getMessage()], 500);
        }
    }

    // Actualiza los datos de un usuario existente
    public function update(Request $request, $id){
        $user = User::findOrFail($id); // Busca el usuario
        $user->update($request->all()); // Actualiza con los datos del request
        return $user; // Devuelve el usuario actualizado
    }

    // Elimina un usuario del sistema
    public function destroy($id)
    {
        $user = User::findOrFail($id); // Busca el usuario
        $user->delete(); // Lo elimina
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    // Devuelve todas las recetas de un usuario específico
    public function showRecipes($id){
        try {
            $user = User::findOrFail($id); // Verifica si el usuario existe
            
            // Obtenemos las recetas del usuario
            $recipes = Recipe::where('user_id', $user->id)->get();
            
            // Retornamos las recetas
            return response()->json([
                'user_id' => $user->id,
                'recipes' => $recipes,
            ], 200);
        
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error del servidor', 'error' => $e->getMessage()], 500);
        }
    }

}
