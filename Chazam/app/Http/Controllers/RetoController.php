<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reto;
use App\Models\Chat;
use App\Models\ChatUsuario;
use App\Models\Mensaje;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Solicitud;

class RetoController extends Controller
{
    /**
     * Muestra el reto actual que es el mismo para todos los usuarios.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Actualizar estado a Disponible (5)
        User::where('id_usuario', $user->id_usuario)->update(['id_estado' => 5]);
        
        // Obtener el reto del día desde la caché o seleccionar uno nuevo
        $reto = $this->getRetoDelDia();
        
        return view('Retos.reto', [
            'reto' => $reto,
            'racha' => $user->racha,
            'puntos' => $user->puntos,
            'username' => $user->username,
            'nombre_completo' => $user->nombre_completo,
            'imagen_perfil' => $user->imagen_perfil,
            'user_id' => $user->id_usuario
        ]);
    }

    /**
     * Busca un compañero aleatorio para el chat
     */
    public function buscarCompanero()
    {
        try {
            Log::info('=== INICIO BÚSQUEDA COMPAÑERO ===');
            
            // Verificar autenticación
            if (!Auth::check()) {
                Log::error('Usuario no autenticado');
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }
            
            $usuarioActual = Auth::user();
            Log::info('Usuario actual: ' . $usuarioActual->id_usuario);
            
            // Obtener el reto del día
            try {
                $reto = $this->getRetoDelDia();
                Log::info('Reto actual: ' . $reto->id_reto);
            } catch (\Exception $e) {
                Log::error('Error al obtener reto del día: ' . $e->getMessage());
                return response()->json(['error' => 'Error al obtener reto del día'], 500);
            }
            
            // Verificar si el usuario ya está en un chat activo
            try {
                $chatActivo = ChatUsuario::where('id_usuario', $usuarioActual->id_usuario)
                    ->whereHas('chat', function($query) use ($reto) {
                        $query->where('id_reto', $reto->id_reto)
                            ->where('fecha_creacion', '>=', now()->subMinutes(30));
                    })
                    ->first();

                if ($chatActivo) {
                    Log::info('Usuario ya tiene un chat activo: ' . $chatActivo->id_chat);
                    
                    // Obtener el compañero del chat activo
                    $companeroChat = ChatUsuario::where('id_chat', $chatActivo->id_chat)
                        ->where('id_usuario', '!=', $usuarioActual->id_usuario)
                        ->first();
                    
                    if ($companeroChat) {
                        $companero = $companeroChat->usuario;
                        return response()->json([
                            'chat_id' => $chatActivo->id_chat,
                            'companero' => [
                                'id' => $companero->id_usuario,
                                'username' => $companero->username,
                                'nombre_completo' => $companero->nombre_completo,
                                'imagen' => $companero->imagen_perfil
                            ]
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error al verificar chat activo: ' . $e->getMessage());
                return response()->json(['error' => 'Error al verificar chat activo'], 500);
            }

            // Contar usuarios disponibles
            try {
                $totalDisponibles = User::where('id_estado', 5)->count();
                Log::info('Total usuarios disponibles: ' . $totalDisponibles);
            } catch (\Exception $e) {
                Log::error('Error al contar usuarios disponibles: ' . $e->getMessage());
                return response()->json(['error' => 'Error al contar usuarios disponibles'], 500);
            }

            // Buscar compañero
            try {
                // Obtener IDs de usuarios bloqueados usando el modelo Solicitud
                $usuariosBloqueados = Solicitud::where(function($query) use ($usuarioActual) {
                    $query->where(function($q) use ($usuarioActual) {
                        $q->where('id_emisor', $usuarioActual->id_usuario)
                          ->where('estado', 'blockeada');
                    })->orWhere(function($q) use ($usuarioActual) {
                        $q->where('id_receptor', $usuarioActual->id_usuario)
                          ->where('estado', 'blockeada');
                    });
                })
                ->get()
                ->map(function($solicitud) use ($usuarioActual) {
                    // Si el usuario actual es el emisor, devolver el receptor y viceversa
                    return $solicitud->id_emisor == $usuarioActual->id_usuario 
                        ? $solicitud->id_receptor 
                        : $solicitud->id_emisor;
                })
                ->unique()
                ->push($usuarioActual->id_usuario)
                ->toArray();

                Log::info('Usuarios bloqueados: ' . implode(', ', $usuariosBloqueados));

                $companero = User::where('id_estado', 5)
                    ->where('id_usuario', '!=', $usuarioActual->id_usuario)
                    ->whereNotIn('id_usuario', $usuariosBloqueados)
                    ->whereDoesntHave('chatUsuarios', function($query) use ($reto) {
                        $query->whereHas('chat', function($q) use ($reto) {
                            $q->where('id_reto', $reto->id_reto)
                                ->where('fecha_creacion', '>=', now()->subMinutes(30));
                        });
                    })
                    ->inRandomOrder()
                    ->first();

                if (!$companero) {
                    Log::info('No se encontró ningún compañero disponible');
                    Log::info('Posibles razones:');
                    Log::info('1. No hay usuarios con estado 5');
                    Log::info('2. Todos los usuarios están en chats activos');
                    Log::info('3. El usuario actual es el único disponible');
                    Log::info('4. Todos los usuarios disponibles están bloqueados');
                    return response()->json(['error' => 'No hay usuarios disponibles'], 404);
                }

                Log::info('Compañero encontrado: ' . $companero->id_usuario);
            } catch (\Exception $e) {
                Log::error('Error al buscar compañero: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return response()->json(['error' => 'Error al buscar compañero'], 500);
            }

            // Crear un nuevo chat asociado al reto actual
            $chat = Chat::create([
                'fecha_creacion' => now(),
                'nombre' => 'Chat Aleatorio',
                'descripcion' => 'Chat entre ' . $usuarioActual->username . ' y ' . $companero->username,
                'id_reto' => $reto->id_reto
            ]);

            // Asociar usuarios al chat
            ChatUsuario::create([
                'id_chat' => $chat->id_chat,
                'id_usuario' => $usuarioActual->id_usuario
            ]);

            ChatUsuario::create([
                'id_chat' => $chat->id_chat,
                'id_usuario' => $companero->id_usuario
            ]);

            // Actualizar el estado de ambos usuarios a "En chat" (5)
            User::whereIn('id_usuario', [$usuarioActual->id_usuario, $companero->id_usuario])
                ->update(['id_estado' => 5]);

            return response()->json([
                'chat_id' => $chat->id_chat,
                'companero' => [
                    'id' => $companero->id_usuario,
                    'username' => $companero->username,
                    'nombre_completo' => $companero->nombre_completo,
                    'imagen' => $companero->imagen_perfil
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error en buscarCompanero: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Envía un mensaje en el chat
     */
    public function enviarMensaje(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id_chat',
            'contenido' => 'required|string'
        ]);

        $usuarioActual = Auth::user();
        $chatUsuario = ChatUsuario::where('id_chat', $request->chat_id)
            ->where('id_usuario', $usuarioActual->id_usuario)
            ->first();

        if (!$chatUsuario) {
            return response()->json(['error' => 'No tienes acceso a este chat'], 403);
        }

        // Obtener el reto actual del chat
        $chat = Chat::find($request->chat_id);
        $retoId = $chat->id_reto;
        
        // Inicializar puntos ganados a 0
        $puntosGanados = 0;
        
        // Verificar si el usuario no ha alcanzado el límite diario
        if ($usuarioActual->puntos_diarios < 300) {
            $sumarPuntos = true;
            
            // Para el reto 1, solo sumar puntos si tiene emojis
            if ($retoId == 1) {
                $sumarPuntos = isset($request->tieneEmojis) && $request->tieneEmojis;
            }
            // Para el reto 3, verificar el parámetro sumarPuntos (basado en longitud)
            else if ($retoId == 3) {
                $sumarPuntos = isset($request->sumarPuntos) ? $request->sumarPuntos : true;
            }
            
            // Si se deben sumar puntos
            if ($sumarPuntos) {
                $puntosGanados = rand(1, 10);
                
                // Asegurarse de no exceder el límite diario
                $puntosDisponibles = 300 - ($usuarioActual->puntos_diarios ?? 0);
                $puntosGanados = min($puntosGanados, $puntosDisponibles);
                
                // Actualizar puntos diarios y totales usando el Query Builder
                DB::table('users')
                    ->where('id_usuario', $usuarioActual->id_usuario)
                    ->update([
                        'puntos_diarios' => DB::raw('COALESCE(puntos_diarios, 0) + ' . $puntosGanados),
                        'puntos' => DB::raw('puntos + ' . $puntosGanados)
                    ]);
            }
        }

        $mensaje = Mensaje::create([
            'id_chat_usuario' => $chatUsuario->id_chat_usuario,
            'contenido' => is_array($request->contenido) ? $request->contenido['texto'] : $request->contenido,
            'fecha_envio' => now()
        ]);

        return response()->json([
            'mensaje' => $mensaje,
            'usuario' => [
                'id' => $usuarioActual->id_usuario,
                'username' => $usuarioActual->username,
                'imagen' => $usuarioActual->imagen_perfil
            ],
            'puntos_ganados' => $puntosGanados ?? 0
        ]);
    }

    /**
     * Obtiene los mensajes de un chat
     */
    public function obtenerMensajes($chatId)
    {
        $usuarioActual = Auth::user();
        $chatUsuario = ChatUsuario::where('id_chat', $chatId)
            ->where('id_usuario', $usuarioActual->id_usuario)
            ->first();

        if (!$chatUsuario) {
            return response()->json(['error' => 'No tienes acceso a este chat'], 403);
        }

        $mensajes = Mensaje::with(['chatUsuario.usuario'])
            ->whereHas('chatUsuario', function($query) use ($chatId) {
                $query->where('id_chat', $chatId);
            })
            ->orderBy('fecha_envio', 'asc')
            ->get()
            ->map(function($mensaje) {
                return [
                    'id_chat_usuario' => $mensaje->id_chat_usuario,
                    'contenido' => $mensaje->contenido,
                    'fecha_envio' => $mensaje->fecha_envio,
                    'updated_at' => $mensaje->updated_at,
                    'created_at' => $mensaje->created_at,
                    'chat_usuario' => [
                        'usuario' => [
                            'id' => $mensaje->chatUsuario->usuario->id_usuario,
                            'username' => $mensaje->chatUsuario->usuario->username,
                            'imagen' => $mensaje->chatUsuario->usuario->imagen_perfil
                        ]
                    ]
                ];
            });

        return response()->json($mensajes);
    }
    
    /**
     * Obtiene el reto del día, que es el mismo para todos los usuarios.
     * El reto cambia automáticamente a las 00:00 de cada día.
     * Los retos se muestran en orden aleatorio sin repetir hasta que se hayan mostrado todos.
     *
     * @return \App\Models\Reto
     */
    private function getRetoDelDia()
    {
        // Temporalmente para pruebas
        // return Reto::find(1);
        // return Reto::find(2);
        // return Reto::find(3);
        // return Reto::find(4);


        $cacheKey = 'reto_del_dia_' . now()->format('Y-m-d');
        
        // Intentar obtener el reto del día desde la caché
        $reto = Cache::get($cacheKey);
        
        // Si no existe un reto del día, seleccionar uno nuevo
        if (!$reto) {
            // Obtener la lista de retos pendientes
            $retosPendientes = Cache::get('retos_pendientes', []);
            
            // Si no hay retos pendientes, crear una nueva lista aleatoria
            if (empty($retosPendientes)) {
                // Obtener todos los IDs de retos
                $todosRetos = Reto::pluck('id_reto')->toArray();
                
                // Mezclar aleatoriamente los IDs
                shuffle($todosRetos);
                
                // Guardar la lista mezclada
                $retosPendientes = $todosRetos;
                Cache::put('retos_pendientes', $retosPendientes, now()->addDays(30));
            }
            
            // Tomar el primer reto de la lista
            $idReto = array_shift($retosPendientes);
            
            // Obtener el reto completo
            $reto = Reto::find($idReto);
            
            // Actualizar la lista de retos pendientes
            Cache::put('retos_pendientes', $retosPendientes, now()->addDays(30));
            
            // Guardar el reto en la caché hasta las 00:00 del día siguiente
            Cache::put($cacheKey, $reto, Carbon::tomorrow());
        }
        
        return $reto;
        
    }

    /**
     * Verifica y elimina chats cuando los usuarios cambian de estado
     */
    public function verificarEstadoChats()
    {
        try {
            Log::info('=== INICIO VERIFICACIÓN ESTADO CHATS ===');
            
            // Obtener todos los chats activos del reto actual
            $reto = $this->getRetoDelDia();
            $chats = Chat::where('id_reto', $reto->id_reto)
                ->where('fecha_creacion', '>=', now()->subMinutes(30))
                ->get();

            foreach ($chats as $chat) {
                // Obtener los usuarios del chat
                $usuarios = $chat->chatUsuarios()->with('usuario')->get();
                
                // Verificar si algún usuario cambió de estado
                foreach ($usuarios as $chatUsuario) {
                    if ($chatUsuario->usuario->id_estado != 5) {
                        Log::info('Usuario ' . $chatUsuario->usuario->id_usuario . ' cambió de estado. Eliminando chat ' . $chat->id_chat);
                        
                        // Iniciar transacción
                        DB::beginTransaction();
                        try {
                            // Eliminar todos los mensajes relacionados con este chat
                            Mensaje::whereHas('chatUsuario', function($query) use ($chat) {
                                $query->where('id_chat', $chat->id_chat);
                            })->delete();
                            
                            // Eliminar todos los registros de chat_usuario relacionados con este chat
                            ChatUsuario::where('id_chat', $chat->id_chat)->delete();
                            
                            // Eliminar el chat
                            $chat->delete();
                            
                            // Confirmar transacción
                            DB::commit();
                            Log::info('Transacción completada exitosamente para el chat ' . $chat->id_chat);
                        } catch (\Exception $e) {
                            // Revertir transacción en caso de error
                            DB::rollBack();
                            Log::error('Error en la transacción para el chat ' . $chat->id_chat . ': ' . $e->getMessage());
                            throw $e;
                        }
                        break;
                    }
                }
            }

            return response()->json(['message' => 'Verificación completada']);
        } catch (\Exception $e) {
            Log::error('Error en verificarEstadoChats: ' . $e->getMessage());
            return response()->json(['error' => 'Error al verificar estados'], 500);
        }
    }

    /**
     * Verifica si un chat específico sigue activo
     */
    public function verificarChat($chatId)
    {
        try {
            $chat = Chat::where('id_chat', $chatId)
                ->where('fecha_creacion', '>=', now()->subMinutes(30))
                ->first();

            if (!$chat) {
                return response()->json(['error' => 'Chat no encontrado'], 404);
            }

            // Verificar si ambos usuarios siguen en el chat
            $usuariosEnChat = ChatUsuario::where('id_chat', $chatId)->count();
            if ($usuariosEnChat < 2) {
                return response()->json(['error' => 'Chat incompleto'], 404);
            }

            return response()->json(['status' => 'active']);
        } catch (\Exception $e) {
            Log::error('Error al verificar chat: ' . $e->getMessage());
            return response()->json(['error' => 'Error al verificar chat'], 500);
        }
    }

    /**
     * Limpia el estado del usuario cuando sale del reto
     */
    public function limpiarEstado()
    {
        try {
            $user = Auth::user();
            if ($user) {
                User::where('id_usuario', $user->id_usuario)->update(['id_estado' => 1]);
                Log::info('Estado limpiado para usuario: ' . $user->id_usuario);
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error al limpiar estado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al limpiar estado'], 500);
        }
    }

    /**
     * Obtiene los puntos diarios del usuario autenticado
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerPuntosDiarios()
    {
        $user = Auth::user();
        $puntosDiarios = $user->puntos_diarios ?? 0;
        
        return response()->json([
            'puntos_diarios' => $puntosDiarios
        ]);
    }
}
