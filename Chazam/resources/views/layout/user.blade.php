<!-- resources/views/layouts/user.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona de Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
</head>
<body>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>User</h2>
            <nav>
                <a href="{{ route('perfil.personalizacion') }}">Mis datos</a>
                <a href="{{ route('perfil.vista') }}">Perfil</a>
                <a href="{{ route('perfil.mejoras') }}">Comprar Mejoras</a>
                <a href="{{ route('perfil.puntos') }}">Comprar Puntos</a>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="main">
            <a href="{{ route('retos.guide') }}" class="cerrar">X</a>
            @yield('content')
        </main>
    </div>
</body>
</html>