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
        const originalText = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando...';
        button.disabled = true;

        try {
            const response = await fetch('/solicitudes/enviar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ id_receptor: userId })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al enviar solicitud');
            }

            if (data.success) {
                button.innerHTML = 'Solicitud enviada';
                button.disabled = true;
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Solicitud de amistad enviada.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Error al enviar solicitud');
            }
        } catch (error) {
            console.error('Error al enviar solicitud:', error);
            button.innerHTML = originalText;
            button.disabled = false;
            
            // Mostrar mensaje específico si ya existe una solicitud
            if (error.message.includes('Ya existe una solicitud')) {
                Swal.fire({
                    title: 'Solicitud existente',
                    text: 'Ya has enviado una solicitud a este usuario',
                    icon: 'info'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'No se pudo enviar la solicitud',
                    icon: 'error'
                });
            }
        }
    },

    async cargarAmistades() {
        try {
            const response = await fetch('/amistades', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) {
                if (response.status === 405) {
                    throw new Error('Método no permitido. Por favor, contacta al administrador.');
                }
                const errorData = await response.json().catch(() => ({ message: 'Error al cargar amistades' }));
                throw new Error(errorData.message || 'Error al cargar amistades');
            }
            
            const data = await response.json();
            const listaAmistades = document.getElementById('listaAmistades');
            if (!listaAmistades) {
                console.warn('Elemento listaAmistades no encontrado');
                return;
            }

            if (!data || data.length === 0) {
                listaAmistades.innerHTML = '<div class="text-center text-muted">No tienes amistades</div>';
                return;
            }

            listaAmistades.innerHTML = data.map(amigo => `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="${window.getProfileImgPath(amigo.img)}" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0">${amigo.username}</h6>
                            <small class="text-muted">${amigo.nombre_completo || ''}</small>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-danger" onclick="window.FriendshipManager.eliminarAmigo(${amigo.id_usuario})">
                            <i class="fas fa-user-minus"></i> Eliminar amistad
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="window.FriendshipManager.bloquearUsuario(${amigo.id_usuario})">
                            <i class="fas fa-ban"></i> Bloquear
                        </button>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error al cargar amistades:', error);
            const listaAmistades = document.getElementById('listaAmistades');
            if (listaAmistades) {
                listaAmistades.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="fas fa-exclamation-circle mb-2"></i>
                        <p>Error al cargar amistades</p>
                        <small>${error.message}</small>
                    </div>`;
            }
            
            // Solo mostrar el SweetAlert si no es un error de método no permitido
            if (!error.message.includes('Método no permitido')) {
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'No se pudieron cargar las amistades',
                    icon: 'error'
                });
            }
        }
    },

    async cargarBloqueados() {
        try {
            const response = await fetch('/amistades/bloqueados');
            if (!response.ok) throw new Error('Error al cargar bloqueados');

            const data = await response.json();
            const listaBloqueados = document.getElementById('listaBloqueados');
            if (!listaBloqueados) return;

            if (data.length === 0) {
                listaBloqueados.innerHTML = '<div class="text-center text-muted">No tienes usuarios bloqueados</div>';
                return;
            }

            listaBloqueados.innerHTML = data.map(bloqueado => `
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="${window.getProfileImgPath(bloqueado.img)}" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0">${bloqueado.username}</h6>
                            <small class="text-muted">${bloqueado.nombre_completo || ''}</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-success" onclick="window.FriendshipManager.desbloquearUsuario(${bloqueado.id_usuario}, this)">
                        <i class="fas fa-unlock"></i> Desbloquear
                    </button>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error al cargar bloqueados:', error);
            Swal.fire({
                title: 'Error',
                text: 'No se pudieron cargar los usuarios bloqueados',
                icon: 'error'
            });
        }
    },

    async iniciarChat(userId) {
        try {
            const response = await fetch(`/user/chat/${userId}/iniciar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Error al iniciar chat');
            
            const data = await response.json();
            if (data.success) {
                window.location.href = '/user/friendchat';
            }
        } catch (error) {
            console.error('Error al iniciar chat:', error);
            Swal.fire({
                title: 'Error',
                text: 'No se pudo iniciar el chat',
                icon: 'error'
            });
        }
    },

    async desbloquearUsuario(idUsuario, button) {
        try {
            const response = await fetch(`/amistades/desbloquear/${idUsuario}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Error al desbloquear usuario');

            const data = await response.json();
            if (data.success) {
                // Eliminar el elemento de la lista
                const listItem = button.closest('.list-group-item');
                if (listItem) {
                    listItem.remove();
                }
                
                // Verificar si quedan elementos en la lista
                const listaBloqueados = document.getElementById('listaBloqueados');
                if (listaBloqueados && listaBloqueados.children.length === 0) {
                    listaBloqueados.innerHTML = '<div class="text-center text-muted">No tienes usuarios bloqueados</div>';
                }

                Swal.fire({
                    title: '¡Usuario desbloqueado!',
                    text: 'El usuario ha sido desbloqueado correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (error) {
            console.error('Error al desbloquear usuario:', error);
            Swal.fire({
                title: 'Error',
                text: 'No se pudo desbloquear al usuario',
                icon: 'error'
            });
        }
    },

    async denunciarUsuario(idUsuario) {
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
                        id_reportado: idUsuario,
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
    },

    async eliminarAmigo(idUsuario) {
        try {
            const result = await Swal.fire({
                title: '¿Eliminar amigo?',
                text: '¿Estás seguro de que deseas eliminar a este amigo? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                const response = await fetch(`/amistades/${idUsuario}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        title: '¡Amigo eliminado!',
                        text: 'El amigo ha sido eliminado correctamente.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // Recargar la lista de amistades
                    this.cargarAmistades();
                } else {
                    throw new Error(data.message || 'Error al eliminar amigo');
                }
            }
        } catch (error) {
            console.error('Error al eliminar amigo:', error);
            Swal.fire({
                title: 'Error',
                text: 'No se pudo eliminar al amigo',
                icon: 'error'
            });
        }
    },

    async bloquearUsuario(idUsuario) {
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
                    Swal.fire({
                        title: '¡Usuario bloqueado!',
                        text: 'El usuario ha sido bloqueado correctamente.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // Recargar la lista de amistades
                    this.cargarAmistades();
                } else {
                    throw new Error(data.message || 'Error al bloquear usuario');
                }
            }
        } catch (error) {
            console.error('Error al bloquear usuario:', error);
            Swal.fire({
                title: 'Error',
                text: 'No se pudo bloquear al usuario',
                icon: 'error'
            });
        }
    },

    async cargarSolicitudesAmistad() {
        try {
            const response = await fetch('/solicitudes/pendientes', {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Error al cargar solicitudes');

            const data = await response.json();
            const solicitudesContainer = document.getElementById('solicitudesContainer');
            const noSolicitudes = document.getElementById('noSolicitudes');
            
            if (!solicitudesContainer || !noSolicitudes) return;

            if (data.length === 0) {
                solicitudesContainer.innerHTML = '';
                noSolicitudes.style.display = 'block';
                return;
            }

            noSolicitudes.style.display = 'none';
            solicitudesContainer.innerHTML = data.map(solicitud => `
                <div class="solicitud-item">
                    <div class="solicitud-info">
                        <div class="marco-externo marco-glow ${solicitud.emisor.rotacion ? 'marco-rotate' : ''}"
                             style="--glow-color: ${solicitud.emisor.brillo || '#fff'}; background-image: url('/img/bordes/${solicitud.emisor.marco ?? 'default.svg'}');">
                            <img src="${window.getProfileImgPath(solicitud.emisor.img)}" 
                                 alt="${solicitud.emisor.username}"
                                 style="width:40px;height:40px;object-fit:cover;border-radius:50%;"
                                 onerror="this.src='${window.getProfileImgPath()}'">
                        </div>
                        <div>
                            <div class="solicitud-username">${solicitud.emisor.username}</div>
                        </div>
                    </div>
                    <div class="solicitud-actions">
                        <button class="btn btn-success btn-sm" onclick="window.FriendshipManager.responderSolicitud(${solicitud.id_solicitud}, true)">
                            <i class="fas fa-check"></i> Aceptar
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="window.FriendshipManager.responderSolicitud(${solicitud.id_solicitud}, false)">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </div>
                </div>
            `).join('');

            // Actualizar el contador de solicitudes
            const solicitudesCount = document.getElementById('solicitudesCount');
            if (solicitudesCount) {
                solicitudesCount.textContent = data.length;
                solicitudesCount.style.display = data.length > 0 ? 'block' : 'none';
            }
        } catch (error) {
            console.error('Error al cargar solicitudes:', error);
            const solicitudesContainer = document.getElementById('solicitudesContainer');
            if (solicitudesContainer) {
                solicitudesContainer.innerHTML = '<div class="text-center text-danger">Error al cargar solicitudes</div>';
            }
        }
    },

    async responderSolicitud(idSolicitud, aceptar) {
        try {
            const response = await fetch('/solicitudes/responder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id_solicitud: idSolicitud,
                    respuesta: aceptar ? 'aceptada' : 'rechazada'
                })
            });

            if (!response.ok) throw new Error('Error al responder solicitud');

            const data = await response.json();
            if (data.success) {
                // Recargar las solicitudes
                await this.cargarSolicitudesAmistad();
                
                // Si se aceptó, recargar también la lista de chats
                if (aceptar && window.chatManager) {
                    await window.chatManager.loadChats();
                }

                Swal.fire({
                    title: aceptar ? '¡Solicitud aceptada!' : 'Solicitud rechazada',
                    text: aceptar ? 'Ahora son amigos' : 'La solicitud ha sido rechazada',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Error al procesar la respuesta');
            }
        } catch (error) {
            console.error('Error al responder solicitud:', error);
            Swal.fire({
                title: 'Error',
                text: error.message || 'No se pudo procesar la solicitud',
                icon: 'error'
            });
        }
    },
};

