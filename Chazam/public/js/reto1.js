// Función para verificar si un carácter es un emoji
function esEmoji(caracter) {
    // Método 1: Usando Extended_Pictographic
    const emojiRegex1 = /\p{Extended_Pictographic}/u;
    
    // Método 2: Usando un conjunto más amplio de propiedades Unicode
    const emojiRegex2 = /[\p{Emoji}\p{Emoji_Presentation}\p{Emoji_Modifier}\p{Emoji_Component}\p{Emoji_Modifier_Base}]/u;
    
    // Método 3: Rango específico de emojis
    const emojiRegex3 = /[\u{1F300}-\u{1F9FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F000}-\u{1F02F}\u{1F0A0}-\u{1F0FF}\u{1F100}-\u{1F64F}\u{1F680}-\u{1F6FF}\u{1F900}-\u{1F9FF}]/u;
    
    // Verificar con cualquiera de los métodos
    return emojiRegex1.test(caracter) || emojiRegex2.test(caracter) || emojiRegex3.test(caracter);
}

// Función para procesar el mensaje antes de enviarlo
function procesarMensaje(mensaje) {
    console.log('Procesando mensaje en reto 1:', mensaje);
    
    // Verificar si el mensaje contiene al menos un emoji
    const tieneEmojis = mensaje.split('').some(caracter => esEmoji(caracter));
    console.log('¿Contiene emojis?:', tieneEmojis);
    
    // Agregar una propiedad al mensaje para indicar si tiene emojis
    mensaje = {
        texto: mensaje,
        tieneEmojis: tieneEmojis
    };
    
    return mensaje;
}

// Exportar las funciones
window.procesarMensaje = procesarMensaje;
