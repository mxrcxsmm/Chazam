<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reto;
use App\Models\User;
use App\Models\Chat;
use App\Models\ChatUsuario;
use App\Models\Mensaje;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
                $companero = User::where('id_estado', 5)
                    ->where('id_usuario', '!=', $usuarioActual->id_usuario)
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

        $mensaje = Mensaje::create([
            'id_chat_usuario' => $chatUsuario->id_chat_usuario,
            'contenido' => $request->contenido,
            'fecha_envio' => now()
        ]);

        return response()->json([
            'mensaje' => $mensaje,
            'usuario' => [
                'id' => $usuarioActual->id_usuario,
                'username' => $usuarioActual->username,
                'imagen' => $usuarioActual->imagen_perfil
            ]
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
            ->get();

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
                        
                        // Eliminar el chat y sus relaciones
                        $chat->delete();
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
}
