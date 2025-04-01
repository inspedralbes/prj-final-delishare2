<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show($id)
    {
        return User::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'biografia' => 'nullable|string|max:1000',
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function updateBiography(Request $request)
    {
        $request->validate([
            'biografia' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $user->biografia = $request->biografia;
        $user->save();

        return response()->json([
            'message' => 'Biografía actualizada correctamente',
            'biografia' => $user->biografia,
        ]);
    }

    public function deleteBiography()
    {
        $user = auth()->user();
        $user->biografia = null;
        $user->save();

        return response()->json(['message' => 'Biografía eliminada correctamente']);
    }

    public function getBiography()
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    
        return response()->json([
            'biografia' => $user->biografia ?? 'No hay biografía disponible',
        ]);
    }
    
}
