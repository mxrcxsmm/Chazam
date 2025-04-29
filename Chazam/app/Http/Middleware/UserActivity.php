<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check())
        {
            $user = Auth::user();
            $expiresAt = Carbon::now()->addMinutes(1);
            
            // Guardar en caché que el usuario está online
            Cache::put('user-is-online-' . $user->id_usuario, true, $expiresAt);
            
            // Determinar el estado basado en la ruta actual
            $currentPath = $request->path();
            Log::info('Ruta actual: ' . $currentPath);
            
            // Verificar si estamos en las páginas de retos
            if (str_contains($currentPath, 'retos/reto') || str_contains($currentPath, 'retos/guide')) {
                $estado = 5; // Disponible
                Log::info('Usuario en retos, cambiando a estado 5');
            } else {
                $estado = 1; // Activo
                Log::info('Usuario no en retos, cambiando a estado 1');
            }
            
            // Actualizar el estado del usuario
            $updated = User::where('id_usuario', $user->id_usuario)->update(['id_estado' => $estado]);
            Log::info('Actualización de estado: ' . ($updated ? 'éxito' : 'fallo') . ' para usuario ' . $user->id_usuario . ' a estado ' . $estado);
        }
        return $next($request);
    }
}
