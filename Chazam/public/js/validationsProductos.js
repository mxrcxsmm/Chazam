document.addEventListener('DOMContentLoaded', function () {
    const createForm = document.getElementById('createForm');
    const editForm = document.getElementById('editForm');

    // Validar formulario de creación
    if (createForm) {
        createForm.addEventListener('input', function () {
            validateProductoForm(createForm);
        });
        createForm.addEventListener('submit', function (e) {
            if (!validateProductoForm(createForm)) {
                e.preventDefault();
            }
        });
    }

    // Validar formulario de edición
    if (editForm) {
        editForm.addEventListener('input', function () {
            validateProductoForm(editForm);
        });
        editForm.addEventListener('submit', function (e) {
            if (!validateProductoForm(editForm)) {
                e.preventDefault();
            }
        });
    }

    function validateProductoForm(form) {
        let isValid = true;

        const titulo = form.querySelector('[name="titulo"]');
        const descripcion = form.querySelector('[name="descripcion"]');
        const valor = form.querySelector('[name="valor"]');
        const tipoProducto = form.querySelector('[name="id_tipo_producto"]');

        // Validar título
        if (!titulo.value.trim()) {
            showFieldError(titulo, 'El título es obligatorio.');
            isValid = false;
        } else if (titulo.value.length > 100) {
            showFieldError(titulo, 'El título no puede tener más de 100 caracteres.');
            isValid = false;
        } else if (!/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,-]+$/.test(titulo.value)) {
            showFieldError(titulo, 'El título solo puede contener letras, números, espacios y algunos caracteres especiales (.,-).');
            isValid = false;
        } else {
            clearFieldError(titulo);
        }

        // Validar descripción
        if (!descripcion.value.trim()) {
            showFieldError(descripcion, 'La descripción es obligatoria.');
            isValid = false;
        } else if (descripcion.value.length < 10) {
            showFieldError(descripcion, 'La descripción debe tener al menos 10 caracteres.');
            isValid = false;
        } else if (descripcion.value.length > 500) {
            showFieldError(descripcion, 'La descripción no puede tener más de 500 caracteres.');
            isValid = false;
        } else {
            clearFieldError(descripcion);
        }

        // Validar valor
        if (!valor.value.trim()) {
            showFieldError(valor, 'El valor es obligatorio.');
            isValid = false;
        } else if (isNaN(valor.value) || valor.value <= 0) {
            showFieldError(valor, 'El valor debe ser un número mayor a 0.');
            isValid = false;
        } else if (valor.value > 1000000) {
            showFieldError(valor, 'El valor no puede ser mayor a 1,000,000.');
            isValid = false;
        } else {
            clearFieldError(valor);
        }

        // Validar tipo de producto
        if (!tipoProducto.value) {
            showFieldError(tipoProducto, 'El tipo de producto es obligatorio.');
            isValid = false;
        } else {
            clearFieldError(tipoProducto);
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