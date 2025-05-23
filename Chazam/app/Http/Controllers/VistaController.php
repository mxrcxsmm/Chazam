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
        $user->borde_overlay = $request->input('borde_overlay');
        $user->save();

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
        $user->borde_glow_color = $request->input('glow_color');
        $user->save();
    
        return back()->with('success', 'Color del brillo actualizado.');
    }    
}