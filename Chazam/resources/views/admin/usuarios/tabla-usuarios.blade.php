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
                <!-- Icono para abrir modal en móvil -->
                <span class="btn-mas d-inline-block d-md-none"
                    data-bs-toggle="modal"
                    data-bs-target="#modalUsuario"
                    data-user-id="{{ $user->id_usuario }}"
                    style="cursor:pointer;">
                    <i class="fas fa-plus"></i>
                </span>
                <!-- Acciones normales (eliminar, banear, etc.) -->
                <form action="{{ route('admin.usuarios.destroy', $user->id_usuario) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="border: none; background: none; cursor: pointer;" title="Eliminar">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </form>
                @if ($user->strikes >= 4 || $user->estado->nom_estado === 'PermaBan')
                <span title="Usuario permabaneado" style="color: #aaa; margin-left: 5px;">
                    <i class="fas fa-ban"></i>
                </span>
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