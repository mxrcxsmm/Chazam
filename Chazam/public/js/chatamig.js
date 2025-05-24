// Configuración global del chat
const CHAT_CONFIG = {
    chatsUrl: '/user/chats',
    messagesUrl: (chatId) => `/user/chat/${chatId}/messages`,
    sendUrl: (chatId) => `/user/chat/${chatId}/send`,
    pollingInterval: 4000,        // 4 segundos
    headerRefreshInterval: 30000,  // 30 segundos
    solicitudesInterval: 60000,    // 1 minuto
    smartPolling: {
        enabled: true,
        idleTimeout: 300000,       // 5 minutos de inactividad
        idleInterval: 120000,      // 2 minutos cuando está inactivo
        activeInterval: 4000       // 4 segundos cuando está activo
    }
};

// Clase principal del chat
class ChatManager {
    constructor() {
        this.chats = [];
        this.currentChatId = null;
        this.lastImageUpdate = 0;
        this.lastActivity = Date.now();
        this.isActive = true;
        this.initializeElements();
        this.setupEventListeners();
        this.startSmartPolling();
    }

    // Inicialización de elementos DOM
    initializeElements() {
        this.elements = {
            messageInput: document.querySelector('.message-input-container input'),
            sendButton: document.querySelector('.message-input-container .fa-paper-plane'),
            optionsToggle: document.querySelector('.options-toggle'),
            closeOptions: document.querySelector('.close-options'),
            optionsSidebar: document.querySelector('.options-sidebar'),
            chatMain: document.querySelector('.chat-main'),
            mainContainer: document.querySelector('.main-container'),
            emojiButton: document.querySelector('.far.fa-smile'),
            emojiPicker: document.querySelector('emoji-picker'),
            chatsList: document.getElementById('chats-list'),
            messagesContainer: document.getElementById('messages-container'),
            btnSolicitudesPendientes: document.getElementById('btnSolicitudesPendientes'),
            solicitudesModal: document.getElementById('solicitudesModal')
        };
    }

    // Configuración de event listeners
    setupEventListeners() {
        this.elements.sendButton.addEventListener('click', () => this.sendMessage());
        this.elements.optionsToggle.addEventListener('click', () => this.toggleOptions());
        this.elements.closeOptions.addEventListener('click', () => this.toggleOptions());
        this.elements.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
        this.elements.emojiButton.addEventListener('click', () => this.toggleEmojiPicker());
        this.setupEmojiPicker();
        this.setupSolicitudesHandlers();
        this.setupWindowResizeHandler();
    }

    // Renderizado de chats
    renderChats(chats) {
        const chatsList = this.elements.chatsList;
        chatsList.innerHTML = '';
        
        chats.forEach(chat => {
            const chatItem = this.createChatElement(chat);
            chatsList.appendChild(chatItem);
        });
    }

    // Creación de elemento de chat
    createChatElement(chat) {
        const chatItem = document.createElement('div');
        chatItem.className = 'chat-item';
        chatItem.dataset.chatId = chat.id_chat;
        chatItem.innerHTML = `
            <div class="chat-avatar">
                <img src="${chat.img || '/img/profile_img/avatar-default.png'}" alt="Avatar" onerror="this.src='/img/profile_img/avatar-default.png'">
            </div>
            <div class="chat-info">
                <div class="chat-header">
                    <h3>${chat.username || chat.nombre}</h3>
                    <span class="time">${chat.last_time || ''}</span>
                </div>
                <p class="last-message">${chat.last_message || ''}</p>
            </div>
        `;
        
        chatItem.addEventListener('click', () => this.handleChatSelection(chatItem, chat));
        return chatItem;
    }

    // Manejo de selección de chat
    handleChatSelection(chatItem, chat) {
        document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
        chatItem.classList.add('active');
        this.loadMessages(chat.id_chat);
        this.updateChatHeader(chat);
    }

    // Renderizado de mensajes
    renderMessages(messages) {
        const messagesContainer = this.elements.messagesContainer;
        const currentScroll = messagesContainer.scrollTop;
        const wasAtBottom = this.isAtBottom(messagesContainer);

        messagesContainer.innerHTML = '';
        
        messages.forEach(msg => {
            const msgDiv = this.createMessageElement(msg);
            messagesContainer.appendChild(msgDiv);
        });

        // Scroll automático si estaba en el último mensaje o si es un mensaje nuevo
        if (wasAtBottom || this.isNewMessage(messages)) {
            this.smoothScrollToBottom(messagesContainer);
        } else {
            messagesContainer.scrollTop = currentScroll;
        }
    }

