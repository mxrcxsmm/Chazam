<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar {{ $comunidad->nombre }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/comunidades-form.css') }}">
</head>
<body>
    <div id="vanta-bg"></div>
    <div class="comunidad-container">
        <a href="{{ route('comunidades.show', $comunidad->id_chat) }}" class="btn grey lighten-1 black-text" style="margin-bottom: 20px;">
            <i class="material-icons left">arrow_back</i> Volver a la Comunidad
        </a>
        <h4>Editar Comunidad</h4>

        <form id="editForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="input-field">
                <i class="material-icons prefix">group</i>
                <input id="nombre" name="nombre" type="text" class="validate" value="{{ $comunidad->nombre }}" required>
                <label for="nombre">Nombre de la Comunidad</label>
            </div>

            <div class="input-field">
                <i class="material-icons prefix">description</i>
                <textarea id="descripcion" name="descripcion" class="materialize-textarea validate" required>{{ $comunidad->descripcion }}</textarea>
                <label for="descripcion">Descripción</label>
            </div>

            <div class="file-field input-field">
                <div class="btn purple">
                    <span>Imagen</span>
                    <input type="file" id="img" name="img" accept="image/*">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Selecciona una imagen">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" style="color:white">Imagen actual</label>
                <div>
                    <img src="{{ asset('img/comunidades/' . $comunidad->img) }}" alt="Imagen actual" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
            </div>

            <div class="input-field center">
                <button type="submit" class="btn btn-comunidad waves-effect">
                    <i class="material-icons left">save</i>Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.waves.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Vanta
        VANTA.WAVES({
            el: "#vanta-bg",
            color: 0x703ea3,
            backgroundColor: 0xaa00ff,
            waveHeight: 20,
            waveSpeed: 0.5,
            zoom: 0.8
        });

        // Inicializar Materialize
        M.textareaAutoResize(document.querySelector('#descripcion'));

        // Manejar el envío del formulario
        const form = document.getElementById('editForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('{{ route("comunidades.update", $comunidad->id_chat) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '{{ route("comunidades.show", $comunidad->id_chat) }}';
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'Hubo un error al actualizar la comunidad',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al procesar la solicitud',
                    icon: 'error'
                });
            });
        });
    });
    </script>
</body>
</html>
