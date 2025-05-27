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

    var modalUsuario = document.getElementById('modalUsuario');
    if (modalUsuario) {
        modalUsuario.addEventListener('show.bs.modal', function (event) {
            var icon = event.relatedTarget;
            var userId = icon.getAttribute('data-user-id');
            if (userId) {
                fetch('/admin/usuarios/' + userId + '/json')
                    .then(response => response.json())
                    .then(user => {
                        const campos = [
                            { label: 'ID', key: 'id_usuario' },
                            { label: 'Nombre de Usuario', key: 'username' },
                            { label: 'Email', key: 'email' },
                            { label: 'Nombre Completo', key: 'nombre_completo' },
                            { label: 'Fecha nacimiento', key: 'fecha_nacimiento' },
                            { label: 'Género', key: 'genero' },
                            { label: 'Descripción', key: 'descripcion' },
                            { label: 'Nacionalidad', key: 'nacionalidad' },
                            { label: 'Estado', key: 'estado' },
                            { label: 'Rol', key: 'rol' }
                        ];
                        let html = '<ul class="list-group">';
                        campos.forEach(campo => {
                            html += `<li class="list-group-item"><strong>${campo.label}:</strong> ${user[campo.key] ?? ''}</li>`;
                        });
                        html += '</ul>';
                        document.getElementById('modalUsuarioBody').innerHTML = html;
                    });
            }
        });
    }
});

function toggleDetalles(btn) {
    // Solo funciona en móvil
    if (window.innerWidth > 768) return;
    const tr = btn.closest('tr');
    const detalles = tr.nextElementSibling;
    if (detalles && detalles.classList.contains('detalles-usuario')) {
        detalles.classList.toggle('d-none');
    }
}