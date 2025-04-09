<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Editar Usuario</title>
</head>
<body>
    <div class="container">
        <h2>Editar Usuario</h2>
        <form action="{{ route('admin.usuarios.update', $user->id_usuario) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="text" name="username" value="{{ $user->username }}" required>
            <input type="email" name="email" value="{{ $user->email }}" required>
            <input type="text" name="nombre" value="{{ $user->nombre }}" required>
            <input type="text" name="apellido" value="{{ $user->apellido }}" required>
            <input type="date" name="fecha_nacimiento" value="{{ $user->fecha_nacimiento }}" required>
            <textarea name="descripcion">{{ $user->descripcion }}</textarea>
            <button type="submit">Actualizar</button>
        </form>
    </div>
</body>
</html>
