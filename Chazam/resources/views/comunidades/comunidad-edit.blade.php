<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de {{ $comunidad->nombre }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Script para funciones globales -->
    <script>
        window.confirmarAbandono = function(id) {
            console.log('Iniciando confirmarAbandono con id:', id);
            Swal.fire({
                title: '¿Abandonar comunidad?',
                text: "¿Estás seguro de que quieres abandonar esta comunidad?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#9147ff',
                confirmButtonText: 'Sí, abandonar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Usuario confirmó abandonar comunidad');
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    console.log('Token CSRF:', token);
                    
                    fetch(`/comunidades/${id}/abandonar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Respuesta del servidor:', response);
                        if (!response.ok) {
                            throw new Error(`Error HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Datos recibidos:', data);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Abandonada!',
                                text: data.message,
                                confirmButtonColor: '#9147ff'
                            }).then(() => {
                                window.location.href = '/comunidades';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo abandonar la comunidad',
                                confirmButtonColor: '#9147ff'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error completo:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un error al abandonar la comunidad: ' + error.message,
                            confirmButtonColor: '#9147ff'
                        });
                    });
                }
            });
        };

        window.confirmarEliminacion = function(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción eliminará la comunidad y todos sus mensajes. No se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#9147ff',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const token = document.querySelector('meta[name="csrf-token"]').content;
                    
                    fetch(`/comunidades/${id}/eliminar`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Eliminada!',
                                text: data.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.href = '/comunidades';
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'No se pudo eliminar la comunidad',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un error al eliminar la comunidad',
                            icon: 'error'
                        });
                    });
                }
            });
        };
    </script>
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/comunidades-form.css') }}">
    <style>
        .comunidad-container {
            width: 90%;
            max-width: 1400px;
            margin: 2% auto;
            padding: 2%;
            background: rgba(45, 45, 45, 0.9);
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .community-header {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
            align-items: center;
            justify-content: space-between;
        }

        .community-image {
            width: 120px;
            height: 120px;
            border-radius: 15px;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .community-details h1 {
            color: white;
            margin: 0 0 15px 0;
            font-size: 2.5rem;
            font-weight: 600;
        }

        .creation-date, .member-count {
            color: #9147ff;
            margin: 8px 0;
            font-size: 1.2rem;
        }

        .community-description {
            background: rgba(26, 26, 26, 0.7);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .community-description h2 {
            color: white;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .community-description p {
            color: #dcddde;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .members-section {
            background: rgba(26, 26, 26, 0.7);
            border-radius: 10px;
            padding: 25px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-header h2 {
            color: white;
            margin: 0;
            font-size: 1.8rem;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: rgba(45, 45, 45, 0.9);
            border-radius: 8px;
            padding: 10px 15px;
            width: 300px;
        }

        .search-box input {
            background: none;
            border: none;
            color: white;
            margin-left: 10px;
            width: 100%;
            outline: none;
            font-size: 1rem;
        }

        .search-box i {
            color: #9147ff;
            font-size: 1.2rem;
        }

        .members-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .member-card {
            background: rgba(45, 45, 45, 0.9);
            border-radius: 10px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            padding-right: 45px;
        }

        .member-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .member-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #9147ff;
        }

        .member-info {
            flex: 1;
        }

        .member-name {
            color: white;
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .member-role {
            color: #9147ff;
            font-size: 0.9rem;
            margin: 5px 0 0 0;
        }

        .member-status {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: auto;
        }

        .member-status.online {
            background-color: #43b581;
            box-shadow: 0 0 8px #43b581;
        }

        .member-status.offline {
            background-color: #747f8d;
        }

        .member-status.idle {
            background-color: #faa61a;
            box-shadow: 0 0 8px #faa61a;
        }

        .member-actions {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            z-index: 10;
        }

        .member-action-btn {
            background: none;
            border: none;
            color: #9147ff;
            cursor: pointer;
            padding: 5px;
            transition: color 0.2s;
            font-size: 1.2rem;
        }

        .member-action-btn:hover {
            color: #7a30dd;
        }

        .member-action-btn:focus, .member-action-btn:active {
            outline: none !important;
            box-shadow: none !important;
            color: #7a30dd;
            background-color: transparent !important;
        }

        .dropdown-menu {
            background-color: #2d2d2d;
            border: 1px solid #9147ff;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: .125rem;
            min-width: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .dropdown-item {
            color: white;
            padding: 10px 15px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item:hover {
            background-color: #3a3a3a;
            color: #9147ff;
        }

        .dropdown-item:focus, .dropdown-item:active {
            background-color: #3a3a3a !important;
            outline: none !important;
            color: #9147ff !important;
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .dropdown-menu-end {
            right: 0 !important;
            left: auto !important;
        }

        .btn.grey.lighten-1 {
            margin-bottom: 30px;
            padding: 0 20px;
            height: 40px;
            line-height: 40px;
            font-size: 1rem;
        }

        h4 {
            color: white;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .community-image-container {
            display: flex;
            align-items: center;
        }

        .community-details {
            flex: 1;
        }

        .community-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .edit-community-btn, .delete-community-btn {
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            height: 45px;
            padding: 0 25px;
            font-size: 1.1rem;
            white-space: nowrap;
        }

        .edit-community-btn i, .delete-community-btn i {
            font-size: 1.3rem;
        }

        .community-code {
            color: #9147ff;
            margin: 8px 0;
            font-size: 1.2rem;
        }

        .codigo-privado {
            background-color: rgba(145, 71, 255, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
        }

        /* Estilos para el modal de Bootstrap */
        .modal {
            overflow: hidden;
        }
        
        .modal-dialog {
            max-height: 90vh;
            margin-top: 2vh;
            margin-bottom: 2vh;
        }
        
        .modal-content {
            background-color: #2d2d2d !important;
            border: 1px solid #9147ff !important;
            border-radius: 15px !important;
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.9) !important;
            opacity: 0.9 !important;
        }
        
        .modal-body {
            padding: 20px;
        }

        .modal-header {
            border-bottom: 1px solid #9147ff;
            background-color: #1a1a1a;
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #9147ff;
            background-color: #1a1a1a;
            border-radius: 0 0 15px 15px;
            padding: 1.5rem;
        }

        .modal-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-label {
            color: white;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background-color: #1a1a1a !important;
            border: 1px solid #9147ff;
            color: white !important;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #1a1a1a !important;
            border-color: #9147ff;
            color: white !important;
            box-shadow: 0 0 0 0.25rem rgba(145, 71, 255, 0.25);
        }

        .form-control::placeholder {
            color: #9147ff;
            opacity: 0.7;
        }

        .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        .btn-primary {
            background-color: #9147ff;
            border-color: #9147ff;
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #7a30dd;
            border-color: #7a30dd;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(145, 71, 255, 0.3);
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .img-thumbnail {
            border: 2px solid #9147ff;
            border-radius: 8px;
            padding: 0.25rem;
            background-color: #1a1a1a;
            transition: all 0.3s ease;
        }

        .img-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(145, 71, 255, 0.3);
        }

        /* Animación para el modal */
        .modal.fade .modal-dialog {
            transform: scale(0.8);
            transition: transform 0.3s ease-in-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* Estilos para el input de archivo */
        .form-control[type="file"] {
            padding: 0.5rem;
            line-height: 1.5;
        }

        .form-control[type="file"]::-webkit-file-upload-button {
            background-color: #9147ff;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-control[type="file"]::-webkit-file-upload-button:hover {
            background-color: #7a30dd;
        }

        /* Estilos para el textarea */
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        /* Estilos para los mensajes de error */
        .invalid-feedback {
            color: #ff5252;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        /* Estilos para el contenedor de la imagen actual */
        .current-image-container {
            background-color: #1a1a1a;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #9147ff;
            margin-top: 0.5rem;
        }

        /* Ajustes de posicionamiento y overflow para body y html cuando el modal está abierto */
        body.modal-open, html.modal-open {
            background-color: #1a1a1a !important;
            overflow: hidden !important;
            padding-right: 0 !important;
            margin: 0 !important;
        }

        /* Asegurar que Vanta esté detrás de todo */
        #vanta-bg {
            z-index: -1 !important;
        }

        /* Asegurar que el contenedor principal esté por encima de Vanta */
        .comunidad-container {
            z-index: 1;
            position: relative;
        }

        /* Estilos adicionales para asegurar fondo oscuro y alta z-index del backdrop de Bootstrap */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.9) !important;
            opacity: 0.9 !important;
            z-index: 1040 !important; /* Bootstrap default z-index for backdrop is 1040 */
        }

        /* Asegurar que el modal de Bootstrap esté por encima del backdrop */
        .modal {
            z-index: 1050 !important; /* Bootstrap default z-index for modal is 1050 */
            overflow-y: auto;
        }

        /* Ocultar overlay de Materialize si Bootstrap modal está abierto */
        .modal-open .lean-overlay {
            display: none !important;
        }

        /* Estilos para el modal de Materialize */
        .modal {
            background-color: #2d2d2d; /* Fondo del modal */
            color: white; /* Color del texto */
            border-radius: 15px;
            max-height: 90%; /* Altura máxima para que no ocupe toda la pantalla */
            width: 90%; /* Ancho del modal */
            max-width: 800px; /* Ancho máximo */
            overflow-y: visible; /* Permitir que el contenido sobresalga si es necesario */
        }

        .modal-content {
            padding: 24px; /* Padding interno */
            color: white;
        }

        .modal-content h4 {
            color: white;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .modal-footer {
            background-color: #1a1a1a; /* Fondo del footer */
            border-top: 1px solid #9147ff; /* Borde superior */
            padding: 12px 24px;
            text-align: right; /* Alinear botones a la derecha */
        }

        .modal-footer .btn-flat {
            color: white; /* Color del botón Cancelar */
        }

        .modal-footer .btn {
            background-color: #9147ff; /* Color del botón Guardar Cambios */
            color: white;
        }

        /* Estilos para el backdrop de Materialize */
        .lean-overlay {
            background-color: rgba(0, 0, 0, 0.8) !important; /* Fondo oscuro */
            opacity: 0.8 !important;
            z-index: 1000 !important; /* Z-index para el overlay de Materialize */
        }

        /* Asegurar que el modal esté por encima del overlay */
        .modal {
             z-index: 1001 !important; /* Z-index para el modal de Materialize */
        }

        /* Estilos para campos de formulario Materialize dentro del modal */
        .modal .input-field label {
            color: white !important;
        }

        .modal .input-field input[type=text],
        .modal .materialize-textarea {
            border-bottom: 1px solid #9e9e9e;
            box-shadow: none;
            color: white;
        }

        .modal .input-field input[type=text]:focus,
        .modal .materialize-textarea:focus:not([read-only]) {
            border-bottom: 1px solid #9147ff !important;
            box-shadow: 0 1px 0 0 #9147ff !important;
        }

        .modal .input-field input[type=text]:focus + label,
        .modal .materialize-textarea:focus:not([read-only]) + label {
            color: #9147ff !important;
        }

        /* Estilos para el input de archivo Materialize dentro del modal */
        .modal .file-field .btn {
            background-color: #9147ff;
            color: white;
        }

        .modal .file-field .file-path.validate {
            border-bottom: 1px solid #9e9e9e;
            box-shadow: none;
            color: white;
        }

        .modal .file-field .file-path.validate:focus {
            border-bottom: 1px solid #9147ff;
            box-shadow: 0 1px 0 0 #9147ff;
        }
    </style>
</head>
<body>
    <div id="vanta-bg"></div>
    <div class="comunidad-container">
        <a href="{{ route('comunidades.show', $comunidad->id_chat) }}" class="btn grey lighten-1 black-text">
            <i class="material-icons left">arrow_back</i> Volver a la Comunidad
        </a>
        <h4>Detalles de la Comunidad</h4>

        <div class="community-info">
            <div class="community-header">
                <div class="community-image-container">
                    <img src="{{ asset('img/comunidades/' . $comunidad->img) }}" alt="{{ $comunidad->nombre }}" class="community-image">
                </div>
                <div class="community-details">
                    <h1>{{ $comunidad->nombre }}</h1>
                    <p class="creation-date">Creada el {{ $comunidad->fecha_creacion->format('d/m/Y') }}</p>
                    <p class="member-count">{{ $comunidad->chat_usuarios_count }} miembros</p>
                    @if($comunidad->tipocomunidad === 'privada' && $comunidad->creator == Auth::id())
                        <p class="community-code">Código: <span class="codigo-privado">{{ $comunidad->codigo }}</span></p>
                    @endif
                </div>
                @if($comunidad->creator == Auth::id())
                    <div class="community-actions">
                        <a href="{{ route('comunidades.edit-form', $comunidad->id_chat) }}" class="btn purple lighten-1 white-text edit-community-btn">
                            <i class="material-icons left">edit</i>Editar
                        </a>
                        <button onclick="confirmarEliminacion({{ $comunidad->id_chat }})" class="btn red lighten-1 white-text delete-community-btn">
                            <i class="material-icons left">delete</i>Eliminar
                        </button>
                    </div>
                @else
                    <div class="community-actions">
                        <button onclick="confirmarAbandono({{ $comunidad->id_chat }})" class="btn red lighten-1 white-text delete-community-btn">
                            <i class="material-icons left">exit_to_app</i>Abandonar
                        </button>
                    </div>
                @endif
            </div>
            <div class="community-description">
                <h2>Descripción</h2>
                <p>{{ $comunidad->descripcion }}</p>
            </div>
        </div>

        <!-- Lista de miembros -->
        <div class="members-section">
            <div class="section-header">
                <h2>Miembros</h2>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="memberSearch" placeholder="Buscar miembros...">
                </div>
            </div>
            <div class="members-list" id="membersList">
                <!-- Los miembros se cargarán dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Scripts de Vanta y Materialize -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.waves.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <!-- Bootstrap JS (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- Para que se vean bien los estados --}}
    <script src="{{ asset('js/estados.js') }}"></script>
    <script>
        // Inicializar Vanta
        VANTA.WAVES({
            el: "#vanta-bg",
            color: 0x703ea3,
            backgroundColor: 0xaa00ff,
            waveHeight: 20,
            waveSpeed: 0.5,
            zoom: 0.8
        });

        document.addEventListener('DOMContentLoaded', function() {
            const memberSearch = document.getElementById('memberSearch');
            const membersList = document.getElementById('membersList');
            let allMembers = [];

            // Función para cargar los miembros
            async function loadMembers() {
                try {
                    const response = await fetch(`/comunidades/${communityId}/members`);
                    const data = await response.json();
                    
                    // Combinar creador y miembros
                    allMembers = [
                        { ...data.creator, isCreator: true },
                        ...data.members
                    ];
                    
                    // Renderizar miembros
                    renderMembers(allMembers);
                    
                    // Verificar estado de solicitudes para cada miembro
                    allMembers.forEach(member => {
                        if (!member.isCreator) {
                            verificarEstadoSolicitud(member.id_usuario);
                        }
                    });
                } catch (error) {
                    console.error('Error al cargar miembros:', error);
                }
            }

            // Función para renderizar los miembros
            function renderMembers(members) {
                membersList.innerHTML = members.map(member => `
                    <div class="member-card">
                        <img src="${member.img}" alt="${member.username}" class="member-avatar">
                        <div class="member-info">
                            <h3 class="member-name">${member.username}</h3>
                            <p class="member-role">${member.isCreator ? 'Creador' : 'Miembro'}</p>
                        </div>
                        <div class="member-status ${member.status}"></div>
                        ${!member.isCreator ? `
                            <div class="member-actions">
                                <div class="dropdown">
                                    <button class="member-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="enviarSolicitud(${member.id_usuario})">
                                            <i class="fas fa-user-plus"></i>Enviar solicitud
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="reportarUsuario(${member.id_usuario})">
                                            <i class="fas fa-flag"></i>Reportar usuario
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="bloquearUsuario(${member.id_usuario})">
                                            <i class="fas fa-ban"></i>Bloquear usuario
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `).join('');
            }

            // Función para filtrar miembros
            function filterMembers(searchTerm) {
                const filtered = allMembers.filter(member => 
                    member.username.toLowerCase().includes(searchTerm.toLowerCase())
                );
                renderMembers(filtered);
            }

            // Evento de búsqueda
            memberSearch.addEventListener('input', (e) => {
                filterMembers(e.target.value);
            });

            // Cargar miembros iniciales
            loadMembers();

            // Función para verificar el estado de la solicitud
            async function verificarEstadoSolicitud(idUsuario) {
                try {
                    const response = await fetch(`/solicitudes/verificar/${idUsuario}`);
                    const data = await response.json();
                    
                    const sendFriendRequestBtn = document.querySelector(`[data-action="enviar-solicitud"][data-user-id="${idUsuario}"]`);
                    if (!sendFriendRequestBtn) return;

                    if (data.estado === 'pendiente') {
                        sendFriendRequestBtn.innerHTML = '<i class="fas fa-clock me-2"></i>Solicitud pendiente';
                        sendFriendRequestBtn.classList.add('disabled');
                        sendFriendRequestBtn.style.pointerEvents = 'none';
                        sendFriendRequestBtn.style.opacity = '0.7';
                    } else if (data.estado === 'aceptada') {
                        sendFriendRequestBtn.parentElement.style.display = 'none';
                    } else if (data.estado === 'no_existe') {
                        sendFriendRequestBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Enviar solicitud';
                        sendFriendRequestBtn.classList.remove('disabled');
                        sendFriendRequestBtn.style.pointerEvents = 'auto';
                        sendFriendRequestBtn.style.opacity = '1';
                    }
                } catch (error) {
                    console.error('Error al verificar estado de solicitud:', error);
                }
            }

            // Función para enviar solicitud de amistad
            window.enviarSolicitud = function(idUsuario) {
                fetch('/solicitudes/enviar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        id_receptor: idUsuario
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar el botón después de enviar la solicitud
                        const sendFriendRequestBtn = document.querySelector(`[data-action="enviar-solicitud"][data-user-id="${idUsuario}"]`);
                        if (sendFriendRequestBtn) {
                            sendFriendRequestBtn.innerHTML = '<i class="fas fa-clock me-2"></i>Solicitud pendiente';
                            sendFriendRequestBtn.classList.add('disabled');
                            sendFriendRequestBtn.style.pointerEvents = 'none';
                            sendFriendRequestBtn.style.opacity = '0.7';
                        }

                        Swal.fire({
                            title: '¡Solicitud enviada!',
                            text: 'La solicitud de amistad ha sido enviada correctamente.',
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'No se pudo enviar la solicitud de amistad.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al enviar la solicitud.',
                        icon: 'error'
                    });
                });
            };

            // Función para reportar usuario
            window.reportarUsuario = function(idUsuario) {
                Swal.fire({
                    title: 'Reportar usuario',
                    html: `
                        <div class="mb-3">
                            <label for="reportTitle" class="form-label text-white">Título del reporte</label>
                            <input type="text" class="form-control bg-dark text-white" id="reportTitle" placeholder="Ingrese un título">
                        </div>
                        <div class="mb-3">
                            <label for="reportDescription" class="form-label text-white">Descripción</label>
                            <textarea class="form-control bg-dark text-white" id="reportDescription" rows="3" placeholder="Describa el motivo del reporte"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Enviar reporte',
                    cancelButtonText: 'Cancelar',
                    background: '#4B0082',
                    color: '#fff',
                    preConfirm: () => {
                        const title = document.getElementById('reportTitle').value;
                        const description = document.getElementById('reportDescription').value;
                        if (!title || !description) {
                            Swal.showValidationMessage('Por favor complete todos los campos');
                            return false;
                        }
                        return { title, description };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/reportes/crear', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                id_reportado: idUsuario,
                                titulo: result.value.title,
                                descripcion: result.value.description
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: '¡Reporte enviado!',
                                    text: 'El reporte ha sido enviado correctamente.',
                                    icon: 'success'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.message || 'No se pudo enviar el reporte.',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Ocurrió un error al enviar el reporte.',
                                icon: 'error'
                            });
                        });
                    }
                });
            };

            // Función para bloquear usuario
            window.bloquearUsuario = function(idUsuario) {
                Swal.fire({
                    title: '¿Bloquear usuario?',
                    text: '¿Estás seguro de que deseas bloquear a este usuario? No podrás ver sus mensajes ni interactuar con él.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, bloquear',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/solicitudes/bloquear', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                id_usuario: idUsuario
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: '¡Usuario bloqueado!',
                                    text: 'El usuario ha sido bloqueado correctamente.',
                                    icon: 'success'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.message || 'No se pudo bloquear al usuario.',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Ocurrió un error al bloquear al usuario.',
                                icon: 'error'
                            });
                        });
                    }
                });
            };
        });
    </script>
</body>
</html>