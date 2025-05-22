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
                <!-- Botón para abrir modal en móvil -->
                <button class="btn btn-primary btn-mas d-inline-block d-md-none"
                    data-bs-toggle="modal"
                    data-bs-target="#modalUsuario"
                    data-user='@json($user)'>
                    <i class="fas fa-plus"></i>
                </button>
                <!-- Acciones normales (eliminar, banear, etc.) -->
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

<!-- Modal para mostrar info completa del usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUsuarioLabel">Información completa del usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="modalUsuarioBody">
        <!-- Aquí se cargará la info por JS -->
      </div>
    </div>
  </div>
</div>
<script src="{{asset('js/usuarios.js')}}"></script>