// Event listeners para abrir los modales de amistad y búsqueda
document.addEventListener('DOMContentLoaded', async function() {
    // Configurar el botón de amistades
    const btnAmistades = document.getElementById('btnAmistades');
    if (btnAmistades) {
        btnAmistades.addEventListener('click', () => {
            ModalUtils.mostrarModal(MODAL_CONFIG.modalIds.amistades);
            FriendshipManager.cargarAmistades();
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
        bloqueadosTab.addEventListener('click', () => {
            FriendshipManager.cargarBloqueados();
        });
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
            // Usar la referencia desde el objeto global si es necesario, aunque cargarSolicitudesAmistad debería estar dentro de FriendshipManager
            if (window.FriendshipManager && window.FriendshipManager.cargarSolicitudesAmistad) {
                 window.FriendshipManager.cargarSolicitudesAmistad();
            } else if (window.cargarSolicitudesAmistad) { // Mantener por si acaso aún se usa la función global antigua
                 window.cargarSolicitudesAmistad();
            }
        });
    }

    // Configurar intervalos de actualización para el modal de solicitudes
    const solicitudesModal = document.getElementById(MODAL_CONFIG.modalIds.solicitudes);
    if (solicitudesModal) {
        let solicitudesInterval;
        solicitudesModal.addEventListener('show.bs.modal', () => {
             // Usar la referencia desde el objeto global si es necesario
             if (window.FriendshipManager && window.FriendshipManager.cargarSolicitudesAmistad) {
                  solicitudesInterval = setInterval(window.FriendshipManager.cargarSolicitudesAmistad, MODAL_CONFIG.solicitudesInterval);
             } else if (window.cargarSolicitudesAmistad) { // Mantener por si acaso
                  solicitudesInterval = setInterval(window.cargarSolicitudesAmistad, MODAL_CONFIG.solicitudesInterval);
             }
        });
        solicitudesModal.addEventListener('hidden.bs.modal', () => {
            clearInterval(solicitudesInterval);
            ModalUtils.limpiarModal(MODAL_CONFIG.modalIds.solicitudes);
        });
    }
});

