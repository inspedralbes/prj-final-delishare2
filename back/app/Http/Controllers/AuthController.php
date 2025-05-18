<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // Registro de nuevos usuarios
    public function register(Request $request)
    {
        // Validación de campos requeridos y únicos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'bio' => 'nullable|string|max:500',
            'role' => 'in:user,chef,admin',
        ]);

        // Enviar errores si falla la validación
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear el usuario con la contraseña encriptada
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'bio' => $request->bio ?? null,
            'role' => $request->role ?? 'user', 
        ]);

        // Crear token de acceso y guardarlo
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->update(['personal_access_token' => $token]);
        
        // Enviar correo de bienvenida
        Mail::send('emails.registro_exitoso', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Registro Exitoso en Delishare');
        });

        // Devolver respuesta con usuario y token
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

    // Login de usuarios existentes
    public function login(Request $request)
    {
        $user = User::where('name', $request->name)->first();

        // Validar existencia y contraseña
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Generar nuevo token
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->update(['personal_access_token' => $token]);

        // Devolver usuario y token
        return response()->json([
            'user' => [
                'id_user' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $user->bio,
                'role' => $user->role,
            ],
            'token' => $token,
        ], 201);
    }

    // Obtener información completa del usuario, incluyendo recetas y folders
    public function getUserInfo($userId)
    {
        $user = User::with(['recipes', 'folders.recipes'])->find($userId);
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Devolver datos completos del usuario
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'img' => $user->img,
                'bio' => $user->bio,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'recipes' => $user->recipes,
            'folders' => $user->folders->map(function($folder) {
                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'recipes_count' => $folder->recipes->count(),
                    'recipes' => $folder->recipes
                ];
            })
        ]);
    }

    // Actualizar nombre, correo o bio del perfil
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

    // Actualizar imagen de perfil
    public function updateProfilePicture(Request $request)
    {
        $user = $request->user(); 
        $user->img = $request->img;
        $user->save();

        return response()->json(['message' => 'Imagen de perfil actualizada exitosamente']);
    }

    // Cerrar sesión eliminando el token actual
    public function logout(Request $request)
    {
        $user = $request->user();  
        $user->currentAccessToken()->delete();
        $user->update(['personal_access_token' => null]);

        return response()->json(['message' => 'Logout exitoso'], 200);
    }

    // Cambiar la contraseña del usuario
    public function cambiarContra(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'contrasena_actual' => 'required',
            'nueva_contrasena' => 'required|min:8',
        ]);

        if (!Hash::check($request->contrasena_actual, $user->password)) {
            return response()->json(['message' => 'Contraseña actual incorrecta'], 403);
        }

        $user->password = Hash::make($request->nueva_contrasena);
        $user->save();

        return response()->json(['message' => 'Contraseña cambiada exitosamente']);
    }

    // Cambiar el rol de un usuario (admin, user o chef)
    public function cambiarRol(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,chef,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'Rol actualizado correctamente',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }
}
