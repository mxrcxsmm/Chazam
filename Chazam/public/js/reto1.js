// Función para verificar si un carácter es un emoji
function esEmoji(caracter) {
    const emojiRegex = /[\p{Emoji}]/u;
    return emojiRegex.test(caracter);
}

// Función para procesar el mensaje antes de enviarlo
function procesarMensaje(mensaje) {
    console.log('Procesando mensaje en reto 1:', mensaje);
    
    // Verificar si el mensaje contiene al menos un emoji
    const tieneEmojis = mensaje.split('').some(caracter => esEmoji(caracter));
    
    // Agregar una propiedad al mensaje para indicar si tiene emojis
    mensaje = {
        texto: mensaje,
        tieneEmojis: tieneEmojis
    };
    
    return mensaje;
}

// Exportar las funciones
window.procesarMensaje = procesarMensaje;
