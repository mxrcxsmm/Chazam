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
    <title>Lista de Productos - Admin</title>
</head>

<body 
    @if(session('success')) data-success-message="{{ session('success') }}" @endif
    @if(session('update')) data-update-message="{{ session('update') }}" @endif
>
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
                        <a class="nav-link" href="{{ route('admin.usuarios.index') }}">Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.retos.index')}}">Retos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.reportes.index')}}">Reportes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.productos.index') }}">Productos</a>
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
    <div class="container mt-4">
        <h1>Lista de Productos</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Crear Producto</button>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Valor</th>
                    <th>Tipo de Producto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                    <tr>
                        <td>{{ $producto->id_producto }}</td>
                        <td>{{ $producto->titulo }}</td>
                        <td>{{ $producto->descripcion }}</td>
                        <td>{{ $producto->valor }}</td>
                        <td>{{ $producto->tipoProducto->tipo_producto ?? 'Sin tipo' }}</td>
                        <td>
                            <a href="javascript:void(0)" onclick="openEditModal({{ $producto }})" class="text-warning" title="Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('admin.productos.destroy', $producto->id_producto) }}" method="POST" class="delete-form" style="display:inline-block;">
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

    <!-- Modal para Crear Producto -->
    <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createForm" action="{{ route('admin.productos.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Crear Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" name="titulo" id="titulo" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor</label>
                            <input type="number" name="valor" id="valor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="id_tipo_producto" class="form-label">Tipo de Producto</label>
                            <select name="id_tipo_producto" id="id_tipo_producto" class="form-select">
                                <option value="" disabled selected>Seleccione un tipo</option>
                                @foreach ($tiposProducto as $tipo)
                                    <option value="{{ $tipo->id_tipo_producto }}">{{ $tipo->tipo_producto }}</option>
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

    <!-- Modal para Editar Producto -->
    <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_titulo" class="form-label">Título</label>
                            <input type="text" name="titulo" id="edit_titulo" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_valor" class="form-label">Valor</label>
                            <input type="number" name="valor" id="edit_valor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_id_tipo_producto" class="form-label">Tipo de Producto</label>
                            <select name="id_tipo_producto" id="edit_id_tipo_producto" class="form-select">
                                <option value="" disabled>Seleccione un tipo</option>
                                @foreach ($tiposProducto as $tipo)
                                    <option value="{{ $tipo->id_tipo_producto }}">{{ $tipo->tipo_producto }}</option>
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

    <script>
        function openEditModal(producto) {
            document.getElementById('edit_titulo').value = producto.titulo;
            document.getElementById('edit_descripcion').value = producto.descripcion;
            document.getElementById('edit_valor').value = producto.valor;
            document.getElementById('edit_id_tipo_producto').value = producto.id_tipo_producto;

            const editForm = document.getElementById('editForm');
            editForm.action = `/admin/productos/${producto.id_producto}`;

            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
    </script>
    <script src="{{ asset('js/productos.js') }}"></script>
    <script src="{{ asset('js/validationsProductos.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>