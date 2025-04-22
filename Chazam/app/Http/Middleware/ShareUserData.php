<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ShareUserData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            View::share('racha', $user->racha);
            View::share('puntos', $user->puntos);
            View::share('username', $user->username);
            View::share('nombre_completo', $user->nombre_completo);
            View::share('imagen_perfil', $user->imagen_perfil);
        } else {
            View::share('racha', 0);
            View::share('puntos', 0);
            View::share('username', '');
            View::share('nombre_completo', '');
            View::share('imagen_perfil', asset('images/avatar-default.png'));
        }

        return $next($request);
    }
} 