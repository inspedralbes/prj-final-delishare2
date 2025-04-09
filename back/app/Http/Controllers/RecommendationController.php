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
    public function storeUserSelections(Request $request)
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
        
        // Primero intentamos obtener datos del json directo
        if ($request->isJson()) {
            $inputData = $request->json()->all();
        }
        // Si no es json o está vacío, usamos request->all()
        if (empty($inputData)) {
            $inputData = $request->all();
        }
        
        // Si aún está vacío, tratamos de parsearlo manualmente
        if (empty($inputData)) {
            $content = $request->getContent();
            if (!empty($content)) {
                $decoded = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $inputData = $decoded;
                }
            }
        }
        
        // Procesar cuisine_ids
        $cuisineIds = $this->processIdsFromInput($inputData, $request, 'cuisine');
        
        // Procesar category_ids
        $categoryIds = $this->processIdsFromInput($inputData, $request, 'category');
        
        // Verificar que al menos uno de los dos arrays no esté vacío
        if ((empty($cuisineIds) || !is_array($cuisineIds)) && 
            (empty($categoryIds) || !is_array($categoryIds))) {
            return response()->json([
                'message' => 'You must select at least one cuisine or one category.',
                'errors' => [
                    'selections' => ['You must select at least one cuisine or one category.']
                ]
            ], 422);
        }
        
        $errors = [];
        
        // Validar cuisines si se proporcionaron
        if (!empty($cuisineIds)) {
            if (count($cuisineIds) > 5) {
                $errors['cuisine_ids'] = ['You can select a maximum of 5 cuisines.'];
            } else {
                // Verificar que los IDs de cuisine existan
                foreach ($cuisineIds as $cuisineId) {
                    $exists = Cuisine::where('id', $cuisineId)->exists();
                    if (!$exists) {
                        $errors['cuisine_ids'][] = "Cuisine with ID {$cuisineId} does not exist.";
                    }
                }
            }
        }
        
        // Validar categories si se proporcionaron
        if (!empty($categoryIds)) {
            if (count($categoryIds) > 5) {
                $errors['category_ids'] = ['You can select a maximum of 5 categories.'];
            } else {
                // Verificar que los IDs de categoría existan
                foreach ($categoryIds as $categoryId) {
                    $exists = Category::where('id', $categoryId)->exists();
                    if (!$exists) {
                        $errors['category_ids'][] = "Category with ID {$categoryId} does not exist.";
                    }
                }
            }
        }
        
        // Si hay errores, retornarlos
        if (!empty($errors)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        }
        
        // Obtener el ID del usuario autenticado
        $userId = Auth::id();
        
        // Crear recomendaciones individuales para cada cuisine_id
        $recommendations = [];
        
        if (!empty($cuisineIds)) {
            foreach ($cuisineIds as $cuisineId) {
                try {
                    $recommendation = Recommendation::create([
                        'user_id' => $userId,
                        'cuisine_id' => $cuisineId,
                        'category_id' => null, // Intentamos con NULL
                        'ingredients' => ''
                    ]);
                    
                    $recommendations[] = $recommendation;
                } catch (\Exception $e) {
                    // Si falla debido a restricción de NULL, devolvemos un error claro
                    return response()->json([
                        'message' => 'Database error: The column category_id does not allow NULL values. Please modify your database structure.',
                        'error_details' => $e->getMessage()
                    ], 500);
                }
            }
        }
        
        // Crear recomendaciones individuales para cada category_id
        if (!empty($categoryIds)) {
            foreach ($categoryIds as $categoryId) {
                try {
                    $recommendation = Recommendation::create([
                        'user_id' => $userId,
                        'cuisine_id' => null, // Intentamos con NULL
                        'category_id' => $categoryId,
                        'ingredients' => ''
                    ]);
                    
                    $recommendations[] = $recommendation;
                } catch (\Exception $e) {
                    // Si falla debido a restricción de NULL, devolvemos un error claro
                    return response()->json([
                        'message' => 'Database error: The column cuisine_id does not allow NULL values. Please modify your database structure.',
                        'error_details' => $e->getMessage()
                    ], 500);
                }
            }
        }
        
        return response()->json([
            'message' => 'Recommendations created successfully',
            'data' => $recommendations
        ], 201);
    }
    
    /**
     * Procesa y extrae los IDs desde los datos de entrada
     * @param array $inputData Datos de entrada procesados
     * @param Request $request Objeto de solicitud
     * @param string $type Tipo de ID a procesar ('cuisine' o 'category')
     * @return array Array de IDs procesados
     */
    private function processIdsFromInput($inputData, $request, $type)
    {
        $idsKey = "{$type}_ids";
        $idKey = "{$type}_id";
        
        // Verificar si tenemos el array de IDs directamente
        if (isset($inputData[$idsKey]) && is_array($inputData[$idsKey])) {
            // Ya tenemos el array
        }
        // Buscar como ID singular
        elseif (isset($inputData[$idKey])) {
            $inputData[$idsKey] = [$inputData[$idKey]];
        }
        // Buscar como parámetro en la URL
        elseif ($request->query($idsKey)) {
            $idsQuery = $request->query($idsKey);
            if (is_string($idsQuery)) {
                // Intentar parsearlo como array json
                $decoded = json_decode($idsQuery, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $inputData[$idsKey] = $decoded;
                } else {
                    // O como valores separados por coma
                    $inputData[$idsKey] = explode(',', $idsQuery);
                }
            }
        }
        
        // Si no se encontraron IDs, devolver array vacío
        if (!isset($inputData[$idsKey]) || !is_array($inputData[$idsKey])) {
            return [];
        }
        
        // Convertir todos los IDs a enteros
        return array_map(function($id) {
            return (int)$id;
        }, $inputData[$idsKey]);
    }
}