<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Método para manejar el inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Actualizar estado a Activo (1) al iniciar sesión
            User::where('id_usuario', $user->id_usuario)->update(['id_estado' => 1]);

            // Redirigir según el rol del usuario
            if ($user->rol->nom_rol === 'Administrador') {
                return redirect()->route('admin.usuarios.index'); // Página de administrador
            } else {
                return redirect()->route('retos.guide'); // Página de usuario normal
            }
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    // Método para cerrar sesión
    public function logout(Request $request)
    {
        // Actualizar estado a Inactivo (2) antes de cerrar sesión
        if (Auth::check()) {
            $user = Auth::user();
            User::where('id_usuario', $user->id_usuario)->update(['id_estado' => 2]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión.');
    }
}
