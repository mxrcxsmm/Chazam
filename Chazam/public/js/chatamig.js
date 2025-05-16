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
                    <img src="${chat.img ? chat.img : '/IMG/profile_img/avatar-default.png'}" alt="Avatar">
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
                    <img src="/images/avatar-default.png" alt="Avatar" class="message-avatar">
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

    function sendMessage() {
        const messageInput = document.querySelector('.message-input-container input');
        const message = messageInput.value.trim();
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
        chatImg.src = companero.img ? companero.img : '/images/avatar-default.png';
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

    // Llama a esta función cada 15 segundos
    setInterval(refreshCurrentChatHeader, 15000);

    // Refrescar mensajes del chat activo cada 5 segundos
    setInterval(function() {
        if (currentChatId) {
            loadMessages(currentChatId);
        }
    }, 5000);

    loadChats();
    startMessagePolling();
});