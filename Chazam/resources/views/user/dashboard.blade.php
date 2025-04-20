<!-- filepath: c:\wamp64\www\DAW2\MP12\Chazam\Chazam\resources\views\user\dashboard.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuario</title>
</head>
<body>
    @include('layout.chatsHeader')

    <div class="main-container">
        <div class="center-container">
            <h1>Bienvenido, Usuario</h1>
            <p>Esta es la página principal para usuarios normales.</p>
        </div>
    </div>

    <!-- Sidebar del dashboard -->
    <div id="sidebar2" class="position-fixed bottom-0 end-0 bg-purple text-white p-4" style="width: 260px; height: 92.5vh; z-index: 1040;">
        <div class="d-flex justify-content-between align-items-center mb-4">
        <ul class="list-unstyled">
            <li class="titulo_dashboard">Dashboard</li>
            <li class="desc_dashboard">Bienvenido a tu panel de control personal</li>

            <a href="{{ route('retos.reto') }}" class="text-decoration-none">
                <button type="button" class="btn btn-warning w-100 rounded-pill reto-btn mb-3">
                    Ir a Retos
                    <span class="triangle"></span>
                    <span class="triangle tight"></span>
                </button>
            </a>

            <a href="{{ route('user.friendchat') }}" class="text-decoration-none">
                <button type="button" class="btn btn-warning w-100 rounded-pill friend-btn">
                    Amigos
                    <span class="triangle"></span>
                    <span class="triangle tight"></span>
                </button>
            </a>
        </ul>
    </div>

    <style>
    .main-container {
        display: flex;
        height: 92.5vh;
        padding: 20px;
    }

    .center-container {
        flex: 1;
        margin-right: 260px; /* Mismo ancho que el sidebar */
    }

    .bg-purple {
        background-color: #1a1a1a;
    }

    .titulo_dashboard {
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 15px;
        color: #9147ff;
    }

    .desc_dashboard {
        margin-bottom: 20px;
        color: #cccccc;
    }

    .reto-btn, .friend-btn {
        background-color: #9147ff;
        color: white;
        border: none;
        transition: all 0.3s ease;
    }

    .reto-btn:hover, .friend-btn:hover {
        background-color: #7a30dd;
        color: white;
    }

    .triangle {
        position: absolute;
        right: 10px;
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid white;
        transition: transform 0.3s ease;
    }

    .triangle.tight {
        right: 12px;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid rgba(255, 255, 255, 0.5);
    }

    .reto-btn:hover .triangle, .friend-btn:hover .triangle {
        transform: translateY(2px);
    }
    </style>
</body>
</html>