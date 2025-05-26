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
            ->whereHas('chat', function($query) {
                $query->whereNull('id_reto');
            })
            ->get()
            ->map(function($chatUsuario) use ($userId) {
                $chat = $chatUsuario->chat;
                // Buscar el otro usuario del chat
                $compa = ChatUsuario::where('id_chat', $chat->id_chat)
                    ->where('id_usuario', '!=', $userId)
                    ->with('usuario')
                    ->first();
                $compaUser = $compa ? $compa->usuario : null;
                
                // Construir la URL de la imagen correctamente
                $imgUrl = asset('img/profile_img/avatar-default.png');
                if ($compaUser && $compaUser->img) {
                    $imgPath = basename($compaUser->img);
                    $imgUrl = asset('img/profile_img/' . $imgPath);
                }
                
                return [
                    'id_chat' => $chat->id_chat,
                    'id_usuario' => $compaUser ? $compaUser->id_usuario : null,
                    'nombre' => $compaUser ? $compaUser->nombre : 'Desconocido',
                    'username' => $compaUser ? $compaUser->username : 'Desconocido',
                    'img' => $imgUrl,
                    'last_message' => optional($chat->mensajes()->latest('fecha_envio')->first())->contenido,
                    'last_time' => optional($chat->mensajes()->latest('fecha_envio')->first())->fecha_envio?->format('H:i'),
                    'id_estado' => $compaUser ? $compaUser->id_estado : 2, // 2 = desconectado por defecto
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
                $chatUsuario = $mensaje->chatUsuario;
                $usuario = $chatUsuario ? $chatUsuario->usuario : null;
                
                // Construir la URL de la imagen correctamente
                $imgUrl = asset('img/profile_img/avatar-default.png');
                if ($usuario && $usuario->img) {
                    $imgPath = basename($usuario->img);
                    $imgUrl = asset('img/profile_img/' . $imgPath);
                }
                
                return [
                    'id_mensaje' => $mensaje->id_mensaje,
                    'contenido' => $mensaje->contenido,
                    'fecha_envio' => $mensaje->fecha_envio->format('H:i'),
                    'usuario' => $usuario ? $usuario->username : 'Desconocido',
                    'es_mio' => $chatUsuario && $chatUsuario->id_usuario == Auth::id(),
                    'img' => $imgUrl,
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
        return response()->json(['success' => true, 'message' => 'Mensaje enviado']);
    }

    public function comunidades()
    {
        $chats = Chat::whereNull('id_reto')->get();
        return view('user.comunidades', compact('chats'));
    }
} 