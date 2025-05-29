// Configuración global para los modales
const MODAL_CONFIG = {
    solicitudesInterval: 60000,    // 1 minuto
    searchDelay: 300,             // 300ms para la búsqueda
    minSearchLength: 2,           // Mínimo de caracteres para buscar
    modalIds: {
        amistades: 'modalAmistades',
        buscar: 'buscarUsuariosModal',
        solicitudes: 'solicitudesModal'
    }
};

// Añadir estilos CSS para mejorar la apariencia
const style = document.createElement('style');
style.textContent = `
    .user-result {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
        transition: all 0.3s ease;
    }

    .user-result:hover {
        background-color: #f8f9fa;
    }

    .user-result img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }

    .user-info {
        flex-grow: 1;
    }

    .user-info h6 {
        margin: 0;
        color: #333;
    }

    .user-info p {
        margin: 0;
        color: #666;
        font-size: 0.9em;
    }

    .send-request-btn {
        padding: 5px 15px;
        border: none;
        border-radius: 20px;
        background-color: #9147ff;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .send-request-btn:hover {
        background-color: #7c3bdb;
    }

    .send-request-btn.sent {
        background-color: #28a745;
        cursor: default;
    }

    .send-request-btn:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
    }

    .list-group-item {
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .marco-externo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-size: cover;
        background-position: center;
        transition: all 0.3s ease;
    }

    .marco-glow {
        box-shadow: 0 0 10px var(--glow-color, #fff);
    }

    .marco-rotate {
        animation: rotate 10s linear infinite;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .modal-header {
        border-bottom: none;
        padding: 1rem 1.5rem;
    }

    .modal-footer {
        border-top: none;
        padding: 1rem 1.5rem;
    }

    .modal-content {
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    .nav-tabs .nav-link {
        color: #666;
        border: none;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        color: #9147ff;
    }

    .nav-tabs .nav-link.active {
        color: #9147ff;
        border-bottom: 2px solid #9147ff;
        background: none;
    }
`;
document.head.appendChild(style);

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

// Función para limpiar correctamente el modal
function limpiarModal(modalId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) {
        // Remover el atributo aria-hidden antes de cerrar
        modalEl.removeAttribute('aria-hidden');
        
        modal.hide();
        // Eliminar el backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        // Restaurar el scroll del body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        // Ocultar el modal
        modalEl.style.display = 'none';
    }
}

