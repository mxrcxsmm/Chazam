<!-- filepath: c:\wamp64\www\DAW2\MP12\Chazam\Chazam\resources\views\tienda\comprar.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
    <title>Comprar Producto</title>
</head>

<body>
    <div class="main-content">
        <h1>Comprar Producto</h1>
        <div class="producto-detalle">
            <h2>{{ $producto->titulo }}</h2>
            <p>{{ $producto->descripcion }}</p>
            <div class="precio">
                <span>{{ $producto->precio }} €</span>
            </div>
            <form action="{{ route('producto.checkout', ['id' => $producto->id_producto]) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Comprar con PayPal</button>
            </form>
        </div>
    </div>
</body>

</html>