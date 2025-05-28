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
             const errorBody = await response.text();
             console.error(`Error HTTP! estado: ${response.status} URL: ${url} Cuerpo: ${errorBody}`);
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
let pollingIntervalId = null; // Intervalo principal para cargar mensajes y estado del chat
let solicitudesIntervalId = null; // Intervalo para solicitudes (manejado en estados.js o chatamig.js?) - Mantener si se usa aquí
let estadoIntervalId = null; // Intervalo para estado general (manejado en estados.js) - Mantener si se usa aquí
let lastMessageId = 0; // Para rastrear el último mensaje cargado

// Definir intervalos de polling en un solo lugar para fácil ajuste
const INTERVALO_POLLING_CHAT = 4000; // Aumentado a 4 segundos para reducir carga

// Función para limpiar todos los intervalos
function limpiarIntervalos() {
    if (pollingIntervalId) {
        clearInterval(pollingIntervalId);
        pollingIntervalId = null;
         console.log('Intervalo de polling del chat detenido.');
    }
    // Deberías detener también los intervalos de solicitudes y estado si se inician aquí
    if (solicitudesIntervalId) { // Asumiendo que podrías tener uno aquí o en otro archivo
        clearInterval(solicitudesIntervalId);
        solicitudesIntervalId = null;
         console.log('Intervalo de solicitudes detenido.');
    }
     if (estadoIntervalId) { // Asumiendo que podrías tener uno aquí o en otro archivo
        clearInterval(estadoIntervalId);
        estadoIntervalId = null;
         console.log('Intervalo de estado detenido.');
     }
    // Llama a detenerControlInactividad de estados.js si existe
    if (window.detenerControlInactividad) {
        window.detenerControlInactividad();
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

    // Limpiar intervalos existentes antes de buscar
    limpiarIntervalos();

    const chatHeader = document.getElementById('chatHeader');
    const chatOptions = document.getElementById('chatOptions');
    const mensajesContainer = document.getElementById('mensajesContainer');

     // Mostrar estado de búsqueda
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
     if (chatOptions) {
         chatOptions.style.display = 'none';
     }
     if (mensajesContainer) {
         mensajesContainer.innerHTML = ''; // Limpiar mensajes anteriores
     }
    lastMessageId = 0; // Resetear el último ID de mensaje

    buscandoCompanero = true;
    while (buscandoCompanero) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('No se encontró el token CSRF');
            }

            console.log('Intentando buscar compañero...');
            const data = await safeFetch('/retos/buscar-companero', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content
                }
            });

            // Si se encuentra un compañero
            if (data.chat_id && data.companero) {
                chatId = data.chat_id;
                companero = data.companero;
                usuarioReportadoId = companero.id; // Asumo que el ID del usuario reportado es el ID del compañero

                console.log('Compañero encontrado:', companero.username, 'Chat ID:', chatId);

                if (chatHeader && chatOptions) {
                    chatHeader.innerHTML = `Chat con ${companero.username}`;
                    chatOptions.style.display = 'block';
                    verificarEstadoSolicitud(); // Verificar si ya hay solicitud
                }

                buscandoCompanero = false;
                cargarMensajes(); // Cargar mensajes iniciales

                // Iniciar polling solo cuando se encuentra un compañero
                // El intervalo se aumentó aquí
                pollingIntervalId = setInterval(() => {
                    if (chatId) {
                        cargarMensajes(); // Carga solo mensajes nuevos
                        verificarEstadoChat(); // Verifica si el chat sigue activo
                        actualizarPuntosDiarios(); // Actualiza puntos
                    }
                }, INTERVALO_POLLING_CHAT); // Usa el intervalo optimizado

                // Iniciar control de inactividad de estados.js si está disponible
                // El setTimeout inicial ya fue ajustado en estados.js
                if (window.iniciarControlInactividad) {
                     // Llama iniciarControlInactividad con el tiempo de espera definido en estados.js
                     window.iniciarControlInactividad();
                }

                break; // Salir del bucle while una vez que se encuentra un compañero

            } else {
                 // Si no se encuentra compañero pero no hubo error (ej. no hay disponibles)
                 console.log('No se encontró compañero, reintentando en 8 segundos...');
                 await new Promise(resolve => setTimeout(resolve, 4000)); // Esperar antes de reintentar
            }

        } catch (error) {
            console.error('Error al buscar compañero:', error);
            // Si hay un error HTTP o de conexión, espera antes de reintentar
             // Solo mostramos un mensaje de error si no estamos buscando activamente (buscandoCompanero = true)
             if (buscandoCompanero) {
                 console.log('Error buscando compañero, reintentando en 8 segundos...');
                 await new Promise(resolve => setTimeout(resolve, 4000));
             } else {
                  // Si el error ocurre después de haber encontrado compañero, el verificarEstadoChat lo manejará
                 break; // Salir del bucle si ya no estamos buscando activamente
             }
        }
    }
}

