// Variables globales para el chat
let chatId = null;
let companero = null;
let buscandoCompanero = true;
let usuarioReportadoId = null; // Nueva variable para mantener el ID del usuario a reportar
let ultimoSkip = null; // Variable para controlar el cooldown del skip

// Función para verificar si el skip está en cooldown
async function skipEnCooldown() {
    try {
        const response = await fetch('/retos/verificar-skip', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            return data.en_cooldown;
        }
        return false;
    } catch (error) {
        console.error('Error al verificar cooldown:', error);
        return false;
    }
}

// Función para obtener tiempo restante de cooldown
async function getTiempoRestanteCooldown() {
    try {
        const response = await fetch('/retos/tiempo-skip', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            return data.tiempo_restante;
        }
        return '00:00';
    } catch (error) {
        console.error('Error al obtener tiempo restante:', error);
        return '00:00';
    }
}

// Función para actualizar el estado del botón skip
async function actualizarBotonSkip() {
    const skipBtn = document.querySelector('.skip-btn');
    if (!skipBtn) return;

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
}

// Función para buscar un compañero automáticamente
async function buscarCompaneroAutomatico() {
    if (chatId) {
        return;
    }
    
    // Detener cualquier control de inactividad existente mientras se busca
    if (window.detenerControlInactividad) {
        window.detenerControlInactividad();
    }
    
    while (buscandoCompanero) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            const response = await fetch('/retos/buscar-companero', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).catch(() => null);
            
            if (response && response.ok) {
                const data = await response.json();
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
                
                // Solo iniciar el control de inactividad cuando se encuentra un compañero
                if (window.iniciarControlInactividad) {
                    setTimeout(() => {
                        window.iniciarControlInactividad();
                        if (window.actualizarUltimoMensaje) {
                            window.actualizarUltimoMensaje();
                        }
                    }, 1000); // Pequeño retraso para asegurar que todo está listo
                }
                break;
            }
            
            await new Promise(resolve => setTimeout(resolve, 3000));
            
        } catch (error) {
            await new Promise(resolve => setTimeout(resolve, 3000));
        }
    }
}

// Función para mostrar la animación de puntos ganados
function mostrarPuntosGanados(puntos) {
    if (!puntos) return;
    
    const puntosContainer = document.querySelector('.puntos-container');
    
    // Crear elemento de animación
    const animacion = document.createElement('span');
    animacion.className = 'puntos-animacion';
    animacion.textContent = `+${puntos}`;
    puntosContainer.appendChild(animacion);
    
    // Eliminar la animación después de que termine
    setTimeout(() => {
        animacion.remove();
    }, 1500);
}

