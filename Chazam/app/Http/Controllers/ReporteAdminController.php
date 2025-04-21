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
        return view('admin.reportes.index', compact('reportes'));
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
}