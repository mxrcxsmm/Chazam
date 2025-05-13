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
    <title>Lista de Retos - Admin</title>
</head>

<body 
    @if(session('success')) data-success-message="{{ session('success') }}" @endif
    @if(session('update')) data-update-message="{{ session('update') }}" @endif
>
    <!-- Navbar -->
    @include('admin.partials.navbar')

    <div class="container mt-4">
        <h1>Lista de Retos</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createModal">Crear Reto</button>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($retos as $reto)
                    <tr>
                        <td>{{ $reto->id_reto }}</td>
                        <td>{{ $reto->nom_reto }}</td>
                        <td>{{ $reto->desc_reto }}</td>
                        <td>
                            <!-- Botón para abrir el modal de editar -->
                            <a href="javascript:void(0)" onclick="openEditModal({{ $reto }})" class="text-warning" title="Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <!-- Botón para eliminar -->
                            <form action="{{ route('admin.retos.destroy', $reto->id_reto) }}" method="POST" class="delete-form" style="display:inline-block;">
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

    <!-- Modal para Crear Reto -->
    <div id="createModal" class="modal fade" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createForm" action="{{ route('admin.retos.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Crear Reto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nom_reto" class="form-label">Nombre del Reto</label>
                            <input type="text" name="nom_reto" id="nom_reto" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="desc_reto" class="form-label">Descripción</label>
                            <textarea name="desc_reto" id="desc_reto" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Reto -->
    <div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Reto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nom_reto" class="form-label">Nombre del Reto</label>
                            <input type="text" name="nom_reto" id="edit_nom_reto" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_desc_reto" class="form-label">Descripción</label>
                            <textarea name="desc_reto" id="edit_desc_reto" class="form-control"></textarea>
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
        function openEditModal(reto) {
            document.getElementById('edit_nom_reto').value = reto.nom_reto;
            document.getElementById('edit_desc_reto').value = reto.desc_reto;

            const editForm = document.getElementById('editForm');
            editForm.action = `/admin/retos/${reto.id_reto}`;

            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
    </script>
    <script src="{{ asset('js/retos.js') }}"></script>
    <script src="{{ asset('js/validationsRetos.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>