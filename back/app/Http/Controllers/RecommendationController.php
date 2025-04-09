<?php

namespace App\Http\Controllers;

use App\Models\Cuisine;
use App\Models\Category;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RecommendationController extends Controller
{
    public function storeSelectedCuisines(Request $request)
    {
        // Log completo de la solicitud para debugging
        Log::info('Full request data:', [
            'all' => $request->all(),
            'json' => $request->json()->all(),
            'headers' => $request->headers->all(),
            'content' => $request->getContent()
        ]);
        
        // Validar que el usuario esté autenticado
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // Determinar qué datos usar para validación
        $inputData = [];
        
        // Primero intentamos obtener de json directo
        if ($request->isJson()) {
            $inputData = $request->json()->all();
        }
        // Si no es json o está vacío, usamos request->all()
        if (empty($inputData)) {
            $inputData = $request->all();
        }
        
        // Si aún está vacío o no contiene cuisine_ids, tratamos de parsearlo manualmente
        if (empty($inputData) || !isset($inputData['cuisine_ids'])) {
            $content = $request->getContent();
            if (!empty($content)) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $inputData = $decoded;
                }
            }
        }
        
        // Si después de todo esto seguimos sin cuisine_ids, verificamos otros formatos
        if (!isset($inputData['cuisine_ids'])) {
            // Buscar como cuisine_id (singular)
            if (isset($inputData['cuisine_id'])) {
                $inputData['cuisine_ids'] = [$inputData['cuisine_id']];
            }
            // Buscar como parámetro en la URL
            elseif ($request->query('cuisine_ids')) {
                $cuisineIdsQuery = $request->query('cuisine_ids');
                if (is_string($cuisineIdsQuery)) {
                    // Intentar parsearlo como array json
                    $decoded = json_decode($cuisineIdsQuery, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $inputData['cuisine_ids'] = $decoded;
                    } else {
                        // O como valores separados por coma
                        $inputData['cuisine_ids'] = explode(',', $cuisineIdsQuery);
                    }
                }
            }
            // Si la solicitud tiene un formato inusual, tratar de extraer datos
            else {
                // Reenviar el error con información detallada
                return response()->json([
                    'message' => 'The cuisine ids field is required.',
                    'errors' => [
                        'cuisine_ids' => ['The cuisine ids field is required.']
                    ],
                    'debug' => [
                        'received_data' => $request->all(),
                        'content' => $request->getContent(),
                        'content_type' => $request->header('Content-Type')
                    ]
                ], 422);
            }
        }
        
        // Validar manualmente los datos
        if (!isset($inputData['cuisine_ids']) || !is_array($inputData['cuisine_ids']) || empty($inputData['cuisine_ids'])) {
            return response()->json([
                'message' => 'The cuisine ids field must be an array with at least one value.',
                'errors' => [
                    'cuisine_ids' => ['The cuisine ids field must be an array with at least one value.']
                ]
            ], 422);
        }
        
        // Convertir todo a enteros
        $cuisineIds = array_map(function($id) {
            return (int)$id;
        }, $inputData['cuisine_ids']);
        
        // Verificar que los IDs de cuisine existan
        foreach ($cuisineIds as $cuisineId) {
            $exists = Cuisine::where('id', $cuisineId)->exists();
            if (!$exists) {
                return response()->json([
                    'message' => "Cuisine with ID {$cuisineId} does not exist.",
                    'errors' => [
                        'cuisine_ids' => ["Cuisine with ID {$cuisineId} does not exist."]
                    ]
                ], 422);
            }
        }
        
        // Obtener una categoría predeterminada (primera disponible)
        $defaultCategory = Category::first();
        if (!$defaultCategory) {
            return response()->json([
                'message' => 'No categories available in the system.',
                'errors' => [
                    'category_id' => ['No categories available in the system.']
                ]
            ], 422);
        }
        
        // Usar la categoría proporcionada o la predeterminada
        $categoryId = isset($inputData['category_id']) ? (int)$inputData['category_id'] : $defaultCategory->id;
        
        // Verificar que la categoría existe
        $categoryExists = Category::where('id', $categoryId)->exists();
        if (!$categoryExists) {
            return response()->json([
                'message' => "Category with ID {$categoryId} does not exist.",
                'errors' => [
                    'category_id' => ["Category with ID {$categoryId} does not exist."]
                ]
            ], 422);
        }
        
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();
        
        // Crear recomendaciones
        $recommendations = [];
        foreach ($cuisineIds as $cuisineId) {
            $recommendation = Recommendation::create([
                'user_id' => $userId,
                'cuisine_id' => $cuisineId,
                'category_id' => $categoryId,
                'ingredients' => '' // Cadena vacía en lugar de null
            ]);
            
            $recommendations[] = $recommendation;
        }
        
        return response()->json([
            'message' => 'Recommendations created successfully',
            'data' => $recommendations
        ], 201);
    }

    public function storeSelectedCategories(Request $request)
    {
        // Log completo de la solicitud para debugging
        Log::info('Category request data:', [
            'all' => $request->all(),
            'json' => $request->json()->all(),
            'headers' => $request->headers->all(),
            'content' => $request->getContent()
        ]);
        
        // Validar que el usuario esté autenticado
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // Determinar qué datos usar para validación
        $inputData = [];
        
        // Primero intentamos obtener de json directo
        if ($request->isJson()) {
            $inputData = $request->json()->all();
        }
        // Si no es json o está vacío, usamos request->all()
        if (empty($inputData)) {
            $inputData = $request->all();
        }
        
        // Si aún está vacío o no contiene category_ids, tratamos de parsearlo manualmente
        if (empty($inputData) || !isset($inputData['category_ids'])) {
            $content = $request->getContent();
            if (!empty($content)) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $inputData = $decoded;
                }
            }
        }
        
        // Si después de todo esto seguimos sin category_ids, verificamos otros formatos
        if (!isset($inputData['category_ids'])) {
            // Buscar como category_id (singular)
            if (isset($inputData['category_id'])) {
                $inputData['category_ids'] = [$inputData['category_id']];
            }
            // Buscar como parámetro en la URL
            elseif ($request->query('category_ids')) {
                $categoryIdsQuery = $request->query('category_ids');
                if (is_string($categoryIdsQuery)) {
                    // Intentar parsearlo como array json
                    $decoded = json_decode($categoryIdsQuery, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $inputData['category_ids'] = $decoded;
                    } else {
                        // O como valores separados por coma
                        $inputData['category_ids'] = explode(',', $categoryIdsQuery);
                    }
                }
            }
            // Si la solicitud tiene un formato inusual, tratar de extraer datos
            else {
                // Reenviar el error con información detallada
                return response()->json([
                    'message' => 'The category ids field is required.',
                    'errors' => [
                        'category_ids' => ['The category ids field is required.']
                    ],
                    'debug' => [
                        'received_data' => $request->all(),
                        'content' => $request->getContent(),
                        'content_type' => $request->header('Content-Type')
                    ]
                ], 422);
            }
        }
        
        // Validar manualmente los datos
        if (!isset($inputData['category_ids']) || !is_array($inputData['category_ids']) || empty($inputData['category_ids'])) {
            return response()->json([
                'message' => 'The category ids field must be an array with at least one value.',
                'errors' => [
                    'category_ids' => ['The category ids field must be an array with at least one value.']
                ]
            ], 422);
        }
        
        // Convertir todo a enteros
        $categoryIds = array_map(function($id) {
            return (int)$id;
        }, $inputData['category_ids']);
        
        // Verificar que el número de categorías está dentro del límite (1-5)
        if (count($categoryIds) > 5) {
            return response()->json([
                'message' => 'You can select a maximum of 5 categories.',
                'errors' => [
                    'category_ids' => ['You can select a maximum of 5 categories.']
                ]
            ], 422);
        }
        
        // Verificar que los IDs de categoría existan
        foreach ($categoryIds as $categoryId) {
            $exists = Category::where('id', $categoryId)->exists();
            if (!$exists) {
                return response()->json([
                    'message' => "Category with ID {$categoryId} does not exist.",
                    'errors' => [
                        'category_ids' => ["Category with ID {$categoryId} does not exist."]
                    ]
                ], 422);
            }
        }
        
        // Obtener una cuisine predeterminada (primera disponible) si es necesaria
        $cuisineId = null;
        if (isset($inputData['cuisine_id'])) {
            $cuisineId = (int)$inputData['cuisine_id'];
            $cuisineExists = Cuisine::where('id', $cuisineId)->exists();
            if (!$cuisineExists) {
                return response()->json([
                    'message' => "Cuisine with ID {$cuisineId} does not exist.",
                    'errors' => [
                        'cuisine_id' => ["Cuisine with ID {$cuisineId} does not exist."]
                    ]
                ], 422);
            }
        } else {
            // Si no se proporciona cuisine_id y es necesario uno predeterminado
            // (descomenta esta sección si quieres que cuisine_id sea obligatorio)
            /*
            $defaultCuisine = Cuisine::first();
            if (!$defaultCuisine) {
                return response()->json([
                    'message' => 'No cuisines available in the system.',
                    'errors' => [
                        'cuisine_id' => ['No cuisines available in the system.']
                    ]
                ], 422);
            }
            $cuisineId = $defaultCuisine->id;
            */
        }
        
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();
        
        // Crear recomendaciones para cada categoría seleccionada
        $recommendations = [];
        foreach ($categoryIds as $categoryId) {
            $recommendation = Recommendation::create([
                'user_id' => $userId,
                'cuisine_id' => $cuisineId, // Puede ser null si no se especificó
                'category_id' => $categoryId,
                'ingredients' => '' // Cadena vacía en lugar de null
            ]);
            
            $recommendations[] = $recommendation;
        }
        
        return response()->json([
            'message' => 'Category recommendations created successfully',
            'data' => $recommendations
        ], 201);
    }
}