// Asegurar que los objetos globales existan antes de asignar funciones a window
window.ModalUtils = window.ModalUtils || {};
window.FriendshipManager = window.FriendshipManager || {};

// Exportar objetos al objeto global (redundante pero por seguridad)
window.ModalUtils = ModalUtils;
window.FriendshipManager = FriendshipManager;

// Exportar funciones de amistad individuales para compatibilidad con onclick en algunos blade files
window.cargarAmistades = FriendshipManager.cargarAmistades;
window.eliminarAmigo = FriendshipManager.eliminarAmigo;
window.bloquearUsuario = FriendshipManager.bloquearUsuario;
window.desbloquearUsuario = FriendshipManager.desbloquearUsuario;
window.denunciarUsuario = FriendshipManager.denunciarUsuario;
window.searchUsers = FriendshipManager.searchUsers;
window.sendFriendRequest = FriendshipManager.sendFriendRequest;
window.cargarBloqueados = FriendshipManager.cargarBloqueados;
window.mostrarModal = ModalUtils.mostrarModal;
window.limpiarModal = ModalUtils.limpiarModal;

// Si getProfileImgPath se usa fuera de FriendshipManager o ModalUtils y no está en otro archivo central, exportarla aquí también.
// Ya la exportamos desde hamburger.js, pero por si acaso para otras vistas:
if (!window.getProfileImgPath && typeof getProfileImgPath !== 'undefined') {
     window.getProfileImgPath = getProfileImgPath;
}
