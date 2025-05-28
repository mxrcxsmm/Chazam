// Función utilitaria para manejar errores de fetch de manera centralizada
// Esto no reduce llamadas, pero mejora la robustez y diagnóstico de errores.
async function safeFetch(url, options = {}) {
    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            // Intenta leer el cuerpo del error si es JSON
            const errorBody = await response.text();
            console.error(`Error HTTP! estado: ${response.status} URL: ${url} Cuerpo: ${errorBody}`);
            throw new Error(`Error HTTP! estado: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error en la petición:', error);
        // Propaga el error para que el llamador pueda manejarlo si es necesario
        throw error;
    }
}

// Variables para control de inactividad y intervalos
var ultimoMensaje = Date.now();
var contadorInactividad = null; // Cambiado de let a var
var contadorRegresivo = null; // Cambiado de let a var
var alertaMostrada = false;
var miAlerta = false;
// tiempoInicialEspera ya fue ajustado a 5000ms en el commit anterior

// Definir intervalos de polling en un solo lugar para fácil ajuste
const INTERVALO_ESTADO = 60000; // 60 segundos para actualizar estado
const INTERVALO_USUARIOS_ONLINE = 60000; // 60 segundos para actualizar usuarios en línea

// Función para actualizar el estado del usuario usando safeFetch
function actualizarEstado(estado) {
    console.log(`Intentando actualizar estado a: ${estado}`);
    safeFetch('/estado/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ estado: estado })
    })
    .then(data => {
        console.log('Estado actualizado correctamente:', data);
    })
    .catch(error => {
        console.error('Error al actualizar estado:', error);
        // No mostramos un SweetAlert aquí para evitar saturar al usuario si hay problemas de red persistentes
    });
}

// Función para obtener y mostrar usuarios en línea usando safeFetch
// Si esta función SÓLO hace console.log y no actualiza la UI,
// puedes considerar aumentar su intervalo significativamente o incluso eliminar la llamada periódica.
function actualizarUsuariosEnLinea() {
    console.log('Intentando obtener usuarios en línea...');
    safeFetch('/estado/usuarios-en-linea')
        .then(usuarios => {
            console.log('Usuarios en línea:', usuarios);
            // *** Si necesitas actualizar la UI aquí, este es el lugar. ***
            // *** Optimizar la manipulación del DOM es crucial si se hace. ***
        })
        .catch(error => {
            console.error('Error al obtener usuarios en línea:', error);
        });
}

// Función para mantener el estado actualizado
function mantenerEstado() {
    const rutaActual = window.location.pathname;
    console.log('=== VERIFICANDO ESTADO ===');
    console.log('Ruta actual:', rutaActual);

    // Verificar si estamos en la página de reto
    if (rutaActual.includes('/retos/reto')) {
        console.log('Usuario en página de reto - Estado: 5 (Disponible)');
        actualizarEstado(5);
    } else {
        console.log('Usuario en otra página - Estado: 1 (Activo)');
        actualizarEstado(1);
    }
}

// Función para actualizar el último mensaje (llamada desde chatrandom.js al enviar/recibir mensaje)
function actualizarUltimoMensaje() {
    ultimoMensaje = Date.now();
    console.log('Último mensaje actualizado:', new Date(ultimoMensaje).toLocaleTimeString());
    console.log('Tiempo de inactividad reseteado');
    if (alertaMostrada && miAlerta) {
        Swal.close();
        alertaMostrada = false;
        miAlerta = false;
    }
     // El contadorInactividad ya se reinicia en iniciarControlInactividad
     // No es necesario reiniciarlo aquí a menos que la lógica de inactividad cambie
}

// Función para iniciar el control de inactividad (llamada desde chatrandom.js)
function iniciarControlInactividad() {
    console.log('=== INICIANDO CONTROL DE INACTIVIDAD ===');
    // Limpiar contadores existentes antes de iniciar uno nuevo
    detenerControlInactividad();

    // Actualizar el tiempo del último mensaje al iniciar el control
    ultimoMensaje = Date.now();

    // Esperar el tiempo inicial antes de comenzar a verificar inactividad
    // tiempoInicialEspera es 5000ms (5 segundos)
    setTimeout(() => {
        console.log('Iniciando verificación de inactividad después del tiempo de espera');
        // Verificar inactividad cada segundo (este intervalo es preciso para detectar inactividad)
        contadorInactividad = setInterval(() => {
            const tiempoInactivo = Date.now() - ultimoMensaje;
             // Umbral para mostrar la alerta: 60 segundos (60000 ms)
            if (tiempoInactivo >= 60000 && !alertaMostrada) {
                console.log('Inactividad detectada, mostrando alerta...');
                mostrarAlertaInactividad();
            }
        }, 1000); // Intervalo de verificación de 1 segundo
    }, window.tiempoInicialEspera || 5000); // Usar la variable global si existe, sino 5 segundos
}

// Función para detener el control de inactividad (llamada desde chatrandom.js)
function detenerControlInactividad() {
    if (contadorInactividad) {
        clearInterval(contadorInactividad);
        contadorInactividad = null;
         console.log('Contador de inactividad detenido');
    }
    if (contadorRegresivo) {
        clearInterval(contadorRegresivo);
        contadorRegresivo = null;
         console.log('Contador regresivo detenido');
    }
    alertaMostrada = false;
    miAlerta = false;
     console.log('Flags de alerta reseteados');
}

// Función para mostrar la alerta de inactividad usando safeFetch para la redirección
function mostrarAlertaInactividad() {
    let tiempoRestante = 15; // Segundos antes de redirigir
    alertaMostrada = true;
    miAlerta = true;

    // Limpiar el contador de inactividad principal mientras se muestra la alerta
    if (contadorInactividad) {
        clearInterval(contadorInactividad);
        contadorInactividad = null;
    }


    Swal.fire({
        title: '¿Sigues ahí?',
        html: `Tienes <b>${tiempoRestante}</b> segundos para confirmar que sigues activo.`,
        icon: 'warning',
        showCancelButton: false,
        confirmButtonText: '¡Sí, estoy conectado!',
        timer: 15000, // El timer de SweetAlert debe coincidir con el tiempoRestante
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: (popup) => {
             // Actualizar el contador regresivo mostrado en la alerta
            contadorRegresivo = setInterval(() => {
                tiempoRestante--;
                const htmlContainer = popup.querySelector('.swal2-html-container');
                if (htmlContainer) {
                    htmlContainer.innerHTML = `Tienes <b>${tiempoRestante}</b> segundos para confirmar que sigues activo.`;
                }
            }, 1000); // Actualizar cada 1 segundo
        },
        willClose: () => {
             // Limpiar el intervalo regresivo al cerrar la alerta
            clearInterval(contadorRegresivo);
            contadorRegresivo = null;
            miAlerta = false;
            alertaMostrada = false; // Resetear al cerrar
             console.log('Alerta cerrada, flags reseteados.');
        }
    }).then(async (result) => { // Usar async para poder usar await en safeFetch
        if (result.isConfirmed) {
            console.log('Usuario confirmó que sigue activo - Reseteando tiempo');
            actualizarUltimoMensaje();
             // Reiniciar el control de inactividad después de confirmar
             iniciarControlInactividad();
            Swal.fire('¡Perfecto!', 'Sigues en el chat.', 'success');
        } else {
            console.log('Usuario no respondió - Redirigiendo a guide');
             // Limpiar estado del chat antes de redirigir si no responde
             if (window.location.pathname.includes('/retos/reto')) {
                 try {
                     await safeFetch('/retos/limpiar-estado', { // Usar safeFetch
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                         }
                     });
                     console.log('Estado del reto limpiado al redirigir.');
                 } catch (error) {
                     console.error('Error al limpiar estado del reto antes de redirigir:', error);
                 }
             }
             // Limpiar todos los intervalos antes de redirigir
            detenerControlInactividad(); // Asegura que todos los intervalos de este archivo se detengan
            window.location.href = '/retos/guide'; // Redirigir
        }
    });
}

// Intervalo para mantener el estado (ahora cada 60 segundos)
setInterval(mantenerEstado, INTERVALO_ESTADO);

// Intervalo para actualizar lista de usuarios en línea (ahora cada 60 segundos)
// Si no usas esto para actualizar la UI, considera comentarlo o aumentar mucho el intervalo.
setInterval(actualizarUsuariosEnLinea, INTERVALO_USUARIOS_ONLINE);


// Actualizar estado inicial cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    console.log('Página cargada - Verificando estado inicial');
    mantenerEstado(); // Establecer el estado inicial

    // Si no estamos en la página de reto, asegurarnos de que el estado no sea 5
    // Esto ayuda a corregir el estado si un usuario cierra la página del reto de forma inesperada
    if (!window.location.pathname.includes('/retos/reto')) {
        console.log('No en página de reto - Limpiando estado del reto por si acaso');
        safeFetch('/retos/limpiar-estado', { // Usar safeFetch
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).catch(error => console.error('Error al limpiar estado en DOMContentLoaded:', error));
    }
     // No iniciar control de inactividad aquí. Se inicia desde chatrandom.js cuando se encuentra un compañero.
});

// Limpiar intervalos y actualizar estado a inactivo al cerrar la página
window.addEventListener('beforeunload', async () => { // Usar async para poder usar await
    console.log('Usuario cerrando página - Estado: 2 (Inactivo)');
    // No hacemos await aquí porque beforeunload debe ser rápido
    actualizarEstado(2);

    // Si estamos en la página de reto, limpiar el estado del reto
    if (window.location.pathname.includes('/retos/reto')) {
         try {
             // Usamos navigator.sendBeacon para enviar la petición de forma no bloqueante
             // Esto es más fiable en beforeunload que fetch con await
             const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
             if (csrfToken) {
                 const blob = new Blob([JSON.stringify({})], {type : 'application/json'}); // Envía cuerpo vacío si no necesitas datos
                 navigator.sendBeacon('/retos/limpiar-estado', blob);
                 console.log('sendBeacon enviado para limpiar estado del reto.');
             } else {
                 console.error('No se encontró token CSRF para sendBeacon.');
             }
         } catch (error) {
             console.error('Error al usar sendBeacon para limpiar estado:', error);
         }
    }
     // Limpiar intervalos. No es estrictamente necesario en beforeunload ya que la página se cierra,
     // pero es una buena práctica.
     detenerControlInactividad();
});


// Hacer las funciones disponibles globalmente para chatrandom.js
window.actualizarUltimoMensaje = actualizarUltimoMensaje;
window.iniciarControlInactividad = iniciarControlInactividad;
window.detenerControlInactividad = detenerControlInactividad;
// Exponer tiempoInicialEspera globalmente también si chatrandom.js lo necesita para el setTimeout inicial
// window.tiempoInicialEspera = tiempoInicialEspera; // (Ya lo ajustamos directamente en chatrandom.js) 