<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

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
            'fecha_nacimiento' => 'nullable|date|before_or_equal:' . Carbon::now()->subYears(13)->format('Y-m-d'),
            'descripcion' => 'nullable|string|max:1000',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'img' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,heic|max:5120',
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

        // 1) Si el usuario ha marcado “quitar foto”
        if ($request->input('remove_img') === '1') {
            // Borra la anterior si existe
            if ($user->img && File::exists(public_path($user->img))) {
                File::delete(public_path($user->img));
            }
            $user->img = null;

        // 2) Si viene un archivo nuevo
        } elseif ($request->hasFile('img')) {
            // Borra la anterior si existe
            if ($user->img && File::exists(public_path($user->img))) {
                File::delete(public_path($user->img));
            }

            $file      = $request->file('img');
            $extension = $file->getClientOriginalExtension();
            $hash      = md5_file($file->getRealPath());
            $filename  = $user->username . '_' . $hash . '.' . $extension;

            $destination = public_path('img/profile_img');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $user->img = 'img/profile_img/'.$filename;
        }

        $user->save();

        // Si es una petición AJAX, devolvemos JSON
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Tus datos se han actualizado correctamente.',
                'img' => $user->img,
                'username' => $user->username,
                'nombre' => $user->nombre,
                'apellido' => $user->apellido,
                'nombre_completo' => $user->nombre . ' ' . $user->apellido,
                'fecha_nacimiento' => $user->fecha_nacimiento,
                'descripcion' => $user->descripcion,
            ]);
        }
    
        // Redirección normal
        return redirect()->route('perfil.personalizacion')->with('success', 'Tus datos se han actualizado correctamente.');
    }
}