<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Lista de Reportes - Admin</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.reportes.index') }}">Admin Panel - Reportes</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.usuarios.index') }}">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.retos.index') }}">Retos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.reportes.index') }}">Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.productos.index') }}">Productos</a>
                    </li>
                </ul>
                <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Lista de Reportes</h1>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reportador</th>
                    <th>Reportado</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportes as $reporte)
                    <tr>
                        <td>{{ $reporte->id_reporte }}</td>
                        <td>{{ $reporte->reportador->username ?? 'N/A' }}</td>
                        <td>{{ $reporte->reportado->username ?? 'N/A' }}</td>
                        <td>{{ $reporte->titulo }}</td>
                        <td>{{ $reporte->descripcion }}</td>
                        <td>
                            <!-- Botón para eliminar -->
                            <form action="{{ route('admin.reportes.destroy', $reporte->id_reporte) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="border: none; background: none; cursor: pointer;" title="Eliminar">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    
    <script src="{{ asset('js/reportes.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>