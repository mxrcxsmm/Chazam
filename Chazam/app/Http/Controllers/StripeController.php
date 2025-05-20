<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Producto;
use App\Models\Pago;
use App\Models\Rol; // Modelo para la tabla roles
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
                $user->id_rol = $premiumRole->id_rol; // Asignar el rol Premium
                $user->save();
            }
        } elseif ($producto->id_tipo_producto == 4) { // Compra de puntos
            // Sumar los puntos del producto al perfil del usuario
            if (is_numeric($producto->puntos)) {
                $user->puntos += $producto->puntos;
                $user->save();
            } else {
                Log::error('El producto no tiene puntos válidos', ['producto_id' => $producto->id_producto]);
            }
        } elseif ($producto->id_tipo_producto == 2) { // Combo: Suscripción + Puntos
            $premiumRole = Rol::where('nom_rol', 'Premium')->first(); // Buscar el rol Premium
            if ($premiumRole) {
                $user->id_rol = $premiumRole->id_rol; // Asignar el rol Premium
            }

            // Sumar los puntos del producto al perfil del usuario
            if (is_numeric($producto->puntos)) {
                $user->puntos += $producto->puntos;
            } else {
                Log::error('El producto no tiene puntos válidos', ['producto_id' => $producto->id_producto]);
            }

            $user->save(); // Guardar los cambios en el usuario
        }

        return view('stripe.success', compact('producto'));
    }

    public function cancel()
    {
        return view('stripe.cancel');
    }

    public function comprarConPuntos(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $user = Auth::user();

        // Verificar si el producto requiere puntos
        if (!is_numeric($producto->puntos)) {
            return response()->json(['error' => 'El producto no tiene un valor de puntos válido.'], 400);
        }

        // Verificar si el usuario tiene suficientes puntos
        if ($user->puntos < $producto->puntos) {
            return response()->json([
                'error' => 'No tienes suficientes puntos para realizar esta compra.',
            ], 400);
        }

        // Descontar los puntos del usuario
        $user->puntos -= $producto->puntos;
        $user->save();

        // Registrar el pago en la base de datos
        Pago::create([
            'id_comprador' => Auth::id(),
            'id_producto' => $producto->id_producto,
            'fecha_pago' => now(),
        ]);

        // Manejar lógica según el tipo de producto
        if ($producto->id_tipo_producto == 1) { // Suscripción Miembro
            $premiumRole = Rol::where('nom_rol', 'Miembro')->first(); // Buscar el rol Premium
            if ($premiumRole) {
                $user->id_rol = $premiumRole->id_rol; // Asignar el rol Premium
                $user->save();
            }
        }

        return response()->json([
            'success' => 'Compra realizada con éxito.',
        ]);
    }

    public function donar(Request $request)
    {
        $cantidad = $request->donacion;

        // Si selecciona "personalizado", usar la cantidad personalizada
        if ($cantidad === 'personalizado') {
            $cantidad = $request->cantidad_personalizada;
        }

        // Validar que la cantidad sea válida
        if (!is_numeric($cantidad) || $cantidad <= 0) {
            return redirect()->back()->with('error', 'Por favor, introduce una cantidad válida.');
        }

        $id_producto = $request->id_producto; // Obtener el ID del producto de donaciones

        // Configurar Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        // Crear una sesión de Stripe Checkout
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Donación',
                    ],
                    'unit_amount' => $cantidad * 100, // Convertir a céntimos
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.donation.success') . '?cantidad=' . $cantidad . '&id_producto=' . $id_producto,
            'cancel_url' => route('stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function donationSuccess(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para completar la donación.');
        }

        $user = Auth::user();
        $cantidad = $request->query('cantidad'); // Obtener la cantidad desde la URL
        $id_producto = $request->query('id_producto'); // Obtener el ID del producto desde la URL

        // Validar que la cantidad no sea nula o inválida
        if (!is_numeric($cantidad) || $cantidad <= 0) {
            return redirect()->route('tienda.index')->with('error', 'Hubo un problema al procesar tu donación.');
        }

        // Registrar la donación en la tabla de pagos
        Pago::create([
            'id_comprador' => Auth::id(), // ID del usuario autenticado
            'id_producto' => $id_producto, // ID del producto de donaciones
            'fecha_pago' => now(),
            'cantidad' => $cantidad, // Cantidad donada
        ]);

        $mensaje = '¡Gracias por tu donación de ' . $cantidad . '€!';
        return view('stripe.success', compact('mensaje'));
    }
}