// Función para mostrar un modal
function mostrarModal(modalId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    
    // Limpiar cualquier modal anterior
    limpiarModal(modalId);
    
    // Mostrar el nuevo modal
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// Función para cargar solicitudes de amistad
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
            const imgPath = getProfileImgPath(solicitud.emisor.img);
            solicitudDiv.innerHTML = `
                <div class="solicitud-info">
                    <div
                    class="marco-externo marco-glow ${solicitud.emisor.rotacion ? 'marco-rotate' : ''}"
                    style="
                        --glow-color: ${solicitud.emisor.brillo || '#fff'};
                        background-image: url('/img/bordes/${solicitud.emisor.marco ?? 'default.svg'}');
                        width: 40px;
                        height: 40px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    "
                    >
                    <img
                        src="${imgPath}"
                        alt="${solicitud.emisor.username}"
                        class="rounded-circle"
                        style="width: 32px; height: 32px; object-fit: cover;"
                        onerror="this.src='${getProfileImgPath()}'"
                    />
                    </div>
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

// Función para responder solicitudes
async function responderSolicitud(idSolicitud, respuesta) {
    try {
        const solicitudDiv = document.getElementById(`solicitud-${idSolicitud}`);
        if (!solicitudDiv) return;
        const buttons = solicitudDiv.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);

        // Cerrar el modal inmediatamente
        limpiarModal(MODAL_CONFIG.modalIds.solicitudes);

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

// Función para bloquear usuario
async function bloquearUsuario(idUsuario) {
    try {
        const result = await Swal.fire({
            title: '¿Bloquear usuario?',
            text: '¿Estás seguro de que deseas bloquear a este usuario?',
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

            if (!response.ok) {
                throw new Error('Error al bloquear al usuario');
            }

            const data = await response.json();
            
            if (data.success) {
                // Actualizar la lista de amigos
                await cargarAmistades();
                
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: '¡Usuario bloqueado!',
                    text: 'El usuario ha sido bloqueado correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
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

// Función para desbloquear usuario
async function desbloquearUsuario(idUsuario) {
    try {
        const result = await Swal.fire({
            title: '¿Desbloquear usuario?',
            text: '¿Estás seguro de que deseas desbloquear a este usuario?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, desbloquear',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            const response = await fetch(`/amistades/${idUsuario}/desbloquear`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error('Error al desbloquear al usuario');
            }

            const data = await response.json();
            
            if (data.success) {
                // Actualizar la lista de bloqueados
                await cargarBloqueados();
                
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: '¡Usuario desbloqueado!',
                    text: 'El usuario ha sido desbloqueado correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Error al desbloquear al usuario');
            }
        }
    } catch (error) {
        console.error('Error al desbloquear usuario:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'Ocurrió un error al desbloquear al usuario',
            icon: 'error'
        });
    }
}

// Función para cargar usuarios bloqueados
async function cargarBloqueados() {
    try {
        const response = await fetch('/amistades/bloqueados');
        if (!response.ok) throw new Error('Error al cargar usuarios bloqueados');
        
        const data = await response.json();
        const listaBloqueados = document.getElementById('listaBloqueados');
        
        if (!listaBloqueados) return;
        
        if (data.length === 0) {
            listaBloqueados.innerHTML = '<div class="text-center text-muted">No tienes usuarios bloqueados</div>';
            return;
        }

        listaBloqueados.innerHTML = data.map(usuario => `
            <div class="list-group-item d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="marco-externo marco-glow ${usuario.rotacion ? 'marco-rotate' : ''}"
                         style="--glow-color: ${usuario.brillo || '#fff'}; background-image: url('/img/bordes/${usuario.marco ?? 'default.svg'}');">
                        <img src="${getProfileImgPath(usuario.img)}" 
                             alt="${usuario.username}" 
                             class="rounded-circle"
                             style="width: 32px; height: 32px; object-fit: cover;"
                             onerror="this.src='${getProfileImgPath()}'">
                    </div>
                    <span>${usuario.username}</span>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="window.desbloquearUsuario(${usuario.id_usuario})">
                    <i class="fas fa-unlock"></i> Desbloquear
                </button>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error al cargar usuarios bloqueados:', error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron cargar los usuarios bloqueados',
            icon: 'error'
        });
    }
}

