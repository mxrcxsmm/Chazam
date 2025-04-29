<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Lista de Usuarios - Admin</title>
</head>

<body 
    @if(session('eliminar')) data-success-message="{{ session('eliminar') }}" @endif
>
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
                        <a class="nav-link active" href="{{ route('admin.usuarios.index') }}">Usuarios</a>
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
                <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Lista de Usuarios</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Crear Usuario</button>
    </div>

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
                            <input type="text" class="form-control" id="filtro_id" placeholder="Buscar por ID">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_username">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="filtro_username" placeholder="Buscar por nombre de usuario">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_nombre_completo">Nombre Completo</label>
                            <input type="text" class="form-control" id="filtro_nombre_completo" placeholder="Buscar por nombre completo">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_genero">Género</label>
                            <select class="form-select" id="filtro_genero" name="filtro_genero">
                                <option value="">Todos los géneros</option>
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_nacionalidad">Nacionalidad</label>
                            <select class="form-select" id="filtro_nacionalidad">
                                <option value="">Todas las nacionalidades</option>
                                @foreach ($nacionalidades as $nacionalidad)
                                    <option value="{{ $nacionalidad->id_nacionalidad }}">{{ $nacionalidad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_rol">Rol</label>
                            <select class="form-select" id="filtro_rol">
                                <option value="">Todos los roles</option>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="3">Premium</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9 mb-3 d-flex align-items-end">
                        <button type="button" id="limpiarFiltros" class="btn btn-secondary">
                            <i class="fas fa-eraser"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="container mt-4">
        <h2>Usuarios Registrados</h2>
        <div id="tablaUsuarios">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Email</th>
                        <th>Nombre Completo</th>
                        <th>Fecha nacimiento</th>
                        <th>Género</th>
                        <th>Descripción</th>
                        <th>Nacionalidad</th>
                        <th>Estado</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admins as $user)
                        <tr>
                            <td>{{ $user->id_usuario }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->nombre }} {{ $user->apellido }}</td>
                            <td>{{ $user->fecha_nacimiento->format('Y-m-d') }}</td>
                            <td>{{ $user->genero }}</td>
                            <td>{{ $user->descripcion }}</td>
                            <td>{{ $user->nacionalidad->nombre ?? 'Sin nacionalidad' }}</td>
                            <td>{{ $user->estado->nom_estado ?? 'Sin estado' }}</td>
                            <td>{{ $user->rol->nom_rol ?? 'Sin rol' }}</td>
                            <td>
                                <form action="{{ route('admin.usuarios.destroy', $user->id_usuario) }}" method="POST" class="delete-form" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border: none; background: none; cursor: pointer;" title="Eliminar">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                                @if ($user->strikes >= 4 || $user->estado->nom_estado === 'PermaBan')
                                    <button disabled title="Usuario permabaneado">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <form action="{{ route('admin.usuarios.ban', $user->id_usuario) }}" method="POST" style="display:inline-block; margin-left: 5px;">
                                        @csrf
                                        <button type="submit" style="border: none; background: none; cursor: pointer;" title="Banear">
                                            <i class="fas fa-ban text-warning"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts personalizados -->
    <script src="{{ asset('js/filtrosadmin.js') }}"></script>
    <script src="{{ asset('js/modals.js') }}"></script>
    <script src="{{ asset('js/validationsUsuarios.js') }}"></script>
    <script src="{{ asset('js/usuarios.js') }}"></script>
</body>

</html>
