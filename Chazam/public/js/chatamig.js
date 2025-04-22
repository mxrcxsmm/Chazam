document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.querySelector('.message-input-container input');
    const sendButton = document.querySelector('.message-input-container .fa-paper-plane');
    const optionsToggle = document.querySelector('.options-toggle');
    const closeOptions = document.querySelector('.close-options');
    const optionsSidebar = document.querySelector('.options-sidebar');
    const chatMain = document.querySelector('.chat-main');
    const mainContainer = document.querySelector('.main-container');

    // Función para enviar mensaje
    function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            // Aquí irá la lógica para enviar mensajes
            messageInput.value = '';
        }
    }

    // Función para alternar el menú de opciones
    function toggleOptions() {
        optionsSidebar.classList.toggle('active');
        chatMain.classList.toggle('shifted');
        
        // Forzar un reflow para asegurar que las transiciones se apliquen correctamente
        void mainContainer.offsetWidth;
    }

    // Event listeners
    sendButton.addEventListener('click', sendMessage);
    optionsToggle.addEventListener('click', toggleOptions);
    closeOptions.addEventListener('click', toggleOptions);

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Marcar chat como activo al hacer clic
    const chatItems = document.querySelectorAll('.chat-item');
    chatItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remover clase active de todos los chats
            chatItems.forEach(chat => chat.classList.remove('active'));
            // Añadir clase active al chat seleccionado
            this.classList.add('active');
        });
    });

    // Cerrar el menú de opciones al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!optionsSidebar.contains(e.target) && 
            !optionsToggle.contains(e.target) && 
            optionsSidebar.classList.contains('active')) {
            toggleOptions();
        }
    });

    // Manejar el redimensionamiento de la ventana
    window.addEventListener('resize', function() {
        if (optionsSidebar.classList.contains('active')) {
            chatMain.style.width = `calc(100% - ${350 + optionsSidebar.offsetWidth}px)`;
        } else {
            chatMain.style.width = 'calc(100% - 350px)';
        }
    });
});
