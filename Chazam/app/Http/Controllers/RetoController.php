<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reto;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RetoController extends Controller
{
    /**
     * Muestra el reto actual que es el mismo para todos los usuarios.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        // Obtener el reto del día desde la caché o seleccionar uno nuevo
        $reto = $this->getRetoDelDia();
        
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        return view('Retos.reto', [
            'reto' => $reto,
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->imagen_perfil,
        ]);
    }
    
    /**
     * Obtiene el reto del día, que es el mismo para todos los usuarios.
     * El reto cambia automáticamente a las 00:00 de cada día.
     * Los retos se muestran en orden aleatorio sin repetir hasta que se hayan mostrado todos.
     *
     * @return \App\Models\Reto
     */
    private function getRetoDelDia()
    {
        $cacheKey = 'reto_del_dia_' . now()->format('Y-m-d');
        
        // Intentar obtener el reto del día desde la caché
        $reto = Cache::get($cacheKey);
        
        // Si no existe un reto del día, seleccionar uno nuevo
        if (!$reto) {
            // Obtener la lista de retos pendientes
            $retosPendientes = Cache::get('retos_pendientes', []);
            
            // Si no hay retos pendientes, crear una nueva lista aleatoria
            if (empty($retosPendientes)) {
                // Obtener todos los IDs de retos
                $todosRetos = Reto::pluck('id_reto')->toArray();
                
                // Mezclar aleatoriamente los IDs
                shuffle($todosRetos);
                
                // Guardar la lista mezclada
                $retosPendientes = $todosRetos;
                Cache::put('retos_pendientes', $retosPendientes, now()->addDays(30));
            }
            
            // Tomar el primer reto de la lista
            $idReto = array_shift($retosPendientes);
            
            // Obtener el reto completo
            $reto = Reto::find($idReto);
            
            // Actualizar la lista de retos pendientes
            Cache::put('retos_pendientes', $retosPendientes, now()->addDays(30));
            
            // Guardar el reto en la caché hasta las 00:00 del día siguiente
            Cache::put($cacheKey, $reto, Carbon::tomorrow());
        }
        
        return $reto;
    }
}
