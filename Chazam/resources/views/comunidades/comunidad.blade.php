@extends('layout.chatsHeader')

@section('title', $comunidad->nombre)

@section('content')
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- CSS personalizado -->
<style>
body, html {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    background-color: #1a1a1a;
    overflow-x: hidden;
    position: relative;
}

.main-container {
    display: flex;
    width: 100%;
    height: calc(100vh - 80px);
    background-color: #1a1a1a;
    position: absolute;
    top: 80px;
    left: 0;
    right: 0;
}

/* Sidebar de comunidades */
.communities-sidebar {
    width: 100px;
    min-width: 100px;
    background-color: #1a1a1a;
    border-right: 1px solid #2d2d2d;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.sidebar-header {
    padding: 15px 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #2d2d2d;
    min-height: 50px;
    background-color: #2d2d2d;
}

.sidebar-header h2 {
    color: white;
    font-size: 0.9rem;
    margin: 0;
    white-space: nowrap;
}

.back-to-communities {
    color: #9147ff;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 6px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(145, 71, 255, 0.1);
    text-decoration: none;
}

.back-to-communities:hover {
    background-color: rgba(145, 71, 255, 0.2);
    transform: scale(1.1);
    text-decoration: none;
}

.create-community-btn {
    color: #9147ff;
    font-size: 1.2rem;
    cursor: pointer;
    transition: color 0.2s ease;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    width: 24px;
    height: 24px;
}

.create-community-btn i {
    font-size: 1.2rem;
    line-height: 1;
}

.create-community-btn:hover {
    color: #7a30dd;
    background: none;
}

.sidebar-communities-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.community-item {
    width: 60px;
    height: 40px;
    margin: 0 auto 10px;
    cursor: pointer;
    transition: transform 0.2s ease;
    position: relative;
    border-radius: 4px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #2d2d2d;
}

.community-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
    display: block;
}

.community-item:hover {
    transform: scale(1.1);
}

.community-item.active {
    border: 2px solid #9147ff;
}

/* Área principal del chat */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    background-color: #1a1a1a;
}

.chat-header {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #2d2d2d;
    background-color: #1a1a1a;
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
    object-fit: cover;
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

.manage-btn {
    color: #9147ff;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.2s ease;
}

.manage-btn:hover {
    color: #7a30dd;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background-color: #1a1a1a;
}

.message {
    margin-bottom: 10px;
    padding: 10px;
    display: flex;
    gap: 10px;
    background-color: #2d2d2d;
    border-radius: 8px;
}

.message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
}

.message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.message-content {
    flex: 1;
}

