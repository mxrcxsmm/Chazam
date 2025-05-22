<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de compra</title>
    <link rel="stylesheet" href="{{ public_path('css/facturas.css') }}">
</head>
<body>
    <div class="factura-container">
        <div class="factura-header">
            <h1>Factura de compra
            <img class="imagen" src="{{ public_path('img/logo.png') }}" alt="Logo">
            </h1>
        </div>

        <div class="factura-datos">
            <div class="factura-datos-box">
                <strong>DATOS DEL CLIENTE</strong>
                {{ $user->nombre }} {{ $user->apellido }}<br>
                {{ $user->email }}<br>
            </div>
            <div class="factura-datos-box">
                <strong>DATOS DE LA EMPRESA</strong>
                Chazam S.L.<br>
                contacto@chazam.com<br>
                Av. Mare de Déu Bellvitge 100-110, Barcelona, España
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="factura-table">
                <thead class="encabezado">
                    <tr>
                        <th>Producto</th>
                        <th class="descripcion">Descripción</th>
                        <th>Precio</th>
                        <th class="fecha_pago">Fecha de pago</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $compra->producto->titulo ?? 'Producto eliminado' }}</td>
                        <td class="descripcion">{{ $compra->producto->descripcion ?? '-' }}</td>
                        <td class="precio">
                            {{ $compra->producto->precio }} {{ $compra->producto->tipo_valor ?? '' }}
                        </td>
                        <td class="fecha_pago">{{ $compra->fecha_pago }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>