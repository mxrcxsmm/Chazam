<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema; // <--- AÑADIDO

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // AÑADIDO PARA COMPATIBILIDAD CON utf8mb4
        Schema::defaultStringLength(191);

        // Compartir variables de usuario con todas las vistas
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $view->with('racha', $user->racha);
                $view->with('puntos', $user->puntos);
                $view->with('username', $user->username);
                $view->with('nombre_completo', $user->nombre_completo);
                $view->with('imagen_perfil', $user->imagen_perfil);
            } else {
                $view->with('racha', 0);
                $view->with('puntos', 0);
                $view->with('username', '');
                $view->with('nombre_completo', '');
                $view->with('imagen_perfil', asset('images/avatar-default.png'));
            }
        });
    }
}
