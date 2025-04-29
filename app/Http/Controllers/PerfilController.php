<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
    public function edit()
    {
        $user = Auth::user();
        return view('perfil.personalizacion', compact('user'));
    }

    /*public function edit()
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
}*/


    /**
     * Actualiza los datos del usuario
     */
    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:30|unique:users,username,' . Auth::id() . ',id_usuario',
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'descripcion' => 'nullable|string|max:1000',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Limitar cambios de username a 3
        /*if ($request->input('username') !== $user->username) {
            if ($user->username_changes >= 3) {
                return back()->withErrors(['username' => 'Solo puedes cambiar el nombre de usuario 3 veces.']);
            }

            $user->username = $request->input('username');
            $user->username_changes = $user->username_changes + 1;
        }*/

        $user->username = $request->input('username'); //comentar cuando descomente lo del limite
        $user->nombre = $request->input('nombre');
        $user->apellido = $request->input('apellido');
        $user->fecha_nacimiento = $request->input('fecha_nacimiento');
        $user->descripcion = $request->input('descripcion');

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $realPath = $image->getRealPath();
            $hash = md5_file($realPath);
            $extension = $image->getClientOriginalExtension();
            $filename = $user->username . '_' . $hash . '.' . $extension;

            $path = 'perfiles/' . $filename;
            if (!Storage::disk('public')->exists($path)) {
                $image->storeAs('perfiles', $filename, 'public');
            }

            $user->img = 'storage/' . $path;
        }

        $user->save();

        return redirect()->route('perfil.personalizacion')->with('success', 'Tus datos se han actualizado correctamente.');
    } //esto es raro
}