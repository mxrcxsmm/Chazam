document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.querySelector('.message-input-container input');
    const sendButton = document.querySelector('.message-input-container .fa-paper-plane');
    const optionsToggle = document.querySelector('.options-toggle');
    const closeOptions = document.querySelector('.close-options');
    const optionsSidebar = document.querySelector('.options-sidebar');
    const chatMain = document.querySelector('.chat-main');
    const mainContainer = document.querySelector('.main-container');
    const emojiButton = document.querySelector('.far.fa-smile');
    const emojiPicker = document.querySelector('emoji-picker');

    let chats = [];
    let currentChatId = null;
    let lastImageUpdate = 0;

    window.userChatConfig = {
        chatsUrl: '/user/chats', // Asegúrate de que esta ruta sea correcta
        messagesUrl: function(chatId) { return `/user/chat/${chatId}/messages`; },
        sendUrl: function(chatId) { return `/user/chat/${chatId}/send`; },
        userId: 1 // Reemplaza con el ID del usuario autenticado
    };

    function renderChats(chats) {
        const chatsList = document.getElementById('chats-list');
        chatsList.innerHTML = '';
        chats.forEach(chat => {
            const chatItem = document.createElement('div');
            chatItem.className = 'chat-item';
            chatItem.dataset.chatId = chat.id_chat;
            chatItem.innerHTML = `
                <div class="chat-avatar">
                    <img src="${chat.img ? chat.img : '/img/profile_img/avatar-default.png'}" alt="Avatar">
                </div>
                <div class="chat-info">
                    <div class="chat-header">
                        <h3>${chat.username ? chat.username : chat.nombre}</h3>
                        <span class="time">${chat.last_time ? chat.last_time : ''}</span>
                    </div>
                    <p class="last-message">${chat.last_message ? chat.last_message : ''}</p>
                </div>
            `;
            chatItem.addEventListener('click', function() {
                document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
                chatItem.classList.add('active');
                loadMessages(chat.id_chat);
                updateChatHeader(chat);
            });
            chatsList.appendChild(chatItem);
        });
    }

    function renderMessages(messages) {
        const messagesContainer = document.getElementById('messages-container');
        messagesContainer.innerHTML = '';
        messages.forEach(msg => {
            const msgDiv = document.createElement('div');
            msgDiv.className = 'message';
            msgDiv.innerHTML = `
                <div class="message-header">
                    <img src="/img/profile_img/avatar-default.png" alt="Avatar" class="message-avatar">
                    <span class="message-username">${msg.usuario}</span>
                    <span class="message-time">${msg.fecha_envio}</span>
                </div>
                <div class="message-content">
                    ${msg.contenido}
                </div>
            `;
            messagesContainer.appendChild(msgDiv);
        });
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function loadChats() {
        const chatsList = document.getElementById('chats-list');
        const loader = document.getElementById('chats-loader');
        loader.style.display = 'block';
        chatsList.style.display = 'none';

        fetch(window.userChatConfig.chatsUrl)
            .then(res => res.json())
            .then(data => {
                chats = data;
                renderChats(chats);
                loader.style.display = 'none';
                chatsList.style.display = 'block';
                if (chats.length > 0) {
                    document.querySelector('.chat-item').classList.add('active');
                    updateChatHeader(chats[0]);
                    loadMessages(chats[0].id_chat);
                }
            })
            .catch(error => {
                loader.innerHTML = 'Error al cargar los chats';
                console.error('Error al cargar los chats:', error);
            });
    }

    function loadMessages(chatId) {
        currentChatId = chatId;
        fetch(window.userChatConfig.messagesUrl(chatId))
            .then(res => res.json())
            .then(data => {
                renderMessages(data);
            });
    }

    // --- NUEVO: Contador y validación de caracteres ---
    // Crear el contador de caracteres
    const contadorContainer = document.createElement('div');
    contadorContainer.id = 'contador-caracteres';
    contadorContainer.style.fontSize = '12px';
    contadorContainer.style.color = '#6c757d';
    contadorContainer.style.marginTop = '5px';
    contadorContainer.style.textAlign = 'right';
    // Insertar el contador justo después del input
    const inputGroup = document.querySelector('.message-input-container');
    if (inputGroup) {
        inputGroup.insertAdjacentElement('afterend', contadorContainer);
    }
    // Actualizar el contador al escribir
    messageInput.addEventListener('input', function() {
        const longitud = this.value.trim().length;
        const caracteresRestantes = 500 - longitud;
        let textoContador = `${longitud}/500 caracteres`;
        if (caracteresRestantes < 50) {
            contadorContainer.style.color = '#dc3545';
        } else {
            contadorContainer.style.color = '#6c757d';
        }
        contadorContainer.textContent = textoContador;
    });
    // Inicializar el contador
    messageInput.dispatchEvent(new Event('input'));

    // Modificar sendMessage para validar máximo 500 caracteres
    function sendMessage() {
        const messageInput = document.querySelector('.message-input-container input');
        const message = messageInput.value.trim();
        if (message.length > 500) {
            Swal.fire({
                title: 'Mensaje demasiado largo',
                text: 'El mensaje no puede exceder los 500 caracteres',
                icon: 'warning'
            });
            return;
        }
        if (message && currentChatId) {
            fetch(window.userChatConfig.sendUrl(currentChatId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ contenido: message })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadMessages(currentChatId);
                    messageInput.value = '';
                    messageInput.dispatchEvent(new Event('input'));
                }
            });
        }
    }

    // Función para alternar el menú de opciones
    function toggleOptions() {
        optionsSidebar.classList.toggle('active');
        chatMain.classList.toggle('shifted');
        
        // Forzar un reflow para asegurar que las transiciones se apliquen correctamente
        void mainContainer.offsetWidth;
    }

    // Event listeners
    sendButton.addEventListener('click', sendMessage);
    optionsToggle.addEventListener('click', toggleOptions);
    closeOptions.addEventListener('click', toggleOptions);

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Marcar chat como activo al hacer clic
    const chatItems = document.querySelectorAll('.chat-item');
    chatItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remover clase active de todos los chats
            chatItems.forEach(chat => chat.classList.remove('active'));
            // Añadir clase active al chat seleccionado
            this.classList.add('active');
        });
    });

    // Cerrar el menú de opciones al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!optionsSidebar.contains(e.target) && 
            !optionsToggle.contains(e.target) && 
            optionsSidebar.classList.contains('active')) {
            toggleOptions();
        }
    });

    // Manejar el redimensionamiento de la ventana
    window.addEventListener('resize', function() {
        if (optionsSidebar.classList.contains('active')) {
            chatMain.style.width = `calc(100% - ${350 + optionsSidebar.offsetWidth}px)`;
        } else {
            chatMain.style.width = 'calc(100% - 350px)';
        }
    });
 
    // Función para actualizar mensajes cada 5 segundos
    function startMessagePolling() {
        // setInterval(refreshCurrentChatHeader, 5000);
    }

    emojiButton.addEventListener('click', () => {
        emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
    });

    document.querySelector('emoji-picker')
      .addEventListener('emoji-click', event => {
        const emoji = event.detail.unicode;
        // Inserta el emoji en el campo de texto
        const input = document.querySelector('.message-input-container input');
        input.value += emoji;
      });

    function updateChatHeader(companero) {
        // Actualiza el nombre
        const chatHeader = document.getElementById('chat-contact-name');
        chatHeader.textContent = companero.username || companero.nombre || 'Usuario';

        // Actualiza el estado según id_estado
        const chatStatus = document.getElementById('chat-contact-status');
        if (companero.id_estado == 1 || companero.id_estado == 5) {
            chatStatus.textContent = 'en línea';
            chatStatus.style.color = '#9147ff';
        } else {
            chatStatus.textContent = 'desconectado';
            chatStatus.style.color = '#b9bbbe';
        }

        // Actualiza la imagen de perfil
        const chatImg = document.getElementById('chat-contact-img');
        chatImg.src = companero.img ? companero.img : '/img/profile_img/avatar-default.png';
    }

    function refreshCurrentChatHeader() {
        if (!currentChatId) return;
        fetch(window.userChatConfig.chatsUrl)
            .then(res => res.json())
            .then(data => {
                const currentChat = data.find(chat => chat.id_chat == currentChatId);
                if (currentChat) {
                    // Actualiza el estado
                    const chatStatus = document.getElementById('chat-contact-status');
                    if (currentChat.id_estado == 1 || currentChat.id_estado == 5) {
                        chatStatus.textContent = 'en línea';
                        chatStatus.style.color = '#9147ff';
                    } else {
                        chatStatus.textContent = 'desconectado';
                        chatStatus.style.color = '#b9bbbe';
                    }
                }
            });
    }

    // Llama a esta función cada 20 segundos
    setInterval(refreshCurrentChatHeader, 20000);

    // Refrescar mensajes del chat activo cada 5 segundos
    setInterval(function() {
        if (currentChatId) {
            loadMessages(currentChatId);
        }
    }, 5000);

    loadChats();
    startMessagePolling();

    // --- Solicitudes de amistad (solo para friendchat) ---
    async function cargarSolicitudesAmistad() {
        try {
            const response = await fetch('/solicitudes/pendientes', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (!response.ok) {
                throw new Error('Error al cargar las solicitudes');
            }
            const data = await response.json();
            const container = document.getElementById('solicitudesContainer');
            const noSolicitudes = document.getElementById('noSolicitudes');
            const solicitudesCount = document.getElementById('solicitudesCount');
            if (solicitudesCount) solicitudesCount.textContent = data.length;
            if (data.length === 0) {
                if (container && noSolicitudes) {
                    noSolicitudes.style.display = 'block';
                    container.innerHTML = '';
                    container.appendChild(noSolicitudes);
                }
                return;
            }
            if (noSolicitudes) noSolicitudes.style.display = 'none';
            if (container) container.innerHTML = '';
            data.forEach(solicitud => {
                if (!container) return;
                const solicitudDiv = document.createElement('div');
                solicitudDiv.className = 'solicitud-item';
                solicitudDiv.id = `solicitud-${solicitud.id_solicitud}`;
                solicitudDiv.innerHTML = `
                    <div class="solicitud-info">
                        <img src="${solicitud.emisor.img || '/img/profile_img/avatar-default.png'}" 
                             alt="${solicitud.emisor.username}" 
                             class="rounded-circle"
                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #ccc;">
                        <span class="solicitud-username">${solicitud.emisor.username}</span>
                    </div>
                    <div class="solicitud-actions">
                        <button class="btn btn-success btn-sm" title="Aceptar" onclick="responderSolicitud(${solicitud.id_solicitud}, 'aceptada')">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" title="Rechazar" onclick="responderSolicitud(${solicitud.id_solicitud}, 'rechazada')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                container.appendChild(solicitudDiv);
            });
        } catch (error) {
            console.error('Error al cargar solicitudes:', error);
            const container = document.getElementById('solicitudesContainer');
            const noSolicitudes = document.getElementById('noSolicitudes');
            if (container && noSolicitudes && container.children.length === 0) {
                noSolicitudes.style.display = 'block';
                container.innerHTML = '';
                container.appendChild(noSolicitudes);
            }
            if (!container) {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudieron cargar las solicitudes de amistad',
                    icon: 'error'
                });
            }
        }
    }

    async function responderSolicitud(idSolicitud, respuesta) {
        try {
            const solicitudDiv = document.getElementById(`solicitud-${idSolicitud}`);
            if (!solicitudDiv) return;
            const buttons = solicitudDiv.querySelectorAll('button');
            buttons.forEach(btn => btn.disabled = true);
            const response = await fetch('/solicitudes/responder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id_solicitud: idSolicitud,
                    respuesta: respuesta
                })
            });
            if (!response.ok) {
                throw new Error('Error al procesar la solicitud');
            }
            const data = await response.json();
            if (data.success) {
                if (data.estado === 'rechazada') {
                    solicitudDiv.remove();
                    const solicitudesCount = document.getElementById('solicitudesCount');
                    const count = parseInt(solicitudesCount.textContent);
                    solicitudesCount.textContent = Math.max(0, count - 1);
                    const container = document.getElementById('solicitudesContainer');
                    if (container.children.length === 0) {
                        const noSolicitudes = document.getElementById('noSolicitudes');
                        if (noSolicitudes) {
                            container.innerHTML = '';
                            noSolicitudes.style.display = 'block';
                            container.appendChild(noSolicitudes);
                        }
                    }
                } else {
                    const actionsDiv = solicitudDiv.querySelector('.solicitud-actions');
                    actionsDiv.innerHTML = `
                        <span class="badge bg-success">Aceptada</span>
                    `;
                }
                loadChats();
                Swal.fire({
                    title: data.estado === 'aceptada' ? '¡Solicitud aceptada!' : 'Solicitud rechazada',
                    text: data.estado === 'aceptada' ? 'Ahora son amigxs' : 'La solicitud ha sido rechazada',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Error al procesar la solicitud');
            }
        } catch (error) {
            console.error('Error al responder solicitud:', error);
            Swal.fire({
                title: 'Error',
                text: error.message || 'Ocurrió un error al procesar la solicitud',
                icon: 'error'
            });
            const solicitudDiv = document.getElementById(`solicitud-${idSolicitud}`);
            if (solicitudDiv) {
                const buttons = solicitudDiv.querySelectorAll('button');
                buttons.forEach(btn => btn.disabled = false);
            }
        }
    }

    async function actualizarContadorSolicitudes() {
        try {
            const response = await fetch('/solicitudes/pendientes', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (!response.ok) return;
            const data = await response.json();
            const solicitudesCount = document.getElementById('solicitudesCount');
            if (solicitudesCount) solicitudesCount.textContent = data.length;
        } catch (error) {
            // Silenciar error
        }
    }

    // Configurar el botón de ver solicitudes SOLO para friendchat
    const btnSolicitudesPendientes = document.getElementById('btnSolicitudesPendientes');
    if (btnSolicitudesPendientes) {
        btnSolicitudesPendientes.addEventListener('click', function(e) {
            e.preventDefault();
            const solicitudesModal = new bootstrap.Modal(document.getElementById('solicitudesModal'));
            solicitudesModal.show();
            cargarSolicitudesAmistad();
        });
        // Actualizar el contador al cargar la página
        actualizarContadorSolicitudes();
    }
    // Actualizar solicitudes cada 30 segundos si el modal está abierto
    let solicitudesInterval;
    const solicitudesModalEl = document.getElementById('solicitudesModal');
    if (solicitudesModalEl) {
        solicitudesModalEl.addEventListener('show.bs.modal', function () {
            solicitudesInterval = setInterval(cargarSolicitudesAmistad, 30000);
        });
        solicitudesModalEl.addEventListener('hidden.bs.modal', function () {
            clearInterval(solicitudesInterval);
        });
    }

    window.responderSolicitud = responderSolicitud;
});