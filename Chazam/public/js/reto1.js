// Función para verificar si un mensaje contiene solo emojis
function contieneSoloEmojis(texto) {
    // Expresión regular que coincide con emojis Unicode
    const emojiRegex = /^[\p{Emoji}\s]+$/u;
    return emojiRegex.test(texto);
}

// Función para procesar el mensaje antes de enviarlo
function procesarMensaje(mensaje) {
    if (!contieneSoloEmojis(mensaje)) {
        alert('¡Solo puedes usar emojis en este reto! 😊');
        return null;
    }
    return mensaje;
}

// Exportar las funciones
window.procesarMensaje = procesarMensaje;
