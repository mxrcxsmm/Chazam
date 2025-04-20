<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Asegurarse de que la imagen de perfil tenga la ruta correcta
        if ($user->imagen_perfil) {
            // Usar asset() para apuntar a la ubicación correcta
            $imagen_perfil = asset('IMG/profile_img/' . $user->imagen_perfil);
        } else {
            $imagen_perfil = null; // O una imagen por defecto
        }
        
        return view('user.dashboard', [
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $imagen_perfil,
        ]); // Vista para el usuario normal
    }
}
