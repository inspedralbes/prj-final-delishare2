<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Mail;

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
        $user->update($request->all());
        return $user;
    }
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
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

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

    // Enviar correo a midelishare@gmail.com
    Mail::to('midelishare@gmail.com')->send(new VerificationMail($email, $username, $messageContent));

    return response()->json(['message' => 'Correo enviado correctamente']);
}

}
