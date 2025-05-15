<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;

class ReporteAdminController extends Controller
{
    public function index()
    {
        // Obtener todos los reportes
        $reportes = Reporte::with(['reportador', 'reportado'])->get();

        // Marcar todos los reportes como leídos
        Reporte::where('leido', false)->update(['leido' => true]);

        // Contar reportes no leídos (después de marcarlos como leídos, será 0)
        $nuevosReportes = Reporte::where('leido', false)->count();

        return view('admin.reportes.index', compact('reportes', 'nuevosReportes'));
    }

    public function destroy($id_reporte)
    {
        try {
            // Buscar el reporte por su ID y eliminarlo
            $reporte = Reporte::findOrFail($id_reporte);
            $reporte->delete();

            return redirect()->route('admin.reportes.index')->with('success', 'Reporte eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.reportes.index')->with('error', 'Error al eliminar el reporte: ' . $e->getMessage());
        }
    }

    public function contarNuevos()
    {
        // Contar reportes no leídos
        $nuevosReportes = Reporte::where('leido', false)->count();

        return response()->json(['nuevos' => $nuevosReportes]);
    }
}