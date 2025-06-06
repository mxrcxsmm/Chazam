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
use App\Models\Personalizacion;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $nacionalidades = Nacionalidad::orderBy('nombre', 'asc')->get();
        return view('login', compact('nacionalidades'));
    }

    // Método para manejar el inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email',
            'password' => 'string|min:8',
        ]);

        // Buscar usuario por email
        $user = \App\Models\User::where('email', $request->email)->first();

        // Si existe y está en estado Activo o Disponible, no dejar entrar
        if ($user && in_array($user->id_estado, [1, 5])) {
            return back()->with('error', 'Ya tienes una sesión activa en otro dispositivo. Por favor, cierra la sesión anterior.');
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Verificar si el usuario está baneado
            if ($user->id_estado == 3 || $user->id_estado == 4) {
                Auth::logout();
                return back()->with('error', 'Tu cuenta está baneada. Por favor, contacta con el administrador.');
            }
            
            // Actualizar estado a Activo (1) al iniciar sesión
            User::where('id_usuario', $user->id_usuario)->update(['id_estado' => 1]);

            // Manejar la racha de login
            $now = now();
            $ultimoLogin = $user->ultimo_login;
            $nuevaRacha = 1;

            if ($ultimoLogin) {
                // Obtener la fecha de hoy a las 00:00
                $hoy = $now->copy()->setTime(0, 0, 0);
                // Obtener la fecha de ayer a las 00:00
                $ayer = $hoy->copy()->subDay();
                // Obtener la fecha del último login a las 00:00
                $ultimoLoginDia = $ultimoLogin->copy()->setTime(0, 0, 0);

                if ($ultimoLoginDia->eq($ayer)) {
                    // Si el último login fue ayer, incrementar la racha
                    $nuevaRacha = $user->racha + 1;
                } elseif ($ultimoLoginDia->lt($ayer)) {
                    // Si el último login fue antes de ayer, resetear la racha
                    $nuevaRacha = 1;
                }
            }

            // Actualizar último login y racha
            User::where('id_usuario', $user->id_usuario)->update([
                'ultimo_login' => $now,
                'racha' => $nuevaRacha
            ]);

            // Verificar y reiniciar puntos diarios si es necesario
            $hoy = $now->copy()->setTime(0, 0, 0);
            $ultimoLoginDia = $ultimoLogin ? $ultimoLogin->copy()->setTime(0, 0, 0) : null;
            
            if (!$ultimoLoginDia || $ultimoLoginDia->lt($hoy)) {
                User::where('id_usuario', $user->id_usuario)->update(['puntos_diarios' => 0]);
            }

            // Redirigir según el rol del usuario
            if ($user->rol->nom_rol === 'Administrador') {
                return redirect()->route('admin.usuarios.index');
            } else {
                return redirect()->route('retos.guide');
            }
        }

        return back()->with('login_error', 'Credenciales incorrectas.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:15',
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
            'descripcion' => 'nullable|string|max:200',
            'genero' => 'required|in:hombre,mujer',
        ], [
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            'fecha_nacimiento.before_or_equal' => 'Debes tener al menos 13 años para registrarte.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
            'apellido.regex' => 'El apellido solo puede contener letras, espacios y guiones.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'email.unique' => 'Este email ya está registrado.',
            'password_confirmation.same' => 'Las contraseñas no coinciden.',
            'genero.required' => 'El género es requerido',
            'genero.in' => 'El género debe ser Hombre o Mujer',
            'img.image' => 'El archivo debe ser una imagen.',
            'img.mimes' => 'La imagen debe ser JPG o PNG.',
            'img.max' => 'La imagen no puede superar los 2MB.',
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
                
                // Asegurarse de que el directorio existe
                $directory = public_path('img/profile_img');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Guardar la imagen directamente en public/img/profile_img
                $image->move($directory, $imageName);
                $imagePath = 'img/profile_img/' . $imageName;
                
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withErrors(['img' => 'Error al procesar la imagen: ' . $e->getMessage()])
                    ->withInput();
            }
        }
        try {
            // Crear usuario
            $user = User::create([
                'username' => $request->username,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'id_nacionalidad' => $request->id_nacionalidad,
                'id_rol' => 2, // Rol de usuario normal
                'id_estado' => 1, // Estado activo
                'img' => $imagePath,
                'descripcion' => $request->descripcion,
                'genero' => $request->genero,
                'puntos' => 500,
                'racha' => 1,
            ]);

            // Autenticar al usuario
            Auth::login($user);

            if (!$user->personalizacion) {
                Personalizacion::create([
                    'id_usuario' => $user->id_usuario,
                    'marco'      => 'default.svg',
                    'rotacion'   => false,
                    'brillo'     => null,
                    'sidebar'    => '#4B0082',
                ]);
            }            

            // Redirigir al guide de retos
            return redirect()->route('retos.guide')->with('success', '¡Registro exitoso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()])
                ->withInput();
        }

    }

    // Método para cerrar sesión
    public function logout(Request $request)
    {
        // Actualizar estado a Inactivo (2) antes de cerrar sesión
        if (Auth::check()) {
            $user = Auth::user();
            User::where('id_usuario', $user->id_usuario)->update(['id_estado' => 2]);
        }
        
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
