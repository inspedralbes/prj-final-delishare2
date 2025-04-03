<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bio' => $request->bio ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->update(['personal_access_token' => $token]);
        return response()->json([
            'user' => [
                'id_user' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $user->bio,
            ],
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->update(['personal_access_token' => $token]);

        return response()->json([
            'user' => [
                'id_user' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $user->bio,
            ],
            'token' => $token,
        ], 201);
    }

    public function updatePerfil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:users,name,' . $request->user()->id,
            'email' => 'sometimes|email|max:255|unique:users,email,' . $request->user()->id,
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $user->update($request->only(['name', 'email', 'bio']));

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'user' => $user,
        ]);
    }
        
    public function updateProfilePicture(Request $request)
    {
        $user = $request->user(); 

        $user->img = $request->img;
        $user->save();

    return response()->json(['message' => 'Imagen de perfil actualizada exitosamente']);
    }

    public function logout(Request $request)
{
    $user = $request->user();  // Obtener al usuario autenticado

    // Revocar el token actual
    $user->currentAccessToken()->delete();

    // Borra el token de acceso en el usuario
    $user->update(['personal_access_token' => null]);

    return response()->json(['message' => 'Logout exitoso'], 200);
}

public function cambiarContra(Request $request)
{
    $user = $request->user();

    // Validación de los campos
    $request->validate([
        'contrasena_actual' => 'required',
        'nueva_contrasena' => 'required|min:8',
    ]);

    // Verificar que la contraseña actual coincida
    if (!Hash::check($request->contrasena_actual, $user->password)) {
        return response()->json(['message' => 'Contraseña actual incorrecta'], 403);
    }

    // Cambiar la contraseña
    $user->password = Hash::make($request->nueva_contrasena);
    $user->save();

    return response()->json(['message' => 'Contraseña cambiada exitosamente']);
    }

}