// Función para cargar amistades
async function cargarAmistades() {
    try {
        const response = await fetch('/amistades');
        if (!response.ok) throw new Error('Error al cargar amistades');
        
        const data = await response.json();
        const listaAmistades = document.getElementById('listaAmistades');
        
        if (!listaAmistades) return;
        
        if (data.length === 0) {
            listaAmistades.innerHTML = '<div class="text-center text-muted">No tienes amigos aún</div>';
            return;
        }

        listaAmistades.innerHTML = data.map(amigo => `
            <div class="list-group-item d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="marco-externo marco-glow ${amigo.rotacion ? 'marco-rotate' : ''}"
                         style="--glow-color: ${amigo.brillo || '#fff'}; background-image: url('/img/bordes/${amigo.marco ?? 'default.svg'}');">
                        <img src="${getProfileImgPath(amigo.img)}" 
                             alt="${amigo.username}" 
                             class="rounded-circle"
                             style="width: 32px; height: 32px; object-fit: cover;"
                             onerror="this.src='${getProfileImgPath()}'">
                    </div>
                    <span>${amigo.username}</span>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" onclick="window.chatManager.handleChatSelection(document.querySelector('[data-user-id=\"${amigo.id_usuario}\"]'), ${JSON.stringify(amigo)})">
                        <i class="fas fa-comment"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="bloquearUsuario(${amigo.id_usuario})">
                        <i class="fas fa-ban"></i>
                    </button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error al cargar amistades:', error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron cargar las amistades',
            icon: 'error'
        });
    }
}

// Función para buscar usuarios
async function searchUsers(query) {
    try {
        const response = await fetch(`/buscar-usuarios?q=${encodeURIComponent(query)}`, {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) throw new Error('Error en la búsqueda');

        const data = await response.json();
        const searchResults = document.getElementById('searchResults');
        
        if (!searchResults) return;

        if (data.length === 0) {
            searchResults.innerHTML = '<div class="text-center text-muted">No se encontraron usuarios</div>';
            return;
        }

        searchResults.innerHTML = data.map(user => `
            <div class="user-result">
                <div class="marco-externo marco-glow ${user.rotacion ? 'marco-rotate' : ''}"
                     style="--glow-color: ${user.brillo || '#fff'}; background-image: url('/img/bordes/${user.marco ?? 'default.svg'}');">
                    <img src="${getProfileImgPath(user.img)}" 
                         alt="${user.username}" 
                         onerror="this.src='${getProfileImgPath()}'">
                </div>
                <div class="user-info">
                    <h6>${user.username}</h6>
                    <p>${user.nombre_completo || ''}</p>
                </div>
                <button class="send-request-btn" 
                        data-user-id="${user.id_usuario}"
                        onclick="sendFriendRequest(${user.id_usuario}, this)">
                    Enviar solicitud
                </button>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error al buscar usuarios:', error);
        Swal.fire({
            title: 'Error',
            text: 'No se pudieron buscar usuarios',
            icon: 'error'
        });
    }
}

// Función para enviar solicitud de amistad
async function sendFriendRequest(userId, button) {
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

// Event listeners para abrir los modales de amistad y búsqueda
document.addEventListener('DOMContentLoaded', async function() {
    // Configurar el botón de amistades
    const btnAmistades = document.getElementById('btnAmistades');
    if (btnAmistades) {
        btnAmistades.addEventListener('click', () => {
            mostrarModal(MODAL_CONFIG.modalIds.amistades);
            cargarAmistades();
        });
    }

    // Configurar el botón de búsqueda de usuarios
    const btnBuscarUsuarios = document.getElementById('btnBuscarUsuarios');
    if (btnBuscarUsuarios) {
        btnBuscarUsuarios.addEventListener('click', () => {
            mostrarModal(MODAL_CONFIG.modalIds.buscar);
        });
    }

    // Configurar el input de búsqueda de usuarios
    const searchUserInput = document.getElementById('searchUserInput');
    if (searchUserInput) {
        let searchTimeout;
        searchUserInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (e.target.value.length >= MODAL_CONFIG.minSearchLength) {
                    searchUsers(e.target.value);
                } else {
                    const searchResults = document.getElementById('searchResults');
                    if (searchResults) {
                        searchResults.innerHTML = '';
                    }
                }
            }, MODAL_CONFIG.searchDelay);
        });
    }

    // Configurar las pestañas del modal de amistades
    const bloqueadosTab = document.getElementById('bloqueados-tab');
    if (bloqueadosTab) {
        bloqueadosTab.addEventListener('click', cargarBloqueados);
    }

    // Configurar los event listeners para limpiar los modales al cerrarse
    Object.values(MODAL_CONFIG.modalIds).forEach(modalId => {
        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                limpiarModal(modalId);
            });
        }
    });

    // Configurar el modal de solicitudes
    const btnSolicitudesPendientes = document.getElementById('btnSolicitudesPendientes');
    if (btnSolicitudesPendientes) {
        btnSolicitudesPendientes.addEventListener('click', () => {
            mostrarModal(MODAL_CONFIG.modalIds.solicitudes);
            cargarSolicitudesAmistad();
        });
    }

    // Configurar intervalos de actualización para el modal de solicitudes
    const solicitudesModal = document.getElementById(MODAL_CONFIG.modalIds.solicitudes);
    if (solicitudesModal) {
        let solicitudesInterval;
        solicitudesModal.addEventListener('show.bs.modal', () => {
            solicitudesInterval = setInterval(cargarSolicitudesAmistad, MODAL_CONFIG.solicitudesInterval);
        });
        solicitudesModal.addEventListener('hidden.bs.modal', () => {
            clearInterval(solicitudesInterval);
            limpiarModal(MODAL_CONFIG.modalIds.solicitudes);
        });
    }

    // Vincular evento a los botones de reportar usuario
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('report-user-btn')) {
            const userId = e.target.dataset.userId;
            if (userId) {
                console.warn('El botón de reportar requiere que la lógica de reporte esté disponible globalmente.');
            }
        }
    });

    // Vincular evento a los botones de bloquear usuario
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('block-user-btn')) {
            const userId = e.target.dataset.userId;
            if (userId) {
                bloquearUsuario(userId);
            }
        }
    });
});

// Exportar funciones al objeto global
window.cargarBloqueados = cargarBloqueados;
window.desbloquearUsuario = desbloquearUsuario;
window.cargarAmistades = cargarAmistades;
window.searchUsers = searchUsers;
window.sendFriendRequest = sendFriendRequest;
window.bloquearUsuario = bloquearUsuario;
window.denunciarUsuario = denunciarUsuario;
window.mostrarModal = mostrarModal;
window.cleanupModals = cleanupModals;
window.getProfileImgPath = getProfileImgPath;
