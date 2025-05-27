<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Chat;
use App\Models\ChatUsuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudUserController extends Controller
{
    /**
     * Enviar una solicitud de amistad
     */
    public function enviarSolicitud(Request $request)
    {
        try {
            // Validar que el usuario no se envíe solicitud a sí mismo
            if ($request->id_receptor == Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes enviarte una solicitud a ti mismo'
                ], 400);
            }

            // Verificar si ya existe una solicitud entre estos usuarios
            $solicitudExistente = Solicitud::where(function($query) use ($request) {
                $query->where('id_emisor', Auth::id())
                      ->where('id_receptor', $request->id_receptor);
            })->orWhere(function($query) use ($request) {
                $query->where('id_emisor', $request->id_receptor)
                      ->where('id_receptor', Auth::id());
            })->first();

            if ($solicitudExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una solicitud entre estos usuarios'
                ], 400);
            }

            // Crear la nueva solicitud
            $solicitud = new Solicitud();
            $solicitud->id_emisor = Auth::id();
            $solicitud->id_receptor = $request->id_receptor;
            $solicitud->estado = 'pendiente';
            $solicitud->save();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud enviada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bloquear a un usuario
     */
    public function bloquearUsuario(Request $request)
    {
        try {
            // Validar que el usuario no se bloquee a sí mismo
            if ($request->id_usuario == Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes bloquearte a ti mismo'
                ], 400);
            }

            DB::beginTransaction();

            // Verificar si ya existe una solicitud entre estos usuarios
            $solicitudExistente = Solicitud::where(function($query) use ($request) {
                $query->where('id_emisor', Auth::id())
                      ->where('id_receptor', $request->id_usuario);
            })->orWhere(function($query) use ($request) {
                $query->where('id_emisor', $request->id_usuario)
                      ->where('id_receptor', Auth::id());
            })->first();

            if ($solicitudExistente) {
                // Actualizar la solicitud existente a bloqueada
                $solicitudExistente->estado = 'blockeada';
                $solicitudExistente->save();
            } else {
                // Crear una nueva solicitud con estado bloqueada
                $solicitud = new Solicitud();
                $solicitud->id_emisor = Auth::id();
                $solicitud->id_receptor = $request->id_usuario;
                $solicitud->estado = 'blockeada';
                $solicitud->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario bloqueado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al bloquear al usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un usuario está bloqueado
     */
    public function verificarBloqueo($id_usuario)
    {
        try {
            $bloqueo = Solicitud::where(function($query) use ($id_usuario) {
                $query->where('id_emisor', Auth::id())
                      ->where('id_receptor', $id_usuario)
                      ->where('estado', 'blockeada');
            })->orWhere(function($query) use ($id_usuario) {
                $query->where('id_emisor', $id_usuario)
                      ->where('id_receptor', Auth::id())
                      ->where('estado', 'blockeada');
            })->exists();

            return response()->json([
                'success' => true,
                'bloqueado' => $bloqueo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el bloqueo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica el estado de la solicitud entre dos usuarios
     */
    public function verificarSolicitud($id_usuario)
    {
        try {
            \Log::info('Verificando solicitud entre usuarios', [
                'usuario_actual' => Auth::id(),
                'usuario_destino' => $id_usuario
            ]);

            $solicitud = Solicitud::where(function($query) use ($id_usuario) {
                $query->where('id_emisor', Auth::id())
                      ->where('id_receptor', $id_usuario);
            })->orWhere(function($query) use ($id_usuario) {
                $query->where('id_emisor', $id_usuario)
                      ->where('id_receptor', Auth::id());
            })->first();

            \Log::info('Resultado de la verificación', [
                'solicitud_encontrada' => $solicitud ? true : false,
                'estado' => $solicitud ? $solicitud->estado : null
            ]);

            if ($solicitud) {
                return response()->json([
                    'success' => true,
                    'estado' => $solicitud->estado
                ]);
            }

            return response()->json([
                'success' => true,
                'estado' => 'no_existe'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al verificar solicitud', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al verificar el estado de la solicitud'
            ], 500);
        }
    }

    /**
     * Obtener las solicitudes de amistad pendientes
     */
    public function getPendientes()
    {
        try {
            $solicitudes = Solicitud::with(['emisor' => function($query) {
                $query->select('id_usuario', 'username', 'img');
            }])
            ->where('id_receptor', Auth::id())
            ->where('estado', 'pendiente')
            ->get()
            ->map(function($solicitud) {
                return [
                    'id_solicitud' => $solicitud->id_solicitud,
                    'emisor' => [
                        'id_usuario' => $solicitud->emisor->id_usuario,
                        'username' => $solicitud->emisor->username,
                        'img' => $solicitud->emisor->img ? basename($solicitud->emisor->img) : null
                    ]
                ];
            });

            return response()->json($solicitudes);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las solicitudes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Responder a una solicitud de amistad
     */
    public function responderSolicitud(Request $request)
    {
        try {
            $request->validate([
                'id_solicitud' => 'required|exists:solicitudes,id_solicitud',
                'respuesta' => 'required|in:aceptada,rechazada'
            ]);

            DB::beginTransaction();

            $solicitud = Solicitud::where('id_solicitud', $request->id_solicitud)
                ->where('id_receptor', Auth::id())
                ->where('estado', 'pendiente')
                ->first();

            if (!$solicitud) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada o ya procesada'
                ], 404);
            }

            // Actualizar el estado de la solicitud actual
            $solicitud->estado = $request->respuesta;
            $solicitud->save();

            if ($request->respuesta === 'aceptada') {
                // Buscar un chat privado (sin id_reto) entre ambos usuarios
                $chatExistente = \App\Models\Chat::whereNull('id_reto')
                    ->whereHas('chatUsuarios', function($q) use ($solicitud) {
                        $q->where('id_usuario', $solicitud->id_emisor);
                    })
                    ->whereHas('chatUsuarios', function($q) use ($solicitud) {
                        $q->where('id_usuario', $solicitud->id_receptor);
                    })
                    ->first();

                if (!$chatExistente) {
                    // Crear nuevo chat privado
                    $chat = \App\Models\Chat::create([
                        'nombre' => 'Chat privado',
                        'fecha_creacion' => now(),
                        'descripcion' => 'Chat entre amigos',
                        'id_reto' => null
                    ]);

                    // Agregar ambos usuarios al chat
                    \App\Models\ChatUsuario::create([
                        'id_chat' => $chat->id_chat,
                        'id_usuario' => $solicitud->id_emisor
                    ]);
                    \App\Models\ChatUsuario::create([
                        'id_chat' => $chat->id_chat,
                        'id_usuario' => $solicitud->id_receptor
                    ]);
                }

                // Crear una solicitud recíproca para mantener el registro
                \App\Models\Solicitud::create([
                    'id_emisor' => $solicitud->id_receptor,
                    'id_receptor' => $solicitud->id_emisor,
                    'estado' => 'aceptada'
                ]);
            } else if ($request->respuesta === 'rechazada') {
                // Eliminar chats relacionados
                $chats = \App\Models\Chat::whereHas('chatUsuarios', function($query) use ($solicitud) {
                    $query->where('id_usuario', $solicitud->id_emisor)
                          ->orWhere('id_usuario', $solicitud->id_receptor);
                })->get();

                foreach ($chats as $chat) {
                    // Eliminar mensajes
                    \App\Models\Mensaje::whereIn('id_chat_usuario', function($query) use ($chat) {
                        $query->select('id_chat_usuario')
                              ->from('chat_usuario')
                              ->where('id_chat', $chat->id_chat);
                    })->delete();

                    // Eliminar relaciones chat_usuario
                    \App\Models\ChatUsuario::where('id_chat', $chat->id_chat)->delete();

                    // Eliminar el chat
                    $chat->delete();
                }
                // Eliminar la solicitud rechazada de la base de datos
                $solicitud->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->respuesta === 'aceptada' ? 'Solicitud aceptada' : 'Solicitud rechazada',
                'estado' => $request->respuesta,
                'solicitud' => $solicitud
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
} 