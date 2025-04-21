<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap -->

    <title>Lista de Usuarios - Admin</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{route('admin.usuarios.index')}}">Admin Panel</a>
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
                        <a class="nav-link" href="{{route('admin.retos.index')}}">Retos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="">Reportes</a>
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

    <div class="container">
        <h1>Lista de Usuarios</h1>
        <!-- Botón para abrir el modal de crear -->
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
                                <option value="3">Usuario</option>
                                <option value="4">Premium</option>
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
                        <th>Descripción</th>
                        <th>Nacionalidad</th> <!-- Nueva columna -->
                        <th>Inicio Ban</th>
                        <th>Fin Ban</th>
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
                            <td>{{ $user->descripcion }}</td>
                            <td>{{ $user->nacionalidad->nombre ?? 'Sin nacionalidad' }}</td>
                            <td>{{ $user->inicio_ban }}</td>
                            <td>{{ $user->fin_ban }}</td>
                            <td>{{ $user->estado->nom_estado ?? 'Sin estado' }}</td> <!-- Mostrar estado -->
                            <td>{{ $user->rol->nom_rol ?? 'Sin rol' }}</td> <!-- Mostrar rol -->
                            <td>
                                <!-- Botón para abrir el modal de editar -->
                                <a href="javascript:void(0)" onclick="openEditModal({{ $user }})" class="text-warning" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form action="{{ route('admin.usuarios.destroy', $user->id_usuario) }}" method="POST" style="display:inline-block;">
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
    </div>

    <!-- Modal para Crear Usuario -->
    <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.usuarios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" name="username" id="username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" name="apellido" id="apellido" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="id_nacionalidad" class="form-label">Nacionalidad</label>
                            <select name="id_nacionalidad" id="id_nacionalidad" class="form-select">
                                <option value="" disabled selected>Seleccione una nacionalidad</option>
                                @foreach ($nacionalidades as $nacionalidad)
                                    <option value="{{ $nacionalidad->id_nacionalidad }}">{{ $nacionalidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Usuario -->
    <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Nombre de Usuario</label>
                            <input type="text" name="username" id="edit_username" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_apellido" class="form-label">Apellido</label>
                            <input type="text" name="apellido" id="edit_apellido" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="edit_fecha_nacimiento"
                                class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_id_nacionalidad" class="form-label">Nacionalidad</label>
                            <select name="id_nacionalidad" id="edit_id_nacionalidad" class="form-select">
                                <option value="" disabled>Seleccione una nacionalidad</option>
                                @foreach ($nacionalidades as $nacionalidad)
                                    <option value="{{ $nacionalidad->id_nacionalidad }}">{{ $nacionalidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/modals.js') }}"></script>
    <script src="{{ asset('js/filtrosadmin.js') }}"></script>
</body>

</html>