// Función para mostrar la animación de puntos ganados
function mostrarPuntosGanados(puntos) {
    if (!puntos || puntos <= 0) return;

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

        // El backend en enviarMensaje ya devuelve el mensaje recién creado y el usuario
        if (data.mensaje && data.usuario) {
            mensajeInput.value = '';
            // Ahora llamamos a agregarMensaje para AÑADIR el nuevo mensaje, no recargar todo
            agregarMensaje(data.mensaje, data.usuario);

            if (data.puntos_ganados > 0) {
                const puntosDiariosActuales = document.getElementById('puntos-diarios-actuales');
                const puntosActuales = document.getElementById('puntos-actuales');

                if (puntosDiariosActuales && puntosActuales) {
                    const puntosDiariosNum = parseInt(puntosDiariosActuales.textContent || '0');
                    const puntosTotalNum = parseInt(puntosActuales.textContent || '0');

                    puntosDiariosActuales.textContent = puntosDiariosNum + data.puntos_ganados;
                    puntosActuales.textContent = puntosTotalNum + data.puntos_ganados;
                }

                mostrarPuntosGanados(data.puntos_ganados);
            }

            // Actualizar el tiempo del último mensaje para el control de inactividad
            if (window.actualizarUltimoMensaje) {
                window.actualizarUltimoMensaje();
            }
        } else {
             console.error('Respuesta inesperada al enviar mensaje:', data);
             Swal.fire({
                 title: 'Error',
                 text: 'No se pudo enviar el mensaje (respuesta inesperada)',
                 icon: 'error'
             });
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

// Función para cargar mensajes (OPTIMIZADA: solo carga mensajes nuevos)
async function cargarMensajes() {
    if (!chatId) return;

    console.log(`[Polling] Intentando cargar mensajes para chat ${chatId} con last_id ${lastMessageId}`);

    try {
        const mensajes = await safeFetch(`/retos/mensajes/${chatId}?last_id=${lastMessageId}`);
        const container = document.getElementById('mensajesContainer');
        if (!container) {
            console.error('[Polling] Contenedor de mensajes no encontrado');
            return;
        }

        if (mensajes && mensajes.length > 0) {
            console.log(`[Polling] Nuevos mensajes recibidos: ${mensajes.length}`, mensajes);
            
            // Ordenar mensajes por ID para asegurar orden correcto
            mensajes.sort((a, b) => a.id - b.id);
            
            let mensajesAgregados = 0;
            mensajes.forEach(mensaje => {
                // Verificación más robusta del objeto usuario anidado
                if (mensaje && mensaje.id_mensaje != null && mensaje.contenido != null &&
                    mensaje.chat_usuario && mensaje.chat_usuario.usuario) {
                    
                    // Normalizar el ID del usuario como en agregarMensaje
                    const userId = mensaje.chat_usuario.usuario?.id_usuario ?? mensaje.chat_usuario.usuario?.id;
                    
                    if (userId != null && mensaje.chat_usuario.usuario.username != null) {
                        console.log(`[Polling] Procesando mensaje ID: ${mensaje.id_mensaje}, Contenido: ${mensaje.contenido ? mensaje.contenido.substring(0, 50) + '...' : 'N/A'}`);
                        console.log('[Polling] Datos de usuario a pasar a agregarMensaje:', mensaje.chat_usuario.usuario);

                        if (agregarMensaje(mensaje, mensaje.chat_usuario.usuario)) {
                            mensajesAgregados++;
                        }
                    } else {
                        console.error('[Polling] Datos de usuario incompletos o inválidos:', mensaje.chat_usuario.usuario);
                    }
                } else {
                    console.error('[Polling] Mensaje inválido o incompleto (falta mensaje, chat_usuario, usuario o campos requeridos del usuario):', mensaje);
                    console.error('[Polling] Mensaje completo que falló la validación inicial:', JSON.stringify(mensaje, null, 2));
                }
            });

            console.log(`[Polling] Mensajes agregados exitosamente: ${mensajesAgregados}`);

            // Actualizar lastMessageId al ID del último mensaje recibido
            const maxMessageId = Math.max(...mensajes.map(m => m.id));
            if (maxMessageId > lastMessageId) {
                console.log(`[Polling] Actualizando lastMessageId de ${lastMessageId} a ${maxMessageId}`);
                lastMessageId = maxMessageId;
            }

            // Hacer scroll al último mensaje solo si se agregaron nuevos
            if (mensajesAgregados > 0) {
                container.scrollTop = container.scrollHeight;
            }
        } else {
            console.log('[Polling] No hay mensajes nuevos.');
        }
    } catch (error) {
        console.error('[Polling] Error al cargar mensajes:', error);
    }
}

// Función para agregar un mensaje al contenedor
function agregarMensaje(mensaje, usuario) {
    console.log('[AgregarMensaje] Iniciando con mensaje:', {
        id: mensaje.id_mensaje,
        contenido: mensaje.contenido ? mensaje.contenido.substring(0, 50) + '...' : 'N/A',
        fecha: mensaje.fecha_envio
    });

    // Verificaciones de datos esenciales: mensaje, id_mensaje, contenido, fecha_envio
    // Usar verificación explícita para null y undefined para mayor robustez
    if (!mensaje || mensaje.id_mensaje == null || mensaje.contenido == null || mensaje.fecha_envio == null) {
        console.error('[AgregarMensaje] Datos del mensaje incompletos o inválidos:', mensaje);
        return false;
    }

    // Normalizar el ID del usuario (aceptar tanto id como id_usuario)
    const userId = usuario?.id_usuario ?? usuario?.id;
    
    // Verificaciones de datos esenciales: usuario, id_usuario/id, username
    if (!usuario || userId == null || usuario.username == null) {
        console.error('[AgregarMensaje] Datos del usuario incompletos o inválidos:', usuario);
        return false;
    }

    const container = document.getElementById('mensajesContainer');
    if (!container) {
        console.error('[AgregarMensaje] Contenedor de mensajes no encontrado');
        return false;
    }

    // Verificar si el mensaje ya existe usando id_mensaje
    if (document.getElementById(`message-${mensaje.id_mensaje}`)) {
        console.log(`[AgregarMensaje] Mensaje ${mensaje.id_mensaje} ya existe, ignorando`);
        return false;
    }

    try {
        const metaUserId = document.querySelector('meta[name="user-id"]');
        const esMio = metaUserId && userId === parseInt(metaUserId.content);
        
        console.log(`[AgregarMensaje] Mensaje ${mensaje.id_mensaje}: Es mío? ${esMio}`);

        const mensajeDiv = document.createElement('div');
        mensajeDiv.className = `reto-message ${esMio ? 'sent' : 'received'}`;
        mensajeDiv.id = `message-${mensaje.id_mensaje}`;
        mensajeDiv.style.cssText = 'display: flex; align-items: flex-start; gap: 10px; margin-bottom: 8px;';

        const userImage = document.createElement('img');
        // Usar la función getProfileImgPath para la imagen del usuario del mensaje
        // Asegurarse de que usuario y usuario.imagen existen
        userImage.src = getProfileImgPath(usuario?.imagen); // Usar ?. para acceso seguro
        userImage.alt = usuario?.username || 'Usuario'; // Usar ?. y default
        userImage.className = 'reto-message-user-image';
        userImage.style.cssText = 'width: 40px; height: 40px; object-fit: cover; border-radius: 50%;';
         // Añadir manejo de error para la imagen por si acaso
         userImage.onerror = function() {
             this.src = getProfileImgPath(null); // Carga la imagen por defecto si falla
         };

        const mensajeWrapper = document.createElement('div');
        mensajeWrapper.className = 'reto-message-wrapper';
        mensajeWrapper.style.cssText = 'display: flex; flex-direction: column; align-items: flex-start;';

        const userName = document.createElement('span');
        userName.className = 'reto-message-username';
        userName.textContent = usuario?.username || 'Desconocido'; // Usar ?. y default
        userName.style.cssText = 'font-weight: bold; color: ' + (esMio ? '#4B0082' : '#6c757d') + '; font-size: 15px; margin-bottom: 2px;';

        const contenido = document.createElement('div');
        contenido.className = 'reto-message-content';
        contenido.textContent = mensaje.contenido; // Asumimos que contenido siempre existe
        contenido.style.cssText = 'background: ' + (esMio ? '#4B0082' : '#f3e6ff') + '; color: ' + (esMio ? 'white' : '#222') + '; padding: 10px 18px; border-radius: 14px; margin-bottom: 2px; max-width: 600px; font-size: 16px; word-break: break-word;';

        const fecha = document.createElement('div');
        fecha.className = 'reto-message-time';
        // Parsear la fecha correctamente (puede ser string de ISO 8601)
        // Asegurarse de que mensaje.fecha_envio existe
        const fechaMensaje = mensaje?.fecha_envio ? new Date(mensaje.fecha_envio) : new Date();
        // Formatear hora localmente (puede requerir polyfill si no es soportado)
        fecha.textContent = fechaMensaje.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        fecha.style.cssText = 'font-size: 12px; color: #888; margin-left: 2px;';

        mensajeWrapper.appendChild(userName);
        mensajeWrapper.appendChild(contenido);
        mensajeWrapper.appendChild(fecha);

        // Asegurarse de que la imagen esté a la izquierda para mensajes recibidos y a la derecha para enviados
        if (esMio) {
             mensajeDiv.style.flexDirection = 'row-reverse'; // Invierte el orden para mensajes enviados
             mensajeWrapper.style.alignItems = 'flex-end'; // Alinea texto a la derecha
             userName.style.textAlign = 'right';
             contenido.style.textAlign = 'right';
             fecha.style.textAlign = 'right';
             fecha.style.marginRight = '2px'; // Ajustar margen
             fecha.style.marginLeft = '0';
             mensajeDiv.appendChild(mensajeWrapper); // Añadir primero el wrapper para invertir el orden
             mensajeDiv.appendChild(userImage);
        } else {
             mensajeDiv.appendChild(userImage);
             mensajeDiv.appendChild(mensajeWrapper);
        }

        // Añadir el mensaje al final del contenedor
        container.appendChild(mensajeDiv);
        console.log(`[AgregarMensaje] Mensaje ${mensaje.id_mensaje} agregado exitosamente`);
        return true;
    } catch (error) {
        console.error(`[AgregarMensaje] Error al agregar mensaje ${mensaje.id_mensaje}:`, error);
        return false;
    }
}

// Función para verificar el estado del chat
async function verificarEstadoChat() {
    if (!chatId) return;

    try {
        const chatResponse = await safeFetch(`/retos/verificar-chat/${chatId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        // Si la respuesta no es OK (por ejemplo, 404 porque el chat terminó)
        if (!chatResponse.status || chatResponse.status === 'ended') { // Asumo que el backend puede enviar status 'ended' o un error HTTP como 404
            console.log('El chat ha terminado o no es válido.');
            // Restablecer el estado del chat en el frontend
            chatId = null;
            companero = null;
            buscandoCompanero = true;

            // Limpiar intervalos y detener control de inactividad
            limpiarIntervalos();

            // Actualizar la UI para mostrar que está buscando compañero nuevamente
            const chatHeader = document.getElementById('chatHeader');
            const chatOptions = document.getElementById('chatOptions');
            const mensajesContainer = document.getElementById('mensajesContainer');

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

            if (chatOptions) {
                chatOptions.style.display = 'none';
            }

            if (mensajesContainer) {
                mensajesContainer.innerHTML = ''; // Limpiar mensajes
            }
             lastMessageId = 0; // Resetear el último ID de mensaje

            // Iniciar la búsqueda de un nuevo compañero
            buscarCompaneroAutomatico();

        } else {
            console.log('El chat sigue activo.');
             // Si el chat sigue activo, no hacer nada (el polling de mensajes ya se encarga)
        }
    } catch (error) {
        console.error('Error al verificar estado del chat:', error);
        // Si hay un error al verificar el estado, asumimos que el chat pudo haber terminado
        // y reiniciamos la búsqueda para mayor seguridad
        console.log('Error al verificar estado, asumiendo fin del chat y reiniciando búsqueda.');
        chatId = null;
        companero = null;
        buscandoCompanero = true;

        limpiarIntervalos();

        const chatHeader = document.getElementById('chatHeader');
        const chatOptions = document.getElementById('chatOptions');
        const mensajesContainer = document.getElementById('mensajesContainer');

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

         if (chatOptions) {
             chatOptions.style.display = 'none';
         }

         if (mensajesContainer) {
             mensajesContainer.innerHTML = ''; // Limpiar mensajes
         }
        lastMessageId = 0; // Resetear el último ID de mensaje


        buscarCompaneroAutomatico(); // Reiniciar la búsqueda
    }
}

// Función para actualizar el contador de puntos diarios
async function actualizarPuntosDiarios() {
    // Esta función ya es bastante ligera, solo hace un fetch y actualiza un texto
    // No requiere optimización adicional aquí, a menos que el backend sea muy lento.
    try {
        const response = await safeFetch('/retos/puntos-diarios', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response) { // safeFetch ya verifica response.ok
            const data = response;
            const puntosDiariosElement = document.getElementById('puntos-diarios-actuales');
            if (puntosDiariosElement) {
                // Solo actualizar si el valor es mayor que el actual (para evitar regresiones visuales si hay latencia)
                const puntosActuales = parseInt(puntosDiariosElement.textContent || '0');
                if (data.puntos_diarios > puntosActuales) {
                    puntosDiariosElement.textContent = data.puntos_diarios;
                }
            }
        }
    } catch (error) {
        // Error silencioso - no molestar al usuario si falla la actualización de puntos
        console.error('Error al actualizar puntos diarios:', error);
    }
}

// Función para verificar el estado de la solicitud de amistad con el compañero actual
async function verificarEstadoSolicitud() {
    // Esta función solo se llama una vez al encontrar compañero y una vez al abrir el modal de opciones
    // No requiere optimización de polling aquí.
    if (!companero) return;

    try {
        const response = await safeFetch(`/solicitudes/verificar/${companero.id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response) { // safeFetch ya verifica response.ok
            const data = response;
            const sendFriendRequestBtn = document.getElementById('sendFriendRequest');

            if (sendFriendRequestBtn) {
                if (data.estado === 'pendiente') {
                    sendFriendRequestBtn.innerHTML = '<i class="fas fa-clock me-2"></i>Solicitud pendiente';
                    sendFriendRequestBtn.classList.add('disabled');
                    sendFriendRequestBtn.style.pointerEvents = 'none';
                    sendFriendRequestBtn.style.opacity = '0.7';
                } else if (data.estado === 'aceptada') {
                     // Ocultar el botón si ya son amigos
                    if(sendFriendRequestBtn.parentElement) {
                        sendFriendRequestBtn.parentElement.style.display = 'none';
                    }
                } else {
                    // Si el estado no es pendiente ni aceptada, asegurar que el botón esté visible y activo
                    sendFriendRequestBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Añadir amigo'; // Texto por defecto
                    sendFriendRequestBtn.classList.remove('disabled');
                    sendFriendRequestBtn.style.pointerEvents = 'auto';
                    sendFriendRequestBtn.style.opacity = '1';
                     if(sendFriendRequestBtn.parentElement) {
                        sendFriendRequestBtn.parentElement.style.display = 'block';
                    }
                }
            }
        }
    } catch (error) {
        console.error('Error al verificar estado de solicitud:', error);
         // Error silencioso
    }
}


// >>> Las funciones cargarSolicitudesAmistad, responderSolicitud,
// actualizarContadorSolicitudes son parte del modal de solicitudes y
// deberían manejarse preferiblemente en chatamig.js o en un script separado para modals.
// Si se mantienen aquí, asegúrate de que sus intervalos (si los tienen)
// sean independientes del polling principal del chat y con una frecuencia baja.
// No las incluyo en esta optimización para enfocarme en el chat principal.


// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
     console.log('DOMContentLoaded en chatrandom.js');
    // Iniciar la búsqueda del compañero automáticamente al cargar la página
    buscarCompaneroAutomatico();
    // Actualizar puntos diarios al cargar
    actualizarPuntosDiarios();
    // actualizarBotonSkip() no es necesario en DOMContentLoaded si solo se usa para el skip del reto

    const skipBtn = document.querySelector('.skip-btn');
    if (skipBtn) {
        // Actualizar el estado inicial del botón skip
        actualizarBotonSkip(); // Llamar aquí para el estado inicial

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
                text: 'Saltarás a este usuario y buscarás uno nuevo. Deberás esperar antes de poder usar el skip nuevamente.',
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

                    if (data.success) {
                        // Obtener el tiempo de cooldown según el rol del usuario
                        const userRole = document.querySelector('meta[name="user-role"]')?.content;
                        let tiempoCooldown = '10 minutos';
                        if (userRole === '3') {
                            tiempoCooldown = '5 minutos';
                        } else if (userRole === '2') {
                            tiempoCooldown = '7.5 minutos';
                        }

                        Swal.fire({
                            title: 'Skip activado',
                            text: `Has saltado al siguiente usuario. Deberás esperar ${tiempoCooldown} antes de poder usar el skip nuevamente.`,
                            icon: 'success'
                        });
                        // Resetear variables de chat
                        chatId = null;
                        companero = null;
                        buscandoCompanero = true;
                        lastMessageId = 0; // Resetear ID del último mensaje

                         // Limpiar intervalos y detener control de inactividad
                        limpiarIntervalos();

                         // Actualizar la UI para mostrar estado de búsqueda
                         const chatHeader = document.getElementById('chatHeader');
                         const chatOptions = document.getElementById('chatOptions');
                         const mensajesContainer = document.getElementById('mensajesContainer');

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

                         if (chatOptions) {
                             chatOptions.style.display = 'none';
                         }

                         if (mensajesContainer) {
                             mensajesContainer.innerHTML = ''; // Limpiar mensajes
                         }

                        // Actualizar el estado del botón skip (mostrará el cooldown)
                        actualizarBotonSkip();
                        // Iniciar la búsqueda de un nuevo compañero
                        buscarCompaneroAutomatico();
                    } else {
                        throw new Error(data.error || 'Error desconocido al activar skip');
                    }

                } catch (error) {
                    console.error('Error al activar skip:', error);
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'Ocurrió un error al intentar saltar al siguiente usuario',
                        icon: 'error'
                    });
                }
            }
        });

        // Intervalo para actualizar el tiempo del cooldown del botón skip cada 12 segundos
        setInterval(actualizarBotonSkip, 12000);
    }


    const enviarMensajeBtn = document.getElementById('enviarMensaje');
    if (enviarMensajeBtn) {
        enviarMensajeBtn.addEventListener('click', enviarMensaje);
    }

    const mensajeInput = document.getElementById('mensajeInput');
    if (mensajeInput) {
        mensajeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevenir el salto de línea por defecto en textarea
                enviarMensaje();
            }
        });
    }

    // El intervalo de estado general (mantenerEstado) se maneja en estados.js
    // estadoIntervalId = setInterval(mantenerEstado, 120000); // Eliminar si se maneja centralmente

    const sendFriendRequestBtn = document.getElementById('sendFriendRequest');
    if (sendFriendRequestBtn) {
        sendFriendRequestBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            if (!companero) return;

            // Deshabilitar el botón temporalmente para evitar clics múltiples
             sendFriendRequestBtn.disabled = true;
             sendFriendRequestBtn.style.pointerEvents = 'none';
             sendFriendRequestBtn.style.opacity = '0.7';


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
                } else {
                     throw new Error(data.message || 'Error desconocido al enviar solicitud');
                }
            } catch (error) {
                console.error('Error al enviar solicitud:', error);
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'No se pudo enviar la solicitud de amistad.',
                    icon: 'error'
                });
                 // Re-habilitar botón si hubo un error
                 sendFriendRequestBtn.disabled = false;
                 sendFriendRequestBtn.style.pointerEvents = 'auto';
                 sendFriendRequestBtn.style.opacity = '1';
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
                    } else {
                         throw new Error(data.message || 'Error desconocido al enviar reporte');
                    }
                } catch (error) {
                    console.error('Error al enviar reporte:', error);
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'Ocurrió un error al enviar el reporte.',
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
                             // Redirigir o actualizar la UI después de bloquear
                            window.location.reload();
                        });
                    } else {
                         throw new Error(data.message || 'Error desconocido al bloquear usuario');
                    }
                } catch (error) {
                    console.error('Error al bloquear usuario:', error);
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'Ocurrió un error al bloquear al usuario.',
                        icon: 'error'
                    });
                }
            }
        });
    }

    // Configurar el botón de ver solicitudes - Si este modal se maneja aquí,
    // considera moverlo a chatamig.js o a un script separado para modals.
    // Si se queda aquí, asegura que el intervalo de carga sea bajo.
    const btnSolicitudesPendientes = document.getElementById('btnSolicitudesPendientes');
    if (btnSolicitudesPendientes) {
        btnSolicitudesPendientes.addEventListener('click', function(e) {
            e.preventDefault();
            const solicitudesModal = new bootstrap.Modal(document.getElementById('solicitudesModal'));
            solicitudesModal.show();
            // cargarSolicitudesAmistad(); // Llama a cargar al abrir
        });
        // actualizarContadorSolicitudes(); // Llama al cargar la página
    }

    // Gestionar el modal de solicitudes - Mover si se centraliza en otro script
    const solicitudesModalEl = document.getElementById('solicitudesModal');
    if (solicitudesModalEl) {
        solicitudesModalEl.addEventListener('show.bs.modal', function () {
            // Iniciar polling de solicitudes solo cuando el modal está abierto
             // solicitudesIntervalId = setInterval(cargarSolicitudesAmistad, 30000); // Intervalo de 30s
        });
        solicitudesModalEl.addEventListener('hidden.bs.modal', function () {
            // Limpiar polling de solicitudes al cerrar el modal
            if (solicitudesIntervalId) {
                clearInterval(solicitudesIntervalId);
                solicitudesIntervalId = null;
                 console.log('Intervalo de solicitudes detenido al cerrar modal.');
            }
        });
    }


    // Configurar el botón de opciones del chat
    const chatOptionsButton = document.getElementById('chatOptionsButton');
    if (chatOptionsButton) {
        chatOptionsButton.addEventListener('click', function() {
            // Verificar estado de solicitud solo al abrir opciones del chat
            verificarEstadoSolicitud();
        });
    }
});

// Limpiar intervalos y actualizar estado al cerrar la página
window.addEventListener('beforeunload', async () => {
    console.log('Usuario cerrando página - Limpiando intervalos de chatrandom.');
    // Asegurarse de detener los intervalos de este script
    limpiarIntervalos();

    // El estado general (inactivo) y la limpieza del estado del reto
    // se manejan mejor en estados.js usando sendBeacon.
    // No duplicar la lógica aquí.
});

// Actualizar estado inicial (esto se maneja en estados.js)
// mantenerEstado(); // Eliminar si se maneja centralmente

// Hacer las funciones relevantes disponibles globalmente si otros scripts las necesitan
// window.enviarMensaje = enviarMensaje; // Ejemplo si el botón está fuera del alcance global

// Exponer tiempoInicialEspera globalmente también si chatrandom.js lo necesita para el setTimeout inicial
// window.tiempoInicialEspera = tiempoInicialEspera; // (Ya lo ajustamos directamente en chatrandom.js)