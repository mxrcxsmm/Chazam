<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Lista de Usuarios - Admin</title>
</head>
<body>
    <div class="container">
        <h2>Usuarios Registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Email</th>
                    <th>Nombre Completo</th>
                    <th>Fecha nacimiento</th>
                    <th>Descripción</th>
                    <th>Inicio Ban</th>
                    <th>Fin Ban</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id_usuario }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->nombre }} {{ $user->apellido }}</td>
                    <td>{{ $user->fecha_nacimiento }}</td>
                    <td>{{ $user->descripcion }}</td>
                    <td>{{ $user->inicio_ban }}</td>
                    <td>{{ $user->fin_ban }}</td>
                    <td>
                        <a href="{{ route('usuarios.edit', $user->id_usuario) }}" class="btn-edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        {{-- <form action="{{ route('usuarios.ban', $user->id_usuario) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn-ban">Banear</button>
                        </form>
                        <form action="{{ route('usuarios.unban', $user->id_usuario) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn-unban">Desbanear</button>
                        </form> --}}
                        <form action="{{ route('usuarios.destroy', $user->id_usuario) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
