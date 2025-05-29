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
            // Llamar a la función de cargar amistades desde FriendshipManager
            if (window.FriendshipManager && window.FriendshipManager.cargarAmistades) {
                 window.FriendshipManager.cargarAmistades();
            }
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
window.actualizarListaChats = actualizarListaChats;
// Las funciones de amistad se gestionan ahora centralmente en friendship_modals.js