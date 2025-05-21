<!-- filepath: resources/views/tienda/historial.blade.php -->
@extends('layout.chatsHeader')

@section('title', 'Mis Compras')

@section('content')

    @push('head')
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush

    <link rel="stylesheet" href="{{ asset('css/compras.css') }}">
    <div class="container mt-4">
        <h2 class="mb-4">Mis Compras</h2>

        <!-- Filtros -->
        <div class="card mb-4 p-3" id="filtros-compras">
            <form id="form-filtros-compras" class="row g-2 align-items-end w-100">
                <div class="col-md-6">
                    <label for="filtro-producto" class="form-label mb-0">Producto</label>
                    <input type="text" class="form-control" id="filtro-producto" name="producto"
                        placeholder="Buscar producto...">
                </div>
                <div class="col-md-4">
                    <label for="filtro-fecha" class="form-label mb-0">Fecha de pago</label>
                    <input type="date" class="form-control" id="filtro-fecha" name="fecha_pago">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="btn-limpiar-filtros" class="btn btn-secondary w-100">Limpiar filtros</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Producto</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Fecha de pago</th>
                        <th>Factura</th>
                    </tr>
                </thead>
                <tbody>
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
                                <a href="{{ route('compras.factura', $compra->id_pago) }}" class="btn btn-sm btn-primary"
                                    target="_blank">
                                    Descargar PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No tienes compras registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script src="{{ asset('js/filtrosCompras.js') }}"></script>
@endsection
