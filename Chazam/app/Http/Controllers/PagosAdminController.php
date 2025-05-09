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

    /**
     * Almacena un nuevo registro de pago.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_comprador' => 'required|exists:users,id',
            'id_producto' => 'required|exists:productos,id_producto',
            'fecha_pago' => 'required|date',
        ]);

        Pago::create([
            'id_comprador' => $request->id_comprador,
            'id_producto' => $request->id_producto,
            'fecha_pago' => $request->fecha_pago,
        ]);

        return redirect()->route('admin.pagos.index')->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Actualiza un registro de pago.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_comprador' => 'required|exists:users,id',
            'id_producto' => 'required|exists:productos,id_producto',
            'fecha_pago' => 'required|date',
        ]);

        $pago = Pago::findOrFail($id);
        $pago->update([
            'id_comprador' => $request->id_comprador,
            'id_producto' => $request->id_producto,
            'fecha_pago' => $request->fecha_pago,
        ]);

        return redirect()->route('admin.pagos.index')->with('update', 'Pago actualizado correctamente.');
    }
}