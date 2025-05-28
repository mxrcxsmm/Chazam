<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Mi Aplicación')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Three.js para VANTA FOG -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <!-- VANTA FOG -->
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.fog.min.js"></script>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/retos.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/perfil.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/userSearch.css') }}">
    @stack('head')
</head>
<body>
    <div id="vanta-bg" style="z-index: 0;"></div>
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm px-4 py-2" style="position: relative; z-index: 1;">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Logo o nombre de la app -->
            <a href="{{ route('retos.guide') }}"><img src="{{ asset('img/Logo_Chazam.png') }}" alt="Logo" class="logo">Chazam</a>

            <!-- Racha, puntos y perfil -->
            <div class="d-flex align-items-center gap-4">
                <!-- Racha de días -->
                <div class="d-flex align-items-center text-warning fw-semibold">
                    <i class="bi bi-fire me-1"></i>
                    <span>Racha: {{ isset($racha) ? $racha : 0 }} días</span>
                </div>

                <!-- Puntos -->
                <div class="d-flex align-items-center text-success fw-semibold puntos-container">
                    <i class="bi bi-star-fill me-1"></i>
                    <span id="puntos-actuales">{{ isset($puntos) ? $puntos : 0 }}</span>
                    <span>pts</span>
                </div>

                <!-- Botón de amistades -->
                <button class="btn btn-outline-dark" id="btnAmistades" type="button">
                    <i class="bi bi-people-fill"></i>
                </button>

                <!-- Botón de búsqueda de usuarios -->
                <button class="btn btn-outline-dark" id="btnBuscarUsuarios" type="button">
                    <i class="bi bi-search"></i>
                </button>

                <!-- Botón para abrir el menú -->
                <button class="btn btn-outline-dark p-0 border-0 bg-transparent" onclick="toggleSidebar()" style="width: 38px; height: 38px;">
                    <div class="marco-externo marco-glow {{ $rotacion ? 'marco-rotate' : '' }}"
                         style="
                            width: 38px;
                            height: 38px;
                            border-radius: 50%;
                            background-image: url('{{ asset('img/bordes/' . ($marco ?? 'default.svg')) }}');
                            background-size: cover;
                            background-position: center;
                            background-repeat: no-repeat;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            @if(isset($brillo))
                                --glow-color: {{ $brillo }};
                            @endif
                         ">
                        @if(isset($imagen_perfil) && $imagen_perfil)
                            <img src="{{ $imagen_perfil }}" alt="Foto" class="avatar-img" style="width: 90%; height: 90%;">
                        @else
                            <i class="bi bi-person-circle fs-5 text-white"></i>
                        @endif
                    </div>
                </button>
                

                <!-- Sidebar estilo perfil -->
                <div id="sidebar" class="position-fixed top-0 end-0 text-white p-4" style="width: 260px; height: 100vh; display: none; z-index: 1050; background-color: {{ $sidebar ?? '#4B0082' }};">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="text-center">
                            <div id="sidebarGeneral" 
                                class="marco-externo sidebar-avatar marco-glow {{ $rotacion ?? false ? 'marco-rotate' : '' }}"
                                style="
                                    background-image: url('{{ asset('img/bordes/' . ($marco ?? 'default.svg')) }}');
                                    @if(isset($brillo))
                                        --glow-color: {{ $brillo }};
                                    @endif
                                ">
                                @if(isset($imagen_perfil) && $imagen_perfil)
                                    <img src="{{ $imagen_perfil }}" alt="Foto de perfil" class="avatar-img">
                                @else
                                    <i class="bi bi-person-circle fs-2"></i>
                                @endif
                            </div>
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
                        <li class="mb-4"><a href="{{ route('perfil.personalizacion') }}" class="text-white text-decoration-none">Perfil</a></li>
                        <li class="mb-4"><a href="{{ route('user.friendchat') }}" class="text-white text-decoration-none">Amigos</a></li>
                        <li class="mb-4"><a href="{{ route('comunidades.index') }}" class="text-white text-decoration-none">Comunidades</a></li>
                        <li class="mb-4"><a href="#" class="text-white text-decoration-none">Reportar</a></li>
                        <li class="mb-4"><a href="{{route('tienda.index')}}" class="text-white text-decoration-none">Tienda</a></li>
                        <li class="mb-4"><a href="{{ route('compras.historial') }}" class="text-white text-decoration-none">Mis Compras</a></li>
                    </ul>

                    <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 rounded-pill">Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <div class="container py-4">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/estados.js') }}"></script>
    <script src="{{ asset('js/hamburger.js') }}"></script>
    <script src="{{ asset('js/userSearch.js') }}"></script>
    @stack('scripts')

    <!-- Modal de amistades -->
    <div class="modal fade" id="modalAmistades" tabindex="-1" aria-labelledby="modalAmistadesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAmistadesLabel">Mis Amistades</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="amistadesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="amistades-tab" data-bs-toggle="tab" data-bs-target="#amistades" type="button" role="tab">Amistades</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bloqueados-tab" data-bs-toggle="tab" data-bs-target="#bloqueados" type="button" role="tab">Bloqueados</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="amistades" role="tabpanel">
                            <div id="listaAmistades" class="list-group"></div>
                        </div>
                        <div class="tab-pane fade" id="bloqueados" role="tabpanel">
                            <div id="listaBloqueados" class="list-group"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de búsqueda de usuarios -->
    <div class="modal fade" id="buscarUsuariosModal" tabindex="-1" aria-labelledby="buscarUsuariosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background:#9147ff; color:#fff;">
                    <h5 class="modal-title" id="buscarUsuariosModalLabel">Buscar Usuarios</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="search-user-container">
                        <div class="search-box mb-3">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchUserInput" placeholder="Buscar por username..." class="form-control">
                        </div>
                        <div id="searchResults" class="search-results">
                            <!-- Los resultados se cargarán aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar(forceClose = false) {
            const sidebar = document.getElementById('sidebar');
            if (forceClose || sidebar.style.display === 'block') {
                sidebar.style.display = 'none';
                document.body.style.overflow = '';
            } else {
                sidebar.style.display = 'block';
                sidebar.focus();
                document.body.style.overflow = 'hidden';
            }
        }

        document.addEventListener('mousedown', function(e) {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar) return;
            if (sidebar.style.display === 'block' && !sidebar.contains(e.target)) {
                toggleSidebar(true);
            }
        });
    </script>
</body>
</html>