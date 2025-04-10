<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>Crear Usuario</title>
</head>
<body>
    <div class="container">
        <h2>Crear Usuario</h2>
        <form action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf
            <input type="text" name="username" placeholder="Nombre de Usuario" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellido" placeholder="Apellido" required>
            <input type="date" name="fecha_nacimiento" placeholder="Fecha de Nacimiento" required>
            <textarea name="descripcion" placeholder="Descripción"></textarea>
            <button type="submit">Crear</button>
        </form>
    </div>
</body>
</html>
