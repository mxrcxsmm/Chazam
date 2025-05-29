// Configuración global del chat
const CHAT_CONFIG = {
    chatsUrl: '/user/chats',
    messagesUrl: (chatId) => `/user/chat/${chatId}/messages`,
    sendUrl: (chatId) => `/user/chat/${chatId}/send`,
    pollingInterval: 4000,        // 4 segundos
    headerRefreshInterval: 30000,  // 30 segundos
    smartPolling: {
        enabled: true,
        idleTimeout: 300000,       // 5 minutos de inactividad
        idleInterval: 120000,      // 2 minutos cuando está inactivo
        activeInterval: 4000       // 4 segundos cuando está activo
    },
    defaultAvatar: '/img/profile_img/avatar-default.png'
};

// Función utilitaria para obtener la ruta correcta de la imagen de perfil
function getProfileImgPath(img) {
    if (!img || img === 'avatar-default.png' || img === '/img/profile_img/avatar-default.png') {
        return `${window.location.origin}/img/profile_img/avatar-default.png`;
    }
    if (img.startsWith('http')) {
        return img;
    }
    const cleanImg = img.replace(/^\/?img\/profile_img\//, '');
    return `${window.location.origin}/img/profile_img/${cleanImg}`;
}

// Clase principal del chat
class ChatManager {
    constructor() {
        this.chats = [];
        this.currentChatId = null;
        this.lastImageUpdate = 0;
        this.lastActivity = Date.now();
        this.isActive = true;
        this.elements = {};
        this.solicitudesIntervalId = null;
        
        // Esperar a que el DOM esté completamente cargado
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initialize());
        } else {
            this.initialize();
        }
    }

    initialize() {
        this.initializeElements();
        this.setupEventListeners();
        this.startSmartPolling();
        this.setupBlockHandlers();
        this.loadChats();
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
            chatHeader: document.getElementById('chat-contact-name'),
            chatStatus: document.getElementById('chat-contact-status'),
            chatImg: document.getElementById('chat-contact-img')
        };

        // Verificar elementos críticos
        if (!this.elements.chatHeader || !this.elements.chatStatus || !this.elements.chatImg) {
            console.error('Elementos críticos del chat no encontrados');
        }
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
        this.setupWindowResizeHandler();
        this.setupReportHandlers();
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
        const imgPath = chat.img || '/img/profile_img/avatar-default.png';
        const framePath = chat.marco 
            ? `/img/frames/${chat.marco}` 
            : ''; // si no hay marco, queda vacío
    
        // Calcular estilos dinámicos
        const brightness = chat.brillo !== undefined 
            ? `brightness(${chat.brillo})` 
            : '';
        const rotation = chat.rotacion 
            ? 'rotate(180deg)' 
            : '';
    
        const chatItem = document.createElement('div');
        chatItem.className = 'chat-item';
        chatItem.dataset.chatId = chat.id_chat;
        if (chat.id_usuario) chatItem.dataset.userId = chat.id_usuario;
    
        chatItem.innerHTML = `
            <div class="chat-avatar" style="
                position: relative;
                ${framePath ? `background: url('${framePath}') no-repeat center/cover;` : ''}
                width: 48px;
                height: 48px;
            ">
                <img
                  src="${imgPath}"
                  alt="Avatar"
                  class="avatar-img"
                  style="
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    filter: ${brightness};
                    transform: ${rotation};
                    transition: transform 0.2s, filter 0.2s;
                  "
                  onerror="this.src='/img/profile_img/avatar-default.png'"
                />
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
        this.currentUserId = chat.id_usuario || chat.usuario_id || chat.user_id; // Guardar el ID del usuario actual
        console.log('ID de usuario actual:', this.currentUserId); // Log para depuración
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
        const imgSrc = getProfileImgPath(msg.img);
        
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${msg.es_mio ? 'message-own' : ''}`;
        msgDiv.innerHTML = `
            <div class="message-header">
                <img src="${imgSrc}" alt="Avatar" class="message-avatar" onerror="this.src='${CHAT_CONFIG.defaultAvatar}'">
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
        if (!companero) return;

        const defaultAvatar = CHAT_CONFIG.defaultAvatar;
        const isFriend = !!companero.id_usuario;
      
        // Verificar que los elementos existen
        if (!this.elements.chatHeader || !this.elements.chatStatus || !this.elements.chatImg) {
            console.error('Elementos del header del chat no encontrados');
            return;
        }

        // Actualizar nombre y estado
        this.elements.chatHeader.textContent = companero.username || companero.nombre || 'Usuario';
        this.elements.chatStatus.textContent = (companero.id_estado == 1 || companero.id_estado == 5) ? 'en línea' : 'desconectado';
        this.elements.chatStatus.style.color = (companero.id_estado == 1 || companero.id_estado == 5) ? '#9147ff' : '#b9bbbe';
        
        // Construir la ruta de la imagen correctamente
        const imgPath = companero.img ? companero.img.replace('/img/profile_img/img/profile_img/', '/img/profile_img/') : defaultAvatar;
        this.elements.chatImg.src = imgPath;
        this.elements.chatImg.onerror = () => {
            this.elements.chatImg.src = defaultAvatar;
        };
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

    // Sistema de polling inteligente
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
        // Eliminar esta sección si la lógica de bloqueo se maneja en friendship_modals.js
    }

    // Configuración de handlers de reporte
    setupReportHandlers() {
        const reportButton = document.querySelector('.report-user-btn');
        if (reportButton) {
            reportButton.addEventListener('click', async () => {
                if (!this.currentUserId) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Selecciona un chat para reportar al usuario',
                        icon: 'warning'
                    });
                    return;
                }

                try {
                    const { value: formValues } = await Swal.fire({
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
                        focusConfirm: false,
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
                    });

                    if (formValues) {
                        const response = await fetch('/reportes/crear', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                id_reportado: this.currentUserId,
                                titulo: formValues.title,
                                descripcion: formValues.description
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            Swal.fire({
                                title: '¡Reporte enviado!',
                                text: 'El reporte ha sido enviado correctamente.',
                                icon: 'success'
                            });
                        } else {
                            throw new Error(data.message || 'Error al enviar el reporte');
                        }
                    }
                } catch (error) {
                    console.error('Error al reportar usuario:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo enviar el reporte',
                        icon: 'error'
                    });
                }
            });
        }
    }

    async cargarSolicitudesAmistad() {
        if (window.FriendshipManager && window.FriendshipManager.cargarSolicitudesAmistad) {
            return window.FriendshipManager.cargarSolicitudesAmistad();
        }
        console.warn('FriendshipManager no está disponible');
    }

    async responderSolicitud(idSolicitud, aceptar) {
        if (window.FriendshipManager && window.FriendshipManager.responderSolicitud) {
            return window.FriendshipManager.responderSolicitud(idSolicitud, aceptar);
        }
        console.warn('FriendshipManager no está disponible');
    }
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    window.chatManager = new ChatManager();

    // Configurar el modal de solicitudes
    const btnSolicitudesPendientes = document.getElementById('btnSolicitudesPendientes');
    if (btnSolicitudesPendientes) {
        btnSolicitudesPendientes.addEventListener('click', () => {
            const solicitudesModal = new bootstrap.Modal(document.getElementById('solicitudesModal'));
            solicitudesModal.show();
            if (window.FriendshipManager) {
                window.FriendshipManager.cargarSolicitudesAmistad();
            }
        });
    }

    // Gestionar el modal de solicitudes
    const solicitudesModalEl = document.getElementById('solicitudesModal');
    if (solicitudesModalEl) {
        let solicitudesInterval;
        solicitudesModalEl.addEventListener('show.bs.modal', () => {
            if (window.FriendshipManager) {
                window.FriendshipManager.cargarSolicitudesAmistad();
                // Iniciar polling de solicitudes solo cuando el modal está abierto
                solicitudesInterval = setInterval(() => 
                    window.FriendshipManager.cargarSolicitudesAmistad(), 30000);
            }
        });
        solicitudesModalEl.addEventListener('hidden.bs.modal', () => {
            // Limpiar polling de solicitudes al cerrar el modal
            if (solicitudesInterval) {
                clearInterval(solicitudesInterval);
                solicitudesInterval = null;
                console.log('Intervalo de solicitudes detenido al cerrar modal.');
            }
        });
    }

    // Cargar solicitudes inicialmente
    if (window.FriendshipManager) {
        window.FriendshipManager.cargarSolicitudesAmistad();
    }
});

// Añadir estilos CSS para las animaciones
const chatStyles = document.createElement('style');
chatStyles.textContent = `
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
document.head.appendChild(chatStyles);
