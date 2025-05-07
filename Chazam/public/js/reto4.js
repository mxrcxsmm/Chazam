// Función para voltear el texto con caracteres especiales
function voltearTexto(texto) {
    const caracteres = {
        a: 'ɐ', b: 'q', c: 'ɔ', d: 'p', e: 'ǝ', f: 'ɟ', g: 'ƃ',
        h: 'ɥ', i: 'ᴉ', j: 'ɾ', k: 'ʞ', l: 'l', m: 'ɯ', n: 'u',
        ñ: 'u', o: 'o', p: 'd', q: 'b', r: 'ɹ', s: 's', t: 'ʇ',
        u: 'n', v: 'ʌ', w: 'ʍ', x: 'x', y: 'ʎ', z: 'z',
        á: 'ɐ', é: 'ǝ', í: 'ᴉ', ó: 'o', ú: 'n', ü: 'n',
        '.': '˙', '!': '¡', ',': '\'', ';': '؛', ':': ':',
        '(': ')', ')': '(', '[': ']', ']': '[', '{': '}', '}': '{'
    };
    
    return texto.toLowerCase().split('').map(c => caracteres[c] || c).reverse().join('');
}

// Función para procesar el mensaje antes de enviarlo
function procesarMensaje(mensaje) {
    return voltearTexto(mensaje);
}

// Exportar las funciones
window.procesarMensaje = procesarMensaje;
