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

        <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap" id="filtros-tipo-valor">
            <button type="button" class="btn btn-filtro active" data-tipo="todos"
                onclick="filtrarTipoValor('todos', this)">Todos</button>
            <button type="button" class="btn btn-filtro" data-tipo="euros"
                onclick="filtrarTipoValor('euros', this)">Euros</button>
            <button type="button" class="btn btn-filtro" data-tipo="puntos"
                onclick="filtrarTipoValor('puntos', this)">Puntos</button>
        </div>

        <!-- Describer compra por puntos -->        
        <div id="mensaje-puntos" class="alert alert-warning text-center my-2"
            style="display:none; font-weight:600; border-radius:12px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Las compras con puntos no se admiten devoluciones
        </div>
        
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
                    <input type="date" class="form-control" id="filtro-fecha" name="fecha_pago"
                        max="{{ date('Y-m-d') }}">
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
                        <tr class="fila-compra tipo-{{ $compra->producto->tipo_valor ?? 'todos' }}">
                            <td data-label="Usuario">{{ $user->nombre }} {{ $user->apellido }}</td>
                            <td data-label="Producto">{{ $compra->producto->titulo ?? 'Producto eliminado' }}</td>
                            <td data-label="Descripción">{{ $compra->producto->descripcion ?? '-' }}</td>
                            <td data-label="Precio">
                                {{ $compra->producto->precio ?? 'N/A' }} {{ $compra->producto->tipo_valor ?? '' }}
                            </td>
                            <td data-label="Fecha de pago">{{ $compra->fecha_pago }}</td>
                            <td data-label="Factura">
                                @if (($compra->producto->tipo_valor ?? '') === 'puntos')
                                    {{-- Dejar en blanco si es puntos --}}
                                @else
                                    <a href="{{ route('compras.factura', $compra->id_pago) }}"
                                        class="btn btn-sm btn-primary" target="_blank">
                                        Descargar PDF
                                    </a>
                                @endif
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
