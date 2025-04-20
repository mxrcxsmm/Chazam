@include('layout.chatsHeader')

<!-- Añadir Font Awesome y el CSS personalizado -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/chatamig.css') }}">

<div class="main-container">
    <div class="chat-sidebar">
        <!-- Header del sidebar -->
        <div class="sidebar-header">
            <h2>Chats</h2>
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
                    <img src="{{ asset('IMG/default-avatar.png') }}" alt="Avatar">
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
                <img src="{{ asset('IMG/default-avatar.png') }}" alt="Contact Avatar">
                <div class="contact-info">
                    <h3>Usuario 1</h3>
                    <p>en línea</p>
                </div>
            </div>
            <div class="chat-actions">
                <i class="fas fa-search"></i>
                <i class="fas fa-ellipsis-v"></i>
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
</div>

<!-- Añadir el JavaScript personalizado -->
<script src="{{ asset('js/chatamig.js') }}"></script>

<style>
.main-container {
    display: flex;
    height: 92.5vh;
    background-color: #1a1a1a;
}

.chat-sidebar {
    width: 350px;
    border-right: 1px solid #2d2d2d;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #1a1a1a;
    border-bottom: 1px solid #2d2d2d;
}

.sidebar-header h2 {
    color: white;
    font-size: 1.2rem;
    margin: 0;
}

.header-icons {
    display: flex;
    gap: 20px;
    color: #9147ff;
}

.search-container {
    padding: 8px 16px;
    background-color: #1a1a1a;
}

.search-box {
    background-color: #2d2d2d;
    border-radius: 8px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-box input {
    background: none;
    border: none;
    color: white;
    width: 100%;
    outline: none;
}

.search-box i {
    color: #9147ff;
}

.chats-list {
    flex: 1;
    overflow-y: auto;
}

.chat-item {
    display: flex;
    padding: 12px 16px;
    gap: 12px;
    cursor: pointer;
    border-bottom: 1px solid #2d2d2d;
}

.chat-item:hover {
    background-color: #2d2d2d;
}

.chat-item.active {
    background-color: #2d2d2d;
}

.chat-avatar img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
}

.chat-info {
    flex: 1;
}

.chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.chat-header h3 {
    color: white;
    font-size: 1rem;
    margin: 0;
}

.time {
    color: #9147ff;
    font-size: 0.8rem;
}

.last-message {
    color: #888;
    font-size: 0.9rem;
    margin: 0;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #1a1a1a;
    border-bottom: 1px solid #2d2d2d;
}

.chat-contact {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-contact img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.contact-info h3 {
    color: white;
    margin: 0;
    font-size: 1rem;
}

.contact-info p {
    color: #9147ff;
    margin: 0;
    font-size: 0.8rem;
}

.chat-actions {
    display: flex;
    gap: 20px;
    color: #9147ff;
}

.messages-container {
    flex: 1;
    background-color: #1a1a1a;
    overflow-y: auto;
    padding: 20px;
}

.message-input-container {
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: #1a1a1a;
    border-top: 1px solid #2d2d2d;
}

.message-input-container input {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background-color: #2d2d2d;
    color: white;
    outline: none;
}

.message-input-container i {
    color: #9147ff;
    font-size: 1.2rem;
    cursor: pointer;
}

/* Estilos para la barra de desplazamiento */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #1a1a1a;
}

::-webkit-scrollbar-thumb {
    background: #9147ff;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #7a30dd;
}
</style>
