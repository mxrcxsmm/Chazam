<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

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
     * Procesa el pago con PayPal Sandbox.
     */
    public function checkout(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        // Configuración de PayPal Sandbox
        $paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        $businessEmail = "sandbox-business-email@example.com"; // Cambia esto por tu correo de sandbox
        $returnUrl = route('tienda.index'); // URL a la que redirige después del pago
        $cancelUrl = route('producto.comprar', ['id' => $producto->id_producto]); // URL si se cancela el pago

        // Redirección a PayPal con los parámetros necesarios
        return redirect()->away("$paypalUrl?cmd=_xclick&business=$businessEmail&item_name={$producto->titulo}&amount={$producto->precio}&currency_code=EUR&return=$returnUrl&cancel_return=$cancelUrl");
    }
}
