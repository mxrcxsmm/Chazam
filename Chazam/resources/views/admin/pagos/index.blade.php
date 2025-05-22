<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/pagos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <title>Pagos de Usuarios</title>
</head>

<body>
    <!-- Navbar -->
    @include('admin.partials.navbar')

    <!-- Sección de filtros -->
    <div class="container mt-4">
        <div class="filtros-container">
            <div class="filtros-header">
                <i class="fas fa-filter"></i>
                <h5 class="mb-0">Filtros de búsqueda</h5>
            </div>
            <form id="filtroForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_id">ID</label>
                            <input type="text" class="form-control" id="filtro_id" name="filtro_id"
                                placeholder="Buscar por ID">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_comprador">Nombre de Comprador</label>
                            <input type="text" class="form-control" id="filtro_comprador" name="filtro_comprador"
                                placeholder="Buscar por nombre de comprador">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_producto">Nombre de Producto</label>
                            <input type="text" class="form-control" id="filtro_producto" name="filtro_producto"
                                placeholder="Buscar por nombre de producto">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_cantidad">Cantidad</label>
                            <input type="text" class="form-control" id="filtro_cantidad" name="filtro_cantidad"
                                placeholder="Buscar por cantidad">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_fecha_pago">Fecha de pago</label>
                            <input type="text" class="form-control" id="filtro_fecha_pago" name="filtro_fecha_pago"
                                placeholder="Buscar por fecha de pago">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="filtro-campo">
                            <button type="button" id="limpiarFiltros" class="btn btn-secondary">
                                <i class="fas fa-eraser"></i> Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container mt-4">
        <h1>Pagos de Usuarios</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('update'))
            <div class="alert alert-info">{{ session('update') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="th-id">ID</th>
                        <th class="th-usuario">Comprador</th>
                        <th class="th-producto">Producto</th>
                        <th class="th-cantidad">Cantidad</th>
                        <th class="th-fecha">Fecha de pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagos as $pago)
                        <tr>
                            <td class="td-id">{{ $pago->id_pago }}</td>
                            <td class="td-usuario">{{ $pago->comprador->username ?? 'Usuario eliminado' }}</td>
                            <td class="td-producto">{{ $pago->producto->titulo ?? 'Producto eliminado' }}</td>
                            <td class="td-cantidad">{{ $pago->cantidad }}</td>
                            <td class="td-fecha">{{ $pago->fecha_pago }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.es.min.js">
    </script>
    <script src="{{ asset('js/filtrosPagos.js') }}"></script>
    <script src="{{ asset('js/reportes.js') }}"></script>
</body>

</html>
