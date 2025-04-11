<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Nacionalidad;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $nacionalidades = Nacionalidad::all();
        return view('login', compact('nacionalidades'));
    }

    // Método para manejar el inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Redirigir según el rol del usuario
            if ($user->rol->nom_rol === 'Administrador') {
                return redirect()->route('admin.usuarios.index'); // Página de administrador
            } else {
                return redirect()->route('user.dashboard'); // Página de usuario normal
            }
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:10',
            'nombre' => ['required', 'max:100', 'regex:/^[\p{L}\s\-]+$/u'], // Permite letras, espacios, guiones, acentos y Ñ
            'apellido' => ['required', 'max:100', 'regex:/^[\p{L}\s\-]+$/u'], // Permite letras, espacios, guiones, acentos y Ñ
            'fecha_nacimiento' => 'required|date|before_or_equal:' . Carbon::now()->subYears(13)->format('Y-m-d'),
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
            'id_nacionalidad' => 'required|exists:nacionalidad,id', // Asegúrate de tener la tabla paises
            'img' => 'nullable|image|mimes:jpg,png|max:2048',
            'descripcion' => 'nullable|string|max:200'
        ], [
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            'fecha_nacimiento.before_or_equal' => 'Debes tener al menos 13 años para registrarte.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
            'apellido.regex' => 'El apellido solo puede contener letras, espacios y guiones.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Procesar imagen si existe
        $imagePath = null;
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $extension = $image->getClientOriginalExtension();
            $imageName = $request->username . '_' . time() . '.' . $extension;
            $imagePath = $image->storeAs('public/img/profile_images', $imageName);
        }

        // Crear usuario
        $user = User::create([
            'username' => $request->username,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_nacionalidad' => $request->id_nacionalidad,
            'id_rol' => 2,
            'id_estado' => 1,
            'img' => $imagePath ? str_replace('public/img/profile_img/', '', $imagePath) : null,
            'descripcion' => $request->descripcion
        ]);

        Auth::login($user);

        return redirect('/dashboard')->with('success', '¡Registro exitoso!'); // Cambia a tu ruta deseada
    }
    // Método para cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión.');
    }
}
