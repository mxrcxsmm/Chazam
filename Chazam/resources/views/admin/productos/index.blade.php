<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/productos.css') }}">
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
    @include('admin.partials.navbar')
    <div class="container mt-4">
        <h1>Lista de Productos</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Crear Producto</button>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th class="th-id d-none d-md-table-cell">ID</th>
                    <th class="th-titulo">Título</th>
                    <th class="th-descripcion d-none d-md-table-cell">Descripción</th>
                    <th class="th-precio d-none d-md-table-cell">Precio</th>
                    <th class="th-tipo-valor d-none d-md-table-cell">Tipo de Valor</th>
                    <th class="th-precio-valor d-table-cell d-md-none">Precio</th>
                    <th class="th-tipo-producto d-none d-md-table-cell">Tipo de Producto</th>
                    <th class="th-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                    <tr>
                        <td class="td-id d-none d-md-table-cell">{{ $producto->id_producto }}</td>
                        <td class="td-titulo">{{ $producto->titulo }}</td>
                        <td class="td-descripcion d-none d-md-table-cell">{{ $producto->descripcion }}</td>
                        <td class="td-precio d-none d-md-table-cell">{{ $producto->precio }}</td>
                        <td class="td-tipo-valor d-none d-md-table-cell">{{ $producto->tipo_valor }}</td>
                        <td class="td-precio-valor d-table-cell d-md-none">
                            @if($producto->tipo_valor === 'puntos')
                                {{ number_format($producto->precio, 0, '', '.') }} {{ $producto->tipo_valor }}
                            @else
                                {{ $producto->precio }} {{ $producto->tipo_valor }}
                            @endif
                        </td>
                        <td class="td-tipo-producto d-none d-md-table-cell">{{ $producto->tipoProducto->tipo_producto ?? 'Sin tipo' }}</td>
                        <td class="td-acciones">
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="producto-form-container">
                    <form id="createForm" action="{{ route('admin.productos.store') }}" method="POST" class="producto-form">
                        @csrf
                        <h5 class="modal-title" id="createModalLabel">Crear Producto</h5>
                        <div class="row">
                            <div class="input-field col-md-6 mb-3">
                                <label for="titulo">Título</label>
                                <input type="text" name="titulo" id="titulo">
                            </div>
                            <div class="input-field col-md-6 mb-3">
                                <label for="precio">Precio</label>
                                <input type="number" name="precio" id="precio" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col-md-6 mb-3">
                                <label for="tipo_valor">Tipo de Valor</label>
                                <select name="tipo_valor" id="tipo_valor">
                                    <option value="" disabled selected>Seleccione un tipo</option>
                                    <option value="euros">Euros</option>
                                    <option value="puntos">Puntos</option>
                                </select>
                            </div>
                            <div class="input-field col-md-6 mb-3">
                                <label for="id_tipo_producto">Tipo de Producto</label>
                                <select name="id_tipo_producto" id="id_tipo_producto">
                                    <option value="" disabled selected>Seleccione un tipo</option>
                                    @foreach ($tiposProducto as $tipo)
                                        <option value="{{ $tipo->id_tipo_producto }}">{{ $tipo->tipo_producto }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col-12 mb-3">
                                <label for="descripcion">Descripción</label>
                                <textarea name="descripcion" id="descripcion"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-custom">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Producto -->
    <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="producto-form-container">
                    <form id="editForm" method="POST" class="producto-form">
                        @csrf
                        @method('PUT')
                        <h5 class="modal-title" id="editModalLabel">Editar Producto</h5>
                        <div class="row">
                            <div class="input-field col-md-6 mb-3">
                                <label for="edit_titulo">Título</label>
                                <input type="text" name="titulo" id="edit_titulo">
                            </div>
                            <div class="input-field col-md-6 mb-3">
                                <label for="edit_precio">Precio</label>
                                <input type="number" name="precio" id="edit_precio" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col-md-6 mb-3">
                                <label for="edit_tipo_valor">Tipo de Valor</label>
                                <select name="tipo_valor" id="edit_tipo_valor">
                                    <option value="" disabled selected>Seleccione un tipo</option>
                                    <option value="euros">Euros</option>
                                    <option value="puntos">Puntos</option>
                                </select>
                            </div>
                            <div class="input-field col-md-6 mb-3">
                                <label for="edit_id_tipo_producto">Tipo de Producto</label>
                                <select name="id_tipo_producto" id="edit_id_tipo_producto">
                                    <option value="" disabled>Seleccione un tipo</option>
                                    @foreach ($tiposProducto as $tipo)
                                        <option value="{{ $tipo->id_tipo_producto }}">{{ $tipo->tipo_producto }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col-12 mb-3">
                                <label for="edit_descripcion">Descripción</label>
                                <textarea name="descripcion" id="edit_descripcion"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-custom">Actualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(producto) {
            document.getElementById('edit_titulo').value = producto.titulo;
            document.getElementById('edit_descripcion').value = producto.descripcion;
            document.getElementById('edit_precio').value = producto.precio;
            document.getElementById('edit_tipo_valor').value = producto.tipo_valor;
            document.getElementById('edit_id_tipo_producto').value = producto.id_tipo_producto;

            const editForm = document.getElementById('editForm');
            editForm.action = `/admin/productos/${producto.id_producto}`;

            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
    </script>
    <script src="{{ asset('js/productos.js') }}"></script>
    <script src="{{ asset('js/validationsProductos.js') }}"></script>
    <script src="{{ asset('js/reportes.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>