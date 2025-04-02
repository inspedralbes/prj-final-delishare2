<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\RecipeUser;

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
        $recipe = Recipe::with(['user', 'category', 'cuisine'])->findOrFail($id);
    
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
    
        $data['user_id'] = auth()->id();
    
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
        'nutrition' => 'nullable|array', 
        'image' => 'nullable|string', 

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
 
public function toggleLike(Request $request, $recipeId)
{
    $userId = $request->user()->id;
    
    $recipeUser = DB::table('recipe_user')
        ->where('user_id', $userId)
        ->where('recipe_id', $recipeId)
        ->first();

    if ($recipeUser && $recipeUser->liked) {
        // Si ya existe y tiene like, quitamos el like
        DB::table('recipe_user')
            ->where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->update(['liked' => false, 'updated_at' => now()]);

        Recipe::where('id', $recipeId)->decrement('likes_count');

        return response()->json([
            'message' => 'Recipe unliked successfully', 
            'liked' => false,
            'likes_count' => Recipe::find($recipeId)->likes_count
        ]);
    } else {
        // Si no existe o no tiene like, añadimos like
        if ($recipeUser) {
            DB::table('recipe_user')
                ->where('user_id', $userId)
                ->where('recipe_id', $recipeId)
                ->update(['liked' => true, 'updated_at' => now()]);
        } else {
            DB::table('recipe_user')->insert([
                'user_id' => $userId,
                'recipe_id' => $recipeId,
                'liked' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Recipe::where('id', $recipeId)->increment('likes_count');

        return response()->json([
            'message' => 'Recipe liked successfully', 
            'liked' => true,
            'likes_count' => Recipe::find($recipeId)->likes_count
        ]);
    }
}

public function getLikes($recipeId)
{
    return response()->json([
        'likes_count' => Recipe::find($recipeId)->likes_count,
        'is_liked' => auth()->check() && DB::table('recipe_user')
            ->where('user_id', auth()->id())
            ->where('recipe_id', $recipeId)
            ->where('liked', true)
            ->exists()
    ]);
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
    
   public function getAllTimes()
   {
       $times = DB::table('recipes')
           ->selectRaw('IFNULL(prep_time, 0) + IFNULL(cook_time, 0) as total_time')
           ->distinct()
           ->pluck('total_time'); 

       return response()->json([
           'times' => $times,
       ], 200);
   }

   public function filterByTime($time)
   {
       $recipes = Recipe::whereRaw('IFNULL(prep_time, 0) + IFNULL(cook_time, 0) <= ?', [$time])
           ->get();

       return response()->json([
           'recipes' => $recipes,
       ], 200);
   }

public function filterByCuisine($id){
    $recipes = Recipe::where('cuisine_id', $id)->get();

    return response()->json([
        'recipes' => $recipes,
    ], 200);
}
public function getAllUsers()
{
    $users = DB::table('users')->select('id', 'name')->get();

    return response()->json([
        'users' => $users,
    ], 200);
}


public function filterByUser($userId)
{
    $recipes = Recipe::where('user_id', $userId)->get();
    return response()->json([
        'recipes' => $recipes,
    ], 200);
}
public function getRecipesByUser(Request $request)
{
    $userId = $request->user()->id;

    $recipes = Recipe::where('user_id', $userId)->get();

    return response()->json([
        'recipes' => $recipes,
    ], 200);
}


//comentario
public function getRecipeComments($recipeId)
{
    $comments = DB::table('recipe_user')
        ->where('recipe_id', $recipeId)
        ->whereNotNull('comment')
        ->join('users', 'recipe_user.user_id', '=', 'users.id')
        ->select('users.name', 'recipe_user.comment', 'recipe_user.updated_at')
        ->orderByDesc('recipe_user.updated_at')
        ->get();

    return response()->json($comments);
}


public function addComment(Request $request, $recipeId)
{
    $userId = $request->user()->id;

    $request->validate([
        'comment' => 'required|string|max:1000'
    ]);

    // Crear un nuevo comentario para la receta
    $recipeUser = RecipeUser::create([
        'user_id' => $userId,
        'recipe_id' => $recipeId,
        'comment' => $request->comment,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json([
        'message' => 'Comentario agregado correctamente',
        'comment' => $recipeUser->comment
    ]);
}


// Eliminar un comentario
public function deleteComment(Request $request, $recipeId)
{
    $userId = $request->user()->id;

    RecipeUser::where('user_id', $userId)
        ->where('recipe_id', $recipeId)
        ->update(['comment' => null, 'updated_at' => now()]);

    return response()->json(['message' => 'Comentario eliminado correctamente']);
}

}