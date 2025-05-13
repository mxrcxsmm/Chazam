<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $solicitud = Solicitud::where(function($query) use ($id_usuario) {
            $query->where('id_emisor', Auth::user()->id_usuario)
                  ->where('id_receptor', $id_usuario);
        })->orWhere(function($query) use ($id_usuario) {
            $query->where('id_emisor', $id_usuario)
                  ->where('id_receptor', Auth::user()->id_usuario);
        })->first();

        if ($solicitud) {
            return response()->json([
                'estado' => $solicitud->estado
            ]);
        }

        return response()->json([
            'estado' => null
        ]);
    }
} 