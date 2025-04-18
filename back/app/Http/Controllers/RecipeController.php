<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\RecipeUser;
use App\Models\Notification;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'video' => 'nullable|string',

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
        'video' => 'nullable|string',


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
    public function getNotifications(Request $request)
    {
        $userId = $request->user()->id;
    
        // Aquí puedes personalizar la lógica para obtener las notificaciones
        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10) // Limitar a las últimas 10 notificaciones
            ->get();
    
        return response()->json($notifications);
    }
    public function toggleLike(Request $request, $recipeId)
    {
        $userId = $request->user()->id;
        $recipe = Recipe::findOrFail($recipeId);
        $ownerId = $recipe->user_id;
    
        $recipeUser = DB::table('recipe_user')
            ->where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->first();
    
        if ($recipeUser && $recipeUser->liked) {
            // Eliminar like
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
            // Añadir like
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
    
            // Crear notificación solo si no es el dueño
            if ($ownerId !== $userId) {
                // Ejemplo en toggleLike
Notification::create([
    'user_id' => $ownerId,
    'triggered_by' => $userId,
    'type' => 'like',
    'recipe_id' => $recipeId,
    'message' => ' ha dado like a tu receta "' . $recipe->title . '"',
    'read' => false
]);
            }
    
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



public function getRecipesByUser(Request $request)
{
    $userId = $request->user()->id;

    $recipes = Recipe::where('user_id', $userId)->get();

    return response()->json([
        'recipes' => $recipes,
    ], 200);
}

// En RecipeController.php
public function getRecipeComments($recipeId)
{
    $comments = DB::table('recipe_user')
        ->where('recipe_id', $recipeId)
        ->whereNotNull('comment')
        ->join('users', 'recipe_user.user_id', '=', 'users.id')
        ->select(
            'users.name', 
            'recipe_user.comment', 
            'recipe_user.updated_at',
            'recipe_user.created_at'
        )
        ->orderBy('recipe_user.updated_at', 'desc')
        ->get();

    return response()->json($comments);
}

public function addComment(Request $request, $recipeId)
{
    $userId = auth()->id();
    $recipe = Recipe::findOrFail($recipeId);
    $ownerId = $recipe->user_id;

    $request->validate([
        'comment' => 'required|string|max:1000'
    ]);

    try {
        // Insertar nuevo comentario (sin verificar si ya existe)
        DB::table('recipe_user')->insert([
            'user_id' => $userId,
            'recipe_id' => $recipeId,
            'comment' => $request->input('comment'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Obtener el comentario recién creado con los datos del usuario
        $newComment = DB::table('recipe_user')
            ->where('user_id', $userId)
            ->where('recipe_id', $recipeId)
            ->where('comment', $request->input('comment'))
            ->join('users', 'recipe_user.user_id', '=', 'users.id')
            ->select('users.name', 'recipe_user.comment', 'recipe_user.updated_at')
            ->first();

        // Crear notificación si no es el dueño
        if ($ownerId !== $userId) {
            $userName = User::find($userId)->name;
            Notification::create([
                'user_id' => $ownerId,
                'triggered_by' => $userId,
                'recipe_id' => $recipeId,
                'type' => 'comment',
                'message' => $userName.' ha comentado tu receta "'.$recipe->title.'": "'.$request->input('comment').'"',
                'read' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'comment' => $newComment
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar el comentario: '.$e->getMessage()
        ], 500);
    }
}
public function getAllIngredients()
{
    $recipes = Recipe::select('ingredients')->get();
    $allIngredients = [];
     
    // Prefijos y sufijos a eliminar (incluyendo unidades de medida)
    $prefixes = ['g ', 'kg ', 'ml ', 'l ', 'cucharadita ', 'cucharada ', 
                'taza ', 'tazas ', 'unitat ', 'unitats ', 's ', 'name:'];

    foreach ($recipes as $recipe) {
        $ingredients = $recipe->ingredients;
        
        if (is_string($ingredients)) {
            $decoded = json_decode($ingredients, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $ingredients = $decoded;
            } else {
                continue;
            }
        }
        
        if (is_array($ingredients)) {
            foreach ($ingredients as $ingredient) {
                if (is_array($ingredient) && isset($ingredient['name'])) {
                    $name = $ingredient['name'];
                    // Eliminar "name:" si existe
                    $name = str_replace('name:', '', $name);
                    $allIngredients[] = trim(strtolower($name));
                } 
                else if (is_string($ingredient)) {
                    // Eliminar prefijos numéricos o de cantidad
                    $name = preg_replace('/^[\d\/\.]+\s*/', '', $ingredient); // Elimina cantidades numéricas
                    
                    // Eliminar prefijos comunes
                    foreach ($prefixes as $prefix) {
                        if (strpos(strtolower($name), $prefix) === 0) {
                            $name = substr($name, strlen($prefix));
                            break;
                        }
                    }
                    
                    $allIngredients[] = trim(strtolower($name));
                }
            }
        }
    }
    
    $cleanedIngredients = array_filter($allIngredients, function($item) {
        return !empty($item) && strlen(trim($item)) > 0;
    });
    
    $uniqueIngredients = array_unique($cleanedIngredients);
    sort($uniqueIngredients);
    
    return response()->json([
        'success' => true,
        'count' => count($uniqueIngredients),
        'ingredients' => array_values($uniqueIngredients)
    ], 200);
}

public function filterByIngredients(Request $request)
{
    $request->validate([
        'ingredients' => 'required|array|min:1',
        'ingredients.*' => 'string'
    ]);

    $ingredients = $request->input('ingredients');
    $recipes = Recipe::all();
    $filteredRecipes = [];

    foreach ($recipes as $recipe) {
        $recipeIngredients = $recipe->ingredients;
        
        if (is_string($recipeIngredients)) {
            $recipeIngredients = json_decode($recipeIngredients, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }
        }

        $matchesAll = true;
        foreach ($ingredients as $ingredient) {
            $found = false;
            
            foreach ($recipeIngredients as $recipeIng) {
                if (is_array($recipeIng)) {
                    if (strtolower($recipeIng['name']) === strtolower($ingredient)) {
                        $found = true;
                        break;
                    }
                } else if (is_string($recipeIng)) {
                    $parts = explode(' ', $recipeIng);
                    $nameParts = array_slice($parts, 1);
                    $name = strtolower(implode(' ', $nameParts));
                    if ($name === strtolower($ingredient)) {
                        $found = true;
                        break;
                    }
                }
            }
            
            if (!$found) {
                $matchesAll = false;
                break;
            }
        }

        if ($matchesAll) {
            $filteredRecipes[] = $recipe;
        }
    }

    return response()->json([
        'recipes' => $filteredRecipes,
    ], 200);
}
public function filterByIngredient($ingredient)
{
    $recipes = Recipe::all();
    $filteredRecipes = [];
    $searchTerm = strtolower(trim($ingredient));

    foreach ($recipes as $recipe) {
        $recipeIngredients = $recipe->ingredients;
        
        if (is_string($recipeIngredients)) {
            $recipeIngredients = json_decode($recipeIngredients, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }
        }

        foreach ($recipeIngredients as $recipeIng) {
            if (is_array($recipeIng)) {
                if (strpos(strtolower($recipeIng['name']), $searchTerm) !== false) {
                    $filteredRecipes[] = $recipe;
                    break;
                }
            } else if (is_string($recipeIng)) {
                $parts = explode(' ', $recipeIng);
                $nameParts = array_slice($parts, 1);
                $name = strtolower(implode(' ', $nameParts));
                if (strpos($name, $searchTerm) !== false) {
                    $filteredRecipes[] = $recipe;
                    break;
                }
            }
        }
    }

    return response()->json([
        'recipes' => $filteredRecipes,
    ], 200);
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

public function downloadFullRecipe($id)
{
    $recipe = Recipe::with(['user', 'category', 'cuisine'])->findOrFail($id);

    $comments = DB::table('recipe_user')
        ->where('recipe_id', $id)
        ->whereNotNull('comment')
        ->join('users', 'recipe_user.user_id', '=', 'users.id')
        ->select('users.name', 'recipe_user.comment', 'recipe_user.updated_at')
        ->orderByDesc('recipe_user.updated_at')
        ->get();

    $total_time = $recipe->prep_time + $recipe->cook_time;

    $data = [
        'recipe' => [
            'title' => $recipe->title,
            'description' => $recipe->description,
            'ingredients' => $recipe->ingredients,
            'steps' => $recipe->steps,
            'prep_time' => $recipe->prep_time,
            'cook_time' => $recipe->cook_time,
            'total_time' => $total_time,
            'servings' => $recipe->servings,
            'nutrition' => $recipe->nutrition,
            'image' => $recipe->image, // Añade esto si quieres incluir la imagen
        ],
        'metadata' => [
            'creador' => $recipe->user->name,
            'categoria' => $recipe->category->name ?? null,
            'tipo_cocina' => $recipe->cuisine->name ?? null,
            'likes_count' => $recipe->likes_count,
        ],
        'comments' => $comments
    ];

    $pdf = Pdf::loadView('recipes.pdf', $data);
    
    // Forzar la descarga con el nombre del archivo
    return $pdf->download('receta-' . Str::slug($recipe->title) . '.pdf');
}
}