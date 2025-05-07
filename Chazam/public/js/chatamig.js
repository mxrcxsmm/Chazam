document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.querySelector('.message-input-container input');
    const sendButton = document.querySelector('.message-input-container .fa-paper-plane');
    const optionsToggle = document.querySelector('.options-toggle');
    const closeOptions = document.querySelector('.close-options');
    const optionsSidebar = document.querySelector('.options-sidebar');
    const chatMain = document.querySelector('.chat-main');
    const mainContainer = document.querySelector('.main-container');

    let chats = [];
    let currentChatId = null;

    function renderChats(chats) {
        const chatsList = document.getElementById('chats-list');
        chatsList.innerHTML = '';
        chats.forEach(chat => {
            const chatItem = document.createElement('div');
            chatItem.className = 'chat-item';
            chatItem.dataset.chatId = chat.id_chat;
            chatItem.innerHTML = `
                <div class="chat-avatar">
                    <img src="${chat.img ? chat.img : '/images/avatar-default.png'}" alt="Avatar">
                </div>
                <div class="chat-info">
                    <div class="chat-header">
                        <h3>${chat.nombre}</h3>
                        <span class="time">${chat.last_time ? chat.last_time : ''}</span>
                    </div>
                    <p class="last-message">${chat.last_message ? chat.last_message : ''}</p>
                </div>
            `;
            chatItem.addEventListener('click', function() {
                document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
                chatItem.classList.add('active');
                loadMessages(chat.id_chat);
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
        fetch(window.userChatConfig.chatsUrl)
            .then(res => res.json())
            .then(data => {
                chats = data;
                renderChats(chats);
                if (chats.length > 0) {
                    document.querySelector('.chat-item').classList.add('active');
                    loadMessages(chats[0].id_chat);
                }
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
        setInterval(() => {
            if (currentChatId) {
                loadMessages(currentChatId);
            }
        }, 5000); // Actualiza cada 5 segundos
    }

    loadChats();
    startMessagePolling();
});
