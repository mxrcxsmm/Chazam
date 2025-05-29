// Función global para cargar solicitudes
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
            const imgPath = getProfileImgPath(solicitud.emisor.img); // Usar función global
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
                        src="${getProfileImgPath(solicitud.emisor.img)}" // Usar función global aquí también
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

// Función global para responder solicitudes
async function responderSolicitud(idSolicitud, respuesta) {
    try {
        const solicitudDiv = document.getElementById(`solicitud-${idSolicitud}`);
        if (!solicitudDiv) return;
        const buttons = solicitudDiv.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);

        // Cerrar el modal inmediatamente (lógica copiada de chatamig.js)
        const modalEl = document.getElementById('solicitudesModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) { backdrop.remove(); }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            modalEl.style.display = 'none';
        }

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

            // Recargar la lista de chats si estamos en la página de chat de amigos (opcional, si chatamig.js está cargado)
            if (window.chatManager && typeof window.chatManager.loadChats === 'function') {
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

// Función global para bloquear usuario
async function bloquearUsuario(idUsuario) {
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
                // Si estamos en la página de chat de amigos, intentar actualizar o redirigir
                if (window.chatManager && typeof window.chatManager.loadChats === 'function') {
                     // Intentar recargar la lista de chats o redirigir si el chat bloqueado era el actual
                     // Esto depende de la lógica interna de chatamig.js
                     console.log("Usuario bloqueado, intentando recargar chats en ChatManager");
                     window.chatManager.loadChats(); // Recargar la lista de chats
                     // Si el chat bloqueado era el activo, chatamig.js debería manejar la UI

                } else {
                    // Si no estamos en la página de chat de amigos, solo recargar la lista de amistades si el modal está abierto
                    const modalAmistadesEl = document.getElementById('modalAmistades');
                    const modalAmistades = bootstrap.Modal.getInstance(modalAmistadesEl);
                    if (modalAmistades && modalAmistadesEl.classList.contains('show')) {
                         cargarAmistades(); // Recargar lista de amigos (puede que el usuario bloqueado estuviera en la lista de amigos)
                         cargarBloqueados(); // Recargar lista de bloqueados
                    }
                }

                // Mostrar mensaje de éxito
                await Swal.fire({
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

// Función para buscar usuarios
async function searchUsers(query) {
    try {
        const response = await fetch(`/buscar-usuarios?q=${encodeURIComponent(query)}`, {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error('Error en la búsqueda');
        }

        const data = await response.json();
        const searchResults = document.getElementById('searchResults');
         if (!searchResults) { // Añadir verificación
             console.error("Element #searchResults not found");
             return;
         }

        if (data.length === 0) {
            searchResults.innerHTML = '<div class="text-center text-muted">No se encontraron usuarios</div>';
            return;
        }

        searchResults.innerHTML = data.map(user => `
            <div class="user-result">
                <img src="${getProfileImgPath(user.img)}" alt="${user.username}" onerror="this.src='${getProfileImgPath()}'">
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
        const searchResults = document.getElementById('searchResults');
         if (searchResults) { // Añadir verificación
             searchResults.innerHTML = '<div class="text-center text-danger">Error al buscar usuarios</div>';
         }
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
             // Si el modal de solicitudes está abierto, actualizar el contador
            const solicitudesCount = document.getElementById('solicitudesCount');
            if(solicitudesCount) { // Añadir verificación
                solicitudesCount.textContent = parseInt(solicitudesCount.textContent || '0') + 1;
            }

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

async function cargarBloqueados() {
    try {
        const response = await fetch('/amistades/bloqueados');
        if (!response.ok) throw new Error('Error al cargar bloqueados');
        const bloqueados = await response.json();
        const lista = document.getElementById('listaBloqueados');
        if (!lista) { // Añadir verificación
             console.error("Element #listaBloqueados not found");
             return;
        }

        let bloqueadosHtml = '';
        if (bloqueados.length === 0) {
            bloqueadosHtml = '<div class="text-center text-muted">No tienes usuarios bloqueados</div>';
        } else {
            bloqueadosHtml = bloqueados.map(user => {
                const imgPath = getProfileImgPath(user.img); // Usar función global
                return `
                    <div class="list-group-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${imgPath}" style="width:32px;height:32px;object-fit:cover;border-radius:50%;">
                            <span>${user.username}</span>
                        </div>
                        <button class="btn btn-sm btn-danger" onclick="desbloquearUsuario(${user.id_usuario})">Desbloquear</button>
                    </div>
                `;
            }).join('');
        }

        lista.innerHTML = bloqueadosHtml;

    } catch (error) {
        console.error('Error al cargar bloqueados:', error);
        const lista = document.getElementById('listaBloqueados');
        if (lista) { // Añadir verificación
            lista.innerHTML = '<div class="text-center text-danger">Error al cargar bloqueados</div>';
        }
    }
}

async function desbloquearUsuario(idUsuario) {
    try {
        const response = await fetch('/amistades/desbloquear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({id_usuario: idUsuario})
        });

        const data = await response.json();

        if (data.success) {
             // Recargar la lista de bloqueados
             await cargarBloqueados();
             // Recargar la lista de amigos (si el modal está abierto y lo tenía bloqueado)
             const modalAmistadesEl = document.getElementById('modalAmistades');
             const modalAmistades = bootstrap.Modal.getInstance(modalAmistadesEl);
             if (modalAmistades && modalAmistadesEl.classList.contains('show')) {
                 await cargarAmistades();
             }

            await Swal.fire({
                title: '¡Usuario desbloqueado!',
                text: 'El usuario ha sido desbloqueado correctamente.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            throw new Error(data.message || 'Error al desbloquear al usuario');
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

// Event listeners para abrir los modales de amistad y búsqueda
document.addEventListener('DOMContentLoaded', async function() {
    // Verificar y configurar el botón de amistades
    const btnAmistades = document.getElementById('btnAmistades');
    if (btnAmistades) {
        btnAmistades.addEventListener('click', function() {
            const modalAmistades = new bootstrap.Modal(document.getElementById('modalAmistades'));
            modalAmistades.show();
            // Cargar la lista de amigos al abrir el modal
            cargarAmistades();
        });
    }

    // Verificar y configurar el botón de búsqueda de usuarios
    const btnBuscarUsuarios = document.getElementById('btnBuscarUsuarios');
    if (btnBuscarUsuarios) {
        btnBuscarUsuarios.addEventListener('click', function() {
            const buscarUsuariosModal = new bootstrap.Modal(document.getElementById('buscarUsuariosModal'));
            buscarUsuariosModal.show();
        });
    }

    // Verificar y configurar el botón de solicitudes pendientes
    const btnSolicitudesPendientes = document.getElementById('btnSolicitudesPendientes');
    if (btnSolicitudesPendientes) {
        btnSolicitudesPendientes.addEventListener('click', function() {
            const solicitudesModal = new bootstrap.Modal(document.getElementById('solicitudesModal'));
            solicitudesModal.show();
            cargarSolicitudesAmistad();
        });
    }

    // Añadir event listeners para los botones de cierre de los modales
    const modals = ['modalAmistades', 'buscarUsuariosModal', 'solicitudesModal'];
    modals.forEach(modalId => {
        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            // Limpiar cuando se cierra el modal (usando el evento de Bootstrap)
            modalEl.addEventListener('hidden.bs.modal', function () {
                limpiarModal(modalId);
            });
            
            // Eliminamos los listeners de click en los botones de cierre,
            // ya que Bootstrap los maneja internamente y dispara 'hidden.bs.modal'.
            // Esto evita posibles conflictos.
            // const closeButtons = modalEl.querySelectorAll('[data-bs-dismiss="modal"]');
            // closeButtons.forEach(button => {
            //     button.addEventListener('click', function() {
            //         limpiarModal(modalId);
            //     });
            // });
        }
    });

    // Configurar el input de búsqueda de usuarios
    const searchUserInput = document.getElementById('searchUserInput');
    if (searchUserInput) {
        let searchTimeout;
        searchUserInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (e.target.value.length >= 2) {
                    searchUsers(e.target.value);
                } else {
                    const searchResults = document.getElementById('searchResults');
                    if (searchResults) {
                        searchResults.innerHTML = '';
                    }
                }
            }, 300);
        });
    }

    // Configurar las pestañas del modal de amistades
    const bloqueadosTab = document.getElementById('bloqueados-tab');
    if (bloqueadosTab) {
        bloqueadosTab.addEventListener('click', cargarBloqueados);
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

    // Cargar solicitudes pendientes al inicio
    await cargarSolicitudesAmistad();
});

// Necesitas una función global cargarAmistades() para cargar la lista de amigos
async function cargarAmistades() {
    try {
        const response = await fetch('/amistades'); // Asegúrate de tener esta ruta y controlador
        if (!response.ok) throw new Error('Error al cargar las amistades');
        const amistades = await response.json();
        const listaAmistades = document.getElementById('listaAmistades');
        if (!listaAmistades) return; // Asegúrate de que el elemento existe

        let amistadesHtml = '';
        if (amistades.length === 0) {
            amistadesHtml = '<div class="text-center text-muted">No tienes amistades</div>';
        } else {
            amistadesHtml = amistades.map(amigo => {
                // Asegúrate de que el objeto amigo tenga la estructura correcta (id_usuario, username, img, etc.)
                const imgPath = getProfileImgPath(amigo.img); // Usar la función global
                return `
                    <div class="list-group-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${imgPath}" style="width:32px;height:32px;object-fit:cover;border-radius:50%;">
                            <span>${amigo.username}</span>
                        </div>
                        <div>
                            
                            
                            <button class="btn btn-sm btn-danger" onclick="eliminarAmigo(${amigo.id_usuario})">Eliminar</button>
                            <button class="btn btn-sm btn-warning block-user-btn" data-user-id="${amigo.id_usuario}">Bloquear</button>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        listaAmistades.innerHTML = amistadesHtml;

    } catch (error) {
        console.error('Error al cargar amistades:', error);
        const listaAmistades = document.getElementById('listaAmistades');
        if (listaAmistades) { // Añadir verificación
            listaAmistades.innerHTML = '<div class="text-center text-danger">Error al cargar amistades</div>';
        }
    }
}

// Necesitas una función global eliminarAmigo(idUsuario) para eliminar amigos
async function eliminarAmigo(idUsuario) {
    try {
        const result = await Swal.fire({
            title: '¿Eliminar amigo?',
            text: '¿Estás seguro de que deseas eliminar a este usuario de tus amigos?',
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
                // Recargar la lista de amistades después de eliminar
                await cargarAmistades();
                await Swal.fire({
                    title: '¡Amigo eliminado!',
                    text: 'El usuario ha sido eliminado de tus amigos.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Error al eliminar al amigo');
            }
        }
    } catch (error) {
        console.error('Error al eliminar amigo:', error);
        Swal.fire({
            title: 'Error',
            text: error.message || 'Ocurrió un error al eliminar al amigo',
            icon: 'error'
        });
    }
}
