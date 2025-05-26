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

// Función global para cargar solicitudes
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
    }
}

// Función global para responder solicitudes
async function responderSolicitud(idSolicitud, respuesta) {
    try {
        const solicitudDiv = document.getElementById(`solicitud-${idSolicitud}`);
        if (!solicitudDiv) return;
        const buttons = solicitudDiv.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);

        // Cerrar el modal inmediatamente
        const modalEl = document.getElementById('solicitudesModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
            // Forzar la eliminación del backdrop y clases
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            modalEl.style.display = 'none';
        }

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

            if (window.chatManager) {
                window.chatManager.loadChats();
            }

            // Mostrar mensaje de éxito después de un pequeño retraso
            setTimeout(() => {
                Swal.fire({
                    title: data.estado === 'aceptada' ? '¡Solicitud aceptada!' : 'Solicitud rechazada',
                    text: data.estado === 'aceptada' ? 'Ahora son amigxs' : 'La solicitud ha sido rechazada',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 100);
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

// Función global para bloquear usuario
async function bloquearUsuario(idUsuario) {
    try {
        const result = await Swal.fire({
            title: '¿Bloquear usuario?',
            text: '¿Estás seguro de que deseas bloquear a este usuario? No podrás ver sus mensajes ni interactuar con él.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, bloquear',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            const response = await fetch(`/amistades/${idUsuario}/bloquear`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            
            if (data.success) {
                // Eliminar el chat de la lista
                const chatItem = document.querySelector(`.chat-item[data-chat-id="${idUsuario}"]`);
                if (chatItem) {
                    chatItem.remove();
                }

                // Actualizar la lista de chats
                if (window.chatManager) {
                    window.chatManager.loadChats();
                }

                // Mostrar mensaje de éxito
                await Swal.fire({
                    title: '¡Usuario bloqueado!',
                    text: 'El usuario ha sido bloqueado correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Si estamos en el chat del usuario bloqueado, redirigir a la lista de chats
                if (window.chatManager && window.chatManager.currentChatId === idUsuario) {
                    window.location.href = '/user/chats';
                }
            } else {
                throw new Error(data.message || 'Error al bloquear al usuario');
            }
        }
    } catch (error) {
        console.error('Error al bloquear usuario:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'Ocurrió un error al bloquear al usuario',
            icon: 'error'
        });
    }
}

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
        this.setupSolicitudesHandlers();
        this.setupBlockHandlers();
        this.setupSearchHandlers();
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
        this.setupReportHandlers();
        this.setupSearchHandlers();
        
        // Añadir evento para abrir el modal de búsqueda
        const searchButton = document.querySelector('.chat-actions .fa-search');
        if (searchButton) {
            searchButton.addEventListener('click', () => {
                const buscarUsuariosModal = new bootstrap.Modal(document.getElementById('buscarUsuariosModal'));
                buscarUsuariosModal.show();
            });
        }

        // Añadir evento para buscar chats
        const chatSearchInput = document.querySelector('.search-box input');
        if (chatSearchInput) {
            chatSearchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase().trim();
                const chatItems = document.querySelectorAll('.chat-item');
                
                chatItems.forEach(chat => {
                    const username = chat.querySelector('h3').textContent.toLowerCase();
                    const lastMessage = chat.querySelector('.last-message').textContent.toLowerCase();
                    
                    if (username.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                        chat.style.display = 'flex';
                    } else {
                        chat.style.display = 'none';
                    }
                });

                // Si no hay resultados, mostrar mensaje
                const visibleChats = document.querySelectorAll('.chat-item[style="display: flex"]');
                const noResultsMessage = document.getElementById('no-results-message');
                
                if (visibleChats.length === 0 && searchTerm !== '') {
                    if (!noResultsMessage) {
                        const message = document.createElement('div');
                        message.id = 'no-results-message';
                        message.className = 'text-center text-muted mt-3';
                        message.textContent = 'No se encontraron chats';
                        this.elements.chatsList.appendChild(message);
                    }
                } else if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            });
        }
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
        console.log('Creando elemento de chat:', chat); // Log para depuración
            const chatItem = document.createElement('div');
            chatItem.className = 'chat-item';
            chatItem.dataset.chatId = chat.id_chat;
        
        // Guardar el ID del usuario en el elemento
        const userId = chat.id_usuario || chat.usuario_id || chat.user_id;
        if (userId) {
            chatItem.dataset.userId = userId;
            console.log('ID de usuario guardado:', userId); // Log para depuración
        } else {
            console.warn('No se encontró ID de usuario para el chat:', chat); // Log de advertencia
        }

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
        console.log('Chat seleccionado:', chat); // Log para depuración
                document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
                chatItem.classList.add('active');
        this.loadMessages(chat.id_chat);
        this.updateChatHeader(chat);
        this.currentChatId = chat.id_chat;
        this.currentUserId = chat.id_usuario; // Guardar el ID del usuario actual
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
        const threshold = 150; // Margen de 150px para considerar que está "cerca" del final
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
            console.log('Datos de chats recibidos:', data); // Log para depuración
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
                
                // Verificar que el modal existe
                if (!this.elements.solicitudesModal) {
                    console.error('El modal de solicitudes no existe');
                    return;
                }

                // Limpiar cualquier modal anterior
                const oldModal = bootstrap.Modal.getInstance(this.elements.solicitudesModal);
                if (oldModal) {
                    oldModal.dispose();
                }

                // Eliminar cualquier backdrop residual
                const oldBackdrop = document.querySelector('.modal-backdrop');
                if (oldBackdrop) {
                    oldBackdrop.remove();
                }

                // Limpiar clases del body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';

                // Crear nueva instancia del modal
                try {
                    const solicitudesModal = new bootstrap.Modal(this.elements.solicitudesModal);
                    solicitudesModal.show();
                    cargarSolicitudesAmistad();
                } catch (error) {
                    console.error('Error al inicializar el modal:', error);
                }
            });
            this.actualizarContadorSolicitudes();
        }

        if (this.elements.solicitudesModal) {
            let solicitudesInterval;
            this.elements.solicitudesModal.addEventListener('show.bs.modal', () => {
                solicitudesInterval = setInterval(cargarSolicitudesAmistad, CHAT_CONFIG.solicitudesInterval);
            });
            this.elements.solicitudesModal.addEventListener('hidden.bs.modal', () => {
                clearInterval(solicitudesInterval);
                // Limpiar el modal después de cerrarse
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
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

    // Configuración de handlers de bloqueo
    setupBlockHandlers() {
        const blockButtons = document.querySelectorAll('.block-user-btn');
        blockButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const userId = button.dataset.userId;
                if (userId) {
                    bloquearUsuario(userId);
                }
            });
        });
    }

    // Configuración de handlers de reporte
    setupReportHandlers() {
        const reportButton = document.querySelector('.report-user-btn');
        if (reportButton) {
            reportButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (!this.currentChatId) return;

                // Obtener el chat actual
                const currentChat = this.chats.find(chat => chat.id_chat === this.currentChatId);
                console.log('Chat actual para reporte:', currentChat); // Log para depuración

                if (!currentChat) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo encontrar la información del usuario',
                        icon: 'error'
                    });
                    return;
                }

                // Obtener el ID del usuario reportado del elemento del chat
                const chatElement = document.querySelector(`.chat-item[data-chat-id="${this.currentChatId}"]`);
                const idReportado = chatElement ? chatElement.dataset.userId : null;
                console.log('ID del usuario a reportar:', idReportado); // Log para depuración

                if (!idReportado) {
                    // Si no encontramos el ID en el dataset, intentar obtenerlo del chat actual
                    const idFromChat = currentChat.id_usuario || currentChat.usuario_id || currentChat.user_id;
                    if (!idFromChat) {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo identificar al usuario a reportar',
                            icon: 'error'
                        });
                        return;
                    }
                    idReportado = idFromChat;
                }

                Swal.fire({
                    title: 'Reportar usuario',
                    html: `
                        <div class="mb-3">
                            <label for="reportTitle" class="form-label">Título del reporte</label>
                            <input type="text" class="form-control" id="reportTitle" placeholder="Ingrese un título">
                        </div>
                        <div class="mb-3">
                            <label for="reportDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="reportDescription" rows="3" placeholder="Describa el motivo del reporte"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Enviar reporte',
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        const title = document.getElementById('reportTitle').value;
                        const description = document.getElementById('reportDescription').value;
                        if (!title || !description) {
                            Swal.showValidationMessage('Por favor complete todos los campos');
                            return false;
                        }
                        return { title, description };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('Enviando reporte para usuario:', idReportado); // Log para depuración
                        fetch('/reportes/crear', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                id_reportado: idReportado,
                                titulo: result.value.title,
                                descripcion: result.value.description
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Respuesta del servidor:', data); // Log para depuración
                            if (data.success) {
                                Swal.fire({
                                    title: '¡Reporte enviado!',
                                    text: 'El reporte ha sido enviado correctamente.',
                                    icon: 'success'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.message || 'No se pudo enviar el reporte.',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error al enviar reporte:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Ocurrió un error al enviar el reporte.',
                                icon: 'error'
                            });
                        });
                    }
                });
            });
        }
    }

    // Configuración de handlers de búsqueda
    setupSearchHandlers() {
        const searchInput = document.getElementById('searchUserInput');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                const query = e.target.value.trim();

                if (query.length < 3) {
                    searchResults.innerHTML = '<div class="text-center text-muted">Ingresa al menos 3 caracteres para buscar</div>';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    this.searchUsers(query);
                }, 300);
            });
        }
    }

    // Función para buscar usuarios
    async searchUsers(query) {
        try {
            const response = await fetch(`/buscar-usuarios?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error('Error en la búsqueda');
            }

            const data = await response.json();
            const searchResults = document.getElementById('searchResults');

            if (data.length === 0) {
                searchResults.innerHTML = '<div class="text-center text-muted">No se encontraron usuarios</div>';
                return;
            }

            searchResults.innerHTML = data.map(user => `
                <div class="user-result">
                    <img src="${user.img}" alt="${user.username}" onerror="this.src='/img/profile_img/avatar-default.png'">
                    <div class="user-info">
                        <h6>${user.username}</h6>
                        <p>${user.nombre_completo}</p>
                    </div>
                    <button class="send-request-btn" 
                            data-user-id="${user.id_usuario}"
                            onclick="window.chatManager.sendFriendRequest(${user.id_usuario}, this)">
                        Enviar solicitud
                    </button>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error al buscar usuarios:', error);
            const searchResults = document.getElementById('searchResults');
            searchResults.innerHTML = '<div class="text-center text-danger">Error al buscar usuarios</div>';
        }
    }

    // Función para enviar solicitud de amistad
    async sendFriendRequest(userId, button) {
        try {
            const response = await fetch('/solicitudes/enviar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id_receptor: userId
                })
            });

            const data = await response.json();

            if (data.success) {
                button.disabled = true;
                button.textContent = 'Solicitud enviada';
                button.classList.add('sent');
                Swal.fire({
                    title: '¡Solicitud enviada!',
                    text: 'La solicitud de amistad ha sido enviada correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Error al enviar la solicitud');
            }
        } catch (error) {
            console.error('Error al enviar solicitud:', error);
            Swal.fire({
                title: 'Error',
                text: error.message || 'No se pudo enviar la solicitud de amistad',
                icon: 'error'
            });
        }
    }
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    window.chatManager = new ChatManager();
    window.chatManager.loadChats();
    window.bloquearUsuario = bloquearUsuario; // Hacer la función globalmente accesible
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