// Función para enviar mensaje
function enviarMensaje() {
    if (!chatId) {
        alert('Esperando a encontrar un compañero...');
        return;
    }

    const mensajeInput = document.getElementById('mensajeInput');
    const mensaje = mensajeInput.value.trim();

    if (!mensaje) return;
    
    // Validar longitud máxima de 500 caracteres
    if (mensaje.length > 500) {
        Swal.fire({
            title: 'Mensaje demasiado largo',
            text: 'El mensaje no puede exceder los 500 caracteres',
            icon: 'warning'
        });
        return;
    }
    
    // Procesar el mensaje según el reto actual si existe la función
    let mensajeProcesado;
    try {
        mensajeProcesado = window.procesarMensaje ? window.procesarMensaje(mensaje) : mensaje;
    } catch (error) {
        mensajeProcesado = mensaje;
    }
    
    // Si el mensaje procesado es null, significa que no pasó la validación del reto
    if (mensajeProcesado === null) {
        return;
    }

    // Manejar el caso donde el mensaje procesado es un objeto
    let contenidoMensaje;
    let tieneEmojis = false;
    let sumarPuntos = true; // Por defecto, sí sumamos puntos
    
    if (typeof mensajeProcesado === 'object' && mensajeProcesado !== null) {
        contenidoMensaje = mensajeProcesado.texto;
        tieneEmojis = mensajeProcesado.tieneEmojis || false;
        // Para el reto 3, verificar si debe sumar puntos basado en la longitud
        if (mensajeProcesado.hasOwnProperty('sumarPuntos')) {
            sumarPuntos = mensajeProcesado.sumarPuntos;
        }
    } else {
        contenidoMensaje = mensajeProcesado;
    }

    fetch('/retos/enviar-mensaje', {
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
    })
    .then(response => response.json())
    .then(data => {
        if (data.mensaje) {
            mensajeInput.value = '';
            agregarMensaje(data.mensaje, data.usuario);
            // Mostrar animación de puntos si se ganaron
            if (data.puntos_ganados > 0) {
                // Actualizar puntos diarios
                const puntosDiariosActuales = document.getElementById('puntos-diarios-actuales');
                const puntosDiariosNum = parseInt(puntosDiariosActuales.textContent);
                const nuevosPuntosDiarios = puntosDiariosNum + data.puntos_ganados;
                puntosDiariosActuales.textContent = nuevosPuntosDiarios;
                
                // Actualizar puntos totales
                const puntosActuales = document.getElementById('puntos-actuales');
                const puntosTotalNum = parseInt(puntosActuales.textContent);
                puntosActuales.textContent = puntosTotalNum + data.puntos_ganados;
                
                // Mostrar animación
                const puntosContainer = document.querySelector('.puntos-container');
                const animacion = document.createElement('span');
                animacion.className = 'puntos-animacion';
                animacion.textContent = `+${data.puntos_ganados}`;
                puntosContainer.appendChild(animacion);
                setTimeout(() => animacion.remove(), 1500);
            }
            // Actualizar el tiempo de inactividad cuando se envía un mensaje
            if (window.actualizarUltimoMensaje) {
                window.actualizarUltimoMensaje();
            }
        } else {
            alert(data.error);
        }
    })
    .catch(error => {
        alert('Error al enviar mensaje');
    });
}

// Función para cargar mensajes
async function cargarMensajes() {
    if (!chatId) return;

    try {
        const response = await fetch(`/retos/mensajes/${chatId}`);
        const mensajes = await response.json();
        
        const container = document.getElementById('mensajesContainer');
        container.innerHTML = '';
        
        mensajes.forEach(mensaje => {
            agregarMensaje(mensaje, mensaje.chat_usuario.usuario);
        });
    } catch (error) {
        // Error silencioso
    }
}

