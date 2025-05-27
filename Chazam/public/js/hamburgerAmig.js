document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('mainMenuSidebar');
    const openBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('closeMainMenuSidebar');
    const chatsList = document.getElementById('chats-list');
    const chatsListSidebar = document.getElementById('chats-list-sidebar');

    function addChatSidebarListeners() {
        // Selecciona todos los chats en la hamburguesa
        const chatItems = chatsListSidebar.querySelectorAll('.chat-item');
        chatItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                // Simula el click en el chat correspondiente de la sidebar principal
                const chatId = this.getAttribute('data-chat-id');
                if (chatId && chatsList) {
                    const mainChat = chatsList.querySelector(`.chat-item[data-chat-id="${chatId}"]`);
                    if (mainChat) {
                        mainChat.click();
                        sidebar.classList.remove('active'); // Cierra la hamburguesa
                    }
                }
            });
        });
    }

    if (openBtn && sidebar) {
        openBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.add('active');
            // Clona la lista de chats al abrir la hamburguesa
            if (chatsList && chatsListSidebar) {
                chatsListSidebar.innerHTML = chatsList.innerHTML;
                addChatSidebarListeners(); // <-- Añade los listeners cada vez que se abre
            }
        });
    }
    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.remove('active');
        });
    }
    // Cierra el sidebar al hacer click fuera de él en responsive
    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && e.target !== openBtn) {
                sidebar.classList.remove('active');
            }
        }
    });
});