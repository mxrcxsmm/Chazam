document.addEventListener('DOMContentLoaded', function () {
    const createForm = document.getElementById('createForm');
    const editForm = document.getElementById('editForm');

    // Validar formulario de creación
    if (createForm) {
        createForm.addEventListener('input', function () {
            validateUsuarioForm(createForm);
        });
        createForm.addEventListener('submit', function (e) {
            if (!validateUsuarioForm(createForm)) {
                e.preventDefault();
            }
        });
    }

    // Validar formulario de edición
    if (editForm) {
        editForm.addEventListener('input', function () {
            validateUsuarioForm(editForm);
        });
        editForm.addEventListener('submit', function (e) {
            if (!validateUsuarioForm(editForm)) {
                e.preventDefault();
            }
        });
    }

    function validateUsuarioForm(form) {
        let isValid = true;

        const username = form.querySelector('[name="username"]');
        const nombre = form.querySelector('[name="nombre"]');
        const apellido = form.querySelector('[name="apellido"]');
        const fechaNacimiento = form.querySelector('[name="fecha_nacimiento"]');
        const email = form.querySelector('[name="email"]');
        const nacionalidad = form.querySelector('[name="id_nacionalidad"]');
        const descripcion = form.querySelector('[name="descripcion"]');

        // Validar nombre de usuario
        if (!username.value.trim()) {
            showFieldError(username, 'El nombre de usuario es obligatorio.');
            isValid = false;
        } else if (username.value.length > 20) {
            showFieldError(username, 'El nombre de usuario no puede tener más de 20 caracteres.');
            isValid = false;
        } else if (!/^[a-zA-Z0-9_]+$/.test(username.value)) {
            showFieldError(username, 'El nombre de usuario solo puede contener letras, números y guiones bajos.');
            isValid = false;
        } else {
            clearFieldError(username);
        }

        // Validar nombre
        if (!nombre.value.trim()) {
            showFieldError(nombre, 'El nombre es obligatorio.');
            isValid = false;
        } else if (nombre.value.length > 50) {
            showFieldError(nombre, 'El nombre no puede tener más de 50 caracteres.');
            isValid = false;
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre.value)) {
            showFieldError(nombre, 'El nombre solo puede contener letras, espacios, acentos y la letra ñ.');
            isValid = false;
        } else {
            clearFieldError(nombre);
        }

        // Validar apellido
        if (!apellido.value.trim()) {
            showFieldError(apellido, 'El apellido es obligatorio.');
            isValid = false;
        } else if (apellido.value.length > 50) {
            showFieldError(apellido, 'El apellido no puede tener más de 50 caracteres.');
            isValid = false;
        } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellido.value)) {
            showFieldError(apellido, 'El apellido solo puede contener letras, espacios, acentos y la letra ñ.');
            isValid = false;
        } else {
            clearFieldError(apellido);
        }

        // Validar fecha de nacimiento
        if (fechaNacimiento) {
            if (!fechaNacimiento.value.trim()) {
                showFieldError(fechaNacimiento, 'La fecha de nacimiento es obligatoria.');
                isValid = false;
            } else if (new Date(fechaNacimiento.value) > new Date()) {
                showFieldError(fechaNacimiento, 'La fecha de nacimiento no puede ser posterior a hoy.');
                isValid = false;
            } else {
                clearFieldError(fechaNacimiento);
            }
        }

        // Validar email
        if (!email.value.trim()) {
            showFieldError(email, 'El email es obligatorio.');
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            showFieldError(email, 'El formato del email no es válido.');
            isValid = false;
        } else {
            clearFieldError(email);
        }

        // Validar nacionalidad
        if (!nacionalidad.value) {
            showFieldError(nacionalidad, 'La nacionalidad es obligatoria.');
            isValid = false;
        } else {
            clearFieldError(nacionalidad);
        }

        // Validar descripción (opcional)
        if (descripcion && descripcion.value.length > 200) {
            showFieldError(descripcion, 'La descripción no puede tener más de 200 caracteres.');
            isValid = false;
        } else {
            clearFieldError(descripcion);
        }

        return isValid;
    }

    // Función para mostrar errores en un campo
    function showFieldError(field, message) {
        field.classList.add('is-invalid');
        let error = field.nextElementSibling;
        if (!error || !error.classList.contains('invalid-feedback')) {
            error = document.createElement('div');
            error.className = 'invalid-feedback';
            field.parentNode.appendChild(error);
        }
        error.textContent = message;
    }

    // Función para limpiar errores de un campo
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        let error = field.nextElementSibling;
        if (error && error.classList.contains('invalid-feedback')) {
            error.textContent = '';
        }
    }
});