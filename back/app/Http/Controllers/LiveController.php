<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Live;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LiveController extends Controller
{
    /**
     * Obtener los lives programados del chef autenticado
     */
    public function misLivesProgramados()
    {
        try {
            // Obtener usuario autenticado
            $user = Auth::user();

            // Verificar que el usuario sea un chef
            if ($user->role !== 'chef') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los chefs pueden ver sus lives programados'
                ], 403);
            }

            // Obtener lives futuros del chef autenticado
            $lives = Live::with(['recipe'])
                        ->where('user_id', $user->id)
                        ->where('dia', '>=', now()->format('Y-m-d'))
                        ->orderBy('dia')
                        ->orderBy('hora')
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $lives
            ]);
        } catch (\Exception $e) {
            // Registrar error en el log
            Log::error('Error al obtener los lives programados del chef: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tus lives programados',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Listar todos los lives públicos (de cualquier chef)
     */
    public function index()
    {
        try {
            // Obtener todos los lives futuros, con datos del chef y la receta
            $lives = Live::with(['chef', 'recipe'])
                        ->where('dia', '>=', now()->format('Y-m-d'))
                        ->orderBy('dia')
                        ->orderBy('hora')
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $lives
            ]);

        } catch (\Exception $e) {
            Log::error('Error al listar lives: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la lista de lives',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Crear un nuevo live (solo chefs autenticados pueden hacerlo)
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Verificar que el usuario sea un chef
            if ($user->role !== 'chef') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los chefs pueden crear lives'
                ], 403);
            }

            // Validar datos de entrada
            $validatedData = $request->validate([
                'recipe_id' => 'required|exists:recipes,id',
                'dia' => 'required|date|after_or_equal:today',
                'hora' => 'required|date_format:H:i'
            ]);

            // Verificar que la receta pertenezca al chef
            $recipe = Recipe::where('id', $validatedData['recipe_id'])
                            ->where('user_id', $user->id)
                            ->first();

            if (!$recipe) {
                return response()->json([
                    'success' => false,
                    'message' => 'La receta no existe o no pertenece a este chef'
                ], 403);
            }

            // Crear el nuevo live
            $live = Live::create([
                'user_id' => $user->id,
                'recipe_id' => $validatedData['recipe_id'],
                'dia' => $validatedData['dia'],
                'hora' => $validatedData['hora']
            ]);

            return response()->json([
                'success' => true,
                'data' => $live
            ], 201);

        } catch (ValidationException $e) {
            // Errores de validación
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear live: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el live',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Mostrar información de un live específico
     */
    public function show($id)
    {
        try {
            // Buscar live con datos del chef y receta
            $live = Live::with(['chef', 'recipe'])->find($id);

            if (!$live) {
                return response()->json([
                    'success' => false,
                    'message' => 'Live no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $live
            ]);

        } catch (\Exception $e) {
            Log::error('Error al mostrar live: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el live',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Actualizar un live existente (solo el chef dueño)
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $live = Live::find($id);

            if (!$live) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Live no encontrado'
                ], 404);
            }

            // Verificar propiedad del live
            if ($live->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para actualizar este live'
                ], 403);
            }

            // Validar campos opcionales
            $validatedData = $request->validate([
                'dia' => 'sometimes|date|after_or_equal:today',
                'hora' => 'sometimes|date_format:H:i'
            ]);

            // Actualizar campos
            $live->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Live actualizado correctamente',
                'data' => $live
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar live: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el live',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Eliminar un live (solo el chef propietario puede hacerlo)
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $live = Live::find($id);

            if (!$live) {
                return response()->json([
                    'success' => false,
                    'message' => 'Live no encontrado'
                ], 404);
            }

            // Verificar propiedad
            if ($live->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este live'
                ], 403);
            }

            // Eliminar live
            $live->delete();

            return response()->json([
                'success' => true,
                'message' => 'Live eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar live: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el live',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener todos los lives futuros del chef autenticado
     */
    public function chefLives()
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'chef') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo los chefs pueden ver sus lives programados'
                ], 403);
            }

            $lives = Live::with(['recipe'])
                        ->where('user_id', $user->id)
                        ->where('dia', '>=', now()->format('Y-m-d'))
                        ->orderBy('dia')
                        ->orderBy('hora')
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $lives
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener los lives programados del chef: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tus lives programados',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener lives futuros de un chef específico por su ID
     */
    public function getUserLives($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Solo devolver lives si el usuario es chef
            if ($user->role !== 'chef') {
                return response()->json([
                    'success' => true,
                    'lives' => []
                ]);
            }

            $lives = Live::with('recipe')
                        ->where('user_id', $user->id)
                        ->where('dia', '>=', now()->format('Y-m-d'))
                        ->orderBy('dia')
                        ->orderBy('hora')
                        ->get();

            return response()->json([
                'success' => true,
                'lives' => $lives
            ]);
        } catch (\Exception $e) {
            Log::error("Error al obtener lives del usuario $userId: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudieron obtener los lives del usuario.'
            ], 500);
        }
    }
}
