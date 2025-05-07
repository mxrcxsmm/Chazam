<!-- filepath: c:\wamp64\www\DAW2\MP12\Chazam\Chazam\resources\views\tienda\index.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
    <title>Tienda</title>
</head>

<body>
    <div class="sidebar">
        <h3>User</h3>
        <ul>
            @foreach ($categorias as $categoria)
                <li><a href="#categoria-{{ $categoria->id_tipo_producto }}">{{ $categoria->tipo_producto }}</a></li>
            @endforeach
            <li><a href="{{ route('retos.guide') }}">Volver</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Tienda</h1>
        @foreach ($categorias as $categoria)
            <div id="categoria-{{ $categoria->id_tipo_producto }}" class="categoria-section">
                <h2>{{ $categoria->tipo_producto }}</h2>
                <div class="productos-grid">
                    @foreach ($productos->where('id_tipo_producto', $categoria->id_tipo_producto) as $producto)
                        <div class="producto-card">
                            <a class="producto" href="{{ route('producto.comprar', ['id' => $producto->id_producto]) }}">
                                <img src="{{ asset('img/' . $producto->titulo . '.png') }}" alt="{{ $producto->titulo }}">
                                <h3>{{ $producto->titulo }}</h3>
                                <p>{{ $producto->descripcion }}</p>
                                <div class="precio">
                                    <span>{{ $producto->precio }}</span>
                                    <span>€</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>