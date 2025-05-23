<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Comunidad</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/comunidades-form.css') }}">
</head>
<body>
    <div id="vanta-bg"></div>
    <div class="comunidad-container">
        <a href="{{ route('comunidades.index') }}" class="btn grey lighten-1 black-text" style="margin-bottom: 20px;">
            <i class="material-icons left">arrow_back</i> Volver a Comunidades
        </a>
        <h4>Crear Nueva Comunidad</h4>

        <form action="{{ route('comunidades.store') }}" method="POST" enctype="multipart/form-data" id="comunidadForm">
            @csrf
            <div class="input-field">
                <i class="material-icons prefix">group</i>
                <input id="nombre" name="nombre" type="text" class="validate {{ $errors->has('nombre') ? 'error' : '' }}" value="{{ old('nombre') }}" autocomplete="off">
                <label for="nombre">Nombre de la Comunidad</label>
                @error('nombre')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            <div class="input-field">
                <i class="material-icons prefix">description</i>
                <textarea id="descripcion" name="descripcion" class="materialize-textarea validate {{ $errors->has('descripcion') ? 'error' : '' }}">{{ old('descripcion') }}</textarea>
                <label for="descripcion">Descripción</label>
                @error('descripcion')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            <div class="input-field">
                <i class="material-icons prefix">public</i>
                <select id="tipocomunidad" name="tipocomunidad" class="{{ $errors->has('tipocomunidad') ? 'error' : '' }}">
                    <option value="" disabled {{ old('tipocomunidad') ? '' : 'selected' }}>Selecciona el tipo</option>
                    <option value="publica" {{ old('tipocomunidad') == 'publica' ? 'selected' : '' }}>Pública</option>
                    <option value="privada" {{ old('tipocomunidad') == 'privada' ? 'selected' : '' }}>Privada</option>
                </select>
                <label for="tipocomunidad">Tipo de Comunidad</label>
                @error('tipocomunidad')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            <div class="file-field input-field">
                <div class="btn purple">
                    <span>Imagen</span>
                    <input type="file" name="img" accept="image/jpeg,image/png,image/jpg,image/gif" class="{{ $errors->has('img') ? 'error' : '' }}">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Sube una imagen para tu comunidad">
                </div>
                @error('img')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            <div class="input-field center">
                <button class="btn btn-comunidad waves-effect" type="submit">Crear Comunidad</button>
            </div>
        </form>
    </div>

    <!-- Scripts de Vanta y Materialize -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.waves.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        VANTA.WAVES({
            el: "#vanta-bg",
            color: 0x703ea3,
            backgroundColor: 0xaa00ff
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar SweetAlert si hay error
            @if(session('error'))
                @if(str_contains(session('error'), 'Ya existe una comunidad'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Nombre duplicado',
                        text: 'Ya existe una comunidad con este nombre',
                        confirmButtonColor: '#9147ff'
                    });
                @elseif(str_contains(session('error'), 'imagen'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de imagen',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#9147ff'
                    });
                @else
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#9147ff'
                    });
                @endif
            @endif

            var elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);
            var textAreas = document.querySelectorAll('textarea');
            M.CharacterCounter.init(textAreas);

            // Función para marcar campo como error
            function markFieldAsError(fieldId) {
                const field = document.getElementById(fieldId);
                field.classList.add('error');
            }

            // Función para limpiar errores
            function clearErrors() {
                document.querySelectorAll('.input-field input, .input-field textarea, .input-field select').forEach(el => {
                    el.classList.remove('error');
                });
            }

            // Función para validar un campo
            function validateField(fieldId) {
                const field = document.getElementById(fieldId);
                const value = field.value.trim();
                
                if (!value) {
                    markFieldAsError(fieldId);
                    return false;
                } else {
                    field.classList.remove('error');
                    return true;
                }
            }

            // Añadir eventos blur a los campos
            document.getElementById('nombre').addEventListener('blur', function() {
                validateField('nombre');
            });

            document.getElementById('descripcion').addEventListener('blur', function() {
                validateField('descripcion');
            });

            document.getElementById('tipocomunidad').addEventListener('change', function() {
                validateField('tipocomunidad');
            });

            // Validación del formulario
            document.getElementById('comunidadForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Obtener los valores de los campos
                const nombre = document.getElementById('nombre').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();
                const tipocomunidad = document.getElementById('tipocomunidad').value;
                const imagenInput = document.querySelector('input[type="file"]');
                let hasErrors = false;
                let emptyFields = [];

                // Validar campos vacíos
                if (!nombre) {
                    markFieldAsError('nombre');
                    emptyFields.push('Nombre');
                    hasErrors = true;
                }

                if (!descripcion) {
                    markFieldAsError('descripcion');
                    emptyFields.push('Descripción');
                    hasErrors = true;
                }

                if (!tipocomunidad) {
                    markFieldAsError('tipocomunidad');
                    emptyFields.push('Tipo de comunidad');
                    hasErrors = true;
                }

                if (!imagenInput.files || imagenInput.files.length === 0) {
                    emptyFields.push('Imagen');
                    hasErrors = true;
                }

                if (hasErrors) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campos incompletos',
                        text: 'Por favor, completa los siguientes campos: ' + emptyFields.join(', '),
                        confirmButtonColor: '#9147ff'
                    });
                    return;
                }

                // Obtener puntos actuales del usuario
                const puntosActuales = {{ Auth::user()->puntos }};
                const costoComunidad = 75000;
                const puntosRestantes = puntosActuales - costoComunidad;

                // Mostrar confirmación con SweetAlert
                Swal.fire({
                    title: '¿Crear comunidad?',
                    html: `
                        <p>Crear una comunidad cuesta 75,000 puntos.</p>
                        <p>Puntos actuales: ${puntosActuales.toLocaleString()}</p>
                        <p>Puntos después de la compra: <span style="color: ${puntosRestantes < 0 ? '#ff5252' : '#43b581'}">${puntosRestantes.toLocaleString()}</span></p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#9147ff',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, crear comunidad',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (puntosRestantes < 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Puntos insuficientes',
                                text: 'No tienes suficientes puntos para crear una comunidad. Necesitas 75,000 puntos.',
                                confirmButtonColor: '#9147ff'
                            });
                        } else {
                            // Enviar el formulario usando fetch
                            const formData = new FormData(this);
                            fetch('{{ route('comunidades.store') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.error,
                                        confirmButtonColor: '#9147ff'
                                    });
                                } else if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Éxito!',
                                        text: data.message,
                                        confirmButtonColor: '#9147ff'
                                    }).then(() => {
                                        window.location.href = '{{ route('comunidades.index') }}';
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Hubo un error al crear la comunidad',
                                    confirmButtonColor: '#9147ff'
                                });
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
