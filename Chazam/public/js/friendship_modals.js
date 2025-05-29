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
const modalStyles = document.createElement('style');
modalStyles.textContent = `
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

    /* Estilos adicionales para los modales */
    .modal {
        backdrop-filter: blur(5px);
    }

    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-dialog {
        margin: 1.75rem auto;
        max-width: 500px;
    }

    .modal-title {
        color: #333;
        font-weight: 600;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .search-input {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }

    .search-input:focus {
        border-color: #9147ff;
        box-shadow: 0 0 0 0.2rem rgba(145, 71, 255, 0.25);
    }

    .no-results {
        text-align: center;
        padding: 2rem;
        color: #666;
    }

    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #9147ff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(modalStyles);

// Funciones de utilidad para modales
const ModalUtils = {
    limpiarModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            // Remover el atributo aria-hidden antes de cerrar
            modalEl.removeAttribute('aria-hidden');
            
            // Asegurarse de que el botón de cierre no tenga el foco
            const closeButton = modalEl.querySelector('.btn-close');
            if (closeButton) {
                closeButton.blur();
            }
            
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
            
            // Remover el atributo aria-modal
            modalEl.removeAttribute('aria-modal');
        }
    },

    mostrarModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        
        // Limpiar cualquier modal anterior
        this.limpiarModal(modalId);
        
        // Configurar atributos de accesibilidad
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.removeAttribute('aria-hidden');
        
        // Mostrar el nuevo modal
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

// Funciones de búsqueda y amistad
const FriendshipManager = {
    async searchUsers(query) {
        try {
            const searchResults = document.getElementById('searchResults');
            if (!searchResults) return;

            // Mostrar spinner de carga
            searchResults.innerHTML = '<div class="text-center"><div class="loading-spinner"></div></div>';

            const response = await fetch(`/buscar-usuarios?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Error en la búsqueda');

            const data = await response.json();

            if (data.length === 0) {
                searchResults.innerHTML = '<div class="no-results">No se encontraron usuarios</div>';
                return;
            }

            searchResults.innerHTML = data.map(user => `
                <div class="user-result">
                    <div class="marco-externo marco-glow ${user.rotacion ? 'marco-rotate' : ''}"
                         style="--glow-color: ${user.brillo || '#fff'}; background-image: url('/img/bordes/${user.marco ?? 'default.svg'}');">
                        <img src="${window.getProfileImgPath(user.img)}" 
                             alt="${user.username}" 
                             onerror="this.src='${window.getProfileImgPath()}'">
                    </div>
                    <div class="user-info">
                        <h6>${user.username}</h6>
                        <p>${user.nombre_completo || ''}</p>
                    </div>
                    <button class="send-request-btn" 
                            data-user-id="${user.id_usuario}"
                            onclick="window.FriendshipManager.sendFriendRequest(${user.id_usuario}, this)">
                        Enviar solicitud
                    </button>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error al buscar usuarios:', error);
            const searchResults = document.getElementById('searchResults');
            if (searchResults) {
                searchResults.innerHTML = '<div class="no-results">Error al buscar usuarios</div>';
            }
            Swal.fire({
                title: 'Error',
                text: 'No se pudieron buscar usuarios',
                icon: 'error'
            });
        }
    },

    async sendFriendRequest(userId, button) {
        try {
            // Deshabilitar el botón y mostrar spinner
            button.disabled = true;
            const originalText = button.textContent;
            button.innerHTML = '<div class="loading-spinner"></div>';

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
            button.disabled = false;
            button.textContent = originalText;
            Swal.fire({
                title: 'Error',
                text: error.message || 'No se pudo enviar la solicitud de amistad',
                icon: 'error'
            });
        }
    }
};

// Event listeners para abrir los modales de amistad y búsqueda
document.addEventListener('DOMContentLoaded', async function() {
    // Configurar el botón de amistades
    const btnAmistades = document.getElementById('btnAmistades');
    if (btnAmistades) {
        btnAmistades.addEventListener('click', () => {
            ModalUtils.mostrarModal(MODAL_CONFIG.modalIds.amistades);
            window.cargarAmistades();
        });
    }

    // Configurar el botón de búsqueda de usuarios
    const btnBuscarUsuarios = document.getElementById('btnBuscarUsuarios');
    if (btnBuscarUsuarios) {
        btnBuscarUsuarios.addEventListener('click', () => {
            ModalUtils.mostrarModal(MODAL_CONFIG.modalIds.buscar);
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
                    FriendshipManager.searchUsers(e.target.value);
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
        bloqueadosTab.addEventListener('click', window.cargarBloqueados);
    }

    // Configurar los event listeners para limpiar los modales al cerrarse
    Object.values(MODAL_CONFIG.modalIds).forEach(modalId => {
        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                ModalUtils.limpiarModal(modalId);
            });
        }
    });

    // Configurar el modal de solicitudes
    const btnSolicitudesPendientes = document.getElementById('btnSolicitudesPendientes');
    if (btnSolicitudesPendientes) {
        btnSolicitudesPendientes.addEventListener('click', () => {
            ModalUtils.mostrarModal(MODAL_CONFIG.modalIds.solicitudes);
            window.cargarSolicitudesAmistad();
        });
    }

    // Configurar intervalos de actualización para el modal de solicitudes
    const solicitudesModal = document.getElementById(MODAL_CONFIG.modalIds.solicitudes);
    if (solicitudesModal) {
        let solicitudesInterval;
        solicitudesModal.addEventListener('show.bs.modal', () => {
            solicitudesInterval = setInterval(window.cargarSolicitudesAmistad, MODAL_CONFIG.solicitudesInterval);
        });
        solicitudesModal.addEventListener('hidden.bs.modal', () => {
            clearInterval(solicitudesInterval);
            ModalUtils.limpiarModal(MODAL_CONFIG.modalIds.solicitudes);
        });
    }
});

// Exportar funciones al objeto global
window.ModalUtils = ModalUtils;
window.FriendshipManager = FriendshipManager;
