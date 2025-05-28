<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th class="th-id">ID</th>
            <th class="th-usuario">Usuario</th>
            <th class="th-producto">Producto</th>
            <th class="th-cantidad">Cantidad</th>
            <th class="th-precio">Precio</th>
            <th class="th-tipo">Tipo</th>
            <th class="th-fecha">Fecha de Pago</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pagos as $pago)
        <tr>
            <td class="td-id">{{ $pago->id_pago }}</td>
            <td class="td-usuario">{{ $pago->comprador->username ?? 'Usuario eliminado' }}</td>
            <td class="td-producto">{{ $pago->producto->titulo ?? 'Producto eliminado' }}</td>
            <td class="td-cantidad">{{ $pago->cantidad ?? 'N/A' }}</td>
            <td class="td-precio">{{ $pago->precio ?? 'N/A' }}</td>
            <td class="td-tipo">{{ $pago->nombre_tipo ?? 'N/A' }}</td>
            <td class="td-fecha">{{ $pago->fecha_pago }}</td>
        </tr>
        @endforeach
    </tbody>
</table>