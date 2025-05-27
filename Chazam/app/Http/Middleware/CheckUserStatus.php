<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Verificar si el usuario está baneado (estado 3) o permaban (estado 4)
            if ($user->id_estado == 3 || $user->id_estado == 4) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Tu cuenta ha sido suspendida. Por favor, contacta con el administrador.');
            }
        }

        return $next($request);
    }
} 