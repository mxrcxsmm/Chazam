@include('layout.chatsHeader')

<div class="main-container">
    <div class="chat-sidebar">
        <!-- Header del sidebar -->
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

        <!-- Barra de búsqueda -->
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Busca un chat o inicia uno nuevo">
            </div>
        </div>

        <!-- Lista de chats -->
        <div class="chats-list" id="chats-list">
            <!-- Los chats se cargarán aquí dinámicamente -->
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <div class="chat-contact">
                <img id="chat-contact-img" src="{{ asset('/img/profile_img/avatar-default.png') }}" alt="Contact Avatar">
                <div class="contact-info">
                    <h3 id="chat-contact-name">Usuario</h3>
                    <p id="chat-contact-status">Estado</p>
                </div>
            </div>
            <div class="chat-actions">
                <i class="fas fa-search"></i>
                <i class="fas fa-ellipsis-v options-toggle"></i>
            </div>
        </div>

        <div class="messages-container" id="messages-container">
            <!-- Los mensajes se cargarán aquí dinámicamente -->
        </div>

        <div class="message-input-container">
            <i class="far fa-smile"></i>
            <input type="text" placeholder="Escribe un mensaje aquí">
            <i class="fas fa-paper-plane"></i>
        </div>
    </div>

    <div class="options-sidebar">
        <div class="options-header">
            <i class="fas fa-times close-options"></i>
            <h3>Opciones</h3>
        </div>
        <div class="options-content">
            <div class="option-item">
                <i class="fas fa-user"></i>
                <span>Personalizar</span>
            </div>
            <div class="option-item">
                <i class="fas fa-users"></i>
                <span>Amigos</span>
            </div>
            <div class="option-item">
                <i class="fas fa-flag"></i>
                <span>Reportar</span>
            </div>
            <div class="option-item">
                <i class="fas fa-coins"></i>
                <span>Comprar puntos</span>
            </div>
            <div class="option-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </div>
        </div>
    </div>
</div>

<!-- Añadir Font Awesome y el CSS personalizado -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/chatamig.css') }}">

<!-- Añadir el JavaScript personalizado -->
<script src="{{ asset('js/chatamig.js') }}"></script>

<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

<emoji-picker style="position: absolute; bottom: 60px; left: 20px; display: none;"></emoji-picker>

<div id="chats-loader" style="text-align:center; padding: 20px;">
    <i class="fas fa-spinner fa-spin"></i> Cargando chats...
</div>

<!-- Modal de Solicitudes de Amistad -->
<div class="modal fade" id="solicitudesModal" tabindex="-1" aria-labelledby="solicitudesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:#9147ff; color:#fff;">
                <h5 class="modal-title" id="solicitudesModalLabel">Solicitudes de Amistad</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="solicitudesContainer">
                <div id="noSolicitudes" style="display:none;">No tienes solicitudes pendientes</div>
                <!-- Las solicitudes se cargarán aquí con AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Estilos para las solicitudes -->
<style>
.solicitud-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}
.solicitud-info {
    display: flex;
    align-items: center;
    gap: 10px;
}
.solicitud-username {
    font-weight: bold;
    font-size: 1rem;
}
.solicitud-actions .btn {
    margin-left: 5px;
}
.btn-icon {
    background: none;
    border: none;
    color: #9147ff;
    font-size: 1.3rem;
    position: relative;
    padding: 0.3rem 0.5rem;
    transition: color 0.2s;
}
.btn-icon:hover {
    color: #fff;
    background: #9147ff33;
    border-radius: 50%;
}
</style>