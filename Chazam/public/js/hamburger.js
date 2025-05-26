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
        fetch('/amistades')
            .then(response => response.json())
            .then(amistades => {
                const listaAmistades = document.getElementById('listaAmistades');
                listaAmistades.innerHTML = '';
                amistades.forEach(amigo => {
                    const amigoItem = document.createElement('div');
                    amigoItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    amigoItem.innerHTML = `
                        <div class="d-flex align-items-center">
                            <img src="${amigo.img ? (amigo.img.startsWith('http') ? amigo.img : window.location.origin + '/img/profile_img/' + amigo.img.replace(/^.*[\\\/]/, '')) : '/img/profile_img/avatar-default.png'}" 
                                 class="rounded-circle me-2" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">${amigo.username}</h6>
                                <small class="text-muted">${amigo.nombre}</small>
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
            .catch(error => console.error('Error al cargar amistades:', error));
    }

    // Función para actualizar la lista de chats
    function actualizarListaChats() {
        const chatsList = document.getElementById('chats-list');
        if (chatsList) {
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
                            loadMessages(chat.id_chat);
                            updateChatHeader(chat);
                        });
                        chatsList.appendChild(chatItem);
                    });
                })
                .catch(error => console.error('Error al actualizar chats:', error));
        }
    }

    // Función para eliminar amistad
    function eliminarAmigo(idUsuario) {
        if (confirm('¿Estás seguro de que quieres eliminar esta amistad?')) {
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
                } else {
                    alert('Error al eliminar la amistad');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la amistad');
            });
        }
    }

    // Función para bloquear amistad
    function bloquearAmigo(idUsuario) {
        if (confirm('¿Estás seguro de que quieres bloquear a este usuario?')) {
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
                } else {
                    alert('Error al bloquear al usuario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al bloquear al usuario');
            });
        }
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

    window.eliminarAmigo = eliminarAmigo;
    window.bloquearAmigo = bloquearAmigo;
});