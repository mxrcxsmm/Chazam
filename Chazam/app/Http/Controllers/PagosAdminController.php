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
        $pagos = Pago::with(['comprador', 'producto.tipoProducto'])
            ->join('productos', 'pagos.id_producto', '=', 'productos.id_producto')
            ->join('tipo_producto', 'productos.id_tipo_producto', '=', 'tipo_producto.id_tipo_producto')
            ->select('pagos.*', 'productos.precio', 'tipo_producto.tipo_producto as nombre_tipo')
            ->orderBy('fecha_pago', 'desc')
            ->get();
        return view('admin.pagos.index', compact('pagos'));
    }

    public function filtrar(Request $request)
    {
        $query = Pago::with(['comprador', 'producto.tipoProducto'])
            ->join('productos', 'pagos.id_producto', '=', 'productos.id_producto')
            ->join('tipo_producto', 'productos.id_tipo_producto', '=', 'tipo_producto.id_tipo_producto')
            ->select(
                'pagos.*',
                'productos.precio',
                'productos.titulo as producto_titulo',
                'tipo_producto.tipo_producto as nombre_tipo',
                'tipo_producto.id_tipo_producto'
            );

        // Filtro por ID de pago
        if ($request->filled('id_pago')) {
            $query->where('pagos.id_pago', $request->id_pago);
        }

        // Filtro por nombre de comprador
        if ($request->filled('comprador')) {
            $query->whereHas('comprador', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->comprador . '%');
            });
        }

        // Filtro por nombre de producto
        if ($request->filled('producto')) {
            $query->where('productos.titulo', 'like', '%' . $request->producto . '%');
        }

        // Filtro por precio
        if ($request->filled('precio')) {
            $query->where('productos.precio', 'like', '%' . $request->precio . '%');
        }

        // Filtro por tipo de producto
        if ($request->filled('tipo')) {
            $query->where('tipo_producto.tipo_producto', 'like', '%' . $request->tipo . '%');
        }

        // Filtro por fecha de pago
        if ($request->filled('fecha_pago')) {
            $query->whereDate('pagos.fecha_pago', $request->fecha_pago);
        }

        // Obtener los resultados filtrados
        $pagos = $query->orderBy('pagos.fecha_pago', 'desc')->get();

        // Verificar si hay resultados
        if ($pagos->isEmpty()) {
            return response()->json([], 200); // Retornar un array vacío si no hay resultados
        }

        // Formatear los resultados para enviarlos al frontend
        $formattedPagos = $pagos->map(function ($pago) {
            return [
                'id_pago' => $pago->id_pago,
                'usuario' => $pago->comprador ? $pago->comprador->username : 'Usuario eliminado',
                'producto' => $pago->producto_titulo ?? 'Producto eliminado',
                'cantidad' => $pago->cantidad ?? 'N/A',
                'precio' => number_format($pago->precio, 2) ?? 'N/A',
                'tipo' => $pago->nombre_tipo ?? 'N/A',
                'fecha_pago' => $pago->fecha_pago->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($formattedPagos);
    }
}
