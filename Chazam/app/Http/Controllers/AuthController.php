<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            // Redirigir según el rol del usuario
            if ($user->rol->nom_rol === 'Administrador') {
                return redirect()->route('admin.usuarios.index'); // Página de administrador
            } else {
                return redirect()->route('user.dashboard'); // Página de usuario normal
            }
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    // Método para cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión.');
    }
}
