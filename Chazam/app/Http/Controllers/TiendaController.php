<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\TipoProducto;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;

class TiendaController extends Controller
{
    /**
     * Muestra la página de la tienda con todos los productos distribuidos por categorías.
     */
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para completar la donación.');
        }

        $user = Auth::user();

        // Obtener los IDs de los productos de compra única (id_tipo_producto = 2) que el usuario ya ha comprado
        $productosComprados = [];
        if ($user) {
            $productosComprados = $user->pagos()
                ->whereHas('producto', function ($query) {
                    $query->where('id_tipo_producto', 2); // Filtrar productos de compra única
                })
                ->pluck('id_producto')
                ->toArray();
        }

        // Obtener todas las categorías
        $categorias = TipoProducto::all();

        // Obtener todos los productos, excluyendo los de compra única ya comprados por el usuario
        $productos = Producto::whereNotIn('id_producto', $productosComprados)->get();

        return view('tienda.index', compact('categorias', 'productos'));
    }
}
