// Configuración global
const USER_SEARCH_CONFIG = {
    searchUrl: '/buscar-usuarios',
    minQueryLength: 3,
    debounceTime: 300
};

// Clase para manejar la búsqueda de usuarios
class UserSearch {
    constructor() {
        this.searchInput = document.getElementById('searchUserInput');
        this.searchResults = document.getElementById('searchResults');
        this.searchTimeout = null;
        this.setupEventListeners();
        this.setupModalEvents();
    }

    setupEventListeners() {
        if (this.searchInput) {
            this.searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                const query = e.target.value.trim();

                if (query.length < USER_SEARCH_CONFIG.minQueryLength) {
                    this.searchResults.innerHTML = '<div class="text-center text-muted">Ingresa al menos 3 caracteres para buscar</div>';
                    return;
                }

                this.searchTimeout = setTimeout(() => {
                    this.searchUsers(query);
                }, USER_SEARCH_CONFIG.debounceTime);
            });
        }
    }

    setupModalEvents() {
        // Evento para abrir el modal de búsqueda
        const btnBuscarUsuarios = document.getElementById('btnBuscarUsuarios');
        if (btnBuscarUsuarios) {
            btnBuscarUsuarios.addEventListener('click', () => {
                const buscarUsuariosModal = new bootstrap.Modal(document.getElementById('buscarUsuariosModal'));
                buscarUsuariosModal.show();
            });
        }

        // Evento para limpiar el input cuando se cierra el modal
        const buscarUsuariosModal = document.getElementById('buscarUsuariosModal');
        if (buscarUsuariosModal) {
            buscarUsuariosModal.addEventListener('hidden.bs.modal', () => {
                if (this.searchInput) {
                    this.searchInput.value = '';
                }
                if (this.searchResults) {
                    this.searchResults.innerHTML = '';
                }
            });
        }
    }

    async searchUsers(query) {
        try {
            const response = await fetch(`${USER_SEARCH_CONFIG.searchUrl}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error('Error en la búsqueda');
            }

            const data = await response.json();

            if (data.length === 0) {
                this.searchResults.innerHTML = '<div class="text-center text-muted">No se encontraron usuarios</div>';
                return;
            }

            this.searchResults.innerHTML = data.map(user => `
                <div class="user-result">
                    <img src="${window.getProfileImgPath(user.img)}" alt="${user.username}" onerror="this.src='${window.getProfileImgPath()}'; this.onerror=null;">
                    <div class="user-info">
                        <h6>${user.username}</h6>
                        <p>${user.nombre_completo}</p>
                    </div>
                    <button class="send-request-btn" 
                            data-user-id="${user.id_usuario}"
                            onclick="window.userSearch.sendFriendRequest(${user.id_usuario}, this)">
                        Enviar solicitud
                    </button>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error al buscar usuarios:', error);
            this.searchResults.innerHTML = '<div class="text-center text-danger">Error al buscar usuarios</div>';
        }
    }

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
    window.userSearch = new UserSearch();
}); 