/**
 * Filtros para la tabla de usuarios en el panel de administración
 * Este script maneja la funcionalidad de filtrado de usuarios mediante AJAX
 */

document.addEventListener('DOMContentLoaded', function () {
    const filtroId = document.getElementById('filtro_id');
    const filtroUsername = document.getElementById('filtro_username');
    const filtroNombreCompleto = document.getElementById('filtro_nombre_completo');
    const filtroGenero = document.getElementById('filtro_genero');
    const filtroNacionalidad = document.getElementById('filtro_nacionalidad');
    const filtroRol = document.getElementById('filtro_rol');
    const limpiarFiltrosBtn = document.getElementById('limpiarFiltros');
    const tablaUsuarios = document.getElementById('tablaUsuarios');

    function aplicarFiltros() {
        // Mostrar spinner de carga
        tablaUsuarios.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        const filtros = {
            id: filtroId.value.trim(),
            username: filtroUsername.value.trim(),
            nombre_completo: filtroNombreCompleto.value.trim(),
            nacionalidad: filtroNacionalidad.value,
            rol: filtroRol.value,
            genero: filtroGenero.value // Capturar el filtro de género
        };

        // Obtener el token CSRF del meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/admin/usuarios/filtrar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(filtros)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.html) {
                tablaUsuarios.innerHTML = data.html;
                // Reasociar el evento del modal tras recargar la tabla
                if (window.bootstrap && window.bootstrap.Modal) {
                    const modal = document.getElementById('modalUsuario');
                    if (modal) {
                        const bsModal = window.bootstrap.Modal.getInstance(modal);
                        if (bsModal) bsModal.hide();
                    }
                }
                // Reasociar el evento show.bs.modal SIN { once: true }
                const modalUsuario = document.getElementById('modalUsuario');
                if (modalUsuario) {
                    // Elimina listeners anteriores para evitar duplicados
                    modalUsuario.replaceWith(modalUsuario.cloneNode(true));
                    const nuevoModalUsuario = document.getElementById('modalUsuario');
                    nuevoModalUsuario.addEventListener('show.bs.modal', function (event) {
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
            } else if (data.error) {
                tablaUsuarios.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        })
        .catch(error => {
            console.error('Error al aplicar filtros:', error);
            tablaUsuarios.innerHTML = '<div class="alert alert-danger">Error al cargar los datos. Por favor, inténtelo de nuevo.</div>';
        });
    }

    // Evento para limpiar filtros
    limpiarFiltrosBtn.addEventListener('click', function () {
        filtroId.value = '';
        filtroUsername.value = '';
        filtroNombreCompleto.value = '';
        filtroNacionalidad.value = '';
        filtroRol.value = '';
        filtroGenero.value = ''; // Limpiar el filtro de género
        aplicarFiltros();
    });

    // Eventos para aplicar filtros
    filtroId.addEventListener('input', aplicarFiltros);
    filtroUsername.addEventListener('input', aplicarFiltros);
    filtroNombreCompleto.addEventListener('input', aplicarFiltros);
    filtroNacionalidad.addEventListener('change', aplicarFiltros);
    filtroRol.addEventListener('change', aplicarFiltros);
    filtroGenero.addEventListener('change', aplicarFiltros); // Evento para el filtro de género

    // Llama a aplicarFiltros al cargar la página
    aplicarFiltros();
});
