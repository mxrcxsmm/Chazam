<!-- resources/views/layouts/user.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona de Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#8F00FF] text-white">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#430077] p-6 flex flex-col gap-4">
            <h2 class="text-xl font-bold mb-4">User</h2>
            <nav class="flex flex-col gap-2">
                <a href="{{ route('user.personalizacion') }}" class="bg-[#8750B2] px-3 py-2 rounded hover:bg-[#A76BD1]">Mis datos</a>
                <a href="{{ route('user.perfil') }}" class="bg-[#8750B2] px-3 py-2 rounded hover:bg-[#A76BD1]">Perfil</a>
                <a href="{{ route('user.mejoras') }}" class="bg-[#8750B2] px-3 py-2 rounded hover:bg-[#A76BD1]">Comprar Mejoras</a>
                <a href="{{ route('user.puntos') }}" class="bg-[#8750B2] px-3 py-2 rounded hover:bg-[#A76BD1]">Comprar Puntos</a>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-10 bg-[#8F00FF] relative">
            <!-- Botón de cerrar (opcional, condicional) -->
            <a href="{{ route('dashboard') }}" class="absolute top-4 right-6 text-white bg-red-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-700">X</a>

            <!-- Slot para contenido -->
            @yield('content')
        </main>
    </div>
</body>
</html>