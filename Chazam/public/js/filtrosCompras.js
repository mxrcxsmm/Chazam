document.addEventListener('DOMContentLoaded', function () {
    const filtroProducto = document.getElementById('filtro-producto');
    const filtroFecha = document.getElementById('filtro-fecha');
    const tablaCompras = document.querySelector('table tbody');

    function aplicarFiltros() {
        const data = new FormData();
        data.append('producto', filtroProducto.value.trim());
        data.append('fecha_pago', filtroFecha.value);

        // Obtener el token CSRF del meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/mis-compras/filtrar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: data
        })
        .then(response => response.json())
        .then(data => {
            tablaCompras.innerHTML = data.html;
        })
        .catch(error => {
            tablaCompras.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar los datos.</td></tr>';
        });
    }

    filtroProducto.addEventListener('input', aplicarFiltros);
    filtroFecha.addEventListener('change', aplicarFiltros);

    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    btnLimpiar.addEventListener('click', function () {
        filtroProducto.value = '';
        filtroFecha.value = '';
        aplicarFiltros();
    });
});