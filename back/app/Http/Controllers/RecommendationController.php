<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; 

use Illuminate\Http\Request;
use App\Models\Cuisine;
use App\Models\Category;
use App\Models\User;
use App\Models\Recommendation;

class RecommendationController extends Controller
{
    /**
     * Obtiene las preferencias de usuario guardadas en recomendaciones
     * Devuelve las cocinas y categorías preferidas, o null si no hay
     */
    public function getUserPreferences(Request $request)
    {
        try {
            $user = $request->user();
            // Buscar la primera recomendación asociada al usuario
            $recommendation = Recommendation::where('user_id', $user->id)->first();

            if (!$recommendation) {
                // No hay recomendaciones, devolver null en data
                return response()->json([
                    'success' => true,
                    'data' => null
                ], 200);
            }

            // Retorna las preferencias guardadas: IDs de cocinas y categorías
            return response()->json([
                'success' => true,
                'data' => [
                    'cuisines' => $recommendation->cuisine_ids,
                    'categories' => $recommendation->category_ids
                ]
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores y retorno de mensaje con el detalle del error
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener preferencias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene todas las cocinas y categorías disponibles
     * Devuelve las listas para que el cliente pueda mostrar opciones
     */
    public function getCuisinesAndCategories()
    {
        try {
            // Obtener todas las cocinas, seleccionando id y renombrando 'country' a 'name'
            $cuisines = Cuisine::all(['id', 'country as name']);
            
            // Obtener todas las categorías con id y nombre
            $categories = Category::all(['id', 'name']);
            
            // Retorna un JSON con los datos de cocinas y categorías
            return response()->json([
                'success' => true,
                'data' => [
                    'cuisines' => $cuisines,
                    'categories' => $categories
                ]
            ], 200);
            
        } catch (\Exception $e) {
            // Manejo de errores en la obtención de datos
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda o actualiza las preferencias del usuario (cocinas y categorías)
     * Valida que los IDs enviados existan en la base de datos
     */
    public function storePreferences(Request $request)
    {
        try {
            // Validar que los arrays opcionales de IDs existen y son válidos
            $validated = $request->validate([
                'cuisine_ids' => 'nullable|array',
                'cuisine_ids.*' => 'exists:cuisines,id',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'exists:categories,id'
            ]);
    
            $user = $request->user();
    
            // Actualiza o crea la fila de recomendaciones para el usuario
            $recommendation = Recommendation::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'cuisine_ids' => $validated['cuisine_ids'] ?? null,
                    'category_ids' => $validated['category_ids'] ?? null
                ]
            );
    
            // Retorna confirmación de éxito y los datos guardados
            return response()->json([
                'success' => true,
                'message' => 'Preferencias guardadas correctamente',
                'data' => $recommendation
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejo específico de errores de validación con detalles
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            // Otros errores en la operación
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar preferencias: ' . $e->getMessage()
            ], 500);
        }
    }
}
