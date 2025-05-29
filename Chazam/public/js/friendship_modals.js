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

// Exportar funciones necesarias globalmente
window.cargarSolicitudesAmistad = cargarSolicitudesAmistad;
window.responderSolicitud = responderSolicitud;
window.limpiarModal = limpiarModal;
window.mostrarModal = mostrarModal;
window.searchUsers = searchUsers;
window.cargarAmistades = cargarAmistades;
window.cargarBloqueados = cargarBloqueados;
window.bloquearUsuario = bloquearUsuario;
window.desbloquearUsuario = desbloquearUsuario;
window.eliminarAmigo = eliminarAmigo;
window.sendFriendRequest = sendFriendRequest;
window.getProfileImgPath = getProfileImgPath;
