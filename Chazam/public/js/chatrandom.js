// Variables globales para el chat
let chatId = null;
let companero = null;
let buscandoCompanero = true;

console.log('=== CHATRANDOM.JS CARGADO ===');
console.log('User ID:', window.userId);
console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.content);

// Función para buscar un compañero automáticamente
async function buscarCompaneroAutomatico() {
    console.log('=== FUNCIÓN buscarCompaneroAutomatico INICIADA ===');
    console.log('=== INICIO DE BÚSQUEDA DE COMPAÑERO ===');
    console.log('Estado inicial:', {
        chatId,
        companero,
        buscandoCompanero,
        userId: window.userId
    });
    
    // Si ya tenemos un chat activo, no buscamos más
    if (chatId) {
        console.log('Ya hay un chat activo, no se busca más');
        return;
    }
    
    while (buscandoCompanero) {
        try {
            console.log('=== INTENTO DE BÚSQUEDA ===');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            console.log('Enviando petición a /retos/buscar-companero');
            const response = await fetch('/retos/buscar-companero', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            console.log('=== RESPUESTA DEL SERVIDOR ===');
            console.log('Status:', response.status);
            console.log('Status Text:', response.statusText);
            
            const data = await response.json();
            console.log('Datos recibidos:', JSON.stringify(data, null, 2));
            
            if (response.ok) {
                console.log('=== COMPAÑERO ENCONTRADO ===');
                console.log('Chat ID:', data.chat_id);
                console.log('Compañero:', data.companero);
                console.log('Detalles del chat:', {
                    chatId: data.chat_id,
                    companeroId: data.companero.id,
                    companeroUsername: data.companero.username
                });
                chatId = data.chat_id;
                companero = data.companero;
                document.getElementById('chatHeader').innerHTML = `Chat con ${companero.username}`;
                buscandoCompanero = false;
                cargarMensajes();
                // Iniciar el control de inactividad cuando se encuentra un compañero
                if (window.iniciarControlInactividad) {
                    console.log('Iniciando control de inactividad para nuevo chat');
                    window.iniciarControlInactividad();
                    // Actualizar el último mensaje para iniciar el contador
                    if (window.actualizarUltimoMensaje) {
                        window.actualizarUltimoMensaje();
                    }
                }
                break;
            } else if (data.error === 'No hay usuarios disponibles') {
                console.log('No hay usuarios disponibles, reintentando...');
                document.getElementById('chatHeader').innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        Buscando usuarios disponibles...
                    </div>
                `;
            }
            
            // Esperar 3 segundos antes de intentar de nuevo
            console.log('Esperando 3 segundos antes del siguiente intento...');
            await new Promise(resolve => setTimeout(resolve, 3000));
        } catch (error) {
            console.error('=== ERROR EN LA PETICIÓN ===');
            console.error('Error:', error);
            console.error('Stack:', error.stack);
            await new Promise(resolve => setTimeout(resolve, 3000));
        }
    }
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

    console.log('=== PROCESANDO MENSAJE ===');
    console.log('Mensaje original:', mensaje);
    
    // Procesar el mensaje según el reto actual si existe la función
    const mensajeProcesado = window.procesarMensaje ? window.procesarMensaje(mensaje) : mensaje;
    console.log('Mensaje procesado:', mensajeProcesado);
    
    // Si el mensaje procesado es null, significa que no pasó la validación del reto
    if (mensajeProcesado === null) {
        console.log('Mensaje rechazado por la validación del reto');
        return;
    }

    fetch('/retos/enviar-mensaje', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            chat_id: chatId,
            contenido: mensajeProcesado
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.mensaje) {
            mensajeInput.value = '';
            agregarMensaje(data.mensaje, data.usuario);
            // Actualizar el tiempo de inactividad cuando se envía un mensaje
            if (window.actualizarUltimoMensaje) {
                window.actualizarUltimoMensaje();
            }
        } else {
            alert(data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
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
        console.error('Error:', error);
    }
}

// Función para agregar un mensaje al contenedor
function agregarMensaje(mensaje, usuario) {
    const container = document.getElementById('mensajesContainer');
    const metaUserId = document.querySelector('meta[name="user-id"]');
    const esMio = metaUserId ? usuario.id === parseInt(metaUserId.content) : false;
    
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `reto-message ${esMio ? 'sent' : 'received'}`;
    
    // Contenedor principal del mensaje
    const mensajeWrapper = document.createElement('div');
    mensajeWrapper.className = 'reto-message-wrapper';
    
    // Información del usuario (solo para mensajes recibidos)
    if (!esMio) {
        const userInfo = document.createElement('div');
        userInfo.className = 'reto-message-user-info';
        
        // Imagen del usuario
        const userImage = document.createElement('img');
        userImage.src = usuario.imagen ? `/IMG/${usuario.imagen}` : '';
        userImage.alt = usuario.username;
        userImage.className = 'reto-message-user-image';
        
        // Nombre del usuario
        const userName = document.createElement('span');
        userName.className = 'reto-message-username';
        userName.textContent = usuario.username;
        
        userInfo.appendChild(userImage);
        userInfo.appendChild(userName);
        mensajeWrapper.appendChild(userInfo);
    }
    
    // Contenedor del mensaje
    const messageContent = document.createElement('div');
    messageContent.className = 'reto-message-content-wrapper';
    
    // Contenido del mensaje
    const contenido = document.createElement('div');
    contenido.className = 'reto-message-content';
    contenido.textContent = mensaje.contenido;
    
    // Fecha del mensaje
    const fecha = document.createElement('div');
    fecha.className = 'reto-message-time';
    const fechaMensaje = new Date(mensaje.fecha_envio);
    fecha.textContent = fechaMensaje.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    messageContent.appendChild(contenido);
    messageContent.appendChild(fecha);
    mensajeWrapper.appendChild(messageContent);
    
    mensajeDiv.appendChild(mensajeWrapper);
    container.appendChild(mensajeDiv);
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
            console.log('Chat eliminado, reiniciando búsqueda...');
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
            
            // Limpiar el contenedor de mensajes
            document.getElementById('mensajesContainer').innerHTML = '';
            
            // Reiniciar la búsqueda
            buscarCompaneroAutomatico();
        }
    } catch (error) {
        console.error('Error al verificar estado del chat:', error);
        // Si hay un error, asumimos que el chat se perdió
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

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM CARGADO - INICIANDO BÚSQUEDA ===');
    // Iniciar la búsqueda automática
    buscarCompaneroAutomatico();
    
    // Configurar el botón de enviar mensaje
    document.getElementById('enviarMensaje').addEventListener('click', enviarMensaje);
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
        }
    }, 1000); // Reducido a 1 segundo para mayor reactividad
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