@forelse ($compras as $compra)
    <tr>
        <td>{{ $user->nombre }} {{ $user->apellido }}</td>
        <td>{{ $compra->producto->titulo ?? 'Producto eliminado' }}</td>
        <td>{{ $compra->producto->descripcion ?? '-' }}</td>
        <td>
            {{ $compra->producto->precio }} {{ $compra->producto->tipo_valor ?? '' }}
        </td>
        <td>{{ $compra->fecha_pago }}</td>
        <td>
            <a href="{{ route('compras.factura', $compra->id_pago) }}" class="btn btn-sm btn-primary" target="_blank">
                Descargar PDF
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">No tienes compras registradas.</td>
    </tr>
@endforelse