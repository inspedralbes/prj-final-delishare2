<?php

namespace App\Http\Controllers;

use App\Models\Cuisine;
use Illuminate\Http\Request;

class CuisineController extends Controller
{
    // Obtener todas las cocinas disponibles
    public function index()
    {
        return Cuisine::all();
    }

    // Mostrar una cocina específica por su ID
    public function show($id)
    {
        // Uso findOrFail para lanzar un error 404 automáticamente si no se encuentra
        return Cuisine::findOrFail($id);
    }

    // Crear una nueva cocina
    public function store(Request $request)
    {
        // Creo la cocina directamente con los datos del request
        // En producción podría añadir validación
        $cuisine = Cuisine::create($request->all());
        return $cuisine;
    }

    // Actualizar una cocina existente
    public function update(Request $request, $id)
    {
        // Busco la cocina, si no existe lanza error automáticamente
        $cuisine = Cuisine::findOrFail($id);

        // Actualizo los campos con los datos del request
        $cuisine->update($request->all());

        return $cuisine;
    }

    // Eliminar una cocina
    public function destroy($id)
    {
        // Encuentro la cocina por su ID
        $cuisine = Cuisine::findOrFail($id);

        // Elimino la entrada
        $cuisine->delete();

        // Devuelvo una respuesta con mensaje de éxito
        return response()->json(['message' => 'Cuisine deleted successfully']);
    }
}
