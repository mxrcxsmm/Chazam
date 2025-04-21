<script>
    // Función para actualizar el estado del usuario
    function actualizarEstado(estado) {
        fetch('/estado/actualizar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ estado: estado })
        });
    }

    // Actualizar estado cada 2 minutos
    setInterval(() => {
        actualizarEstado(1); // Estado activo
    }, 120000);

    // Actualizar estado cuando el usuario cierra la pestaña o navegador
    window.addEventListener('beforeunload', () => {
        actualizarEstado(2); // Estado inactivo
    });

    // Función para obtener y mostrar usuarios en línea
    function actualizarUsuariosEnLinea() {
        fetch('/estado/usuarios-en-linea')
            .then(response => response.json())
            .then(usuarios => {
                // Aquí puedes actualizar tu UI con los usuarios en línea
                console.log('Usuarios en línea:', usuarios);
            });
    }

    // Actualizar lista de usuarios en línea cada 30 segundos
    setInterval(actualizarUsuariosEnLinea, 30000);
</script> 