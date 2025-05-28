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

    <!-- Añade esto justo después de <body> -->
    <button id="sidebarToggle" class="btn d-md-none"
        style="margin: 20px 0 0 20px;">
        <i class="bi bi-list" style="font-size:2rem;"></i>
    </button>
    <!-- Overlay para cerrar sidebar tocando fuera -->
    <div id="sidebarOverlay" class="d-md-none"
        style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:2000;">
    </div>

    <div class="d-flex">
        <!-- Sidebar fijo a la izquierda -->
        <aside id="sidebar" class="sidebar" style="background-color: {{ $personalizacion->sidebar }}">
            <div class="text-center mb-4">
                <div id="sidebarAvatar"
                    class="marco-externo sidebar-avatar marco-glow {{ $personalizacion->rotacion ? 'marco-rotate' : '' }}"
                    style="
                        background-image: url('{{ asset('img/bordes/' . $personalizacion->marco) }}');
                        @if ($personalizacion->brillo) --glow-color: {{ $personalizacion->brillo }}; @endif
                    ">
                    @if (isset($user->imagen_perfil) && $user->imagen_perfil)
                        <img src="{{ asset($user->imagen_perfil) }}" alt="Foto de perfil" class="avatar-img">
                    @else
                        <i class="bi bi-person-circle fs-2"></i>
                    @endif
                </div>
                <div>{{ $username ?? 'Usuario' }}</div>
                <div class="small">{{ $nombre_completo ?? '' }}</div>
            </div>

            <div class="text-center mb-3">
                <div class="text-warning fw-semibold mb-2">
                    <i class="bi bi-fire me-1"></i>
                    Racha: {{ $racha ?? 0 }} días
                </div>
                <div class="text-success fw-semibold" id="userPuntos">
                    <i class="bi bi-star-fill me-1"></i>
                    {{ $puntos ?? 0 }} pts
                </div>
            </div>

            <hr class="border-light">

            <nav>
                <ul class="list-unstyled">
                    <li class="mb-4"><a href="{{ route('perfil.personalizacion') }}"
                            class="text-white text-decoration-none">Mis datos</a></li>
                    <li class="mb-4"><a href="{{ route('perfil.vista') }}"
                            class="text-white text-decoration-none">Personalizar</a></li>
                    <li class="mb-4"><a href="{{ route('tienda.index') }}"
                            class="text-white text-decoration-none">Tienda</a></li>
                    <li class="mb-4"><a href="{{ route('retos.guide') }}"
                            class="text-white text-decoration-none">Volver</a></li>
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
    <script src="{{ asset('js/sidebar.js') }}"></script>
    @stack('scripts')
</body>

</html>
