<?php

namespace App\Http\Controllers;

use App\Models\Cuisine;

use Illuminate\Http\Request;

class CuisineController extends Controller
{
    public function index()
    {
        return Cuisine::all();
    }

    public function show($id)
    {
        return Cuisine::findOrFail($id);
    }

    public function store(Request $request)
    {
        $cuisine = Cuisine::create($request->all());
        return $cuisine;
    }

    public function update(Request $request, $id)
    {
        $cuisine = Cuisine::findOrFail($id);
        $cuisine->update($request->all());
        return $cuisine;
    }

    public function destroy($id)
    {
        $cuisine = Cuisine::findOrFail($id);
        $cuisine->delete();
        return response()->json(['message' => 'Cuisine deleted successfully']);
    }
}