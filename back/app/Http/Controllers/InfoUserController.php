<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Folder;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InfoUserController extends Controller
{
    
    public function index(){
        return User::all();
    }


    public function show($id){
        try {
            $user = User::findOrFail($id);
            
            $folders = Folder::where('user_id', $user->id)->get();
        
            $recipes = Recipe::where('user_id', $user->id)->get();
        
            $recipesInFolders = [];
            foreach ($folders as $folder) {
                $recipesInFolders[$folder->name] = $folder->recipes;
            }
        
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'img' => $user->img,
                'password' => $user->password,
                'personal_access_token' => $user->personal_access_token,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'folders' => $folders,
                'recipes' => $recipes,
                'recipes_in_folders' => $recipesInFolders,
            ], 200);
        
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error del servidor', 'error' => $e->getMessage()], 500);
        }
    }
    

    public function update(Request $request, $id){
        $user = User::findOrFail($id);
        $user->update($request->all());
        return $user;
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }


    public function showRecipes($id){
    try {
        $user = User::findOrFail($id);
        
        $recipes = Recipe::where('user_id', $user->id)->get();
        
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
