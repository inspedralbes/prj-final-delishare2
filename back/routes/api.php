<?php
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CuisineController;
use App\Http\Controllers\SavedRecipeController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecommendationController;
use Barryvdh\DomPDF\Facade\Pdf;


Route::get('/user/{id}/recipes', [InfoUserController::class, 'showRecipes']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/recommendations/preferences', [RecommendationController::class, 'storePreferences']);
    Route::get('/user/preferences', [RecommendationController::class, 'getPreferenceNames']);
    Route::get('/recipes/recommended', [RecipeController::class, 'getRecommendedRecipes'])->middleware('auth:sanctum');
    Route::get('/recommendations/options', [RecommendationController::class, 'getCuisinesAndCategories']);
    Route::get('/user/liked-recipes', [RecipeController::class, 'getUserLikedRecipes']);

});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'getUserNotifications']);
    Route::post('/notifications', [NotificationController::class, 'createNotification']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});

// Usuarios
Route::get('userInfo/{id}', [InfoUserController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/', [UserController::class, 'store']);

    ;
});

// Categorías
Route::middleware('auth:sanctum')->post('/categories', [CategoryController::class, 'store']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::middleware('auth:sanctum')->get('/categories/{id}', [CategoryController::class, 'show']);
Route::middleware('auth:sanctum')->put('/categories/{id}', [CategoryController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/user/recetas', [RecipeController::class, 'getRecipesByUser']);

// Cocinas
Route::middleware('auth:sanctum')->post('/cuisines', [CuisineController::class, 'store']);
Route::get('/cuisines', [CuisineController::class, 'index']);  
Route::get('/filterByCuisine/{id}', [CuisineController::class, 'filterByCuisine']);  // Filtrar recetas por cocina (país)

Route::middleware('auth:sanctum')->get('/cuisines/{id}', [CuisineController::class, 'show']);
Route::middleware('auth:sanctum')->put('/cuisines/{id}', [CuisineController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/cuisines/{id}', [CuisineController::class, 'destroy']);

Route::get('/recipes', [RecipeController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/recipes/{id}', [RecipeController::class, 'show']);
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::put('/recipes/{id}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);
    Route::post('/recipes/{recipe}/like', [RecipeController::class, 'toggleLike']);
    Route::get('/recipes/{recipe}/likes', [RecipeController::class, 'getLikes']);});

Route::get('/', function () {
    return view('welcome');
});

// Rutas para configuarcion de user
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
Route::post('/updatePerfile', [AuthController::class, 'updatePerfil']);  });   
Route::middleware('auth:sanctum')->put('/updateProfilePicture', [AuthController::class, 'updateProfilePicture']);
Route::middleware('auth:sanctum')->post('/cambiarContra', [AuthController::class, 'cambiarContra']);

//Rutas para obtener todos los usuarios
Route::get('/getAllUsers', [RecipeController::class, 'getAllUsers']);
Route::get('/getAllRecipes', [RecipeController::class, 'getAllRecipes']);
Route::get('/filterByCategory/{id}', [RecipeController::class, 'filterByCategory']);
Route::get('/filterByCuisine/{id}', [RecipeController::class, 'filterByCuisine']);
Route::get('/filterByTime/{time}', [RecipeController::class, 'filterByTime']);
Route::get('/times', [RecipeController::class, 'getAllTimes']);

//Rutas para guardadas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/saved-recipes', [SavedRecipeController::class, 'index']);
    Route::post('/saved-recipes/toggle/{recipeId}', [SavedRecipeController::class, 'toggleSave']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) { 
    return response()->json($request->user());
});

Route::get('/ingredients', [RecipeController::class, 'getAllIngredients']);
Route::post('/recipes/filter-by-ingredients', [RecipeController::class, 'filterByIngredients']);

//comments
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/recipes/{id}/comment', [RecipeController::class, 'addComment']);
    Route::get('/recipes/{id}/comments', [RecipeController::class, 'getRecipeComments']);
    Route::delete('recipes/{recipeId}/comments', [RecipeController::class, 'deleteCommentByText']);
    Route::get('/comments', [RecipeController::class, 'getAllComments']);


});


//rutas para me gustas
Route::middleware('auth:sanctum')->post('/recipes/{id}/like', [RecipeController::class, 'likeRecipe']);
Route::middleware('auth:sanctum')->post('/recipes/{id}/unlike', [RecipeController::class, 'unlikeRecipe']);


//rutas para folder
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/folders', [FolderController::class, 'store']); 
    Route::post('/folders/{folderId}/recipes/{recipeId}', [FolderController::class, 'addRecipe']); 
    Route::get('/folders/{folder}', [FolderController::class, 'show']); 
    Route::delete('/folders/{folderId}/recipes/{recipeId}', [FolderController::class, 'removeRecipe']); 
    Route::delete('/folders/{folderId}', [FolderController::class, 'removeFolder']);
    Route::middleware('auth:sanctum')->get('/folders', [FolderController::class, 'index']);  
});
Route::middleware('auth:sanctum')->get('/folders/{folder}/recipes', [FolderController::class, 'getRecipes']);

Route::get('/recipes/{id}/download', [RecipeController::class, 'downloadFullRecipe']);