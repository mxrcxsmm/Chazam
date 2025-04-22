document.addEventListener('DOMContentLoaded', function () {
    const createForm = document.getElementById('createForm');
    const editForm = document.getElementById('editForm');

    // Validar formulario de creación
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            if (!validateRetoForm(createForm)) {
                e.preventDefault();
            }
        });
    }

    // Validar formulario de edición
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            if (!validateRetoForm(editForm)) {
                e.preventDefault();
            }
        });
    }

    function validateRetoForm(form) {
        const nombreReto = form.querySelector('[name="nom_reto"]');
        const descripcionReto = form.querySelector('[name="desc_reto"]');

        // Validar nombre del reto
        if (!nombreReto.value.trim()) {
            alert('El nombre del reto es obligatorio.');
            return false;
        }
        if (nombreReto.value.length > 100) {
            alert('El nombre del reto no puede tener más de 100 caracteres.');
            return false;
        }
        if (!/^[a-zA-Z0-9\s]+$/.test(nombreReto.value)) {
            alert('El nombre del reto solo puede contener letras, números y espacios.');
            return false;
        }

        // Validar descripción del reto
        if (!descripcionReto.value.trim()) {
            alert('La descripción del reto es obligatoria.');
            return false;
        }
        if (descripcionReto.value.length > 500) {
            alert('La descripción del reto no puede tener más de 500 caracteres.');
            return false;
        }

        return true;
    }
});