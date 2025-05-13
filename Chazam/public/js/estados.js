// Variables para control de inactividad
let ultimoMensaje = Date.now();
let contadorInactividad = null;
let contadorRegresivo = null;
let alertaMostrada = false;
let miAlerta = false;
let tiempoInicialEspera = 30000; // 30 segundos de espera inicial

// Función para actualizar el estado del usuario
function actualizarEstado(estado) {
    fetch('/estado/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ estado: estado })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Estado actualizado:', data);
    })
    .catch(error => {
        console.error('Error al actualizar estado:', error);
    });
}

// Función para obtener y mostrar usuarios en línea
function actualizarUsuariosEnLinea() {
    fetch('/estado/usuarios-en-linea')
        .then(response => response.json())
        .then(usuarios => {
            console.log('Usuarios en línea:', usuarios);
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

// Función para actualizar el último mensaje
function actualizarUltimoMensaje() {
    ultimoMensaje = Date.now();
    console.log('Último mensaje actualizado:', new Date(ultimoMensaje).toLocaleTimeString());
    console.log('Tiempo de inactividad reseteado');
    if (alertaMostrada && miAlerta) {
        Swal.close();
        alertaMostrada = false;
        miAlerta = false;
    }
    // Reiniciar el contador de inactividad
    if (contadorInactividad) {
        clearInterval(contadorInactividad);
        contadorInactividad = setInterval(() => {
            const tiempoInactivo = Date.now() - ultimoMensaje;
            console.log('Tiempo inactivo:', tiempoInactivo / 1000, 'segundos');
            if (tiempoInactivo >= 60000 && !alertaMostrada) { // 1 minuto
                console.log('Inactividad detectada, mostrando alerta...');
                mostrarAlertaInactividad();
            }
        }, 1000);
    }
}

// Función para iniciar el control de inactividad
function iniciarControlInactividad() {
    console.log('=== INICIANDO CONTROL DE INACTIVIDAD ===');
    // Limpiar contadores existentes
    detenerControlInactividad();
    
    // Esperar el tiempo inicial antes de comenzar a verificar inactividad
    setTimeout(() => {
        console.log('Iniciando verificación de inactividad después del tiempo de espera');
        // Verificar inactividad cada segundo
        contadorInactividad = setInterval(() => {
            const tiempoInactivo = Date.now() - ultimoMensaje;
            console.log('Tiempo inactivo:', tiempoInactivo / 1000, 'segundos');
            if (tiempoInactivo >= 60000 && !alertaMostrada) { // 1 minuto
                console.log('Inactividad detectada, mostrando alerta...');
                mostrarAlertaInactividad();
            }
        }, 1000);
    }, tiempoInicialEspera);
}

// Función para detener el control de inactividad
function detenerControlInactividad() {
    if (contadorInactividad) {
        clearInterval(contadorInactividad);
        contadorInactividad = null;
    }
    if (contadorRegresivo) {
        clearInterval(contadorRegresivo);
        contadorRegresivo = null;
    }
    alertaMostrada = false;
    miAlerta = false;
}

// Función para mostrar la alerta de inactividad
function mostrarAlertaInactividad() {
    let tiempoRestante = 15;
    alertaMostrada = true;
    miAlerta = true;
    
    const swalInstance = Swal.fire({
        title: '¿Sigues ahí?',
        html: `Tienes <b>${tiempoRestante}</b> segundos para confirmar que sigues activo.`,
        icon: 'warning',
        showCancelButton: false,
        confirmButtonText: '¡Sí, estoy conectado!',
        timer: 15000,
        timerProgressBar: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: (popup) => {
            contadorRegresivo = setInterval(() => {
                tiempoRestante--;
                const htmlContainer = popup.querySelector('.swal2-html-container');
                if (htmlContainer) {
                    htmlContainer.innerHTML = `Tienes <b>${tiempoRestante}</b> segundos para confirmar que sigues activo.`;
                }
            }, 1000);
        },
        willClose: () => {
            clearInterval(contadorRegresivo);
            miAlerta = false;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Usuario confirmó que sigue activo - Reseteando tiempo');
            actualizarUltimoMensaje();
            Swal.fire('¡Perfecto!', 'Sigues en el chat.', 'success');
        } else {
            console.log('Usuario no respondió - Redirigiendo a guide');
            window.location.href = '/retos/guide';
        }
    });
}

// Verificar estado cada 30 segundos
setInterval(mantenerEstado, 30000);

// Actualizar estado cuando el usuario cierra la pestaña o navegador
window.addEventListener('beforeunload', () => {
    console.log('Usuario cerrando página - Estado: 2 (Inactivo)');
    actualizarEstado(2);
    
    // Si estamos en la página de reto, limpiar el estado
    if (window.location.pathname.includes('/retos/reto')) {
        fetch('/retos/limpiar-estado', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).catch(error => console.error('Error al limpiar estado:', error));
    }
});

// Actualizar lista de usuarios en línea cada 30 segundos
setInterval(actualizarUsuariosEnLinea, 30000);

// Actualizar estado inicial cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    console.log('Página cargada - Verificando estado inicial');
    mantenerEstado();
    
    // Si no estamos en la página de reto, asegurarnos de que el estado no sea 5
    if (!window.location.pathname.includes('/retos/reto')) {
        fetch('/retos/limpiar-estado', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).catch(error => console.error('Error al limpiar estado:', error));
    }
});

// Hacer las funciones disponibles globalmente
window.actualizarUltimoMensaje = actualizarUltimoMensaje;
window.iniciarControlInactividad = iniciarControlInactividad;
window.detenerControlInactividad = detenerControlInactividad; 