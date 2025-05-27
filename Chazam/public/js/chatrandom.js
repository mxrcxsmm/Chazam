// Función utilitaria para obtener la ruta correcta de la imagen de perfil
function getProfileImgPath(img) {
    if (!img || img === 'avatar-default.png' || img === '/img/profile_img/avatar-default.png') {
        return `${window.location.origin}/img/profile_img/avatar-default.png`;
    }
    if (img.startsWith('http://') || img.startsWith('https://')) {
        return img;
    }
    const cleanImg = img.replace(/^\/?img\/profile_img\//, '');
    return `${window.location.origin}/img/profile_img/${cleanImg}`;
}

// Función utilitaria para manejar errores de fetch
async function safeFetch(url, options = {}) {
    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error en la petición:', error);
        throw error;
    }
}

// Variables globales para el chat
let chatId = null;
let companero = null;
let buscandoCompanero = true;
let usuarioReportadoId = null;
let ultimoSkip = null;
let pollingIntervalId = null;
let solicitudesIntervalId = null;
let estadoIntervalId = null;

// Función para limpiar todos los intervalos
function limpiarIntervalos() {
    if (pollingIntervalId) {
        clearInterval(pollingIntervalId);
        pollingIntervalId = null;
    }
    if (solicitudesIntervalId) {
        clearInterval(solicitudesIntervalId);
        solicitudesIntervalId = null;
    }
    if (estadoIntervalId) {
        clearInterval(estadoIntervalId);
        estadoIntervalId = null;
    }
}

// Función para verificar si el skip está en cooldown
async function skipEnCooldown() {
    try {
        const data = await safeFetch('/retos/verificar-skip', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        return data.en_cooldown;
    } catch (error) {
        console.error('Error al verificar cooldown:', error);
        return false;
    }
}

// Función para obtener tiempo restante de cooldown
async function getTiempoRestanteCooldown() {
    try {
        const data = await safeFetch('/retos/tiempo-skip', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        return data.tiempo_restante;
    } catch (error) {
        console.error('Error al obtener tiempo restante:', error);
        return '00:00';
    }
}

// Función para actualizar el estado del botón skip
async function actualizarBotonSkip() {
    const skipBtn = document.querySelector('.skip-btn');
    if (!skipBtn) return;

    try {
        const enCooldown = await skipEnCooldown();
        const tiempoRestante = await getTiempoRestanteCooldown();

        if (enCooldown) {
            skipBtn.disabled = true;
            skipBtn.innerHTML = `Skip <span class="time">(${tiempoRestante})</span><span class="triangle"></span><span class="triangle tight"></span>`;
            skipBtn.classList.add('disabled');
        } else {
            skipBtn.disabled = false;
            skipBtn.innerHTML = `Skip<span class="triangle"></span><span class="triangle tight"></span>`;
            skipBtn.classList.remove('disabled');
        }
    } catch (error) {
        console.error('Error al actualizar botón skip:', error);
    }
}

// Función para buscar un compañero automáticamente
async function buscarCompaneroAutomatico() {
    if (chatId) return;

    if (window.detenerControlInactividad) {
        window.detenerControlInactividad();
    }
    
    limpiarIntervalos();

    while (buscandoCompanero) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('No se encontró el token CSRF');
            }

            const data = await safeFetch('/retos/buscar-companero', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                }
            });

            chatId = data.chat_id;
            companero = data.companero;
            usuarioReportadoId = companero.id;
            
            const chatHeader = document.getElementById('chatHeader');
            const chatOptions = document.getElementById('chatOptions');
            
            if (chatHeader && chatOptions) {
                chatHeader.innerHTML = `Chat con ${companero.username}`;
                chatOptions.style.display = 'block';
                verificarEstadoSolicitud();
            }
            
            buscandoCompanero = false;
            cargarMensajes();
            
            // Iniciar polling solo cuando se encuentra un compañero
            pollingIntervalId = setInterval(() => {
                if (chatId) {
                    cargarMensajes();
                    verificarEstadoChat();
                    actualizarPuntosDiarios();
                }
            }, 3000);
            
            if (window.iniciarControlInactividad) {
                setTimeout(() => {
                    window.iniciarControlInactividad();
                    if (window.actualizarUltimoMensaje) {
                        window.actualizarUltimoMensaje();
                    }
                }, 15000);
            }
            break;
            
        } catch (error) {
            console.error('Error al buscar compañero:', error);
            await new Promise(resolve => setTimeout(resolve, 3000));
        }
    }
}

