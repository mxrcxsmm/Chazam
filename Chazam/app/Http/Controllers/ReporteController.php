<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    /**
     * Crear un nuevo reporte
     */
    public function crear(Request $request)
    {
        try {
            // Validar que el usuario no se reporte a sí mismo
            if ($request->id_reportado == Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes reportarte a ti mismo'
                ], 400);
            }

            // Validar que el usuario reportado existe y es el mismo que se intentó reportar
            $usuarioReportado = \App\Models\User::find($request->id_reportado);
            if (!$usuarioReportado) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario reportado no existe'
                ], 404);
            }

            // Crear el reporte
            $reporte = new Reporte();
            $reporte->titulo = $request->titulo;
            $reporte->descripcion = $request->descripcion;
            $reporte->id_reportador = Auth::id();
            $reporte->id_reportado = $request->id_reportado;
            $reporte->save();

            return response()->json([
                'success' => true,
                'message' => 'Reporte enviado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el reporte: ' . $e->getMessage()
            ], 500);
        }
    }
} 