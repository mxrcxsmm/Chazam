document.addEventListener('DOMContentLoaded', function () {
    const createForm = document.getElementById('createForm');
    const editForm = document.getElementById('editForm');

    // Validar formulario de creación
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            if (!validateProductoForm(createForm)) {
                e.preventDefault();
            }
        });
    }

    // Validar formulario de edición
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            if (!validateProductoForm(editForm)) {
                e.preventDefault();
            }
        });
    }

    function validateProductoForm(form) {
        const titulo = form.querySelector('[name="titulo"]');
        const descripcion = form.querySelector('[name="descripcion"]');
        const valor = form.querySelector('[name="valor"]');
        const tipoProducto = form.querySelector('[name="id_tipo_producto"]');

        if (!titulo.value.trim()) {
            alert('El título es obligatorio.');
            return false;
        }

        if (!descripcion.value.trim()) {
            alert('La descripción es obligatoria.');
            return false;
        }

        if (!valor.value.trim() || isNaN(valor.value) || valor.value <= 0) {
            alert('El valor debe ser un número mayor a 0.');
            return false;
        }

        if (!tipoProducto.value) {
            alert('El tipo de producto es obligatorio.');
            return false;
        }

        return true;
    }
});