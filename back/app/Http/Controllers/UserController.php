<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    // Devuelve todos los usuarios
    public function index()
    {
        return User::all();
    }

    // Devuelve un usuario específico por id
    public function show($id)
    {
        return User::findOrFail($id);
    }

    // Actualiza un usuario con los datos recibidos
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return $user;
    }

    // Valida y crea un nuevo usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        
        $validated['password'] = bcrypt($validated['password']);
        
        return User::create($validated);
    }

    // Elimina un usuario por id
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    // Devuelve el usuario autenticado
    public function getAuthenticatedUser(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        return response()->json([
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    // Envía un email de verificación
    public function sendVerificationEmail(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $email = $user->email;
        $username = $user->name;
        $messageContent = $request->input('message');

        Mail::to('midelishare@gmail.com')->send(new VerificationMail($email, $username, $messageContent));

        return response()->json(['message' => 'Correo enviado correctamente']);
    }
}