.message-header {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.message-username {
    color: #9147ff;
    font-weight: bold;
    margin-right: auto;
}

.message-time {
    color: #b9bbbe;
    font-size: 0.8rem;
}

.message-text {
    color: #dcddde;
    word-break: break-word;
    white-space: pre-line;
}

.message-input-container {
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: #1a1a1a;
    border-top: 1px solid #2d2d2d;
}

.message-form {
    flex: 1;
    display: flex;
    gap: 10px;
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

.emoji-picker-toggle {
    color: #9147ff;
    font-size: 1.2rem;
    cursor: pointer;
}

.send-button {
    background: none;
    border: none;
    color: #9147ff;
    font-size: 1.2rem;
    cursor: pointer;
    transition: color 0.2s ease;
}

.send-button:hover {
    color: #7a30dd;
}

/* Sidebar de miembros */
.members-sidebar {
    width: 250px;
    min-width: 250px;
    background-color: #1a1a1a;
    border-left: 1px solid #2d2d2d;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.search-container {
    padding: 15px;
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

.members-list {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
}

.members-section {
    margin-bottom: 20px;
}

.members-section h3 {
    color: white;
    font-size: 1rem;
    margin-bottom: 10px;
}

.member-creator, .member-item {
    display: flex;
    align-items: center;
    padding: 8px;
    border-radius: 8px;
    transition: background-color 0.2s ease;
    border: 2px solid transparent;
    margin-bottom: 8px;
}

.member-creator:last-child, .member-item:last-child {
    margin-bottom: 0;
}

.member-creator.online, .member-item.online {
    border-color: #43b581;
}

.member-creator.offline, .member-item.offline {
    border-color: #747f8d;
}

.member-creator.idle, .member-item.idle {
    border-color: #faa61a;
}

.member-creator:hover, .member-item:hover {
    background-color: #2d2d2d;
}

.member-info {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
}

.member-info img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.member-details {
    display: flex;
    flex-direction: column;
}

.member-name {
    color: white;
    font-size: 0.9rem;
}

.member-role {
    color: #9147ff;
    font-size: 0.8rem;
}

/* Scrollbar personalizado */
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

<div class="main-container">
    <!-- Sidebar de comunidades -->
    <div class="communities-sidebar">
        <div class="sidebar-header">
            <a href="{{ route('comunidades.index') }}" class="back-to-communities" title="Volver a Comunidades">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2>Comunidades</h2>
        </div>
        
        <div class="sidebar-communities-list">
            @foreach($comunidadesCreadas as $com)
                <a href="#" class="community-item {{ $com->id_chat == $comunidad->id_chat ? 'active' : '' }}" 
                   data-id="{{ $com->id_chat }}"
                   data-name="{{ $com->nombre }}"
                   data-img="{{ asset('img/comunidades/' . $com->img) }}"
                   data-members="{{ $com->chat_usuarios_count }}"
                   data-creator="{{ $com->creator }}">
                    <img src="{{ asset('img/comunidades/' . $com->img) }}" alt="{{ $com->nombre }}">
                </a>
            @endforeach

            @foreach($comunidadesUnidas as $com)
                <a href="#" class="community-item {{ $com->id_chat == $comunidad->id_chat ? 'active' : '' }}" 
                   data-id="{{ $com->id_chat }}"
                   data-name="{{ $com->nombre }}"
                   data-img="{{ asset('img/comunidades/' . $com->img) }}"
                   data-members="{{ $com->chat_usuarios_count }}"
                   data-creator="{{ $com->creator }}">
                    <img src="{{ asset('img/comunidades/' . $com->img) }}" alt="{{ $com->nombre }}">
                </a>
            @endforeach
        </div>
    </div>

    <!-- Área principal del chat -->
    <div class="chat-main">
        <div class="chat-header">
            <div class="chat-contact">
                <img src="{{ asset('img/comunidades/' . $comunidad->img) }}" alt="{{ $comunidad->nombre }}" id="current-community-img">
                <div class="contact-info">
                    <h3 id="current-community-name">{{ $comunidad->nombre }}</h3>
                    <p id="current-community-members">{{ $comunidad->chat_usuarios_count }} miembros</p>
                </div>
            </div>
            <a href="{{ $comunidad->creator == Auth::id() ? route('comunidades.edit', $comunidad->id_chat) : '#' }}" id="details-link" class="manage-btn">{{ $comunidad->creator == Auth::id() ? 'Editar' : 'Detalles del servidor' }}</a>
        </div>
        
        <div class="messages-container" id="chat-messages">
            <!-- Los mensajes se cargarán aquí dinámicamente -->
        </div>
        
        <div class="message-input-container">
            <i class="far fa-smile emoji-picker-toggle"></i>
            <form id="message-form" class="message-form">
                <input type="text" id="message-input" placeholder="Escribe un mensaje...">
                <button type="submit" class="send-button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Sidebar de miembros -->
    <div class="members-sidebar">
        <div class="sidebar-header">
            <h2>Miembros</h2>
        </div>
        
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar miembros...">
            </div>
        </div>
        
        <div class="members-list" id="members-list">
            <!-- Los miembros se cargarán dinámicamente -->
        </div>
    </div>
</div>

<!-- Selector de emojis -->
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
<emoji-picker style="position: absolute; bottom: 60px; left: 20px; display: none;"></emoji-picker>

<!-- Scripts para el chat -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const chatMessages = document.getElementById('chat-messages');
    const emojiPicker = document.querySelector('emoji-picker');
    const emojiButton = document.querySelector('.emoji-picker-toggle');
    const communityItems = document.querySelectorAll('.community-item');
    const membersList = document.getElementById('members-list');
    const detailsLink = document.getElementById('details-link');
    let currentCommunityId = null;
    let lastMessageId = 0;

    // Función para actualizar el enlace de detalles
    function updateDetailsLink(communityId, isCreator) {
        detailsLink.href = `/comunidades/${communityId}/edit`;
        detailsLink.textContent = isCreator ? 'Editar' : 'Detalles del servidor';
    }

    // Función para cargar los miembros de una comunidad
    async function loadMembers(communityId) {
        try {
            const response = await fetch(`/comunidades/${communityId}/members`);
            const data = await response.json();
            
            // Guardar los miembros en una variable global para el filtrado
            window.allMembers = [
                { ...data.creator, isCreator: true },
                ...data.members
            ];
            
            // Limpiar lista actual
            membersList.innerHTML = '';
            
            // Añadir creador
            const creatorSection = document.createElement('div');
            creatorSection.className = 'members-section';
            creatorSection.innerHTML = `
                <h3>Creador</h3>
                <div class="member-creator ${data.creator.id_usuario == {{ Auth::id() }} ? 'online' : data.creator.status}">
                    <div class="member-info">
                        <img src="${data.creator.img}" alt="${data.creator.username}">
                        <div class="member-details">
                            <span class="member-name">${data.creator.username}</span>
                            <span class="member-role">Creador</span>
                        </div>
                    </div>
                </div>
            `;
            membersList.appendChild(creatorSection);
            
            // Añadir sección de miembros solo si hay miembros
            if (data.members && data.members.length > 0) {
                const membersSection = document.createElement('div');
                membersSection.className = 'members-section';
                membersSection.innerHTML = `
                    <h3>Miembros - ${data.members.length}</h3>
                    ${data.members.map(member => `
                        <div class="member-item ${member.id_usuario == {{ Auth::id() }} ? 'online' : member.status}">
                            <div class="member-info">
                                <img src="${member.img}" alt="${member.username}">
                                <div class="member-details">
                                    <span class="member-name">${member.username}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                `;
                membersList.appendChild(membersSection);
            } else {
                // Mostrar mensaje si no hay miembros
                const noMembersSection = document.createElement('div');
                noMembersSection.className = 'members-section';
                noMembersSection.innerHTML = `
                    <h3>Miembros</h3>
                    <div class="no-members">No hay otros miembros en esta comunidad</div>
                `;
                membersList.appendChild(noMembersSection);
            }
        } catch (error) {
            console.error('Error al cargar miembros:', error);
            membersList.innerHTML = `
                <div class="members-section">
                    <h3>Error</h3>
                    <div class="error-message">No se pudieron cargar los miembros</div>
                </div>
            `;
        }
    }

    // Función para filtrar miembros
    function filterMembers(searchTerm) {
        if (!window.allMembers) return;

        const filteredMembers = window.allMembers.filter(member => 
            member.username.toLowerCase().includes(searchTerm.toLowerCase())
        );

        // Limpiar lista actual
        membersList.innerHTML = '';

        // Separar creador y miembros
        const creator = filteredMembers.find(member => member.isCreator);
        const members = filteredMembers.filter(member => !member.isCreator);

        // Añadir creador si existe y coincide con la búsqueda
        if (creator) {
            const creatorSection = document.createElement('div');
            creatorSection.className = 'members-section';
            creatorSection.innerHTML = `
                <h3>Creador</h3>
                <div class="member-creator ${creator.id_usuario == {{ Auth::id() }} ? 'online' : creator.status}">
                    <div class="member-info">
                        <img src="${creator.img}" alt="${creator.username}">
                        <div class="member-details">
                            <span class="member-name">${creator.username}</span>
                            <span class="member-role">Creador</span>
                        </div>
                    </div>
                </div>
            `;
            membersList.appendChild(creatorSection);
        }

        // Añadir sección de miembros si hay resultados
        if (members.length > 0) {
            const membersSection = document.createElement('div');
            membersSection.className = 'members-section';
            membersSection.innerHTML = `
                <h3>Miembros - ${members.length}</h3>
                ${members.map(member => `
                    <div class="member-item ${member.id_usuario == {{ Auth::id() }} ? 'online' : member.status}">
                        <div class="member-info">
                            <img src="${member.img}" alt="${member.username}">
                            <div class="member-details">
                                <span class="member-name">${member.username}</span>
                            </div>
                        </div>
                    </div>
                `).join('')}
            `;
            membersList.appendChild(membersSection);
        } else if (!creator) {
            // Mostrar mensaje si no hay resultados
            const noResultsSection = document.createElement('div');
            noResultsSection.className = 'members-section';
            noResultsSection.innerHTML = `
                <h3>Miembros</h3>
                <div class="no-members">No se encontraron miembros</div>
            `;
            membersList.appendChild(noResultsSection);
        }
    }

    // Evento de búsqueda
    document.querySelector('.search-box input').addEventListener('input', (e) => {
        filterMembers(e.target.value);
    });

    // Función para cargar los mensajes de una comunidad
    async function loadMessages(communityId) {
        try {
            const response = await fetch(`/comunidades/${communityId}/messages`);
            const messages = await response.json();
            
            // Limpiar mensajes actuales
            chatMessages.innerHTML = '';
            
            // Añadir nuevos mensajes
            messages.forEach(message => {
                appendMessage(message);
                // Actualizar el último ID de mensaje
                if (message.id_mensaje > lastMessageId) {
                    lastMessageId = message.id_mensaje;
                }
            });
            
            // Scroll al último mensaje
            scrollToBottom();
        } catch (error) {
            console.error('Error al cargar mensajes:', error);
        }
    }

    // Función para verificar nuevos mensajes
    async function checkNewMessages() {
        if (!currentCommunityId) return;

        try {
            const response = await fetch(`/comunidades/${currentCommunityId}/messages`);
            const messages = await response.json();
            
            // Filtrar solo los mensajes nuevos
            const newMessages = messages.filter(message => message.id_mensaje > lastMessageId);
            
            // Añadir los nuevos mensajes
            newMessages.forEach(message => {
                appendMessage(message);
                if (message.id_mensaje > lastMessageId) {
                    lastMessageId = message.id_mensaje;
                }
            });
            
            if (newMessages.length > 0) {
                scrollToBottom();
            }
        } catch (error) {
            console.error('Error al verificar nuevos mensajes:', error);
        }
    }

    // Función para añadir un mensaje al chat
    function appendMessage(message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'message';
        messageElement.innerHTML = `
            <div class="message-avatar">
                <img src="${message.img}" alt="${message.usuario}">
            </div>
            <div class="message-content">
                <div class="message-header">
                    <span class="message-username">${message.usuario}</span>
                    <span class="message-time">${message.fecha_envio}</span>
                </div>
                <div class="message-text">${message.contenido}</div>
            </div>
        `;
        chatMessages.appendChild(messageElement);
    }

    // Función para hacer scroll al final del chat
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Manejar envío de mensajes
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        
        if (message && currentCommunityId) {
            try {
                const response = await fetch(`/comunidades/${currentCommunityId}/send-message`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        messageInput.value = '';
                        // Recargar mensajes para asegurar el orden correcto
                        loadMessages(currentCommunityId);
                    }
                }
            } catch (error) {
                console.error('Error al enviar mensaje:', error);
            }
        }
    });

    // Manejar cambio de comunidad
    communityItems.forEach(item => {
        item.addEventListener('click', async (e) => {
            e.preventDefault();
            
            // Actualizar clases activas
            communityItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            
            // Actualizar información de la comunidad
            const communityId = item.dataset.id;
            const isCreator = item.dataset.creator == {{ Auth::id() }};
            currentCommunityId = communityId;
            document.getElementById('current-community-img').src = item.dataset.img;
            document.getElementById('current-community-name').textContent = item.dataset.name;
            document.getElementById('current-community-members').textContent = `${item.dataset.members} miembros`;
            
            // Actualizar el enlace de detalles
            updateDetailsLink(communityId, isCreator);
            
            // Resetear el último ID de mensaje
            lastMessageId = 0;
            
            // Cargar miembros y mensajes
            await loadMembers(communityId);
            await loadMessages(communityId);
            
            // Actualizar URL sin recargar
            history.pushState({}, '', `/comunidades/${communityId}`);
        });
    });

    // Toggle emoji picker
    emojiButton.addEventListener('click', () => {
        emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
    });

    // Handle emoji selection
    emojiPicker.addEventListener('emoji-click', event => {
        messageInput.value += event.detail.unicode;
        messageInput.focus();
    });

    // Close emoji picker when clicking outside
    document.addEventListener('click', (e) => {
        if (!emojiPicker.contains(e.target) && !emojiButton.contains(e.target)) {
            emojiPicker.style.display = 'none';
        }
    });

    // Inicializar el enlace de detalles con la comunidad actual
    const initialCommunityItem = document.querySelector('.community-item.active');
    const initialCommunityId = initialCommunityItem.dataset.id;
    const initialIsCreator = initialCommunityItem.dataset.creator == {{ Auth::id() }};
    currentCommunityId = initialCommunityId;
    updateDetailsLink(initialCommunityId, initialIsCreator);
    loadMembers(initialCommunityId);
    loadMessages(initialCommunityId);

    // Verificar nuevos mensajes cada 3 segundos
    setInterval(checkNewMessages, 3000);
});
</script>
@endsection
