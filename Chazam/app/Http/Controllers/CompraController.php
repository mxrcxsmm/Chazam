<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Producto;
use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{
    /**
     * Muestra la pantalla de compra para un producto específico.
     */
    public function show($id)
    {
        $producto = Producto::findOrFail($id); // Obtiene el producto por ID
        return view('tienda.compra', compact('producto'));
    }

    /**
     * Muestra el historial de compras del usuario autenticado.
     */
    public function historial()
    {
        $user = Auth::user();
        $compras = Pago::with('producto')
            ->where('id_comprador', $user->id_usuario)
            ->orderByDesc('fecha_pago')
            ->get();

        return view('tienda.historial', compact('compras', 'user'));
    }

    /**
     * Descarga la factura de una compra específica.
     */
    public function descargarFactura($pagoId)
    {
        $user = Auth::user();
        $compra = Pago::with('producto')->where('id_pago', $pagoId)
            ->where('id_comprador', $user->id_usuario)
            ->firstOrFail();

        $pdf = Pdf::loadView('tienda.factura', compact('compra', 'user'));
        return $pdf->download('factura_'.$compra->id_pago.'.pdf');
    }

    /**
     * Filtra las compras del usuario autenticado mediante AJAX.
     */
    public function filtrarAjax(Request $request)
    {
        $user = Auth::user();
        $query = Pago::with('producto')
            ->where('id_comprador', $user->id_usuario);

        if ($request->filled('producto')) {
            $query->whereHas('producto', function($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->producto . '%');
            });
        }

        if ($request->filled('fecha_pago')) {
            $query->whereDate('fecha_pago', $request->fecha_pago);
        }

        $compras = $query->orderByDesc('fecha_pago')->get();

        // Renderiza solo el tbody de la tabla desde tienda/tabla_compras.blade.php
        $html = view('tienda.tabla_compras', compact('compras', 'user'))->render();

        return response()->json(['html' => $html]);
    }
}
