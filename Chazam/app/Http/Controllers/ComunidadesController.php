<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComunidadesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener comunidades creadas por el usuario
        $comunidadesCreadas = Chat::where('creator', $user->id_usuario)
            ->withCount('chatUsuarios')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
            
        // Obtener comunidades públicas (excluyendo las creadas por el usuario)
        $comunidadesPublicas = Chat::where('tipocomunidad', 'publica')
            ->where('creator', '!=', $user->id_usuario)
            ->withCount('chatUsuarios')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
            
        // Obtener comunidades privadas (excluyendo las creadas por el usuario)
        $comunidadesPrivadas = Chat::where('tipocomunidad', 'privada')
            ->where('creator', '!=', $user->id_usuario)
            ->withCount('chatUsuarios')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
        
        return view('comunidades.comunidades', [
            'comunidadesCreadas' => $comunidadesCreadas,
            'comunidadesPublicas' => $comunidadesPublicas,
            'comunidadesPrivadas' => $comunidadesPrivadas,
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->img ? 'img/profile_img/' . $user->img : null,
        ]);
    }

    public function join($id)
    {
        $comunidad = Chat::findOrFail($id);
        // Aquí puedes añadir la lógica para unir al usuario a la comunidad
        return response()->json(['success' => true]);
    }

    public function create()
    {
        $user = Auth::user();
        return view('comunidades.comunidad-create', [
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->img ? 'img/profile_img/' . $user->img : null,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $costoComunidad = 75000;

            // Verificar si tiene suficientes puntos
            if ($user->puntos < $costoComunidad) {
                return back()->with('error', 'No tienes suficientes puntos para crear una comunidad. Necesitas 75,000 puntos.');
            }

            $request->validate([
                'nombre' => 'required|string|max:255|unique:chats,nombre',
                'descripcion' => 'required|string',
                'tipocomunidad' => 'required|in:publica,privada',
                'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'nombre.required' => 'El nombre de la comunidad es obligatorio.',
                'nombre.unique' => 'Ya existe una comunidad con este nombre. Por favor, elige otro nombre.',
                'nombre.max' => 'El nombre no puede tener más de 255 caracteres.',
                'descripcion.required' => 'La descripción es obligatoria.',
                'tipocomunidad.required' => 'Debes seleccionar un tipo de comunidad.',
                'tipocomunidad.in' => 'El tipo de comunidad debe ser público o privado.',
                'img.required' => 'Debes subir una imagen para la comunidad.',
                'img.image' => 'El archivo debe ser una imagen.',
                'img.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
                'img.max' => 'La imagen no debe pesar más de 2MB.'
            ]);

            // Procesar la imagen
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $extension = $file->getClientOriginalExtension();
                $imgName = time() . '_' . uniqid() . '.' . $extension;
                
                // Intentar mover el archivo
                $file->move(public_path('img/comunidades'), $imgName);
            } else {
                return back()
                    ->withInput()
                    ->with('error', 'Debes subir una imagen para la comunidad.');
            }

            // Crear la comunidad
            $comunidad = Chat::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipocomunidad' => $request->tipocomunidad,
                'codigo' => $request->tipocomunidad === 'privada' ? Chat::generarCodigoUnico() : null,
                'creator' => Auth::id(),
                'img' => $imgName,
                'fecha_creacion' => now()
            ]);

            // Asociar al creador con la comunidad en chat_usuario
            \App\Models\ChatUsuario::create([
                'id_chat' => $comunidad->id_chat,
                'id_usuario' => Auth::id()
            ]);

            // Registrar el pago en la tabla pagos
            \App\Models\Pago::create([
                'id_comprador' => Auth::id(),
                'id_producto' => 13, // ID del producto "Crear comunidad"
                'cantidad' => $costoComunidad,
                'fecha_pago' => now()
            ]);

            // Descontar los puntos al usuario
            DB::table('users')->where('id_usuario', $user->id_usuario)->decrement('puntos', $costoComunidad);

            return response()->json([
                'success' => true,
                'message' => 'Comunidad creada exitosamente'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si hay error de validación, eliminar la imagen si se subió
            if (isset($imgName) && file_exists(public_path('img/comunidades/' . $imgName))) {
                unlink(public_path('img/comunidades/' . $imgName));
            }

            // Verificar si es error de nombre duplicado
            if ($e->validator->errors()->has('nombre')) {
                return response()->json([
                    'error' => 'Ya existe una comunidad con este nombre'
                ], 422);
            }

            // Verificar si es error de imagen
            if ($e->validator->errors()->has('img')) {
                return response()->json([
                    'error' => 'Debes subir una imagen para la comunidad'
                ], 422);
            }

            // Para otros errores de validación
            return response()->json([
                'error' => 'Por favor, completa todos los campos correctamente'
            ], 422);
        } catch (\Exception $e) {
            // Si hay error, eliminar la imagen si se subió
            if (isset($imgName) && file_exists(public_path('img/comunidades/' . $imgName))) {
                unlink(public_path('img/comunidades/' . $imgName));
            }

            return back()
                ->withInput()
                ->with('error', 'Error al procesar la imagen: ' . $e->getMessage());
        }
    }
} 