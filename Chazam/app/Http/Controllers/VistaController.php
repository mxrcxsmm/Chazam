<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;         // Para acceder al usuario autenticado
use Illuminate\Http\Request;                  // Para manejar solicitudes HTTP
use App\Models\Personalizacion;               // Modelo para la tabla de personalización
use App\Models\Producto;
use App\Models\User;
use App\Models\Pago;

class VistaController extends Controller
{
    /**
     * Muestra la vista personalizada con los datos del usuario.
     */
    public function show()
    {
        $user = Auth::user(); // Obtiene al usuario actualmente autenticado

        // Intenta obtener la personalización asociada al usuario.
        // Si no existe, se crea una instancia con valores por defecto (no se guarda en DB aún).
        $p = $user->personalizacion ?? new Personalizacion([
            'marco'    => 'default.svg',    // Valor por defecto para marco
            'rotacion' => false,            // Valor por defecto para rotación
            'brillo'   => null,             // Brillo opcional, puede ser null
            'sidebar'  => '#4B0082',        // Color por defecto del sidebar
        ]);

        // Devuelve la vista `perfil.vista` con los datos del usuario y su personalización
        return view('perfil.vista', [
            'user'            => $user,
            'personalizacion' => $p,
        ]);
    }

    /**
     * Guarda los cambios de personalización realizados por el usuario.
     */
    /*public function actualizar(Request $request)
    {
        $user = Auth::user(); // Obtiene al usuario autenticado
    
        // Intenta obtener la personalización del usuario. Si no tiene, crea una nueva con su ID.
        $p = $user->personalizacion ?? new Personalizacion(['id_usuario' => $user->id_usuario]);
    
        // Establece los nuevos valores que vienen del formulario (ya sea clásico o vía fetch)
        $p->marco    = $request->input('marco', 'default.svg'); // Usa 'default.svg' si no viene valor
        $p->rotacion = filter_var($request->input('rotacion'), FILTER_VALIDATE_BOOLEAN); // Asegura valor booleano
        $p->sidebar  = $request->input('sidebar', '#4B0082'); // Usa color por defecto si no se recibe
    
        if ($request->has('brillo')) {
            $p->brillo = $request->input('brillo');
        }

        $p->save(); // Guarda la personalización (crea o actualiza)
    
        // Si es una petición AJAX (fetch), responde con JSON
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Personalización actualizada.']);
        }
    
        // Si es una petición normal, redirige con mensaje de éxito
        return back()->with('success', 'Personalización actualizada.');
    }*/

    public function restablecer(Request $request)
    {
        $user = Auth::user();

        $p = $user->personalizacion ?? new Personalizacion(['id_usuario' => $user->id_usuario]);

        $p->marco = 'default.svg';
        $p->rotacion = false;
        $p->brillo = null;
        $p->sidebar = '#4B0082';
        $p->save();

        return response()->json(['message' => 'Personalización restablecida.']);
    }

    public function actualizar(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Usuarios con rol 3 o 4 son premium
        $isPremium = in_array($user->id_rol, [3, 4]);

        // Obtengo o inicializo la personalización
        $p = $user->personalizacion
        ?? new Personalizacion(['id_usuario' => $user->id]);

        $precioTotal = 0;
        $pagos = [];

        // 1) Cambio de marco
        if ($request->has('marco')) {
            $nuevoMarco = $request->input('marco');
            if ($nuevoMarco !== $p->marco && ! $isPremium) {
                $producto = Producto::where('descripcion', $nuevoMarco)->first();
                $pts = $producto->puntos ?? 0;
                $precioTotal += $pts;
                $pagos[] = [
                    'id_producto' => $producto->id_producto,
                    'cantidad'    => $pts,
                ];
            }
            $p->marco = $nuevoMarco;
        }

        // 2) Cambio de rotación
        if ($request->has('rotacion')) {
            $nuevaRotacion = filter_var($request->input('rotacion'), FILTER_VALIDATE_BOOLEAN);
            if ($nuevaRotacion !== $p->rotacion && ! $isPremium) {
                $titulo = $nuevaRotacion ? 'Marco Rotatorio' : 'Marco Estático';
                $producto = Producto::where('titulo', $titulo)->first();
                $pts = $producto->puntos ?? 0;
                $precioTotal += $pts;
                $pagos[] = [
                    'id_producto' => $producto->id_producto,
                    'cantidad'    => $pts,
                ];
            }
            $p->rotacion = $nuevaRotacion;
        }

        // 3) Cambio de color de sidebar
        if ($request->has('sidebar')) {
            $nuevoSidebar = $request->input('sidebar');
            if ($nuevoSidebar !== $p->sidebar && ! $isPremium) {
                $producto = Producto::where('titulo', 'Color de Sidebar')->first();
                $pts = $producto->puntos ?? 0;
                $precioTotal += $pts;
                $pagos[] = [
                    'id_producto' => $producto->id_producto,
                    'cantidad'    => $pts,
                ];
            }
            $p->sidebar = $nuevoSidebar;
        }

        // 4) Cambio de brillo
        if ($request->has('brillo')) {
            $nuevoBrillo = $request->input('brillo');
            if ($nuevoBrillo !== $p->brillo && ! $isPremium) {
                $producto = Producto::where('titulo', 'Brillo de Marco')->first();
                $pts = $producto->puntos ?? 0;
                $precioTotal += $pts;
                $pagos[] = [
                    'id_producto' => $producto->id_producto,
                    'cantidad'    => $pts,
                ];
            }
            $p->brillo = $nuevoBrillo;
        }

        // Si no es premium y hay que pagar
        if (! $isPremium && $precioTotal > 0) {
            // Verificar saldo
            if ($user->puntos < $precioTotal) {
                return response()->json([
                    'message' => 'No tienes suficientes puntos para esta personalización.',
                ], 403);
            }

            // Descontar puntos al usuario
            $user->gastarPuntos($precioTotal);

            // Registrar cada pago
            foreach ($pagos as $info) {
                Pago::create([
                    'id_comprador' => Auth::id(),
                    'id_producto'  => $info['id_producto'],
                    'cantidad'     => $info['cantidad'],
                    'fecha_pago'   => now(),
                ]);
            }
        }

        // Guardar la personalización
        $p->save();

        return response()->json([
            'message'          => 'Personalización actualizada.',
            'puntos_restantes' => $user->puntos,
        ]);
    }
}
