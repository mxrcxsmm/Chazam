@include('layout.chatsHeader')

<div class="main-container">
    <div class="chat-sidebar">
        <!-- Header del sidebar -->
        <div class="sidebar-header">
            <div class="header-titles">
            <h2>Chats</h2>
                <a href="{{ route('user.momentms') }}" class="momentms-btn">Momentms</a>
            </div>
            <div class="header-icons">
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
                <img id="chat-contact-img" src="{{ asset('images/avatar-default.png') }}" alt="Contact Avatar">
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