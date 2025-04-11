// Función para abrir un modal
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

// Función para cerrar un modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Función para abrir el modal de editar con datos prellenados
function openEditModal(user) {
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_nombre').value = user.nombre;
    document.getElementById('edit_apellido').value = user.apellido;
    document.getElementById('edit_fecha_nacimiento').value = user.fecha_nacimiento;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_descripcion').value = user.descripcion;

    // Preseleccionar la nacionalidad
    const nacionalidadSelect = document.getElementById('edit_id_nacionalidad');
    nacionalidadSelect.value = user.id_nacionalidad;

    // Actualizar la acción del formulario
    const editForm = document.getElementById('editForm');
    editForm.action = `/admin/${user.id_usuario}`;

    openModal('editModal');
}