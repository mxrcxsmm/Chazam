<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Chat;
use App\Models\ChatUsuario;
use App\Models\Mensaje;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AmistadController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        
        $amistades = DB::table('solicitudes')
            ->join('users', function($join) use ($usuario) {
                $join->on('users.id_usuario', '=', DB::raw('CASE 
                    WHEN solicitudes.id_emisor = ? THEN solicitudes.id_receptor
                    ELSE solicitudes.id_emisor
                END'))
                ->whereRaw('solicitudes.estado = "aceptada"')
                ->where(function($query) use ($usuario) {
                    $query->where('solicitudes.id_emisor', $usuario->id_usuario)
                          ->orWhere('solicitudes.id_receptor', $usuario->id_usuario);
                });
            })
            ->select('users.*', 'solicitudes.id_emisor', 'solicitudes.id_receptor')
            ->setBindings([$usuario->id_usuario])
            ->get();

        // Deduplicar por par de usuarios (emisor, receptor) sin importar el orden
        $unique = [];
        foreach ($amistades as $amigo) {
            $key = min($amigo->id_emisor, $amigo->id_receptor) . '-' . max($amigo->id_emisor, $amigo->id_receptor);
            if (!isset($unique[$key])) {
                $unique[$key] = $amigo;
            }
        }

        return response()->json(array_values($unique));
    }

    public function destroy($idUsuario)
    {
        try {
            DB::beginTransaction();

            $usuario = Auth::user();
            
            // Eliminar todas las solicitudes entre ambos usuarios
            $solicitudes = Solicitud::where(function($query) use ($usuario, $idUsuario) {
                $query->where(function($q) use ($usuario, $idUsuario) {
                    $q->where('id_emisor', $usuario->id_usuario)
                      ->where('id_receptor', $idUsuario);
                })->orWhere(function($q) use ($usuario, $idUsuario) {
                    $q->where('id_emisor', $idUsuario)
                      ->where('id_receptor', $usuario->id_usuario);
                });
            })->get();

            foreach ($solicitudes as $solicitud) {
                // Eliminar SOLO los chats entre estos dos usuarios específicos
                $chats = Chat::whereHas('chatUsuarios', function($query) use ($usuario, $idUsuario) {
                    $query->where(function($q) use ($usuario, $idUsuario) {
                        $q->where('id_usuario', $usuario->id_usuario)
                          ->whereExists(function($subquery) use ($idUsuario) {
                              $subquery->select(DB::raw(1))
                                     ->from('chat_usuario as cu2')
                                     ->whereRaw('cu2.id_chat = chat_usuario.id_chat')
                                     ->where('cu2.id_usuario', $idUsuario);
                          });
                    });
                })->get();

                foreach ($chats as $chat) {
                    // Eliminar mensajes
                    Mensaje::whereIn('id_chat_usuario', function($query) use ($chat) {
                        $query->select('id_chat_usuario')
                              ->from('chat_usuario')
                              ->where('id_chat', $chat->id_chat);
                    })->delete();

                    // Eliminar relaciones chat_usuario
                    ChatUsuario::where('id_chat', $chat->id_chat)->delete();

                    // Eliminar el chat
                    $chat->delete();
                }

                // Eliminar la solicitud
                $solicitud->delete();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bloquear($idUsuario)
    {
        try {
            DB::beginTransaction();

            $usuario = Auth::user();
            
            // Encontrar TODAS las solicitudes entre ambos usuarios (en ambas direcciones)
            $solicitudes = Solicitud::where(function($query) use ($usuario, $idUsuario) {
                $query->where(function($q) use ($usuario, $idUsuario) {
                    $q->where('id_emisor', $usuario->id_usuario)
                      ->where('id_receptor', $idUsuario);
                })->orWhere(function($q) use ($usuario, $idUsuario) {
                    $q->where('id_emisor', $idUsuario)
                      ->where('id_receptor', $usuario->id_usuario);
                });
            })->get();

            foreach ($solicitudes as $solicitud) {
                $solicitud->estado = 'blockeada';
                $solicitud->save();
            }

            // Eliminar chats relacionados
            $chats = Chat::whereNull('id_reto')
                ->whereHas('chatUsuarios', function($query) use ($usuario, $idUsuario) {
                    $query->where('id_usuario', $usuario->id_usuario);
                })
                ->whereHas('chatUsuarios', function($query) use ($usuario, $idUsuario) {
                    $query->where('id_usuario', $idUsuario);
                })
                ->get();

            foreach ($chats as $chat) {
                // Eliminar mensajes
                Mensaje::whereIn('id_chat_usuario', function($query) use ($chat) {
                    $query->select('id_chat_usuario')
                          ->from('chat_usuario')
                          ->where('id_chat', $chat->id_chat);
                })->delete();

                // Eliminar relaciones chat_usuario
                ChatUsuario::where('id_chat', $chat->id_chat)->delete();

                // Eliminar el chat
                $chat->delete();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getBloqueados()
    {
        $userId = Auth::id();
        // Busca solicitudes donde el usuario actual bloqueó a otro
        $bloqueados = DB::table('solicitudes')
            ->join('users', 'users.id_usuario', '=', 'solicitudes.id_receptor')
            ->where('solicitudes.id_emisor', $userId)
            ->where('solicitudes.estado', 'blockeada')
            ->select('users.id_usuario', 'users.username', 'users.img')
            ->get();

        return response()->json($bloqueados);
    }

    public function desbloquearUsuario(Request $request, $idUsuario)
    {
        $userId = Auth::id();
        // $otroId = $request->input('id_usuario'); // Ya no necesitamos esto
        $otroId = $idUsuario; // Obtenemos el ID de la ruta

        // Borra todas las solicitudes de bloqueo entre ambos usuarios
        DB::table('solicitudes')
            ->where(function($q) use ($userId, $otroId) {
                $q->where('id_emisor', $userId)->where('id_receptor', $otroId);
            })
            ->orWhere(function($q) use ($userId, $otroId) {
                $q->where('id_emisor', $otroId)->where('id_receptor', $userId);
            })
            ->where('estado', 'blockeada')
            ->delete();

        return response()->json(['success' => true]);
    }
} 