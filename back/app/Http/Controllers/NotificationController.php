<?php

namespace App\Http\Controllers;
use App\Models\Recipe;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();
    
        $notifications = Notification::with([
            'triggeredBy:id,name,img',
            'recipe:id,title,image'
        ])
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->triggeredBy->name . ' ' . $notification->message,
                'read' => $notification->read,
                'created_at' => $notification->created_at,
                'user_image' => $notification->triggeredBy->profile_image ?? null,
                'recipe_id' => $notification->recipe_id,
                'recipe_title' => $notification->recipe->title ?? null,
                'recipe_image' => $notification->recipe->image ?? null // <- 🔥 AÑADIDO AQUÍ
            ];
        });
    
        return response()->json($notifications);
    }
    
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read' => true]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function createNotification(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'recipe_id' => 'required|exists:recipes,id',
            'type' => 'required|in:like,comment,save',
            'comment_text' => 'nullable|string|max:1000'
        ]);
    
        // Verificar si ya existe una notificación igual reciente
        $existing = Notification::where('user_id', $data['user_id'])
            ->where('triggered_by', $request->user()->id)
            ->where('recipe_id', $data['recipe_id'])
            ->where('type', $data['type'])
            ->where('created_at', '>', now()->subMinutes(5)) // Últimos 5 minutos
            ->first();
    
        if ($existing) {
            return response()->json(['message' => 'Notification already exists'], 200);
        }
        $triggeredBy = $request->user()->id;
        $recipe = Recipe::find($data['recipe_id']);
    
        // Mensaje base por tipo
        $messages = [
            'like' => 'le ha dado like a tu receta "' . $recipe->title . '"',
            'comment' => 'ha comentado tu receta "' . $recipe->title . '"',
            'save' => 'ha guardado tu receta "' . $recipe->title . '"'
        ];
    
        // Si es comentario, añade el texto del comentario al mensaje
        $message = $messages[$data['type']];
        if ($data['type'] === 'comment' && !empty($data['comment_text'])) {
            $message .= ': "' . $data['comment_text'] . '"';
        }
    
        $notification = Notification::create([
            'user_id' => $data['user_id'],
            'triggered_by' => $triggeredBy,
            'recipe_id' => $data['recipe_id'],
            'type' => $data['type'],
            'message' => $message
        ]);
    
        return response()->json($notification, 201);
    }
    
}