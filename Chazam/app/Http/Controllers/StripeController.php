<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Producto;

class StripeController extends Controller
{
    public function checkout(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        // Crear una sesión de Stripe Checkout
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $producto->titulo,
                        'description' => $producto->descripcion,
                    ],
                    'unit_amount' => $producto->precio * 100, // Convertir a céntimos
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success', ['id' => $producto->id_producto]),
            'cancel_url' => route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success($id)
    {
        $producto = Producto::findOrFail($id);

        // Aquí puedes registrar el pago en la base de datos o realizar otras acciones
        return view('stripe.success', compact('producto'));
    }

    public function cancel()
    {
        return view('stripe.cancel');
    }
}