<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\RecipeUser;
use App\Models\Notification;
use App\Models\User;
use App\Models\Recommendation;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Recipe;
use App\Models\Live;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    // Devuelve todas las recetas con sus relaciones usuario, categoría y cocina cargadas
    public function index()
    {
        return Recipe::with(['user', 'category', 'cuisine'])->get();
    }
    
    // Devuelve una receta específica con sus relaciones y renombra el usuario a "creador"
    public function show($id)
    {
        $recipe = Recipe::with(['user', 'category', 'cuisine'])->findOrFail($id);
    
        $recipe->creador = $recipe->user->name; 
    
        unset($recipe->user); 
    
        return response()->json($recipe);
    }
    
    // Valida y guarda una nueva receta asociada al usuario autenticado
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
        
        // Asegura que los valores nutricionales existan y tengan campos por defecto si no vienen
        if (isset($data['nutrition'])) {
            $data['nutrition'] = array_merge([
                'calories' => 0,
                'protein' => 0,
                'fats' => 0,
                'carbs' => 0
            ], $data['nutrition']);
        } else {
            $data['nutrition'] = [
                'calories' => 0,
                'protein' => 0,
                'fats' => 0,
                'carbs' => 0
            ];
        }
        
        $recipe = Recipe::create($data);
        
        return response()->json($recipe, 201);
    }
   
    // Actualiza una receta existente con datos validados
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

    // Elimina una receta por su ID
    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        $recipe->delete();
        return response()->json(['message' => 'Recipe deleted successfully']);
    }
    
    // Obtiene las últimas 10 notificaciones para el usuario autenticado
    public function getNotifications(Request $request)
    {
        $userId = $request->user()->id;
    
        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    
        return response()->json($notifications);
    }

    // Alterna el "like" de una receta por parte del usuario autenticado
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
            // Si ya está likeada, la quita y decrementa el contador
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
            // Si no está likeada, la añade y crea notificación si no es el dueño
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
    
            if ($ownerId !== $userId) {
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

    // Obtiene todas las recetas que el usuario ha liked
    public function getUserLikedRecipes()
    {
        $userId = auth()->id();
        
        return Recipe::join('recipe_user', 'recipes.id', '=', 'recipe_user.recipe_id')
            ->where('recipe_user.user_id', $userId)
            ->where('recipe_user.liked', true)
            ->with(['user', 'category', 'cuisine'])
            ->select('recipes.*')
            ->get();
    }

    // Devuelve el contador de likes y si el usuario autenticado ha likeado la receta
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

    // Devuelve todas las recetas (sin filtros)
    public function getAllRecipes()
    {
        $recipes = Recipe::all();
        return response()->json([
            'recipes' => $recipes,
        ], 200);
    }
        
    // Filtra recetas según la categoría indicada
    public function filterByCategory ($id)
    {
        $recipes = Recipe::where('category_id', $id)->get();
        return response()->json([
            'recipes' => $recipes,
        ], 200);
    }
    
    // Obtiene los tiempos totales de preparación + cocción únicos en recetas
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

    // Filtra recetas cuyo tiempo total es menor o igual al indicado
    public function filterByTime($time)
    {
        $recipes = Recipe::whereRaw('IFNULL(prep_time, 0) + IFNULL(cook_time, 0) <= ?', [$time])
            ->get();

        return response()->json([
            'recipes' => $recipes,
        ], 200);
    }

    // Filtra recetas según la cocina indicada
    public function filterByCuisine($id){
        $recipes = Recipe::where('cuisine_id', $id)->get();

        return response()->json([
            'recipes' => $recipes,
        ], 200);
    }

    // Devuelve todos los usuarios con id y nombre
    public function getAllUsers()
    {
        $users = DB::table('users')->select('id', 'name')->get();

        return response()->json([
            'users' => $users,
        ], 200);
    }

    // Devuelve recetas creadas por el usuario autenticado
    public function getRecipesByUser(Request $request)
    {
        $userId = $request->user()->id;

        $recipes = Recipe::where('user_id', $userId)->get();

        return response()->json([
            'recipes' => $recipes,
        ], 200);
    }

    // Obtiene los comentarios de una receta con el nombre del usuario y fechas
    public function getRecipeComments($recipeId)
    {
        $comments = DB::table('comments')
            ->where('recipe_id', $recipeId)
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->select('comments.*', 'users.name as user_name')
            ->orderBy('comments.created_at', 'desc')
            ->get();

        return response()->json([
            'comments' => $comments,
        ], 200);
    }
    
    // Guarda un nuevo comentario para una receta
    public function storeComment(Request $request, $recipeId)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);
    
        $userId = $request->user()->id;
    
        $comment = DB::table('comments')->insert([
            'user_id' => $userId,
            'recipe_id' => $recipeId,
            'comment' => $request->input('comment'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        return response()->json(['message' => 'Comment added successfully']);
    }

    // Genera un PDF con las recetas del usuario autenticado
    public function exportRecipesPdf(Request $request)
    {
        $userId = $request->user()->id;
        $recipes = Recipe::where('user_id', $userId)->get();

        $pdf = Pdf::loadView('recipes_pdf', compact('recipes'));
        return $pdf->download('recipes.pdf');
    }

    // Devuelve las recomendaciones para el usuario autenticado con info del usuario y receta
    public function getRecommendedRecipes(Request $request)
    {
        $userId = $request->user()->id;

        try {
            // Get user's recommendation preferences
            $recommendation = Recommendation::with('user')
                ->where('user_id', $userId)
                ->firstOrFail();

            // Get all recipes
            $recipes = Recipe::with(['user', 'category', 'cuisine'])
                ->get();

            // Filter recipes based on preferences
            $filteredRecipes = $recipes->filter(function ($recipe) use ($recommendation) {
                $cuisineMatch = in_array($recipe->cuisine_id, $recommendation->cuisine_ids ?? []);
                $categoryMatch = in_array($recipe->category_id, $recommendation->category_ids ?? []);
                return $cuisineMatch || $categoryMatch;
            });

            // Shuffle and take top 10
            $recommendedRecipes = $filteredRecipes->shuffle()->take(10);

            return response()->json([
                'recommendation' => $recommendation,
                'recommended_recipes' => $recommendedRecipes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se encontraron preferencias de recomendaciones para este usuario'
            ], 404);
        }
    }
    public function getAllIngredients()
    {
        // Obtener todas las recetas con solo el campo de ingredientes
        $recipes = Recipe::select('ingredients')->get();
        
        // Array para almacenar todos los ingredientes
        $allIngredients = [];
        
        // Recorrer cada receta y extraer los ingredientes
        foreach ($recipes as $recipe) {
            $ingredients = $recipe->ingredients;
            
            // Si es un string, intentar decodificar como JSON
            if (is_string($ingredients)) {
                $decoded = json_decode($ingredients, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $ingredients = $decoded;
                } else {
                    // Si no es JSON válido, continuar con la siguiente receta
                    continue;
                }
            }
            
            // Si es un array, procesar cada ingrediente
            if (is_array($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    // Si el ingrediente es un array (como en tu estructura de datos)
                    if (is_array($ingredient) && isset($ingredient['name'])) {
                        $allIngredients[] = $ingredient['name'];
                    } 
                    // Si es un string directo
                    elseif (is_string($ingredient)) {
                        $allIngredients[] = $ingredient;
                    }
                }
            }
        }
        
        // Limpiar ingredientes: eliminar espacios en blanco, valores vacíos y duplicados
        $cleanedIngredients = array_map(function($item) {
            return is_string($item) ? trim($item) : $item;
        }, $allIngredients);
        
        $cleanedIngredients = array_filter($cleanedIngredients, function($item) {
            return !empty($item) && is_string($item);
        });
        
        $uniqueIngredients = array_unique($cleanedIngredients);
        
        // Ordenar alfabéticamente
        sort($uniqueIngredients);
        
        return response()->json([
            'success' => true,
            'count' => count($uniqueIngredients),
            'ingredients' => array_values($uniqueIngredients) // reindexar array
        ], 200);
    }

    // Filtrar recetas por ingredientes
    public function filterByIngredients(Request $request)
    {
        $request->validate([
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'string'
        ]);

        $ingredients = $request->input('ingredients');

        // Buscar recetas que contengan TODOS los ingredientes
        $recipes = Recipe::where(function ($query) use ($ingredients) {
            foreach ($ingredients as $ingredient) {
                $query->whereRaw('JSON_CONTAINS(ingredients, ?)', [json_encode($ingredient)]);
            }
        })->get();

        return response()->json([
            'recipes' => $recipes,
        ], 200);
    }
    // Devuelve la receta con ID aleatorio
    public function getRandomRecipe()
    {
        $recipe = Recipe::inRandomOrder()->first();

        return response()->json($recipe);
    }
    
    // Devuelve el último "live" en curso, ordenado por fecha
    public function getLastLive()
    {
        $live = Live::orderBy('date', 'desc')->first();

        return response()->json($live);
    }

    // Devuelve todas las recetas con paginación, filtradas por texto de búsqueda en título
    public function getAllRecipesPaginated(Request $request)
    {
        $search = $request->input('search');
        $query = Recipe::query();

        if ($search) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        $recipes = $query->paginate(12);

        return response()->json($recipes);
    }
}