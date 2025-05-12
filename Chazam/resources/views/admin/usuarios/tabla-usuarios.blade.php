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
                <form action="{{ route('admin.usuarios.destroy', $user->id_usuario) }}" method="POST" style="display:inline-block;">
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