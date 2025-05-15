<!-- filepath: c:\wamp64\www\DAW2\MP12\Chazam\Chazam\resources\views\tienda\index.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/tienda.js') }}"></script>
    <title>Tienda</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo"> Chazam
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                </ul>
                <div class="d-flex align-items-center ms-3">
                    <span class="puntos-text me-2">Puntos: {{ Auth::user()->puntos ?? 0 }}</span>
                    <i class="fas fa-coins puntos-icon"></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenedor principal -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>User</h3>
            <ul class="categorias">
                @foreach ($categorias as $categoria)
                    <li><a href="#categoria-{{ $categoria->id_tipo_producto }}">{{ $categoria->tipo_producto }}</a></li>
                @endforeach
                <li><a href="{{ route('retos.guide') }}">Volver</a></li>
            </ul>
        </div>

        <!-- Contenido principal -->
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
                                        @if ($producto->tipo_valor == 'puntos')
                                            <span>{{ number_format($producto->precio, 0, '', '.') }}</span>
                                            <span>Puntos</span>
                                        @else
                                            <span>{{ $producto->precio }}</span>
                                            <span>€</span>
                                        @endif
                                    </div>
                                </a>
                                @if ($producto->tipo_valor == 'puntos')
                                    <button class="btn btn-primary comprar-con-puntos"
                                        data-producto-id="{{ $producto->id_producto }}">
                                        Comprar con puntos
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="donaciones-section">
                <form action="{{ route('stripe.donar') }}" method="POST" class="donaciones-form">
                    @csrf
                    <input type="hidden" name="id_producto" value="4"> <!-- ID del producto de donaciones -->

                    <label for="donacion">Selecciona una cantidad:</label>
                    <select name="donacion" id="donacion" class="form-select">
                        <option value="1">1€</option>
                        <option value="2">2€</option>
                        <option value="5">5€</option>
                        <option value="10">10€</option>
                        <option value="20">20€</option>
                        <option value="50">50€</option>
                        <option value="100">100€</option>
                        <option value="personalizado">Cantidad personalizada</option>
                    </select>

                    <div id="personalizado-container" style="display: none; margin-top: 10px;">
                        <label for="cantidad-personalizada">Introduce tu cantidad:</label>
                        <input type="number" name="cantidad_personalizada" id="cantidad-personalizada"
                            class="form-control" min="1" placeholder="Cantidad en €">
                    </div>

                    <button type="submit" class="btn btn-success mt-3">Donar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('donacion').addEventListener('change', function() {
            const personalizadoContainer = document.getElementById('personalizado-container');
            if (this.value === 'personalizado') {
                personalizadoContainer.style.display = 'block';
            } else {
                personalizadoContainer.style.display = 'none';
            }
        });
    </script>
</body>

</html>
