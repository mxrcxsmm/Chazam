@extends('layout.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Chat de Amigos</div>

                <div class="card-body">
                    <div class="chat-container">
                        <!-- Aquí irá el contenido del chat -->
                        <p>Bienvenido al chat de amigos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Sidebar de amigos -->
<div id="sidebar2" class="position-fixed bottom-0 end-0 bg-purple text-white p-4" style="width: 260px; height: 92.5vh; z-index: 1040;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Amigos</h5>
    </div>
    <ul class="list-unstyled friends-list">
        <li class="titulo_amigos">Lista de Amigos</li>
        <div class="friends-container">
            <!-- Lista de amigos se cargará dinámicamente aquí -->
        </div>
        
        <button type="button" class="btn btn-warning w-100 rounded-pill add-friend-btn mt-3">
            Añadir Amigo
            <span class="triangle"></span>
            <span class="triangle tight"></span>
        </button>
    </ul>
</div>

<style>
.main-container {
    display: flex;
    height: 92.5vh;
    padding: 20px;
}

.center-container {
    flex: 1;
    margin-right: 260px; /* Mismo ancho que el sidebar */
}

.chat-container {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    height: 100%;
    display: flex;
    flex-direction: column;
    padding: 20px;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 20px;
}

.input-container {
    display: flex;
    gap: 10px;
}

#message-input {
    background-color: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
}

.send-button {
    background-color: #9147ff;
    border: none;
}

.send-button:hover {
    background-color: #7a30dd;
}

.bg-purple {
    background-color: #1a1a1a;
}

.friends-list {
    height: 100%;
    overflow-y: auto;
}

.titulo_amigos {
    font-size: 1.5em;
    font-weight: bold;
    margin-bottom: 15px;
    color: #9147ff;
}

.friends-container {
    margin-bottom: 15px;
}

.add-friend-btn {
    position: relative;
    background-color: #9147ff;
    color: white;
    border: none;
    transition: all 0.3s ease;
}

.add-friend-btn:hover {
    background-color: #7a30dd;
}

.triangle {
    position: absolute;
    right: 10px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid white;
    transition: transform 0.3s ease;
}

.triangle.tight {
    right: 12px;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid rgba(255, 255, 255, 0.5);
}

.add-friend-btn:hover .triangle {
    transform: translateY(2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aquí irá la lógica del chat
    const messageInput = document.getElementById('message-input');
    const sendButton = document.querySelector('.send-button');

    sendButton.addEventListener('click', function() {
        const message = messageInput.value.trim();
        if (message) {
            // Aquí irá la lógica para enviar mensajes
            messageInput.value = '';
        }
    });

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendButton.click();
        }
    });
});
</script>
