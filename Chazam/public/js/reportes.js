document.addEventListener('DOMContentLoaded', function () {
    // Confirmación al eliminar un reporte
    const deleteForms = document.querySelectorAll('form[action*="reportes"][method="POST"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Evitar el envío del formulario

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¡No podrás revertir esta acción!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Enviar el formulario si se confirma
                }
            });
        });
    });

    const contador = document.getElementById('contador-reportes');

    if (contador) {
        function actualizarContador() {
            fetch('/admin/reportes/nuevos')
                .then(response => response.json())
                .then(data => {
                    contador.textContent = data.nuevos; // Actualizar el contador con el número de reportes no leídos
                })
                .catch(error => console.error('Error al obtener el contador de reportes:', error));
        }

        // Actualizar el contador cada 10 segundos
        setInterval(actualizarContador, 10000);

        // Actualizar el contador al cargar la página
        actualizarContador();
    }
});