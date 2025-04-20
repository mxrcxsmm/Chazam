<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

        return response()->json([
            'mensaje' => 'Estado actualizado correctamente',
            'estado' => $estadoId
        ]);
    }
} 