// Función para agregar un mensaje al contenedor
function agregarMensaje(mensaje, usuario) {
    const container = document.getElementById('mensajesContainer');
    const metaUserId = document.querySelector('meta[name="user-id"]');
    const esMio = metaUserId ? usuario.id === parseInt(metaUserId.content) : false;
    
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `reto-message ${esMio ? 'sent' : 'received'}`;
    mensajeDiv.style.display = 'flex';
    mensajeDiv.style.alignItems = 'flex-start';
    mensajeDiv.style.gap = '10px';
    mensajeDiv.style.marginBottom = '8px';
    
    // Imagen del usuario
    const userImage = document.createElement('img');
    userImage.src = usuario.imagen || '';
    userImage.alt = usuario.username;
    userImage.className = 'reto-message-user-image';
    userImage.style.width = '40px';
    userImage.style.height = '40px';
    userImage.style.objectFit = 'cover';
    userImage.style.borderRadius = '50%';
    userImage.onerror = function() {
        this.src = '/IMG/profile_img/avatar-default.png';
    };
    
    // Contenedor principal del mensaje (nombre, mensaje, hora)
    const mensajeWrapper = document.createElement('div');
    mensajeWrapper.className = 'reto-message-wrapper';
    mensajeWrapper.style.display = 'flex';
    mensajeWrapper.style.flexDirection = 'column';
    mensajeWrapper.style.alignItems = 'flex-start';
    
    // Nombre del usuario
    const userName = document.createElement('span');
    userName.className = 'reto-message-username';
    userName.textContent = usuario.username;
    userName.style.fontWeight = 'bold';
    userName.style.color = esMio ? '#4B0082' : '#6c757d';
    userName.style.fontSize = '15px';
    userName.style.marginBottom = '2px';
    
    // Contenido del mensaje
    const contenido = document.createElement('div');
    contenido.className = 'reto-message-content';
    contenido.textContent = mensaje.contenido;
    contenido.style.background = esMio ? '#4B0082' : '#f3e6ff';
    contenido.style.color = esMio ? 'white' : '#222';
    contenido.style.padding = '10px 18px';
    contenido.style.borderRadius = '14px';
    contenido.style.marginBottom = '2px';
    contenido.style.maxWidth = '600px';
    contenido.style.fontSize = '16px';
    contenido.style.wordBreak = 'break-word';
    
    // Fecha del mensaje
    const fecha = document.createElement('div');
    fecha.className = 'reto-message-time';
    const fechaMensaje = new Date(mensaje.fecha_envio);
    fecha.textContent = fechaMensaje.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    fecha.style.fontSize = '12px';
    fecha.style.color = '#888';
    fecha.style.marginLeft = '2px';
    
    // Añadir elementos al wrapper
    mensajeWrapper.appendChild(userName);
    mensajeWrapper.appendChild(contenido);
    mensajeWrapper.appendChild(fecha);
    
    // Añadir imagen y wrapper al mensajeDiv
    mensajeDiv.appendChild(userImage);
    mensajeDiv.appendChild(mensajeWrapper);
    container.appendChild(mensajeDiv);
    
    // Asegurarse de que el contenedor se desplace al último mensaje
    container.scrollTop = container.scrollHeight;
}

