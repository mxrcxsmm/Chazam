document.addEventListener('DOMContentLoaded', function () {
    // Mostrar alerta de éxito al crear un usuario
    if (document.body.dataset.successMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: document.body.dataset.successMessage,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    }

    // Mostrar alerta de éxito al editar un usuario
    if (document.body.dataset.updateMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: document.body.dataset.updateMessage,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    }

    // Confirmación al eliminar un usuario
    const deleteForms = document.querySelectorAll('form.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

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
                    form.submit();
                }
            });
        });
    });
});

function toggleDetalles(btn) {
    var detalles = btn.closest('tr').nextElementSibling;
    if (detalles.classList.contains('d-none')) {
        detalles.classList.remove('d-none');
        btn.querySelector('i').classList.remove('fa-plus');
        btn.querySelector('i').classList.add('fa-minus');
    } else {
        detalles.classList.add('d-none');
        btn.querySelector('i').classList.remove('fa-minus');
        btn.querySelector('i').classList.add('fa-plus');
    }
}