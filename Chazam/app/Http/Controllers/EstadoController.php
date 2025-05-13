<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class EstadoController extends Controller
{
    public function actualizarEstado(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $user = Auth::user();
        $estadoId = $request->input('estado');
        
        // Validar que el estado sea uno de los permitidos
        $estadosPermitidos = [1, 2, 3, 4, 5]; 
        if (!in_array($estadoId, $estadosPermitidos)) {
            return response()->json(['error' => 'Estado no válido'], 400);
        }

        // Actualizar el estado del usuario
        User::where('id_usuario', $user->id_usuario)->update(['id_estado' => $estadoId]);

        // Si el usuario cambia de estado 5 a otro, verificar y eliminar chats
        if ($estadoId != 5) {
            $retoController = new RetoController();
            $retoController->verificarEstadoChats();
        }

        // Actualizar el caché de usuarios en línea
        $this->actualizarUsuariosEnLinea($user->id_usuario, $estadoId);

        return response()->json([
            'mensaje' => 'Estado actualizado correctamente',
            'estado' => $estadoId
        ]);
    }

    private function actualizarUsuariosEnLinea($userId, $estado)
    {
        $usuariosEnLinea = Cache::get('usuarios_en_linea', []);
        
        if ($estado == 1) {
            $usuariosEnLinea[$userId] = now()->timestamp;
        } else {
            unset($usuariosEnLinea[$userId]);
        }

        Cache::put('usuarios_en_linea', $usuariosEnLinea, now()->addMinutes(5));
    }

    public function obtenerUsuariosEnLinea()
    {
        $usuariosEnLinea = Cache::get('usuarios_en_linea', []);
        
        // Limpiar usuarios que no han actualizado su estado en los últimos 5 minutos
        $usuariosEnLinea = array_filter($usuariosEnLinea, function($timestamp) {
            return now()->timestamp - $timestamp < 300; // 5 minutos
        });

        $usuarios = User::whereIn('id_usuario', array_keys($usuariosEnLinea))
            ->where('id_estado', 1)
            ->get();

        return response()->json($usuarios);
    }
} 