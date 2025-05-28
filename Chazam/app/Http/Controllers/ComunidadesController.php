<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatUsuario;
use App\Models\Mensaje;
use App\Models\User;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            
        // Obtener comunidades a las que pertenece el usuario (sin ser creador)
        $comunidadesUnidas = Chat::whereHas('chatUsuarios', function($query) use ($user) {
                $query->where('id_usuario', $user->id_usuario);
            })
            ->where('creator', '!=', $user->id_usuario)
            ->withCount('chatUsuarios')
            ->with('creador')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
            
        // Obtener comunidades públicas (excluyendo las creadas por el usuario y a las que pertenece)
        $comunidadesPublicas = Chat::where('tipocomunidad', 'publica')
            ->where('creator', '!=', $user->id_usuario)
            ->whereDoesntHave('chatUsuarios', function($query) use ($user) {
                $query->where('id_usuario', $user->id_usuario);
            })
            ->withCount('chatUsuarios')
            ->with('creador')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
            
        // Obtener comunidades privadas (excluyendo las creadas por el usuario y a las que pertenece)
        $comunidadesPrivadas = Chat::where('tipocomunidad', 'privada')
            ->where('creator', '!=', $user->id_usuario)
            ->whereDoesntHave('chatUsuarios', function($query) use ($user) {
                $query->where('id_usuario', $user->id_usuario);
            })
            ->withCount('chatUsuarios')
            ->with('creador')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
        
        return view('comunidades.comunidades', [
            'comunidadesCreadas' => $comunidadesCreadas,
            'comunidadesUnidas' => $comunidadesUnidas,
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
        try {
            $user = Auth::user();
            $comunidad = Chat::findOrFail($id);
            
            // Verificar si el usuario ya es miembro
            $esMiembro = ChatUsuario::where('id_chat', $id)
                ->where('id_usuario', $user->id_usuario)
                ->exists();
                
            if ($esMiembro) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya eres miembro de esta comunidad'
                ]);
            }
            
            // Verificar si es una comunidad privada
            if ($comunidad->tipocomunidad === 'privada') {
                // Verificar si se proporcionó el código
                if (!request()->has('codigo')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Esta es una comunidad privada. Necesitas el código de acceso para unirte.'
                    ]);
                }
                
                // Verificar si el código es correcto
                if (request('codigo') !== $comunidad->codigo) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Código de acceso incorrecto'
                    ]);
                }
            }
            
            // Crear la relación en la tabla intermedia
            ChatUsuario::create([
                'id_chat' => $id,
                'id_usuario' => $user->id_usuario
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Te has unido a la comunidad exitosamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al unirse a la comunidad'
            ]);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        
        // Obtener la comunidad actual
        $comunidad = Chat::with(['chatUsuarios.usuario', 'creador'])
            ->findOrFail($id);
        
        // Obtener todas las comunidades creadas por el usuario
        $comunidadesCreadas = Chat::where('creator', $user->id_usuario)
            ->withCount('chatUsuarios')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
        
        // Obtener todas las comunidades a las que pertenece el usuario (sin ser creador)
        $comunidadesUnidas = Chat::whereHas('chatUsuarios', function($query) use ($user) {
                $query->where('id_usuario', $user->id_usuario);
            })
            ->where('creator', '!=', $user->id_usuario)
            ->withCount('chatUsuarios')
            ->with('creador')
            ->orderBy('chat_usuarios_count', 'desc')
            ->get();
        
        // Verificar si el usuario es miembro de la comunidad
        $esMiembro = $comunidad->chatUsuarios()
            ->where('id_usuario', $user->id_usuario)
            ->exists();
            
        if (!$esMiembro) {
            return redirect()->route('comunidades.index')
                ->with('error', 'No eres miembro de esta comunidad');
        }

        return view('comunidades.comunidad', [
            'comunidad' => $comunidad,
            'comunidadesCreadas' => $comunidadesCreadas,
            'comunidadesUnidas' => $comunidadesUnidas,
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->img ? 'img/profile_img/' . $user->img : null,
        ]);
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

    public function getMembers($id)
    {
        $comunidad = Chat::with(['creador'])->findOrFail($id);
            
        $members = ChatUsuario::where('id_chat', $id)
            ->where('id_usuario', '!=', $comunidad->creador->id_usuario)
            ->with('usuario')
            ->get()
            ->map(function($chatUsuario) {
                return [
                    'id_usuario' => $chatUsuario->usuario->id_usuario,
                    'username' => $chatUsuario->usuario->username,
                    'img' => $chatUsuario->usuario->img ? basename($chatUsuario->usuario->img) : null,
                    'status' => $chatUsuario->usuario->id_estado == 1 ? 'online' : 'offline'
                ];
            });
            
        return response()->json([
            'creator' => [
                'id_usuario' => $comunidad->creador->id_usuario,
                'username' => $comunidad->creador->username,
                'img' => $comunidad->creador->img ? basename($comunidad->creador->img) : null,
                'status' => $comunidad->creador->id_estado == 1 ? 'online' : 'offline'
            ],
            'members' => $members
        ]);
    }

    public function getMessages($id)
    {
        $userId = Auth::id();
        $chatUsuario = ChatUsuario::where('id_chat', $id)
            ->where('id_usuario', $userId)
            ->first();
        
        if (!$chatUsuario) {
            return response()->json(['error' => 'No tienes acceso a este chat'], 403);
        }
        
        $mensajes = Mensaje::whereHas('chatUsuario', function($q) use ($id) {
                $q->where('id_chat', $id);
            })
            ->with(['chatUsuario.usuario'])
            ->orderBy('fecha_envio', 'asc')
            ->get()
            ->map(function($mensaje) {
                return [
                    'id_mensaje' => $mensaje->id_mensaje,
                    'contenido' => $mensaje->contenido,
                    'fecha_envio' => $mensaje->fecha_envio->format('H:i'),
                    'usuario' => $mensaje->chatUsuario->usuario->username,
                    'img' => $mensaje->chatUsuario->usuario->img ? basename($mensaje->chatUsuario->usuario->img) : null,
                    'es_mio' => $mensaje->chatUsuario->id_usuario == Auth::id(),
                ];
            });
        return response()->json($mensajes);
    }

    public function sendMessage(Request $request, $id)
    {
        $userId = Auth::id();
        $chatUsuario = ChatUsuario::where('id_chat', $id)
            ->where('id_usuario', $userId)
            ->first();
        
        if (!$chatUsuario) {
            return response()->json(['error' => 'No tienes acceso a este chat'], 403);
        }
        
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
        
        $mensaje = Mensaje::create([
            'id_chat_usuario' => $chatUsuario->id_chat_usuario,
            'contenido' => $request->message,
            'fecha_envio' => now(),
        ]);
        
        $mensajeData = [
            'id_mensaje' => $mensaje->id_mensaje,
            'contenido' => $mensaje->contenido,
            'fecha_envio' => $mensaje->fecha_envio->format('H:i'),
            'usuario' => Auth::user()->username,
            'img' => asset('img/profile_img/' . Auth::user()->img),
            'es_mio' => true
        ];
        
        // Disparar el evento
        event(new \App\Events\NuevoMensajeComunidad($mensajeData, $id));
        
        return response()->json([
            'success' => true,
            'mensaje' => $mensajeData
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $comunidad = Chat::withCount('chatUsuarios')->findOrFail($id);
        
        // Verificar si el usuario es miembro de la comunidad
        $esMiembro = ChatUsuario::where('id_chat', $id)
            ->where('id_usuario', $user->id_usuario)
            ->exists();
            
        if (!$esMiembro) {
            return redirect()->route('comunidades.show', $id)
                ->with('error', 'No tienes acceso a esta comunidad');
        }
        
        return view('comunidades.comunidad-edit', [
            'comunidad' => $comunidad,
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->img ? 'img/profile_img/' . $user->img : null,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $comunidad = Chat::findOrFail($id);
            
            // Verificar si el usuario es el creador de la comunidad
            if ($comunidad->creator !== $user->id_usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para editar esta comunidad'
                ], 403);
            }
            
            $request->validate([
                'nombre' => 'required|string|max:255|unique:chats,nombre,' . $id . ',id_chat',
                'descripcion' => 'required|string',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $data = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion
            ];
            
            // Procesar la imagen si se ha subido una nueva
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $extension = $file->getClientOriginalExtension();
                $imgName = time() . '_' . uniqid() . '.' . $extension;
                
                // Eliminar la imagen anterior si existe
                if ($comunidad->img && file_exists(public_path('img/comunidades/' . $comunidad->img))) {
                    unlink(public_path('img/comunidades/' . $comunidad->img));
                }
                
                // Mover la nueva imagen
                $file->move(public_path('img/comunidades'), $imgName);
                $data['img'] = $imgName;
            }
            
            $comunidad->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Comunidad actualizada exitosamente'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la comunidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function abandonar($id)
    {
        try {
            $user = Auth::user();
            $comunidad = Chat::findOrFail($id);
            
            // Verificar que el usuario no sea el creador
            if ($comunidad->creator === $user->id_usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes abandonar una comunidad que has creado'
                ]);
            }
            
            // Verificar que el usuario sea miembro de la comunidad
            $chatUsuario = ChatUsuario::where('id_chat', $id)
                ->where('id_usuario', $user->id_usuario)
                ->first();
                
            if (!$chatUsuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'No eres miembro de esta comunidad'
                ]);
            }
            
            // Iniciar transacción
            DB::beginTransaction();
            
            try {
                // Eliminar todos los mensajes asociados a este chat_usuario
                Mensaje::where('id_chat_usuario', $chatUsuario->id_chat_usuario)->delete();
                
                // Eliminar la relación en la tabla intermedia
                $deleted = $chatUsuario->delete();
                
                if (!$deleted) {
                    throw new \Exception('Error al eliminar la relación del usuario con la comunidad');
                }
                
                // Confirmar transacción
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Has abandonado la comunidad exitosamente'
                ]);
                
            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error al abandonar comunidad: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al abandonar la comunidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function eliminar($id)
    {
        try {
            $user = Auth::user();
            $comunidad = Chat::findOrFail($id);
            
            // Verificar que el usuario sea el creador de la comunidad
            if ($comunidad->creator !== $user->id_usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo el creador puede eliminar la comunidad'
                ], 403);
            }
            
            // Iniciar transacción
            DB::beginTransaction();
            
            try {
                // Obtener todos los chat_usuarios asociados a esta comunidad
                $chatUsuarios = ChatUsuario::where('id_chat', $id)->get();
                
                // Eliminar todos los mensajes asociados a cada chat_usuario
                foreach ($chatUsuarios as $chatUsuario) {
                    Mensaje::where('id_chat_usuario', $chatUsuario->id_chat_usuario)->delete();
                }
                
                // Eliminar todas las relaciones en la tabla intermedia
                ChatUsuario::where('id_chat', $id)->delete();
                
                // Eliminar la imagen de la comunidad si existe
                if ($comunidad->img && file_exists(public_path('img/comunidades/' . $comunidad->img))) {
                    unlink(public_path('img/comunidades/' . $comunidad->img));
                }
                
                // Eliminar la comunidad
                $comunidad->delete();
                
                // Confirmar transacción
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Comunidad eliminada exitosamente'
                ]);
                
            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar comunidad: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al eliminar la comunidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editForm($id)
    {
        $user = Auth::user();
        $comunidad = Chat::withCount('chatUsuarios')->findOrFail($id);
        
        // Verificar si el usuario es el creador de la comunidad
        if ($comunidad->creator !== $user->id_usuario) {
            return redirect()->route('comunidades.show', $id)
                ->with('error', 'No tienes permiso para editar esta comunidad');
        }
        
        return view('comunidades.edit', [
            'comunidad' => $comunidad,
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->img ? 'img/profile_img/' . $user->img : null,
        ]);
    }

    /**
     * Verifica el estado de una solicitud de amistad
     */
    public function verificarSolicitud($idUsuario)
    {
        try {
            $usuarioActual = Auth::user();
            
            // Buscar específicamente una solicitud PENDIENTE donde el usuario actual es el receptor
            $solicitudPendienteRecibida = Solicitud::where('id_receptor', $usuarioActual->id_usuario)
                                                ->where('id_emisor', $idUsuario)
                                                ->where('estado', 'pendiente')
                                                ->first();

            if ($solicitudPendienteRecibida) {
                return response()->json(['estado' => 'pendiente']);
            }

            // Si no hay una solicitud pendiente recibida, verificar si ya son amigos (estado 'aceptada')
            $solicitudAceptada = Solicitud::where(function($query) use ($usuarioActual, $idUsuario) {
                                            $query->where(function($q) use ($usuarioActual, $idUsuario) {
                                                $q->where('id_emisor', $usuarioActual->id_usuario)
                                                  ->where('id_receptor', $idUsuario);
                                            })->orWhere(function($q) use ($usuarioActual, $idUsuario) {
                                                $q->where('id_emisor', $idUsuario)
                                                  ->where('id_receptor', $usuarioActual->id_usuario);
                                            });
                                        })
                                        ->where('estado', 'aceptada')
                                        ->first();

            if ($solicitudAceptada) {
                return response()->json(['estado' => 'aceptada']);
            }

            // Si no son amigos ni hay solicitud pendiente recibida, verificar si hay una solicitud rechazada o bloqueada
             $solicitudOtra = Solicitud::where(function($query) use ($usuarioActual, $idUsuario) {
                                            $query->where(function($q) use ($usuarioActual, $idUsuario) {
                                                $q->where('id_emisor', $usuarioActual->id_usuario)
                                                  ->where('id_receptor', $idUsuario);
                                            })->orWhere(function($q) use ($usuarioActual, $idUsuario) {
                                                $q->where('id_emisor', $idUsuario)
                                                  ->where('id_receptor', $usuarioActual->id_usuario);
                                            });
                                        })
                                        ->whereIn('estado', ['rechazada', 'blockeada'])
                                        ->first();

            if ($solicitudOtra) {
                 // Si hay una solicitud rechazada o bloqueada, el estado es como si no existiera para fines de enviar solicitud
                return response()->json(['estado' => 'no_existe']); // O podrías devolver 'rechazada' si quieres mostrar algo diferente
            }
            
            // Si no existe ninguna solicitud relevante
            return response()->json(['estado' => 'no_existe']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al verificar solicitud'], 500);
        }
    }

    /**
     * Obtiene las solicitudes de amistad pendientes
     */
    public function solicitudesPendientes()
    {
        try {
            $usuarioActual = Auth::user();
            
            $solicitudes = Solicitud::where('id_receptor', $usuarioActual->id_usuario)
                ->where('estado', 'pendiente')
                ->with('emisor')
                ->get()
                ->map(function($solicitud) {
                    return [
                        'id_solicitud' => $solicitud->id_solicitud,
                        'emisor' => [
                            'id' => $solicitud->emisor->id_usuario,
                            'username' => $solicitud->emisor->username,
                            'img' => $solicitud->emisor->img ? basename($solicitud->emisor->img) : null
                        ]
                    ];
                });

            return response()->json($solicitudes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener solicitudes'], 500);
        }
    }

    /**
     * Envía una solicitud de amistad
     */
    public function enviarSolicitud(Request $request)
    {
        try {
            $usuarioActual = Auth::user();
            $idReceptor = $request->id_receptor;

            // Verificar si ya existe una solicitud
            $solicitudExistente = Solicitud::where(function($query) use ($usuarioActual, $idReceptor) {
                $query->where(function($q) use ($usuarioActual, $idReceptor) {
                    $q->where('id_emisor', $usuarioActual->id_usuario)
                      ->where('id_receptor', $idReceptor);
                })->orWhere(function($q) use ($usuarioActual, $idReceptor) {
                    $q->where('id_emisor', $idReceptor)
                      ->where('id_receptor', $usuarioActual->id_usuario);
                });
            })->first();

            if ($solicitudExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una solicitud entre estos usuarios'
                ]);
            }

            // Crear nueva solicitud
            Solicitud::create([
                'id_emisor' => $usuarioActual->id_usuario,
                'id_receptor' => $idReceptor,
                'estado' => 'pendiente',
                'fecha_solicitud' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud enviada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar solicitud'
            ], 500);
        }
    }

    /**
     * Responde a una solicitud de amistad
     */
    public function responderSolicitud(Request $request)
    {
        try {
            $solicitud = Solicitud::findOrFail($request->id_solicitud);
            
            if ($solicitud->id_receptor !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para responder esta solicitud'
                ], 403);
            }

            $solicitud->estado = $request->respuesta;
            $solicitud->save();

            return response()->json([
                'success' => true,
                'estado' => $solicitud->estado,
                'message' => $solicitud->estado === 'aceptada' ? 
                    'Solicitud aceptada correctamente' : 
                    'Solicitud rechazada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ], 500);
        }
    }

    /**
     * Bloquea a un usuario
     */
    public function bloquearUsuario(Request $request)
    {
        try {
            $usuarioActual = Auth::user();
            $idUsuarioBloquear = $request->id_usuario;

            // Verificar si ya existe una solicitud bloqueada
            $solicitudExistente = Solicitud::where(function($query) use ($usuarioActual, $idUsuarioBloquear) {
                $query->where(function($q) use ($usuarioActual, $idUsuarioBloquear) {
                    $q->where('id_emisor', $usuarioActual->id_usuario)
                      ->where('id_receptor', $idUsuarioBloquear);
                })->orWhere(function($q) use ($usuarioActual, $idUsuarioBloquear) {
                    $q->where('id_emisor', $idUsuarioBloquear)
                      ->where('id_receptor', $usuarioActual->id_usuario);
                });
            })->first();

            if ($solicitudExistente) {
                $solicitudExistente->estado = 'blockeada';
                $solicitudExistente->save();
            } else {
                Solicitud::create([
                    'id_emisor' => $usuarioActual->id_usuario,
                    'id_receptor' => $idUsuarioBloquear,
                    'estado' => 'blockeada',
                    'fecha_solicitud' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Usuario bloqueado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al bloquear usuario'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $comunidad = Chat::findOrFail($id);
            
            // Verificar que el usuario sea el creador
            if ($comunidad->creator !== $user->id_usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar esta comunidad'
                ]);
            }
            
            // Iniciar transacción
            DB::beginTransaction();
            
            try {
                // Obtener todos los chat_usuario relacionados
                $chatUsuarios = ChatUsuario::where('id_chat', $id)->get();
                
                // Para cada chat_usuario, eliminar sus mensajes
                foreach ($chatUsuarios as $chatUsuario) {
                    Mensaje::where('id_chat_usuario', $chatUsuario->id_chat_usuario)->delete();
                }
                
                // Eliminar todos los chat_usuario
                ChatUsuario::where('id_chat', $id)->delete();
                
                // Eliminar la comunidad
                $comunidad->delete();
                
                // Confirmar transacción
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Comunidad eliminada exitosamente'
                ]);
                
            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar comunidad: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hubo un error al eliminar la comunidad: ' . $e->getMessage()
            ], 500);
        }
    }
} 