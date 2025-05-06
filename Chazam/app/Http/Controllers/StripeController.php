<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Producto;
use App\Models\Pago;
use App\Models\Rol; // Modelo para la tabla roles
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Log;

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
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para completar la compra.');
        }

        $producto = Producto::findOrFail($id);
        $user = Auth::user();

        // Registrar el pago en la base de datos
        Pago::create([
            'id_comprador' => Auth::id(), // ID del usuario autenticado
            'id_producto' => $producto->id_producto,
            'fecha_pago' => now(),
        ]);

        // Manejar lógica según el tipo de producto
        if ($producto->id_tipo_producto == 1) { // Suscripción Premium
            $premiumRole = Rol::where('nom_rol', 'Premium')->first(); // Buscar el rol Premium
            if ($premiumRole) {
                $user->id_rol = 3; // Asignar el rol Premium
                $user->save();
            }
        } elseif ($producto->id_tipo_producto == 4) { // Compra de puntos
            // Sumar los puntos del producto al perfil del usuario
            if (is_numeric($producto->puntos)) {
                $user->puntos += $producto->puntos;
                $user->save();
            } else {
                \Log::error('El producto no tiene puntos válidos', ['producto_id' => $producto->id_producto]);
            }
        }

        return view('stripe.success', compact('producto'));
    }

    public function cancel()
    {
        return view('stripe.cancel');
    }
}