// Función para verificar el estado del chat
async function verificarEstadoChat() {
    if (!chatId) return;

    try {
        // Verificar directamente si el chat sigue existiendo
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
            
            // Detener el control de inactividad cuando el chat termina
            if (window.detenerControlInactividad) {
                window.detenerControlInactividad();
            }
            
            // Actualizar la interfaz inmediatamente
            document.getElementById('chatHeader').innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    Buscando usuarios disponibles...
                </div>
            `;
            
            // Ocultar el menú de opciones
            document.getElementById('chatOptions').style.display = 'none';
            
            // Limpiar el contenedor de mensajes
            document.getElementById('mensajesContainer').innerHTML = '';
            
            // Reiniciar la búsqueda
            buscarCompaneroAutomatico();
        }
    } catch (error) {
        chatId = null;
        companero = null;
        buscandoCompanero = true;
        
        // Detener el control de inactividad cuando hay un error
        if (window.detenerControlInactividad) {
            window.detenerControlInactividad();
        }
        
        document.getElementById('chatHeader').innerHTML = `
            <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                Buscando usuarios disponibles...
            </div>
        `;
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

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    buscarCompaneroAutomatico();
    actualizarPuntosDiarios(); // Actualizar puntos diarios al cargar la página
    
    // Configurar el botón de skip
    document.querySelector('.skip-btn').addEventListener('click', async function() {
        console.log('Botón skip clickeado');
        if (await skipEnCooldown()) {
            const tiempoRestante = await getTiempoRestanteCooldown();
            Swal.fire({
                title: 'Espera un momento',
                text: `Debes esperar ${tiempoRestante} antes de volver a usar el skip`,
                icon: 'warning'
            });
            return;
        }

        if (!chatId) {
            return;
        }

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Saltarás a este usuario y buscarás uno nuevo. Deberás esperar 10 minutos antes de poder usar el skip nuevamente.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, saltar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    console.log('Activando skip...');
                    const response = await fetch('/retos/activar-skip', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    console.log('Respuesta activar skip:', response.status);
                    if (response.ok) {
                        const data = await response.json();
                        console.log('Datos respuesta:', data);
                        
                        chatId = null;
                        companero = null;
                        buscandoCompanero = true;
                        
                        // Detener el control de inactividad
                        if (window.detenerControlInactividad) {
                            window.detenerControlInactividad();
                        }
                        
                        // Actualizar la interfaz
                        document.getElementById('chatHeader').innerHTML = `
                            <div class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                Buscando usuarios disponibles...
                            </div>
                        `;
                        
                        // Ocultar el menú de opciones
                        document.getElementById('chatOptions').style.display = 'none';
                        
                        // Limpiar el contenedor de mensajes
                        document.getElementById('mensajesContainer').innerHTML = '';
                        
                        actualizarBotonSkip();
                        buscarCompaneroAutomatico();
                    }
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
    });

    // Configurar el botón de enviar mensaje
    document.getElementById('enviarMensaje').addEventListener('click', enviarMensaje);
    
    // Configurar el input para enviar con Enter
    document.getElementById('mensajeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            enviarMensaje();
        }
    });

    // Actualizar estado cada 2 minutos
    setInterval(mantenerEstado, 120000);
    
    // Polling para actualizar mensajes y verificar estado del chat cada segundo
    setInterval(() => {
        if (chatId) {
            cargarMensajes();
            verificarEstadoChat();
            actualizarPuntosDiarios(); // Actualizar puntos diarios periódicamente
        }
    }, 1000); // Reducido a 1 segundo para mayor reactividad

    // Enviar solicitud de amistad
    document.getElementById('sendFriendRequest').addEventListener('click', function(e) {
        e.preventDefault();
        if (!companero) return;
        
        fetch('/solicitudes/enviar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                id_receptor: companero.id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const sendFriendRequestBtn = document.getElementById('sendFriendRequest');
                sendFriendRequestBtn.innerHTML = '<i class="fas fa-clock me-2"></i>Solicitud pendiente';
                sendFriendRequestBtn.classList.add('disabled');
                sendFriendRequestBtn.style.pointerEvents = 'none';
                sendFriendRequestBtn.style.opacity = '0.7';
                
                Swal.fire({
                    title: '¡Solicitud enviada!',
                    text: 'La solicitud de amistad ha sido enviada correctamente.',
                    icon: 'success'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'No se pudo enviar la solicitud de amistad.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al enviar la solicitud.',
                icon: 'error'
            });
        });
    });

    // Reportar usuario
    document.getElementById('reportUser').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Verificar que tenemos un ID de usuario válido para reportar
        if (!usuarioReportadoId) {
            Swal.fire({
                title: 'Error',
                text: 'No hay un usuario seleccionado para reportar',
                icon: 'error'
            });
            return;
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
                // Usar el ID guardado en lugar del compañero actual
                fetch('/reportes/crear', {
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
                })
                .then(response => response.json())
                .then(data => {
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
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al enviar el reporte.',
                        icon: 'error'
                    });
                });
            }
        });
    });

    // Bloquear usuario
    document.getElementById('blockUser').addEventListener('click', function(e) {
        e.preventDefault();
        if (!companero) return;

        Swal.fire({
            title: '¿Bloquear usuario?',
            text: '¿Estás seguro de que deseas bloquear a este usuario? No podrás ver sus mensajes ni interactuar con él.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, bloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/solicitudes/bloquear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        id_usuario: companero.id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Usuario bloqueado!',
                            text: 'El usuario ha sido bloqueado correctamente.',
                            icon: 'success'
                        }).then(() => {
                            // Recargar la página para aplicar el bloqueo
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'No se pudo bloquear al usuario.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al bloquear al usuario.',
                        icon: 'error'
                    });
                });
            }
        });
    });

    // Intervalo para actualizar el estado del botón skip cada segundo
    setInterval(actualizarBotonSkip, 1000);
});

// Actualizar estado cuando el usuario cierra la pestaña
window.addEventListener('beforeunload', () => {
    fetch('/estado/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ estado: 2 }) // Estado inactivo
    });
});

// Actualizar estado inicial
mantenerEstado(); 