// Función para mostrar la animación de puntos ganados
function mostrarPuntosGanados(puntos) {
    if (!puntos) return;
    
    const puntosContainer = document.querySelector('.puntos-container');
    if (!puntosContainer) return;
    
    const animacion = document.createElement('span');
    animacion.className = 'puntos-animacion';
    animacion.textContent = `+${puntos}`;
    puntosContainer.appendChild(animacion);
    
    setTimeout(() => animacion.remove(), 1500);
}

// Función para enviar mensaje
async function enviarMensaje() {
    if (!chatId) {
        Swal.fire({
            title: 'Espera',
            text: 'Esperando a encontrar un compañero...',
            icon: 'info'
        });
        return;
    }

    const mensajeInput = document.getElementById('mensajeInput');
    const mensaje = mensajeInput.value.trim();

    if (!mensaje) return;
    
    if (mensaje.length > 500) {
        Swal.fire({
            title: 'Mensaje demasiado largo',
            text: 'El mensaje no puede exceder los 500 caracteres',
            icon: 'warning'
        });
        return;
    }
    
    let mensajeProcesado;
    try {
        mensajeProcesado = window.procesarMensaje ? window.procesarMensaje(mensaje) : mensaje;
    } catch (error) {
        console.error('Error al procesar mensaje:', error);
        mensajeProcesado = mensaje;
    }
    
    if (mensajeProcesado === null) return;

    let contenidoMensaje;
    let tieneEmojis = false;
    let sumarPuntos = true;
    
    if (typeof mensajeProcesado === 'object' && mensajeProcesado !== null) {
        contenidoMensaje = mensajeProcesado.texto;
        tieneEmojis = mensajeProcesado.tieneEmojis || false;
        if (mensajeProcesado.hasOwnProperty('sumarPuntos')) {
            sumarPuntos = mensajeProcesado.sumarPuntos;
        }
    } else {
        contenidoMensaje = mensajeProcesado;
    }

    try {
        const data = await safeFetch('/retos/enviar-mensaje', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                chat_id: chatId,
                contenido: contenidoMensaje,
                tieneEmojis: tieneEmojis,
                sumarPuntos: sumarPuntos
            })
        });

        if (data.mensaje) {
            mensajeInput.value = '';
            agregarMensaje(data.mensaje, data.usuario);
            
            if (data.puntos_ganados > 0) {
                const puntosDiariosActuales = document.getElementById('puntos-diarios-actuales');
                const puntosActuales = document.getElementById('puntos-actuales');
                
                if (puntosDiariosActuales && puntosActuales) {
                    const puntosDiariosNum = parseInt(puntosDiariosActuales.textContent);
                    const puntosTotalNum = parseInt(puntosActuales.textContent);
                    
                    puntosDiariosActuales.textContent = puntosDiariosNum + data.puntos_ganados;
                    puntosActuales.textContent = puntosTotalNum + data.puntos_ganados;
                }
                
                mostrarPuntosGanados(data.puntos_ganados);
            }
            
            if (window.actualizarUltimoMensaje) {
                window.actualizarUltimoMensaje();
            }
        }
    } catch (error) {
        console.error('Error al enviar mensaje:', error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudo enviar el mensaje',
            icon: 'error'
        });
    }
}

// Función para cargar mensajes
async function cargarMensajes() {
    if (!chatId) return;

    try {
        const mensajes = await safeFetch(`/retos/mensajes/${chatId}`);
        const container = document.getElementById('mensajesContainer');
        if (!container) return;
        
        container.innerHTML = '';
        
        mensajes.forEach(mensaje => {
            agregarMensaje(mensaje, mensaje.chat_usuario.usuario);
        });
    } catch (error) {
        console.error('Error al cargar mensajes:', error);
    }
}

