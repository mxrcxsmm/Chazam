<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VistaController extends Controller
{
    /**
     * Muestra la vista personalizada con los datos del usuario.
     */
    public function show()
    {
        $user = Auth::user();

        return view('perfil.vista', compact('user'));
    }

    public function cambiarMarco(Request $request)
    {
        $user = Auth::user();

        // Si no existe la personalización, la creamos
        $personalizacion = $user->personalizacion ?? new \App\Models\Personalizacion(['id_usuario' => $user->id_usuario]);

        $personalizacion->marco = $request->input('borde_overlay') ?? 'default.svg';
        $personalizacion->rotacion = $request->input('rotativo_temp') ?? 0;

        $personalizacion->save();

        return redirect()->back()->with('success', 'Marco actualizado correctamente.');
    }

    /*public function cambiarMarco(Request $request)
    {
        $user = Auth::user();
        $user->borde_overlay = $request->input('borde_overlay');
        $user->marco_rotativo = $request->input('rotativo') ?? 0;
        $user->save();

        return redirect()->back()->with('success', 'Marco actualizado correctamente.');
    }*/

    public function cambiarBrillo(Request $request)
    {
        $user = Auth::user();

        $personalizacion = $user->personalizacion ?? new \App\Models\Personalizacion(['id_usuario' => $user->id_usuario]);
        $personalizacion->brillo = $request->input('glow_color');

        $personalizacion->save();

        return back()->with('success', 'Color del brillo actualizado.');
    } 
}