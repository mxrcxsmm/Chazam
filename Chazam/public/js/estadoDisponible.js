// Función para mantener el estado actualizado
function mantenerEstado() {
    fetch('/estado/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ estado: 5 })
    });
}

// Actualizar estado cada 2 minutos
setInterval(mantenerEstado, 120000);

// Actualizar estado cuando el usuario cierra la pestaña
window.addEventListener('beforeunload', () => {
    fetch('/estado/actualizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ estado: 2 }) // Estado inactivo
    });
});

// Actualizar estado inicial
mantenerEstado();
