document.addEventListener('DOMContentLoaded', () => {
    const hamburgerButton = document.getElementById('hamburgerButton');
    const navbarNav = document.getElementById('navbarNav');
    const btnAmistades = document.getElementById('btnAmistades');
    const modalAmistades = document.getElementById('modalAmistades');
    let modal = null;

    if (modalAmistades) {
        modal = bootstrap.Modal.getOrCreateInstance(modalAmistades);
    }

    if (hamburgerButton && navbarNav) {
        hamburgerButton.addEventListener('click', () => {
            navbarNav.classList.toggle('show');
        });
    }

    // Función para cargar las amistades
    function cargarAmistades() {
        fetch('/api/amistades', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const listaAmistades = document.getElementById('listaAmistades');
            listaAmistades.innerHTML = '';

            if (data.length === 0) {
                listaAmistades.innerHTML = '<div class="text-center p-3">No tienes amistades aún</div>';
                return;
            }

            data.forEach(amigo => {
                const amigoElement = document.createElement('div');
                amigoElement.className = 'd-flex justify-content-between align-items-center mb-3 p-2 border rounded';
                amigoElement.innerHTML = `
                    <div class="d-flex align-items-center">
                        <img src="${amigo.img || '/img/default-avatar.png'}" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0">${amigo.username}</h6>
                            <small class="text-muted">${amigo.nombre} ${amigo.apellido}</small>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-danger" onclick="eliminarAmistad(${amigo.id_usuario})">
                            <i class="bi bi-person-x"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="bloquearAmistad(${amigo.id_usuario})">
                            <i class="bi bi-slash-circle"></i>
                        </button>
                    </div>
                `;
                listaAmistades.appendChild(amigoElement);
            });
        })
        .catch(error => {
            console.error('Error al cargar amistades:', error);
            Swal.fire('Error', 'No se pudieron cargar las amistades', 'error');
        });
    }

    // Función para eliminar amistad
    window.eliminarAmistad = function(idUsuario) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará la amistad y los chats existentes',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/api/amistades/${idUsuario}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Eliminado!', 'La amistad ha sido eliminada', 'success');
                        cargarAmistades();
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al eliminar amistad:', error);
                    Swal.fire('Error', 'No se pudo eliminar la amistad', 'error');
                });
            }
        });
    };

    // Función para bloquear amistad
    window.bloquearAmistad = function(idUsuario) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción bloqueará al usuario y eliminará los chats existentes',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, bloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/api/amistades/${idUsuario}/bloquear`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Bloqueado!', 'El usuario ha sido bloqueado', 'success');
                        cargarAmistades();
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al bloquear usuario:', error);
                    Swal.fire('Error', 'No se pudo bloquear al usuario', 'error');
                });
            }
        });
    };

    // Evento para cargar amistades cuando se abre el modal
    if (btnAmistades && modal) {
        btnAmistades.addEventListener('click', () => {
            cargarAmistades();
            modal.show();
        });
    }

    if (modalAmistades) {
        modalAmistades.addEventListener('hidden.bs.modal', function () {
            if (btnAmistades) btnAmistades.focus();
        });
    }
});