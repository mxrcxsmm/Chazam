<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (strlen($query) < 3) {
            return response()->json([]);
        }

        $users = User::where('username', 'LIKE', "%{$query}%")
            ->where('id_usuario', '!=', Auth::id())
            ->whereIn('id_rol', [2, 3, 4])
            ->whereNotIn('id_usuario', function($q) {
                $q->select('id_receptor')
                    ->from('solicitudes')
                    ->where('id_emisor', Auth::id())
                    ->where('estado', 'aceptada');
            })
            ->whereNotIn('id_usuario', function($q) {
                $q->select('id_emisor')
                    ->from('solicitudes')
                    ->where('id_receptor', Auth::id())
                    ->where('estado', 'aceptada');
            })
            ->select('id_usuario', 'username', 'nombre', 'apellido', 'img')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id_usuario' => $user->id_usuario,
                    'username' => $user->username,
                    'nombre_completo' => $user->nombre . ' ' . $user->apellido,
                    'img' => $user->img ? basename($user->img) : null
                ];
            });

        return response()->json($users);
    }
} 