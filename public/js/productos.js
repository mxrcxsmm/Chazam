document.addEventListener('DOMContentLoaded', function () {
    // Mostrar alerta de éxito al crear un producto
    if (document.body.dataset.successMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: document.body.dataset.successMessage,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    }

    // Mostrar alerta de éxito al editar un producto
    if (document.body.dataset.updateMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: document.body.dataset.updateMessage,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    }

    // Confirmación al eliminar un producto
    const deleteForms = document.querySelectorAll('form[action*="productos"][method="POST"].delete-form');
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
});