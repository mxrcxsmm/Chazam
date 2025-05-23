// Función para mezclar un array
function mezclarArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

// Función para desordenar el mensaje
function desordenarMensaje(texto) {
    // Dividir el texto en palabras
    const palabras = texto.split(' ');
    // Mezclar las palabras
    const palabrasMezcladas = mezclarArray([...palabras]);
    // Unir las palabras de nuevo
    return palabrasMezcladas.join(' ');
}

// Función para procesar el mensaje antes de enviarlo
function procesarMensaje(mensaje) {
    // Verificar que el mensaje tenga al menos 60 caracteres para sumar puntos
    if (mensaje.length < 60) {
        // Devolver un objeto indicando que no debe sumar puntos
        return {
            texto: desordenarMensaje(mensaje),
            sumarPuntos: false
        };
    }
    
    // Si tiene 60 o más caracteres, devolver con la indicación para sumar puntos
    return {
        texto: desordenarMensaje(mensaje),
        sumarPuntos: true
    };
}

// Exportar las funciones
window.procesarMensaje = procesarMensaje;
