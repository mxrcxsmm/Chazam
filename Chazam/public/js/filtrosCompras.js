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

    filtrarTipoValor('todos', document.querySelector('#filtros-tipo-valor .btn-filtro[data-tipo="todos"]'));
});

function filtrarTipoValor(tipo, btn) {
    // Cambia el botón activo
    document.querySelectorAll('#filtros-tipo-valor .btn-filtro').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Muestra/oculta filas según el filtro
    document.querySelectorAll('.fila-compra').forEach(row => {
        if (tipo === 'todos' || row.classList.contains('tipo-' + tipo)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Mostrar mensaje si es puntos o todos
    const mensaje = document.getElementById('mensaje-puntos');
    if (tipo === 'puntos' || tipo === 'todos') {
        mensaje.style.display = '';
    } else {
        mensaje.style.display = 'none';
    }
}