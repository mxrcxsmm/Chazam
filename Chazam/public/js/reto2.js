// Función para encriptar el mensaje
function encriptarMensaje(texto) {
    const caracteresEspeciales = '!@#$%^&*()_+-=[]{}|;:,.<>?';
    
    return texto.split('').map((caracter, index) => {
        // Mantener espacios y signos de puntuación
        if (caracter === ' ' || caracteresEspeciales.includes(caracter)) {
            return caracter;
        }
        // Reemplazar aleatoriamente algunos caracteres con asteriscos
        return Math.random() < 0.33 ? '*' : caracter;
    }).join('');
}

// Función para procesar el mensaje antes de enviarlo
function procesarMensaje(mensaje) {
    return encriptarMensaje(mensaje);
}

// Exportar las funciones
window.procesarMensaje = procesarMensaje;