// Función para agregar un mensaje al contenedor
function agregarMensaje(mensaje, usuario) {
    const container = document.getElementById('mensajesContainer');
    if (!container) return;

    const metaUserId = document.querySelector('meta[name="user-id"]');
    const esMio = metaUserId ? usuario.id === parseInt(metaUserId.content) : false;
    
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `reto-message ${esMio ? 'sent' : 'received'}`;
    mensajeDiv.style.cssText = 'display: flex; align-items: flex-start; gap: 10px; margin-bottom: 8px;';
    
    const userImage = document.createElement('img');
    userImage.src = getProfileImgPath(usuario.imagen);
    userImage.alt = usuario.username;
    userImage.className = 'reto-message-user-image';
    userImage.style.cssText = 'width: 40px; height: 40px; object-fit: cover; border-radius: 50%;';
    
    const mensajeWrapper = document.createElement('div');
    mensajeWrapper.className = 'reto-message-wrapper';
    mensajeWrapper.style.cssText = 'display: flex; flex-direction: column; align-items: flex-start;';
    
    const userName = document.createElement('span');
    userName.className = 'reto-message-username';
    userName.textContent = usuario.username;
    userName.style.cssText = 'font-weight: bold; color: ' + (esMio ? '#4B0082' : '#6c757d') + '; font-size: 15px; margin-bottom: 2px;';
    
    const contenido = document.createElement('div');
    contenido.className = 'reto-message-content';
    contenido.textContent = mensaje.contenido;
    contenido.style.cssText = 'background: ' + (esMio ? '#4B0082' : '#f3e6ff') + '; color: ' + (esMio ? 'white' : '#222') + '; padding: 10px 18px; border-radius: 14px; margin-bottom: 2px; max-width: 600px; font-size: 16px; word-break: break-word;';
    
    const fecha = document.createElement('div');
    fecha.className = 'reto-message-time';
    const fechaMensaje = new Date(mensaje.fecha_envio);
    fecha.textContent = fechaMensaje.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    fecha.style.cssText = 'font-size: 12px; color: #888; margin-left: 2px;';
    
    mensajeWrapper.appendChild(userName);
    mensajeWrapper.appendChild(contenido);
    mensajeWrapper.appendChild(fecha);
    
    mensajeDiv.appendChild(userImage);
    mensajeDiv.appendChild(mensajeWrapper);
    container.appendChild(mensajeDiv);
    
    container.scrollTop = container.scrollHeight;
}

