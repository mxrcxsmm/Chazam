<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\TipoProducto;

class TiendaController extends Controller
{
    /**
     * Muestra la página de la tienda con todos los productos distribuidos por categorías.
     */
    public function index()
    {
        $categorias = TipoProducto::all(); // Obtiene todas las categorías
        $productos = Producto::all(); // Obtiene todos los productos
        return view('tienda.index', compact('categorias', 'productos'));
    }
}
