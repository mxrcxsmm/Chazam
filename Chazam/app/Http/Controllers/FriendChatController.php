<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatUsuario;
use App\Models\Mensaje;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class FriendChatController extends Controller
{
    public function index()
    {
        return view('user.friendchat');
    }


    public function momentms()
    {
        return view('user.momentms');
    }
  
    public function getUserChats()
    {
        $userId = Auth::id();
        $chats = ChatUsuario::with(['chat', 'usuario'])
            ->where('id_usuario', $userId)
            ->get()
            ->map(function($chatUsuario) {
                $chat = $chatUsuario->chat;
                return [
                    'id_chat' => $chat->id_chat,
                    'nombre' => $chat->nombre,
                    'username' => $chatUsuario->usuario->username,
                    'img' => $chat->img,
                    'last_message' => optional($chat->mensajes()->latest('fecha_envio')->first())->contenido,
                    'last_time' => optional($chat->mensajes()->latest('fecha_envio')->first())->fecha_envio?->format('H:i'),
                ];
            });
        return response()->json($chats);
    }

    public function getChatMessages($chatId)
    {
        $userId = Auth::id();
        $chatUsuario = ChatUsuario::where('id_chat', $chatId)
            ->where('id_usuario', $userId)
            ->first();
        if (!$chatUsuario) {
            return response()->json(['error' => 'No tienes acceso a este chat'], 403);
        }
        $mensajes = Mensaje::whereHas('chatUsuario', function($q) use ($chatId) {
                $q->where('id_chat', $chatId);
            })
            ->with(['chatUsuario.usuario'])
            ->orderBy('fecha_envio', 'asc')
            ->get()
            ->map(function($mensaje) {
                return [
                    'id_mensaje' => $mensaje->id_mensaje,
                    'contenido' => $mensaje->contenido,
                    'fecha_envio' => $mensaje->fecha_envio->format('H:i'),
                    'usuario' => $mensaje->chatUsuario->usuario->username,
                    'es_mio' => $mensaje->chatUsuario->id_usuario == Auth::id(),
                ];
            });
        return response()->json($mensajes);
    }

    public function sendMessage(Request $request, $chatId)
    {
        $userId = Auth::id();
        $chatUsuario = ChatUsuario::where('id_chat', $chatId)
            ->where('id_usuario', $userId)
            ->first();
        if (!$chatUsuario) {
            return response()->json(['error' => 'No tienes acceso a este chat'], 403);
        }
        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);
        $mensaje = Mensaje::create([
            'id_chat_usuario' => $chatUsuario->id_chat_usuario,
            'contenido' => $request->contenido,
            'fecha_envio' => now(),
        ]);
        return response()->json(['success' => true, 'mensaje' => $mensaje]);
    }
} 