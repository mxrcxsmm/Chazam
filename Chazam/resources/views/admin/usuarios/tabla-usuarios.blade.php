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
                <td>{{ $user->genero }}</td>
                <td>{{ $user->descripcion }}</td>
                <td>{{ $user->nacionalidad->nombre ?? 'Sin nacionalidad' }}</td>
                <td>{{ $user->inicio_ban }}</td>
                <td>{{ $user->fin_ban }}</td>
                <td>{{ $user->estado->nom_estado ?? 'Sin estado' }}</td>
                <td>{{ $user->rol->nom_rol ?? 'Sin rol' }}</td>
                <td>
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