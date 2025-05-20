@include('layout.chatsHeader')

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Momentms View JS-->
<script src="{{ asset('js/momentms/momentms-view.js') }}"></script>

<!-- Momentms View CSS -->
<link rel="stylesheet" href="{{ asset('css/momentm/momentms-view.css') }}">

<script>
    window.authUserId = {{ Auth::user()->id_usuario }};
</script>

<div class="momentms-container">
    <div class="momentms-header">
        <h1 class="gradient-text">MOMENTMS DEL DIA</h1>
        <a href="{{ route('momentms.create') }}" class="create-momentm-btn">Crear Momentm</a>
    </div>

    <div class="momentms-searchbar">
        <input type="text" id="momentmsSearchInput" placeholder="Buscar por usuario, nombre o apellido...">
        <div class="momentms-searchbar-row">
            <select id="momentmsSearchFilter">
                <option value="todos">Todos</option>
                <option value="mios">Mis Momentms</option>
                <option value="amigos">Solo amigos</option>
            </select>
            <select id="momentmsOrder">
                <option value="default" selected>Por defecto</option>
                <option value="fecha_desc">Más recientes</option>
                <option value="fecha_asc">Más antiguos</option>
                <option value="alfabetico_asc">Usuario A-Z</option>
                <option value="alfabetico_desc">Usuario Z-A</option>
            </select>
        </div>
    </div>

    <div class="momentms-grid">
        <div class="momentms-section">
            @php
                use Carbon\Carbon;
                $ahora = Carbon::now();
                $momentms24h = $momentms->filter(function($m) use ($ahora) {
                    return $m->fecha_inicio && $ahora->diffInHours($m->fecha_inicio) < 24;
                });
            @endphp
            @forelse($momentms24h as $momentm)
                <div class="momentm-card" data-momentm-id="{{ $momentm->id_historia }}">
                    <div class="momentm-preview">
                        <img src="{{ asset($momentm->img) }}" alt="Momentm de {{ $momentm->usuario->username ?? 'Usuario' }}">
                    </div>
                    <div class="momentm-info">
                        <div class="momentm-avatar">
                            <img src="{{ asset('img/profile_img/' . ($momentm->usuario->img ?? 'default.png')) }}" alt="Avatar de {{ $momentm->usuario->username ?? 'Usuario' }}">
                        </div>
                        <p class="momentm-username">{{ $momentm->usuario->id_usuario == Auth::user()->id_usuario ? 'Tú' : ($momentm->usuario->username ?? 'Usuario') }}</p>
                        <p class="momentm-time">{{ $momentm->fecha_inicio->diffForHumans() }}</p>
                        @if($momentm->usuario->id_usuario == Auth::user()->id_usuario)
                            <button class="delete-momentm-btn" data-id="{{ $momentm->id_historia }}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="no-content-message">
                    <p>No hay Momentms recientes. ¡Crea uno nuevo!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Añadir el modal al final del archivo, justo antes de cerrar el body -->
<div id="momentmModal" class="momentm-modal">
    <div class="momentm-modal-content">
        <span class="close-modal">&times;</span>
        
        <!-- Navegación -->
        <button class="nav-btn prev-btn" id="prevMomentm">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="nav-btn next-btn" id="nextMomentm">
            <i class="fas fa-chevron-right"></i>
        </button>

        <!-- Contenido del Momentm -->
        <div class="momentm-view">
            <!-- Barra de progreso -->
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            
            <div class="momentm-view-header">
                <div class="momentm-user-info">
                    <img class="momentm-user-avatar" src="" alt="Avatar">
                    <span class="momentm-user-name"></span>
                </div>
                <span class="momentm-time"></span>
            </div>
            <div class="momentm-image-container">
                <img class="momentm-full-image" src="" alt="Momentm">
            </div>
        </div>
    </div>
</div>