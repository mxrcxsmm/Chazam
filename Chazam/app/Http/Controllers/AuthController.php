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
                return redirect()->route('retos.guide'); // Página de usuario normal
            }
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:10',
            'nombre' => ['required', 'max:100', 'regex:/^[\p{L}\s\-]+$/u'],
            'apellido' => ['required', 'max:100', 'regex:/^[\p{L}\s\-]+$/u'],
            'fecha_nacimiento' => 'required|date|before_or_equal:' . Carbon::now()->subYears(13)->format('Y-m-d'),
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
            'password_confirmation' => 'required|same:password',
            'id_nacionalidad' => 'required|exists:nacionalidad,id_nacionalidad',
            'img' => 'nullable|image|mimes:jpg,png|max:2048',
            'descripcion' => 'nullable|string|max:200'
        ], [
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            'fecha_nacimiento.before_or_equal' => 'Debes tener al menos 13 años para registrarte.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
            'apellido.regex' => 'El apellido solo puede contener letras, espacios y guiones.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'email.unique' => 'Este email ya está registrado.',
            'password_confirmation.same' => 'Las contraseñas no coinciden.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Procesar imagen si existe
        $imagePath = null;
        if ($request->hasFile('img')) {
            try {
                $image = $request->file('img');
                $extension = $image->getClientOriginalExtension();
                $imageName = $request->username . '_' . time() . '.' . $extension;
                
                // Guardar la imagen en storage/app/public/img/profile_img
                $imagePath = $image->storeAs('public/img/profile_img', $imageName);
                
                if (!$imagePath) {
                    throw new \Exception('Error al guardar la imagen');
                }
                
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['img' => 'Error al procesar la imagen: ' . $e->getMessage()])
                    ->withInput();
            }
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
            'img' => $imagePath ? str_replace('public/', '', $imagePath) : null,
            'descripcion' => $request->descripcion
        ]);

        Auth::login($user);

        return redirect()->route('login')->with('success', '¡Registro exitoso!');
        // return redirect()->route('user.dashboard')->with('success', '¡Registro exitoso!');
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

    public function checkAvailability(Request $request)
    {
        $type = $request->input('type');
        $value = $request->input('value');

        if ($type === 'username') {
            $exists = User::where('username', $value)->exists();
        } elseif ($type === 'email') {
            $exists = User::where('email', $value)->exists();
        } else {
            return response()->json(['error' => 'Tipo de validación no válido'], 400);
        }

        return response()->json(['available' => !$exists]);
    }
}