// Función para verificar el estado del chat
async function verificarEstadoChat() {
    if (!chatId) return;

    try {
        const chatResponse = await fetch(`/retos/verificar-chat/${chatId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!chatResponse.ok) {
            chatId = null;
            companero = null;
            buscandoCompanero = true;
            
            if (window.detenerControlInactividad) {
                window.detenerControlInactividad();
            }
            
            limpiarIntervalos();
            
            const chatHeader = document.getElementById('chatHeader');
            if (chatHeader) {
                chatHeader.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        Buscando usuarios disponibles...
                    </div>
                `;
            }
            
            const chatOptions = document.getElementById('chatOptions');
            if (chatOptions) {
                chatOptions.style.display = 'none';
            }
            
            const mensajesContainer = document.getElementById('mensajesContainer');
            if (mensajesContainer) {
                mensajesContainer.innerHTML = '';
            }
            
            buscarCompaneroAutomatico();
        }
    } catch (error) {
        console.error('Error al verificar estado del chat:', error);
        chatId = null;
        companero = null;
        buscandoCompanero = true;
        
        if (window.detenerControlInactividad) {
            window.detenerControlInactividad();
        }
        
        limpiarIntervalos();
        
        const chatHeader = document.getElementById('chatHeader');
        if (chatHeader) {
            chatHeader.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    Buscando usuarios disponibles...
                </div>
            `;
        }
        buscarCompaneroAutomatico();
    }
}

// Función para actualizar el contador de puntos diarios
async function actualizarPuntosDiarios() {
    try {
        const response = await fetch('/retos/puntos-diarios', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            const puntosDiariosElement = document.getElementById('puntos-diarios-actuales');
            if (puntosDiariosElement) {
                // Solo actualizar si el valor es mayor que el actual
                const puntosActuales = parseInt(puntosDiariosElement.textContent);
                if (data.puntos_diarios > puntosActuales) {
                    puntosDiariosElement.textContent = data.puntos_diarios;
                }
            }
        }
    } catch (error) {
        // Error silencioso
    }
}

// Función para verificar el estado de la solicitud
async function verificarEstadoSolicitud() {
    if (!companero) return;
    
    try {
        const response = await fetch(`/solicitudes/verificar/${companero.id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            const sendFriendRequestBtn = document.getElementById('sendFriendRequest');
            
            if (data.estado === 'pendiente') {
                sendFriendRequestBtn.innerHTML = '<i class="fas fa-clock me-2"></i>Solicitud pendiente';
                sendFriendRequestBtn.classList.add('disabled');
                sendFriendRequestBtn.style.pointerEvents = 'none';
                sendFriendRequestBtn.style.opacity = '0.7';
            } else if (data.estado === 'aceptada') {
                sendFriendRequestBtn.parentElement.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error al verificar estado de solicitud:', error);
    }
}

// Función para cargar las solicitudes de amistad
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

        // Actualizar contador
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
            solicitudDiv.className = 'solicitud-item d-flex align-items-center justify-content-between p-2 border-bottom';
            solicitudDiv.id = `solicitud-${solicitud.id_solicitud}`;
            solicitudDiv.innerHTML = `
                <div class="solicitud-info d-flex align-items-center gap-2">
                    <img src="${solicitud.emisor.img || '/img/profile_img/avatar-default.png'}" 
                         alt="${solicitud.emisor.username}" 
                         class="rounded-circle"
                         style="width: 32px; height: 32px; object-fit: cover;"
                         onerror="this.src='/img/profile_img/avatar-default.png'">
                    <span class="solicitud-username">${solicitud.emisor.username}</span>
                </div>
                <div class="solicitud-actions d-flex gap-2">
                    <button class="btn btn-sm btn-success" onclick="responderSolicitud(${solicitud.id_solicitud}, 'aceptada')">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="responderSolicitud(${solicitud.id_solicitud}, 'rechazada')">
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
        // Solo muestra el SweetAlert si realmente no hay contenedor
        if (!container) {
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron cargar las solicitudes de amistad',
            icon: 'error'
        });
        }
    }
}

// Función para responder a una solicitud
async function responderSolicitud(idSolicitud, respuesta) {
    try {
        const solicitudDiv = document.getElementById(`solicitud-${idSolicitud}`);
        if (!solicitudDiv) return;

        // Deshabilitar botones durante la operación
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
                // Eliminar el elemento del DOM si fue rechazada
                solicitudDiv.remove();
            // Actualizar el contador de solicitudes
            const solicitudesCount = document.getElementById('solicitudesCount');
            const count = parseInt(solicitudesCount.textContent);
            solicitudesCount.textContent = Math.max(0, count - 1);
            // Si no hay más solicitudes, mostrar el mensaje
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
                // Si fue aceptada, actualizar el badge
                const actionsDiv = solicitudDiv.querySelector('.solicitud-actions');
                actionsDiv.innerHTML = `
                    <span class="badge bg-success">Aceptada</span>
                `;
            }
            // Mostrar mensaje de éxito
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

        // Restaurar botones en caso de error
        const solicitudDiv = document.getElementById(`solicitud-${idSolicitud}`);
        if (solicitudDiv) {
            const buttons = solicitudDiv.querySelectorAll('button');
            buttons.forEach(btn => btn.disabled = false);
        }
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    buscarCompaneroAutomatico();
    actualizarPuntosDiarios();
    
    const skipBtn = document.querySelector('.skip-btn');
    if (skipBtn) {
        skipBtn.addEventListener('click', async function() {
            if (await skipEnCooldown()) {
                const tiempoRestante = await getTiempoRestanteCooldown();
                Swal.fire({
                    title: 'Espera un momento',
                    text: `Debes esperar ${tiempoRestante} antes de volver a usar el skip`,
                    icon: 'warning'
                });
                return;
            }

            if (!chatId) return;

            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: 'Saltarás a este usuario y buscarás uno nuevo. Deberás esperar 10 minutos antes de poder usar el skip nuevamente.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, saltar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    const data = await safeFetch('/retos/activar-skip', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    chatId = null;
                    companero = null;
                    buscandoCompanero = true;
                    
                    if (window.detenerControlInactividad) {
                        window.detenerControlInactividad();
                    }
                    
                    limpiarIntervalos();
                    
                    const chatHeader = document.getElementById('chatHeader');
                    if (chatHeader) {
                        chatHeader.innerHTML = `
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                Buscando usuarios disponibles...
                            </div>
                        `;
                    }
                    
                    const chatOptions = document.getElementById('chatOptions');
                    if (chatOptions) {
                        chatOptions.style.display = 'none';
                    }
                    
                    const mensajesContainer = document.getElementById('mensajesContainer');
                    if (mensajesContainer) {
                        mensajesContainer.innerHTML = '';
                    }
                    
                    actualizarBotonSkip();
                    buscarCompaneroAutomatico();
                } catch (error) {
                    console.error('Error al activar skip:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al intentar saltar al siguiente usuario',
                        icon: 'error'
                    });
                }
            }
        });
    }

    const enviarMensajeBtn = document.getElementById('enviarMensaje');
    if (enviarMensajeBtn) {
        enviarMensajeBtn.addEventListener('click', enviarMensaje);
    }
    
    const mensajeInput = document.getElementById('mensajeInput');
    if (mensajeInput) {
        mensajeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                enviarMensaje();
            }
        });
    }

    estadoIntervalId = setInterval(mantenerEstado, 120000);

    const sendFriendRequestBtn = document.getElementById('sendFriendRequest');
    if (sendFriendRequestBtn) {
        sendFriendRequestBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            if (!companero) return;
            
            try {
                const data = await safeFetch('/solicitudes/enviar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        id_receptor: companero.id
                    })
                });

                if (data.success) {
                    sendFriendRequestBtn.innerHTML = '<i class="fas fa-clock me-2"></i>Solicitud pendiente';
                    sendFriendRequestBtn.classList.add('disabled');
                    sendFriendRequestBtn.style.pointerEvents = 'none';
                    sendFriendRequestBtn.style.opacity = '0.7';
                    
                    Swal.fire({
                        title: '¡Solicitud enviada!',
                        text: 'La solicitud de amistad ha sido enviada correctamente.',
                        icon: 'success'
                    });
                }
            } catch (error) {
                console.error('Error al enviar solicitud:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo enviar la solicitud de amistad.',
                    icon: 'error'
                });
            }
        });
    }

    const reportUserBtn = document.getElementById('reportUser');
    if (reportUserBtn) {
        reportUserBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            if (!usuarioReportadoId) {
                Swal.fire({
                    title: 'Error',
                    text: 'No hay un usuario seleccionado para reportar',
                    icon: 'error'
                });
                return;
            }

            const result = await Swal.fire({
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
            });

            if (result.isConfirmed) {
                try {
                    const data = await safeFetch('/reportes/crear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            id_reportado: usuarioReportadoId,
                            titulo: result.value.title,
                            descripcion: result.value.description
                        })
                    });

                    if (data.success) {
                        Swal.fire({
                            title: '¡Reporte enviado!',
                            text: 'El reporte ha sido enviado correctamente.',
                            icon: 'success'
                        });
                    }
                } catch (error) {
                    console.error('Error al enviar reporte:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al enviar el reporte.',
                        icon: 'error'
                    });
                }
            }
        });
    }

    const blockUserBtn = document.getElementById('blockUser');
    if (blockUserBtn) {
        blockUserBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            if (!companero) return;

            const result = await Swal.fire({
                title: '¿Bloquear usuario?',
                text: '¿Estás seguro de que deseas bloquear a este usuario? No podrás ver sus mensajes ni interactuar con él.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, bloquear',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    const data = await safeFetch('/solicitudes/bloquear', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            id_usuario: companero.id
                        })
                    });

                    if (data.success) {
                        Swal.fire({
                            title: '¡Usuario bloqueado!',
                            text: 'El usuario ha sido bloqueado correctamente.',
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                } catch (error) {
                    console.error('Error al bloquear usuario:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al bloquear al usuario.',
                        icon: 'error'
                    });
                }
            }
        });
    }

    // Configurar el botón de ver solicitudes
    const btnSolicitudesPendientes = document.getElementById('btnSolicitudesPendientes');
    if (btnSolicitudesPendientes) {
        btnSolicitudesPendientes.addEventListener('click', function(e) {
            e.preventDefault();
            const solicitudesModal = new bootstrap.Modal(document.getElementById('solicitudesModal'));
            solicitudesModal.show();
            cargarSolicitudesAmistad();
        });
        actualizarContadorSolicitudes();
    }

    // Gestionar el modal de solicitudes
    const solicitudesModalEl = document.getElementById('solicitudesModal');
    if (solicitudesModalEl) {
        solicitudesModalEl.addEventListener('show.bs.modal', function () {
            solicitudesIntervalId = setInterval(cargarSolicitudesAmistad, 30000);
        });
        solicitudesModalEl.addEventListener('hidden.bs.modal', function () {
            if (solicitudesIntervalId) {
                clearInterval(solicitudesIntervalId);
                solicitudesIntervalId = null;
            }
        });
    }

    // Configurar el botón de opciones del chat
    const chatOptionsButton = document.getElementById('chatOptionsButton');
    if (chatOptionsButton) {
        chatOptionsButton.addEventListener('click', function() {
            verificarEstadoSolicitud();
        });
    }
});

// Limpiar intervalos y actualizar estado al cerrar la página
window.addEventListener('beforeunload', () => {
    limpiarIntervalos();
    fetch('/estado/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ estado: 2 })
    });
});

// Actualizar estado inicial
mantenerEstado();

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