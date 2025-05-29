// Función para cargar las amistades
function cargarAmistades() {
    fetch('/amistades')
        .then(response => response.json())
        .then(amistades => {
            const listaAmistades = document.getElementById('listaAmistades');
            if (!listaAmistades) return;
            
            listaAmistades.innerHTML = '';
            if (amistades.length === 0) {
                listaAmistades.innerHTML = '<div class="text-center text-muted">No tienes amistades</div>';
                return;
            }

            amistades.forEach(amigo => {
                const amigoItem = document.createElement('div');
                amigoItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                const imgPath = getProfileImgPath(amigo.img);
                amigoItem.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="marco-externo marco-glow ${amigo.rotacion ? 'marco-rotate' : ''}"
                             style="--glow-color: ${amigo.brillo || '#fff'}; background-image: url('/img/bordes/${amigo.marco ?? 'default.svg'}');">
                            <img src="${imgPath}" 
                                 class="rounded-circle" 
                                 style="width: 40px; height: 40px; object-fit: cover;"
                                 onerror="this.src='${getProfileImgPath()}'">
                        </div>
                        <div class="ms-2">
                            <h6 class="mb-0">${amigo.username}</h6>
                            <small class="text-muted">${amigo.nombre || ''}</small>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-danger" onclick="eliminarAmigo(${amigo.id_usuario})">
                            <i class="fas fa-user-minus"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="bloquearAmigo(${amigo.id_usuario})">
                            <i class="fas fa-ban"></i>
                        </button>
                    </div>
                `;
                listaAmistades.appendChild(amigoItem);
            });
        })
        .catch(error => {
            console.error('Error al cargar amistades:', error);
            const listaAmistades = document.getElementById('listaAmistades');
            if (listaAmistades) {
                listaAmistades.innerHTML = '<div class="text-center text-danger">Error al cargar amistades</div>';
            }
        });
}

// Función para actualizar la lista de chats
function actualizarListaChats() {
    const chatsList = document.getElementById('chats-list');
    if (!chatsList) return;

    fetch('/user/chats')
        .then(response => response.json())
        .then(chats => {
            chatsList.innerHTML = '';
            chats.forEach(chat => {
                const chatItem = document.createElement('div');
                chatItem.className = 'chat-item';
                chatItem.dataset.chatId = chat.id_chat;
                chatItem.innerHTML = `
                    <div class="chat-avatar">
                        <img src="${chat.img ? chat.img : '/img/profile_img/avatar-default.png'}" alt="Avatar">
                    </div>
                    <div class="chat-info">
                        <div class="chat-header">
                            <h3>${chat.username ? chat.username : chat.nombre}</h3>
                            <span class="time">${chat.last_time ? chat.last_time : ''}</span>
                        </div>
                        <p class="last-message">${chat.last_message ? chat.last_message : ''}</p>
                    </div>
                `;
                chatItem.addEventListener('click', function() {
                    document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
                    chatItem.classList.add('active');
                    if (window.chatManager) {
                        window.chatManager.loadMessages(chat.id_chat);
                        window.chatManager.updateChatHeader(chat);
                    }
                });
                chatsList.appendChild(chatItem);
            });
        })
        .catch(error => console.error('Error al actualizar chats:', error));
}

// Función para eliminar amistad
function eliminarAmigo(idUsuario) {
    Swal.fire({
        title: '¿Eliminar amistad?',
        text: '¿Estás seguro de que quieres eliminar esta amistad?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/amistades/${idUsuario}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarAmistades();
                    actualizarListaChats();
                    Swal.fire({
                        title: '¡Amistad eliminada!',
                        text: 'La amistad ha sido eliminada correctamente.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'Error al eliminar la amistad');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Error al eliminar la amistad',
                    icon: 'error'
                });
            });
        }
    });
}

// Función para bloquear amistad
function bloquearAmigo(idUsuario) {
    Swal.fire({
        title: '¿Bloquear usuario?',
        text: '¿Estás seguro de que quieres bloquear a este usuario?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, bloquear',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/amistades/${idUsuario}/bloquear`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarAmistades();
                    actualizarListaChats();
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
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'Error al bloquear al usuario',
                    icon: 'error'
                });
            });
        }
    });
}

// Función para obtener la ruta de la imagen de perfil
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

// Event listeners cuando el DOM está listo
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

// Exportar funciones al objeto global
window.cargarAmistades = cargarAmistades;
window.actualizarListaChats = actualizarListaChats;
window.eliminarAmigo = eliminarAmigo;
window.bloquearAmigo = bloquearAmigo;
window.getProfileImgPath = getProfileImgPath;