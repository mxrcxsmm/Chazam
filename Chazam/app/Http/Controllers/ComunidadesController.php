<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComunidadesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener comunidades creadas por el usuario
        $comunidadesCreadas = Chat::whereNotNull('creator')
            ->where('creator', $user->id)
            ->withCount('chatUsuarios')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
            
        // Obtener comunidades públicas
        $comunidadesPublicas = Chat::whereNotNull('creator')
            ->where('creator', '!=', $user->id)
            ->where('tipocomunidad', 'publica')
            ->withCount('chatUsuarios')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
            
        // Obtener comunidades privadas
        $comunidadesPrivadas = Chat::whereNotNull('creator')
            ->where('creator', '!=', $user->id)
            ->where('tipocomunidad', 'privada')
            ->withCount('chatUsuarios')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
        
        return view('comunidades.comunidades', [
            'comunidadesCreadas' => $comunidadesCreadas,
            'comunidadesPublicas' => $comunidadesPublicas,
            'comunidadesPrivadas' => $comunidadesPrivadas,
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->img ? 'img/profile_img/' . $user->img : null,
        ]);
    }

    public function join($id)
    {
        $comunidad = Chat::findOrFail($id);
        // Aquí puedes añadir la lógica para unir al usuario a la comunidad
        return response()->json(['success' => true]);
    }
} 