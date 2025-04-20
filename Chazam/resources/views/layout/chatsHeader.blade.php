<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Mi Aplicación')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (opcional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/retos.css') }}">
    @stack('head')
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm px-4 py-2">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <!-- Logo o nombre de la app -->
            <a href="#"><img src="{{ asset('IMG/Logo_Chazam.png') }}" alt="Logo" class="logo">Chazam</a>

            <!-- Racha, puntos y perfil -->
            <div class="d-flex align-items-center gap-4">

                <!-- Racha de días -->
                <div class="d-flex align-items-center text-warning fw-semibold">
                    <i class="bi bi-fire me-1"></i>
                    <span>Racha: {{ isset($racha) ? $racha : 0 }} días</span>
                </div>

                <!-- Puntos -->
                <div class="d-flex align-items-center text-success fw-semibold">
                    <i class="bi bi-star-fill me-1"></i>
                    <span>{{ isset($puntos) ? $puntos : 0 }} pts</span>
                </div>

                <!-- Botón para abrir el menú -->
                <button class="btn btn-outline-dark" onclick="toggleSidebar()">
                    @if(isset($imagen_perfil) && $imagen_perfil)
                        <img src="{{ $imagen_perfil }}" alt="Foto de perfil" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                    @else
                        <i class="bi bi-person-circle"></i>
                    @endif
                </button>

                <!-- Sidebar estilo perfil -->
                <div id="sidebar" class="position-fixed top-0 end-0 bg-purple text-white p-4" style="width: 260px; height: 100vh; display: none; z-index: 1050;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="text-center">
                            @if(isset($imagen_perfil) && $imagen_perfil)
                                <img src="{{ $imagen_perfil }}" alt="Foto de perfil" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <i class="bi bi-person-circle fs-2"></i>
                            @endif
                            <div>{{ isset($username) ? $username : 'Usuario' }}</div>
                            <div class="small">{{ isset($nombre_completo) ? $nombre_completo : '' }}</div>
                        </div>
                        <button class="btn btn-danger btn-sm" onclick="toggleSidebar()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="d-flex flex-column align-items-center mb-3">
                        <!-- Racha de días -->
                        <div class="d-flex align-items-center text-warning fw-semibold mb-2">
                            <i class="bi bi-fire me-1"></i>
                            <span>Racha: {{ isset($racha) ? $racha : 0 }} días</span>
                        </div>
                    
                        <!-- Puntos -->
                        <div class="d-flex align-items-center text-success fw-semibold">
                            <i class="bi bi-star-fill me-1"></i>
                            <span>{{ isset($puntos) ? $puntos : 0 }} pts</span>
                        </div>
                    </div>
                    

                    <hr class="border-light">

                    <ul class="list-unstyled">
                        <li class="mb-4"><a href="#" class="text-white text-decoration-none">Personalizar</a></li>
                        <li class="mb-4"><a href="#" class="text-white text-decoration-none">Amigos</a></li>
                        <li class="mb-4"><a href="#" class="text-white text-decoration-none">Reportar</a></li>
                        <li class="mb-4"><a href="#" class="text-white text-decoration-none">Comprar puntos</a></li>
                    </ul>

                    <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 rounded-pill">Cerrar Sesión</button>
                    </form>
                </div>

                <script>
                    function toggleSidebar() {
                        const sidebar = document.getElementById('sidebar');
                        sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
                    }
                </script>
            </div>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <div class="container py-4">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
