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
        <div class="chats-list">
            <!-- Chat individual -->
            <div class="chat-item active">
                <div class="chat-avatar">
                    <img src="{{ asset('images/avatar-default.png') }}" alt="Avatar">
                </div>
                <div class="chat-info">
                    <div class="chat-header">
                        <h3>Usuario 1</h3>
                        <span class="time">17:22</span>
                    </div>
                    <p class="last-message">Último mensaje enviado...</p>
                </div>
            </div>
            <!-- Más chats aquí -->
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <div class="chat-contact">
                <img src="{{ asset('images/avatar-default.png') }}" alt="Contact Avatar">
                <div class="contact-info">
                    <h3>Usuario 1</h3>
                    <p>en línea</p>
                </div>
            </div>
            <div class="chat-actions">
                <i class="fas fa-search"></i>
                <i class="fas fa-ellipsis-v options-toggle"></i>
            </div>
        </div>

        <div class="messages-container">
            <!-- Los mensajes irán aquí -->
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