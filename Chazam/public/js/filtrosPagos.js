document.addEventListener('DOMContentLoaded', function () {
    const filtroId = document.getElementById('filtro_id');
    const filtroComprador = document.getElementById('filtro_comprador');
    const filtroProducto = document.getElementById('filtro_producto');
    const filtroCantidad = document.getElementById('filtro_cantidad');
    const filtroFechaPago = document.getElementById('filtro_fecha_pago');
    const limpiarFiltrosBtn = document.getElementById('limpiarFiltros');
    const tablaPagos = document.querySelector('tbody');

    function aplicarFiltros() {
        // Mostrar spinner de carga
        tablaPagos.innerHTML = '<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';

        const filtros = {
            id_pago: filtroId.value.trim(),
            comprador: filtroComprador.value.trim(),
            producto: filtroProducto.value.trim(),
            cantidad: filtroCantidad.value.trim(),
            fecha_pago: filtroFechaPago.value.trim(),
        };

        // Obtener el token CSRF del meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/admin/pagos/filtrar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify(filtros),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then((data) => {
                if (data.length === 0) {
                    tablaPagos.innerHTML = '<tr><td colspan="4" class="text-center">No se encontraron resultados</td></tr>';
                    return;
                }

                tablaPagos.innerHTML = '';
                data.forEach((pago) => {
                    const row = `<tr>
                        <td>${pago.id_pago}</td>
                        <td>${pago.comprador}</td>
                        <td>${pago.producto}</td>
                        <td>${pago.cantidad}</td>
                        <td>${pago.fecha_pago}</td>
                    </tr>`;
                    tablaPagos.innerHTML += row;
                });
            })
            .catch((error) => {
                console.error('Error al aplicar filtros:', error);
                tablaPagos.innerHTML = '<tr><td colspan="4" class="text-center alert alert-danger">Error al cargar los datos. Por favor, inténtelo de nuevo.</td></tr>';
            });
    }

    // Evento para limpiar filtros
    limpiarFiltrosBtn.addEventListener('click', function () {
        filtroId.value = '';
        filtroComprador.value = '';
        filtroProducto.value = '';
        filtroCantidad.value = '';
        filtroFechaPago.value = '';
        aplicarFiltros();
    });

    // Eventos para aplicar filtros
    filtroId.addEventListener('input', aplicarFiltros);
    filtroComprador.addEventListener('input', aplicarFiltros);
    filtroProducto.addEventListener('input', aplicarFiltros);
    filtroCantidad.addEventListener('input', aplicarFiltros);

    // Configurar el datepicker para el campo de fecha de pago
    $('#filtro_fecha_pago').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        endDate: new Date(), // Establecer el día máximo hasta hoy
    }).on('changeDate', aplicarFiltros);
});