<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\TipoProducto;

class ProductosAdminController extends Controller
{
    public function index()
    {
        $productos = Producto::with('tipoProducto')->get();
        $tiposProducto = TipoProducto::all();
        return view('admin.productos.index', compact('productos', 'tiposProducto'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'precio' => str_replace(',', '.', $request->precio), // Convierte comas a puntos
        ]);

        $request->validate([
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string|min:10|max:500',
            'precio' => 'required|regex:/^\d{1,6}(\.\d{1,2})?$/|max:1000000',
            'tipo_valor' => 'required|in:euros,puntos',
            'id_tipo_producto' => 'required|exists:tipo_producto,id_tipo_producto',
        ]);

        try {
            Producto::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'tipo_valor' => $request->tipo_valor,
                'id_tipo_producto' => $request->id_tipo_producto,
            ]);

            return redirect()->route('admin.productos.index')->with('success', 'Producto creado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->merge([
            'precio' => str_replace(',', '.', $request->precio), // Convierte comas a puntos
        ]);

        $request->validate([
            'titulo' => 'required|string|max:100',
            'descripcion' => 'required|string|min:10|max:500',
            'precio' => 'required|regex:/^\d{1,6}(\.\d{1,2})?$/|max:1000000',
            'tipo_valor' => 'required|in:euros,puntos',
            'id_tipo_producto' => 'required|exists:tipo_producto,id_tipo_producto',
        ]);

        try {
            $producto = Producto::findOrFail($id);
            $producto->update([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
                'tipo_valor' => $request->tipo_valor,
                'id_tipo_producto' => $request->id_tipo_producto,
            ]);

            return redirect()->route('admin.productos.index')->with('update', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();

            return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }
}