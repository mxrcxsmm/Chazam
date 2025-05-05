<!-- filepath: c:\wamp64\www\DAW2\MP12\Chazam\Chazam\resources\views\admin\pagos\index.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Pagos de Usuarios</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.usuarios.index') }}">Admin Panel</a>
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
                        <a class="nav-link" href="{{ route('admin.reportes.index') }}">Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.productos.index') }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.pagos.index') }}">Pagos</a>
                    </li>
                </ul>
                <!-- Botón de cerrar sesión -->
                <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

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
                        <th>ID</th>
                        <th>Comprador</th>
                        <th>Producto</th>
                        <th>Fecha de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagos as $pago)
                        <tr>
                            <td>{{ $pago->id_pago }}</td>
                            <td>{{ $pago->comprador->username ?? 'Usuario eliminado' }}</td>
                            <td>{{ $pago->producto->titulo ?? 'Producto eliminado' }}</td>
                            <td>{{ $pago->fecha_pago }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
