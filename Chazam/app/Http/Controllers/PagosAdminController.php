<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\User;
use App\Models\Producto;

class PagosAdminController extends Controller
{
    /**
     * Muestra la lista de pagos realizados por los usuarios.
     */
    public function index()
    {
        $pagos = Pago::with(['comprador', 'producto'])->orderBy('fecha_pago', 'desc')->get();
        return view('admin.pagos.index', compact('pagos'));
    }

    public function filtrar(Request $request)
    {
        $query = Pago::with(['comprador', 'producto']);

        // Filtro por ID de pago
        if ($request->filled('id_pago')) {
            $query->where('id_pago', $request->id_pago);
        }

        // Filtro por nombre de comprador
        if ($request->filled('comprador')) {
            $query->whereHas('comprador', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->comprador . '%');
            });
        }

        // Filtro por nombre de producto
        if ($request->filled('producto')) {
            $query->whereHas('producto', function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->producto . '%');
            });
        }

        // Filtro por fecha de pago
        if ($request->filled('fecha_pago')) {
            $query->whereDate('fecha_pago', $request->fecha_pago);
        }

        // Obtener los resultados filtrados
        $pagos = $query->orderBy('fecha_pago', 'desc')->get();

        // Verificar si hay resultados
        if ($pagos->isEmpty()) {
            return response()->json([], 200); // Retornar un array vacío si no hay resultados
        }

        // Formatear los resultados para enviarlos al frontend
        $formattedPagos = $pagos->map(function ($pago) {
            return [
                'id_pago' => $pago->id_pago,
                'comprador' => $pago->comprador ? $pago->comprador->username : 'Usuario eliminado',
                'producto' => $pago->producto ? $pago->producto->titulo : 'Producto eliminado',
                'fecha_pago' => $pago->fecha_pago->format('Y-m-d H:i:s'), // Formatear fecha
            ];
        });

        return response()->json($formattedPagos);
    }
}
