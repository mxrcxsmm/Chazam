document.addEventListener('DOMContentLoaded', function () {
    const createForm = document.getElementById('createForm');
    const editForm = document.getElementById('editForm');

    // Validar formulario de creación
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            if (!validateUsuarioForm(createForm, true)) { // true indica que es el formulario de creación
                e.preventDefault();
            }
        });
    }

    // Validar formulario de edición
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            if (!validateUsuarioForm(editForm, false)) { // false indica que es el formulario de edición
                e.preventDefault();
            }
        });
    }

    function validateUsuarioForm(form, isCreateForm) {
        const username = form.querySelector('[name="username"]');
        const nombre = form.querySelector('[name="nombre"]');
        const apellido = form.querySelector('[name="apellido"]');
        const fechaNacimiento = form.querySelector('[name="fecha_nacimiento"]');
        const email = form.querySelector('[name="email"]');
        const nacionalidad = form.querySelector('[name="id_nacionalidad"]');
        const descripcion = form.querySelector('[name="descripcion"]');

        // Validar nombre de usuario
        if (!username.value.trim()) {
            showError('El nombre de usuario es obligatorio.');
            return false;
        }
        if (username.value.length > 20) {
            showError('El nombre de usuario no puede tener más de 20 caracteres.');
            return false;
        }
        if (!/^[a-zA-Z0-9_]+$/.test(username.value)) {
            showError('El nombre de usuario solo puede contener letras, números y guiones bajos.');
            return false;
        }

        // Validar nombre
        if (!nombre.value.trim()) {
            showError('El nombre es obligatorio.');
            return false;
        }
        if (nombre.value.length > 50) {
            showError('El nombre no puede tener más de 50 caracteres.');
            return false;
        }
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre.value)) {
            showError('El nombre solo puede contener letras, espacios, acentos y la letra ñ.');
            return false;
        }

        // Validar apellido
        if (!apellido.value.trim()) {
            showError('El apellido es obligatorio.');
            return false;
        }
        if (apellido.value.length > 50) {
            showError('El apellido no puede tener más de 50 caracteres.');
            return false;
        }
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellido.value)) {
            showError('El apellido solo puede contener letras, espacios, acentos y la letra ñ.');
            return false;
        }

        // Validar fecha de nacimiento (solo en el formulario de creación)
        if (isCreateForm) {
            if (!fechaNacimiento.value.trim()) {
                showError('La fecha de nacimiento es obligatoria.');
                return false;
            }
            if (new Date(fechaNacimiento.value) > new Date()) {
                showError('La fecha de nacimiento no puede ser posterior a hoy.');
                return false;
            }
        }

        // Validar email
        if (!email.value.trim()) {
            showError('El email es obligatorio.');
            return false;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            showError('El formato del email no es válido.');
            return false;
        }

        // Validar nacionalidad
        if (!nacionalidad.value) {
            showError('La nacionalidad es obligatoria.');
            return false;
        }

        // Validar descripción (opcional)
        if (descripcion && descripcion.value.length > 200) {
            showError('La descripción no puede tener más de 200 caracteres.');
            return false;
        }

        return true;
    }

    // Función para mostrar errores con SweetAlert2
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error de Validación',
            text: message,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        });
    }
});