<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function dashboard()
    {
        return view('perfil.dashboard'); // Vista para el usuario normal
    }

    /**
     * Muestra el formulario de personalización del usuario
     */
    /*public function edit()
    {
        $user = Auth::user();
        return view('perfil.personalizacion', compact('user'));
    }*/

    public function edit()
{
    // Usuario de prueba
    $user = (object)[
        'nombre' => 'David',
        'apellido' => 'Gómez',
        'email' => 'david@example.com',
        'fecha_nacimiento' => now()->subYears(20),
        'descripcion' => 'Soy estudiante y me gusta programar.',
        'img' => 'default.png'
    ];

    return view('perfil.personalizacion', compact('user'));
}


    /**
     * Actualiza los datos del usuario
     */
    public function update(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'descripcion' => 'nullable|string|max:1000',
            'img' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        $user->nombre = $request->input('nombre');
        $user->apellido = $request->input('apellido');
        $user->email = $request->input('email');
        $user->fecha_nacimiento = $request->input('fecha_nacimiento');
        $user->descripcion = $request->input('descripcion');
        $user->img = $request->input('img');

        $user->save();

        return redirect()->route('perfil.personalizacion')->with('success', 'Tus datos se han actualizado correctamente.');
    }    
}