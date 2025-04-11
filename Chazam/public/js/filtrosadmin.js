/**
 * Filtros para la tabla de usuarios en el panel de administración
 * Este script maneja la funcionalidad de filtrado de usuarios mediante AJAX
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de filtros
    const filtroId = document.getElementById('filtro_id');
    const filtroUsername = document.getElementById('filtro_username');
    const filtroNombreCompleto = document.getElementById('filtro_nombre_completo');
    const filtroNacionalidad = document.getElementById('filtro_nacionalidad');
    const filtroRol = document.getElementById('filtro_rol');
    const limpiarFiltrosBtn = document.getElementById('limpiarFiltros');
    const tablaUsuarios = document.getElementById('tablaUsuarios');

    // Función para aplicar los filtros
    function aplicarFiltros() {
        // Mostrar indicador de carga
        tablaUsuarios.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
        
        // Recopilar los valores de los filtros
        const filtros = {
            id: filtroId.value.trim(),
            username: filtroUsername.value.trim(),
            nombre_completo: filtroNombreCompleto.value.trim(),
            nacionalidad: filtroNacionalidad.value,
            rol: filtroRol.value
        };

        // Realizar la petición AJAX
        fetch('/admin/usuarios/filtrar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(filtros)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.text();
        })
        .then(html => {
            // Actualizar la tabla con los resultados filtrados
            tablaUsuarios.innerHTML = html;
        })
        .catch(error => {
            console.error('Error al aplicar filtros:', error);
            tablaUsuarios.innerHTML = '<div class="alert alert-danger">Error al cargar los datos. Por favor, inténtelo de nuevo.</div>';
        });
    }

    // Función para limpiar los filtros
    function limpiarFiltros() {
        // Restablecer todos los campos del formulario
        filtroId.value = '';
        filtroUsername.value = '';
        filtroNombreCompleto.value = '';
        filtroNacionalidad.value = '';
        filtroRol.value = '';
        
        // Volver a cargar la tabla con todos los usuarios
        aplicarFiltros();
    }

    // Event listener para el botón de limpiar filtros
    limpiarFiltrosBtn.addEventListener('click', limpiarFiltros);

    // Aplicar filtros automáticamente al cambiar cualquier campo
    filtroId.addEventListener('input', aplicarFiltros);
    filtroUsername.addEventListener('input', aplicarFiltros);
    filtroNombreCompleto.addEventListener('input', aplicarFiltros);
    filtroNacionalidad.addEventListener('change', aplicarFiltros);
    filtroRol.addEventListener('change', aplicarFiltros);
});
