@include('layout.chatsHeader')

<!-- Meta tags optimizados -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-id" content="{{ Auth::id() }}">
<meta name="user-img" content="{{ Auth::user()->img
    ? asset('img/profile_img/' . basename(Auth::user()->img))
    : asset('img/profile_img/avatar-default.png') }}">

<!-- Configuración JS global -->
<script>
  window.CHAT_CONFIG = {
    defaultAvatar: "{{ asset('img/profile_img/avatar-default.png') }}",
    chatsUrl: "{{ url('/user/chats') }}",
    messagesUrl: function(chatId) { return `/user/chat/${chatId}/messages`; },
    sendUrl:     function(chatId) { return `/user/chat/${chatId}/send`; },
    pollingInterval: 4000,
    headerRefreshInterval: 30000,
    solicitudesInterval: 60000,
    smartPolling: {
      enabled: true,
      idleTimeout: 300000,
      idleInterval: 120000,
      activeInterval: 4000
    }
  };
</script>

<!-- Menú lateral hamburguesa (solo móvil) -->
<div id="mainMenuSidebar" class="main-menu-sidebar">
    <button class="close-sidebar" id="closeMainMenuSidebar">&times;</button>
    <hr>
    <div class="sidebar-section">
        <h5 class="text-center" style="color:#9147ff;">Tus chats</h5>
        <div class="chats-list" id="chats-list-sidebar"></div>
    </div>
    <hr>
    <div class="sidebar-section text-center">
        <a href="{{ route('user.momentms') }}" class="btn btn-momentms" style="background:#9147ff;color:#fff;">
            <i class="fas fa-bolt"></i> Momentms
        </a>
    </div>
</div>

<div class="main-container">
    <!-- Sidebar optimizado -->
    <div class="chat-sidebar">
        <div class="sidebar-header d-flex justify-content-between align-items-center">
            <div class="header-titles">
                <h2>Chats</h2>
                <a href="{{ route('user.momentms') }}" class="momentms-btn">Momentms</a>
            </div>
            <div class="header-icons d-flex align-items-center gap-3">
                <button id="btnSolicitudesPendientes" class="btn btn-icon position-relative" title="Solicitudes de amistad">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle p-1" id="solicitudesCount" style="font-size:0.7rem;">0</span>
                </button>
                <i class="fas fa-edit"></i>
                <i class="fas fa-ellipsis-v"></i>
            </div>
        </div>

        <!-- Lista de chats optimizada -->
        <div class="chats-list" id="chats-list"></div>
    </div>

    <!-- Chat principal optimizado -->
    <div class="chat-main">
        <div class="chat-header">
            <div class="chat-contact d-flex align-items-center">
                <!-- Hamburguesa solo en móvil -->
                <button id="sidebarToggle" class="btn btn-icon d-md-none me-2" type="button" style="font-size:1.5rem;">
                    <i class="fas fa-bars"></i>
                </button>
                <div id="chat-contact-avatar">
                    <img
                      id="chat-contact-img"
                      src="{{ Auth::user()->img
                        ? asset('img/profile_img/' . basename(Auth::user()->img))
                        : asset('img/profile_img/avatar-default.png') }}"
                      alt="Contact Avatar"
                      class="rounded-circle"
                      style="width:40px; height:40px; object-fit:cover;"
                    />
                </div>
                <div class="contact-info ms-2">
                    <h3 id="chat-contact-name">Usuario</h3>
                    <p id="chat-contact-status">en línea</p>
                </div>
            </div>
            <div class="chat-actions">
                <i class="fas fa-search"></i>
                <i class="fas fa-ellipsis-v options-toggle"></i>
            </div>
        </div>

        <div class="messages-container" id="messages-container"></div>

        <div class="message-input-container">
            <i class="far fa-smile"></i>
            <input type="text" placeholder="Escribe un mensaje aquí">
            <i class="fas fa-paper-plane"></i>
        </div>
    </div>

    <!-- Sidebar de opciones optimizado -->
    <div class="options-sidebar">
        <div class="options-header">
            <h3>Opciones</h3>
            <button class="close-options"><i class="fas fa-times"></i></button>
        </div>
        <div class="options-content">
            <div class="option-item">
                <button class="btn btn-warning report-user-btn">
                    <i class="fas fa-flag"></i> Reportar usuario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Solicitudes optimizado -->
<div class="modal fade" id="solicitudesModal" tabindex="-1" aria-labelledby="solicitudesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#9147ff; color:#fff;">
                <h5 class="modal-title" id="solicitudesModalLabel">Solicitudes de Amistad</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="solicitudesContainer">
                <div id="noSolicitudes" style="display:none;">No tienes solicitudes pendientes</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- Recursos externos optimizados -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/chatamig.css') }}">

<!-- Componentes adicionales -->
<emoji-picker style="position: absolute; bottom: 60px; left: 20px; display: none;"></emoji-picker>

<div id="chats-loader" style="text-align:center; padding: 20px;">
    <i class="fas fa-spinner fa-spin"></i> Cargando chats...
</div>

<!-- Estilos específicos -->
<style>
  .solicitud-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee; }
  .solicitud-info { display: flex; align-items: center; gap: 10px; }
  .solicitud-username { font-weight: bold; font-size: 1rem; }
  .solicitud-actions .btn { margin-left: 5px; }
  .btn-icon {
    background: none; border: none; color: #9147ff; font-size: 1.3rem;
    position: relative; padding: 0.3rem 0.5rem; transition: color 0.2s;
  }
  .btn-icon:hover {
    color: #fff; background: #9147ff33; border-radius: 50%;
  }
</style>

<!-- Scripts -->
<script src="{{ asset('js/chatamig.js') }}"></script>
<script src="{{ asset('js/hamburgerAmig.js') }}"></script>
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
