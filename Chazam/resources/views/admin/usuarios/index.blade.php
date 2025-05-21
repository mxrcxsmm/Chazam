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

<body @if (session('eliminar')) data-success-message="{{ session('eliminar') }}" @endif>
    <!-- Navbar -->
    @include('admin.partials.navbar')

    <div class="container mt-4">
        <h1>Lista de Usuarios</h1>
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
                            <input type="text" class="form-control" id="filtro_username"
                                placeholder="Buscar por nombre de usuario">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_nombre_completo">Nombre Completo</label>
                            <input type="text" class="form-control" id="filtro_nombre_completo"
                                placeholder="Buscar por nombre completo">
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
                                    <option value="{{ $nacionalidad->id_nacionalidad }}">{{ $nacionalidad->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="filtro-campo">
                            <label for="filtro_rol">Rol</label>
                            <select class="form-select" id="filtro_rol">
                                <option value="">Todos los roles</option>
                                <option value="1">Administrador</option>
                                <option value="2">Usuario</option>
                                <option value="3">Premium</option>
                                <option value="4">Miembro</option>
                            </select>
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

    <div class="container mt-4">
        <h2>Usuarios Registrados</h2>
        <div id="tablaUsuarios">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="th-id">ID</th>
                        <th class="th-usuario">Nombre de Usuario</th>
                        <th class="th-email">Email</th>
                        <th class="th-nombre">Nombre Completo</th>
                        <th class="th-fecha">Fecha nacimiento</th>
                        <th class="th-genero">Género</th>
                        <th class="th-descripcion">Descripción</th>
                        <th class="th-nacionalidad">Nacionalidad</th>
                        <th class="th-estado">Estado</th>
                        <th class="th-rol">Rol</th>
                        <th class="th-acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admins as $user)
                        <tr>
                            <td class="td-id">{{ $user->id_usuario }}</td>
                            <td class="td-usuario">{{ $user->username }}</td>
                            <td class="td-email">{{ $user->email }}</td>
                            <td class="td-nombre">{{ $user->nombre }} {{ $user->apellido }}</td>
                            <td class="td-fecha">{{ $user->fecha_nacimiento->format('Y-m-d') }}</td>
                            <td class="td-genero">{{ $user->genero }}</td>
                            <td class="td-descripcion">{{ $user->descripcion }}</td>
                            <td class="td-nacionalidad">{{ $user->nacionalidad->nombre ?? 'Sin nacionalidad' }}</td>
                            <td class="td-estado">{{ $user->estado->nom_estado ?? 'Sin estado' }}</td>
                            <td class="td-rol">{{ $user->rol->nom_rol ?? 'Sin rol' }}</td>
                            <td class="td-acciones">
                                <!-- Botón + solo visible en móvil -->
                                <button class="btn-mas d-inline-block d-md-none" onclick="toggleDetalles(this)">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <!-- Acciones normales (editar, eliminar, etc.) -->
                                <form action="{{ route('admin.usuarios.destroy', $user->id_usuario) }}"
                                    method="POST" class="delete-form" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border: none; background: none; cursor: pointer;"
                                        title="Eliminar">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                                @if ($user->strikes >= 4 || $user->estado->nom_estado === 'PermaBan')
                                    <button disabled title="Usuario permabaneado">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <form action="{{ route('admin.usuarios.ban', $user->id_usuario) }}"
                                        method="POST" style="display:inline-block; margin-left: 5px;">
                                        @csrf
                                        <button type="submit"
                                            style="border: none; background: none; cursor: pointer;" title="Banear">
                                            <i class="fas fa-ban text-warning"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        <!-- Fila de detalles, oculta por defecto, solo para móvil -->
                        <tr class="detalles-usuario d-none d-md-table-row">
                            <td colspan="11">
                                <strong>ID:</strong> {{ $user->id_usuario }}<br>
                                <strong>Nombre de Usuario:</strong> {{ $user->username }}<br>
                                <strong>Email:</strong> {{ $user->email }}<br>
                                <strong>Nombre Completo:</strong> {{ $user->nombre }} {{ $user->apellido }}<br>
                                <strong>Fecha nacimiento:</strong> {{ $user->fecha_nacimiento->format('Y-m-d') }}<br>
                                <strong>Género:</strong> {{ $user->genero }}<br>
                                <strong>Descripción:</strong> {{ $user->descripcion }}<br>
                                <strong>Nacionalidad:</strong> {{ $user->nacionalidad->nombre ?? 'Sin nacionalidad' }}<br>
                                <strong>Estado:</strong> {{ $user->estado->nom_estado ?? 'Sin estado' }}<br>
                                <strong>Rol:</strong> {{ $user->rol->nom_rol ?? 'Sin rol' }}
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
    <script src="{{ asset('js/reportes.js') }}"></script>
</body>

</html>
