<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Zona de Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar fijo a la izquierda -->
        <aside id="sidebar" class="sidebar">
            <div class="text-center mb-4">
                @if(isset($user->img) && $user->img)
                    <img src="{{ asset($user->img) }}" alt="Foto de perfil">
                @else
                    <i class="bi bi-person-circle fs-2"></i>
                @endif
                <div>{{ $username ?? 'Usuario' }}</div>
                <div class="small">{{ $nombre_completo ?? '' }}</div>
            </div>

            <div class="text-center mb-3">
                <div class="text-warning fw-semibold mb-2">
                    <i class="bi bi-fire me-1"></i>
                    Racha: {{ $racha ?? 0 }} días
                </div>
                <div class="text-success fw-semibold">
                    <i class="bi bi-star-fill me-1"></i>
                    {{ $puntos ?? 0 }} pts
                </div>
            </div>

            <hr class="border-light">

            <nav>
                <ul class="list-unstyled">
                    <li class="mb-4"><a href="{{ route('perfil.personalizacion') }}" class="text-white text-decoration-none">Mis datos</a></li>
                    <li class="mb-4"><a href="{{ route('perfil.vista') }}" class="text-white text-decoration-none">Perfil</a></li>
                    <li class="mb-4"><a href="{{ route('perfil.mejoras') }}" class="text-white text-decoration-none">Comprar Mejoras</a></li>
                    <li class="mb-4"><a href="{{ route('tienda.index') }}" class="text-white text-decoration-none">Tienda</a></li>
                    <li class="mb-4"><a href="{{ route('retos.guide') }}" class="text-white text-decoration-none">Volver</a></li>
                </ul>
            </nav>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger w-100 rounded-pill">Cerrar Sesión</button>
            </form>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content p-4 w-100">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
