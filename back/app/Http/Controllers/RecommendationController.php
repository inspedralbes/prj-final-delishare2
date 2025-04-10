<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; // ¡Esta es la línea que falta!

use Illuminate\Http\Request;
use App\Models\Cuisine;
use App\Models\Category;
use App\Models\User;
use App\Models\Recommendation;

class RecommendationController extends Controller
{
    /**
     * Obtiene todas las cocinas y categorías disponibles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCuisinesAndCategories()
    {
        try {
            // Obtener todas las cocinas
            $cuisines = Cuisine::all(['id', 'country as name']);
            
            // Obtener todas las categorías
            $categories = Category::all(['id', 'name']);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'cuisines' => $cuisines,
                    'categories' => $categories
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
        
    }
    public function storePreferences(Request $request)
    {
        try {
            $validated = $request->validate([
                'cuisine_ids' => 'nullable|array',
                'cuisine_ids.*' => 'exists:cuisines,id',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'exists:categories,id'
            ]);
    
            $user = $request->user();
    
            $recommendation = Recommendation::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'cuisine_ids' => $validated['cuisine_ids'] ?? null,
                    'category_ids' => $validated['category_ids'] ?? null
                ]
            );
    
            return response()->json([
                'success' => true,
                'message' => 'Preferencias guardadas correctamente',
                'data' => $recommendation
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar preferencias: ' . $e->getMessage()
            ], 500);
        }
    }
}