    // Verificar si está en el último mensaje
    isAtBottom(element) {
        const threshold = 100; // Margen de 100px para considerar que está "cerca" del final
        return element.scrollHeight - element.scrollTop - element.clientHeight <= threshold;
    }

    // Verificar si hay mensajes nuevos
    isNewMessage(messages) {
        if (!messages.length) return false;
        const lastMessage = messages[messages.length - 1];
        const now = new Date();
        const messageTime = new Date(lastMessage.fecha_envio);
        // Considerar como nuevo si el mensaje tiene menos de 5 segundos
        return (now - messageTime) < 5000;
    }

    // Creación de elemento de mensaje
    createMessageElement(msg) {
        const imgSrc = msg.es_mio ? window.userImg : (msg.img || '/img/profile_img/avatar-default.png');
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${msg.es_mio ? 'message-own' : ''}`;
        msgDiv.innerHTML = `
            <div class="message-header">
                <img src="${imgSrc}" alt="Avatar" class="message-avatar" onerror="this.src='/img/profile_img/avatar-default.png'">
                <span class="message-username">${msg.usuario}</span>
                <span class="message-time">${msg.fecha_envio}</span>
            </div>
            <div class="message-content">
                ${msg.contenido}
            </div>
        `;
        return msgDiv;
    }

    // Carga de chats
    async loadChats() {
        try {
            const response = await fetch(CHAT_CONFIG.chatsUrl);
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            this.chats = data;
            this.renderChats(data);
            
            if (data.length > 0 && !this.currentChatId) {
                const firstChat = document.querySelector('.chat-item');
                if (firstChat) {
                    firstChat.classList.add('active');
                    this.updateChatHeader(data[0]);
                    this.loadMessages(data[0].id_chat);
                }
            }
        } catch (error) {
            console.error('Error al cargar los chats:', error);
        }
    }

    // Carga de mensajes
    async loadMessages(chatId) {
        if (!this.isActive && !this.currentChatId) return;
        
        this.currentChatId = chatId;
        try {
            const response = await fetch(CHAT_CONFIG.messagesUrl(chatId));
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            
            const data = await response.json();
            if (data.length > 0) {
                this.renderMessages(data);
            }
        } catch (error) {
            console.error('Error al cargar mensajes:', error);
        }
    }

    // Envío de mensaje
    async sendMessage() {
        const message = this.elements.messageInput.value.trim();
        
        if (message.length > 500) {
            Swal.fire({
                title: 'Mensaje demasiado largo',
                text: 'El mensaje no puede exceder los 500 caracteres',
                icon: 'warning'
            });
            return;
        }

        if (message && this.currentChatId) {
            this.elements.messageInput.disabled = true;

            try {
                const response = await fetch(CHAT_CONFIG.sendUrl(this.currentChatId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ contenido: message })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.elements.messageInput.value = '';
                    // Cargar mensajes inmediatamente después de enviar
                    await this.loadMessages(this.currentChatId);
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo enviar el mensaje. Por favor, intenta de nuevo.',
                    icon: 'error'
                });
            } finally {
                this.elements.messageInput.disabled = false;
                this.elements.messageInput.focus();
            }
        }
    }

    // Actualización del encabezado del chat
    updateChatHeader(companero) {
        const chatHeader = document.getElementById('chat-contact-name');
        const chatStatus = document.getElementById('chat-contact-status');
        const chatImg = document.getElementById('chat-contact-img');

        chatHeader.textContent = companero.username || companero.nombre || 'Usuario';
        chatStatus.textContent = (companero.id_estado == 1 || companero.id_estado == 5) ? 'en línea' : 'desconectado';
        chatStatus.style.color = (companero.id_estado == 1 || companero.id_estado == 5) ? '#9147ff' : '#b9bbbe';
        chatImg.src = companero.img || '/img/profile_img/avatar-default.png';
    }

    // Toggle de opciones
    toggleOptions() {
        this.elements.optionsSidebar.classList.toggle('active');
        this.elements.chatMain.classList.toggle('shifted');
        void this.elements.mainContainer.offsetWidth;
    }

    // Toggle del emoji picker
    toggleEmojiPicker() {
        const display = this.elements.emojiPicker.style.display;
        this.elements.emojiPicker.style.display = display === 'none' ? 'block' : 'none';
    }

    // Configuración del emoji picker
    setupEmojiPicker() {
        this.elements.emojiPicker.addEventListener('emoji-click', event => {
            const emoji = event.detail.unicode;
            this.elements.messageInput.value += emoji;
        });
    }

    // Configuración de handlers de solicitudes
    setupSolicitudesHandlers() {
        if (this.elements.btnSolicitudesPendientes) {
            this.elements.btnSolicitudesPendientes.addEventListener('click', (e) => {
                e.preventDefault();
                const solicitudesModal = new bootstrap.Modal(this.elements.solicitudesModal);
                solicitudesModal.show();
                this.cargarSolicitudesAmistad();
            });
            this.actualizarContadorSolicitudes();
        }

        if (this.elements.solicitudesModal) {
            let solicitudesInterval;
            this.elements.solicitudesModal.addEventListener('show.bs.modal', () => {
                solicitudesInterval = setInterval(() => this.cargarSolicitudesAmistad(), CHAT_CONFIG.solicitudesInterval);
            });
            this.elements.solicitudesModal.addEventListener('hidden.bs.modal', () => {
                clearInterval(solicitudesInterval);
            });
        }
    }

    // Configuración del handler de redimensionamiento
    setupWindowResizeHandler() {
        window.addEventListener('resize', () => {
            if (this.elements.optionsSidebar.classList.contains('active')) {
                this.elements.chatMain.style.width = `calc(100% - ${350 + this.elements.optionsSidebar.offsetWidth}px)`;
            } else {
                this.elements.chatMain.style.width = 'calc(100% - 350px)';
            }
        });
    }

    // --- Solicitudes de amistad (solo para friendchat) ---
    async cargarSolicitudesAmistad() {
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

    async responderSolicitud(idSolicitud, respuesta) {
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
                this.loadChats();
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

    async actualizarContadorSolicitudes() {
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

    // Nuevo: Sistema de polling inteligente
    startSmartPolling() {
        // Detectar actividad del usuario
        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        activityEvents.forEach(event => {
            document.addEventListener(event, () => {
                this.lastActivity = Date.now();
                if (!this.isActive) {
                    this.isActive = true;
                    this.updatePollingInterval();
                }
            });
        });

        // Verificar inactividad periódicamente
        setInterval(() => {
            const now = Date.now();
            const idleTime = now - this.lastActivity;
            
            if (idleTime >= CHAT_CONFIG.smartPolling.idleTimeout && this.isActive) {
                this.isActive = false;
                this.updatePollingInterval();
            }
        }, 60000); // Verificar cada minuto

        // Iniciar polling con el intervalo inicial
        this.updatePollingInterval();
    }

    updatePollingInterval() {
        // Limpiar intervalos existentes
        if (this.messagePollingInterval) {
            clearInterval(this.messagePollingInterval);
        }
        if (this.headerPollingInterval) {
            clearInterval(this.headerPollingInterval);
        }

        // Establecer nuevos intervalos basados en el estado de actividad
        const interval = this.isActive ? 
            CHAT_CONFIG.smartPolling.activeInterval : 
            CHAT_CONFIG.smartPolling.idleInterval;

        // Polling de mensajes
        this.messagePollingInterval = setInterval(() => {
            if (this.currentChatId) {
                this.loadMessages(this.currentChatId);
            }
        }, interval);

        // Polling de estado (solo cuando está activo)
        if (this.isActive) {
            this.headerPollingInterval = setInterval(() => {
                this.refreshCurrentChatHeader();
            }, CHAT_CONFIG.headerRefreshInterval);
        }
    }

    refreshCurrentChatHeader() {
        if (!this.currentChatId) return;
        fetch(CHAT_CONFIG.chatsUrl)
            .then(res => res.json())
            .then(data => {
                const currentChat = data.find(chat => chat.id_chat == this.currentChatId);
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

    // Scroll suave
    smoothScrollToBottom(element) {
        const targetScroll = element.scrollHeight;
        const startScroll = element.scrollTop;
        const distance = targetScroll - startScroll;
        const duration = 300;
        let start = null;

        function animation(currentTime) {
            if (start === null) start = currentTime;
            const timeElapsed = currentTime - start;
            const progress = Math.min(timeElapsed / duration, 1);
            const easeInOutCubic = progress < 0.5
                ? 4 * progress * progress * progress
                : 1 - Math.pow(-2 * progress + 2, 3) / 2;

            element.scrollTop = startScroll + (distance * easeInOutCubic);

            if (timeElapsed < duration) {
                requestAnimationFrame(animation);
            }
        }

        requestAnimationFrame(animation);
    }
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    const chatManager = new ChatManager();
    chatManager.loadChats();
});

// Añadir estilos CSS para las animaciones
const style = document.createElement('style');
style.textContent = `
    .typing-indicator {
        padding: 10px;
        color: #666;
        font-style: italic;
        font-size: 0.9em;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(10px); }
    }

    .message {
        transition: all 0.3s ease;
    }

    .message:hover {
        transform: translateX(5px);
    }
`;
document.head.appendChild(style);