<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;         // Para acceder al usuario autenticado
use Illuminate\Http\Request;                  // Para manejar solicitudes HTTP
use App\Models\Personalizacion;               // Modelo para la tabla de personalización

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
    public function actualizar(Request $request)
    {
        $user = Auth::user(); // Obtiene al usuario autenticado
    
        // Intenta obtener la personalización del usuario. Si no tiene, crea una nueva con su ID.
        $p = $user->personalizacion ?? new Personalizacion(['id_usuario' => $user->id_usuario]);
    
        // Establece los nuevos valores que vienen del formulario (ya sea clásico o vía fetch)
        $p->marco    = $request->input('marco', 'default.svg'); // Usa 'default.svg' si no viene valor
        $p->rotacion = filter_var($request->input('rotacion'), FILTER_VALIDATE_BOOLEAN); // Asegura valor booleano
        $p->brillo   = $request->input('brillo'); // Campo opcional, puede ser null
        $p->sidebar  = $request->input('sidebar', '#4B0082'); // Usa color por defecto si no se recibe
    
        $p->save(); // Guarda la personalización (crea o actualiza)
    
        // Si es una petición AJAX (fetch), responde con JSON
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Personalización actualizada.']);
        }
    
        // Si es una petición normal, redirige con mensaje de éxito
        return back()->with('success', 'Personalización actualizada.');
    }    

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
}