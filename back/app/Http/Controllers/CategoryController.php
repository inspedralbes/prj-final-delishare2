<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Obtener todas las categorías
    public function index()
    {
        return Category::all();
    }

    // Mostrar una categoría específica por su ID
    public function show($id)
    {
        return Category::findOrFail($id);
    }

    // Crear una nueva categoría
    public function store(Request $request)
    {
        // Aquí simplemente estoy creando la categoría con todos los datos del request.
        // Considerar validación si se vuelve más complejo.
        $category = Category::create($request->all());
        return $category;
    }

    // Actualizar una categoría existente
    public function update(Request $request, $id)
    {
        // Primero busco la categoría. Si no existe, lanzará excepción automáticamente.
        $category = Category::findOrFail($id);

        // Actualizo con los nuevos datos
        $category->update($request->all());

        return $category;
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        // Encuentro la categoría por ID
        $category = Category::findOrFail($id);

        // La elimino del sistema
        $category->delete();

        // Devuelvo respuesta de confirmación
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
