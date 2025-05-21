<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Comprador</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Fecha de Pago</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pagos as $pago)
        <tr>
            <td>{{ $pago->id_pago }}</td>
            <td>{{ $pago->comprador->username ?? 'Usuario eliminado' }}</td>
            <td>{{ $pago->producto->titulo ?? 'Producto eliminado' }}</td>
            <td>{{ $pago->cantidad }}</td>
            <td>{{ $pago->fecha_pago }}</td>
        </tr>
        @endforeach
    </tbody>
</table>