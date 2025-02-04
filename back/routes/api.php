<?php
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CuisineController;
use App\Http\Controllers\SavedRecipeController;

// Categorías
Route::middleware('auth:sanctum')->post('/categories', [CategoryController::class, 'store']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::middleware('auth:sanctum')->get('/categories/{id}', [CategoryController::class, 'show']);
Route::middleware('auth:sanctum')->put('/categories/{id}', [CategoryController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/categories/{id}', [CategoryController::class, 'destroy']);

// Cocinas
Route::middleware('auth:sanctum')->post('/cuisines', [CuisineController::class, 'store']);
Route::get('/cuisines', [CuisineController::class, 'index']);  
Route::get('/filterByCuisine/{id}', [CuisineController::class, 'filterByCuisine']);  // Filtrar recetas por cocina (país)

Route::middleware('auth:sanctum')->get('/cuisines/{id}', [CuisineController::class, 'show']);
Route::middleware('auth:sanctum')->put('/cuisines/{id}', [CuisineController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/cuisines/{id}', [CuisineController::class, 'destroy']);



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/recipes/{id}', [RecipeController::class, 'show']);
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::put('/recipes/{id}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);
    Route::get('/recipes', [RecipeController::class, 'index']);

});

// Ruta para dar like a una receta
Route::middleware('auth:sanctum')->post('/recipes/{id}/like', [RecipeController::class, 'likeRecipe']);

// Ruta para quitar like a una receta
Route::middleware('auth:sanctum')->post('/recipes/{id}/unlike', [RecipeController::class, 'unlikeRecipe']);

Route::get('/', function () {
    return view('welcome');
});

// Registro
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas con autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/updatePerfile', [AuthController::class, 'updatePerfil']);
});   //ruta para update peril- http://127.0.0.1:8000/api/updatePerfile

Route::middleware('auth:sanctum')->post('/cambiarContra', [AuthController::class, 'cambiarContra']);
//ruta para cambiar contra- http://127.0.0.1:8000/api/cambiarContra
Route::middleware('auth:sanctum')->get('/user/recipes', [RecipeController::class, 'getUserRecipes']);


//Ruta para obtener todos los usuarios("Hazta hacer el filtro")
Route::get('/getAllUsers', [AuthController::class, 'getAllUsers']);
Route::get('/getAllRecipes', [RecipeController::class, 'getAllRecipes']);
Route::get('/filterByCategory/{id}', [RecipeController::class, 'filterByCategory']);
Route::get('/filterByCuisine/{id}', [RecipeController::class, 'filterByCuisine']);
Route::get('/filterByTime/{time}', [RecipeController::class, 'filterByTime']);
Route::get('/times', [RecipeController::class, 'getAllTimes']);
Route::get('/recipes/filterByUser/{userId}', [RecipeController::class, 'filterByUser']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/saved-recipes', [SavedRecipeController::class, 'index']);
    Route::post('/saved-recipes/toggle/{recipeId}', [SavedRecipeController::class, 'toggleSave']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) { // ✅ Pasa $request como parámetro
    return response()->json($request->user());
});