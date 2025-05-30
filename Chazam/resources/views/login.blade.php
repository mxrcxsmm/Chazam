<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <div id="vanta-bg"></div>
    <div class="login-outer">
        <div class="login-container">
            <img src="{{ asset('img/Login_Chazam.png') }}" alt="" style="" class="logo">

            <div class="row">
                <div class="col s12">
                    <ul class="tabs transparent">
                        <li class="tab col s6"><a href="#login" class="active">Log In</a></li>
                        <li class="tab col s6"><a href="#signup">Sign Up</a></li>
                    </ul>
                </div>

                <!-- Login -->
                <div id="login" class="col s12">
                    <form action="{{ route('auth.login') }}" method="POST">
                        @csrf
                        <div class="input-field">
                            <input id="email" name="email" type="email" class="validate">
                            <label for="email">Email</label>
                        </div>

                        <div class="input-field">
                            <input id="password" name="password" type="password" class="validate">
                            <label for="password">Password</label>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="hola">
                                <button class="btn btn-custom" type="submit">Log In</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Sign Up -->
                <div id="signup" class="col s12">
                    <form action="{{ route('auth.register') }}" method="POST" class="form-signup" enctype="multipart/form-data">
                        @csrf
                        <!-- Primera fila -->
                        <div class="row">
                            <!-- Username -->
                            <div class="input-field col s4">
                                <i class="material-icons prefix">person_outline</i>
                                <input id="username" name="username" type="text" class="validate">
                                <label for="username">Username</label>
                            </div>
                            
                            <!-- Nombre y Apellido -->
                            <div class="input-field col s4">
                                <i class="material-icons prefix">badge</i>
                                <input id="nombre" name="nombre" type="text" class="validate">
                                <label for="nombre">Nombre</label>
                            </div>
                            <div class="input-field col s4">
                                <i class="material-icons prefix">people</i>
                                <input id="apellido" name="apellido" type="text" class="validate">
                                <label for="apellido">Apellido</label>
                            </div>
                        </div>

                        <!-- Segunda fila -->
                        <div class="row">
                            <!-- Fecha de Nacimiento -->
                            <div class="input-field col s3">
                                <i class="material-icons prefix">event</i>
                                <input id="fecha_nacimiento" name="fecha_nacimiento" type="text" class="datepicker validate">
                                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            </div>
                            
                            <!-- Email -->
                            <div class="input-field col s3">
                                <i class="material-icons prefix">email</i>
                                <input id="email_signup" name="email" type="email" class="validate">
                                <label for="email_signup">Email</label>
                            </div>

                            <!-- Género -->
                            <div class="input-field col s3">
                                {{-- <i class="material-icons prefix">wc</i> --}}
                                <select id="genero" name="genero" class="validate">
                                    <option value="" disabled selected>Selecciona</option>
                                    <option value="hombre">Hombre</option>
                                    <option value="mujer">Mujer</option>
                                </select>
                                <label>Género</label>
                            </div>

                            <!-- Nacionalidad -->
                            <div class="input-field col s3">
                                <select id="id_nacionalidad" name="id_nacionalidad" class="validate">
                                    <option value="" disabled selected>Elige tu país</option>
                                    @foreach($nacionalidades as $nacionalidad)
                                        <option value="{{ $nacionalidad->id_nacionalidad }}">{{ $nacionalidad->nombre }}</option>
                                    @endforeach
                                </select>
                                <label>Nacionalidad</label>
                            </div>
                        </div>

                        <!-- Tercera fila -->
                        <div class="row">
                            <!-- Password -->
                            <div class="input-field col s6">
                                <i class="material-icons prefix">lock</i>
                                <input id="password_signup" name="password" type="password" class="validate" autocomplete="new-password">
                                <label for="password_signup">Contraseña</label>
                            </div>
                            <div class="input-field col s6">
                                <i class="material-icons prefix">lock_outline</i>
                                <input id="password_confirm" name="password_confirmation" type="password" class="validate" autocomplete="new-password">
                                <label for="password_confirm">Repetir Contraseña</label>
                            </div>
                        </div>

                        <!-- Cuarta fila -->
                        <div class="row">
                            <!-- Imagen -->
                            <div class="input-field col s12">
                                <div class="image-upload-container" style="display: flex; flex-direction: column; gap: 10px;">
                                    <div style="display: flex; gap: 10px;">
                                        <button type="button" class="btn purple" id="uploadBtn">
                                            <i class="material-icons left">upload_file</i>
                                            Subir foto
                                        </button>
                                        <button type="button" class="btn purple" id="cameraBtn">
                                            <i class="material-icons left">camera_alt</i>
                                            Tomar foto
                                        </button>
                                    </div>
                                    <input type="file" id="imageInput" name="img" accept="image/jpeg,image/png" style="display: none;">
                                    <div id="preview-container" style="margin-top: 10px; text-align: center;">
                                        <img id="preview-img" src="" style="max-width: 120px; display: none; border-radius: 10px; border: 2px solid #703ea3;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de cámara -->
                        <div id="cameraModal" class="modal">
                            <div class="modal-content">
                                <h4 style="color: #703ea3; text-align: center;">Tomar foto</h4>
                                <div class="camera-container" style="display: flex; flex-direction: column; align-items: center;">
                                    <video id="video" autoplay playsinline style="width: 100%; max-width: 400px; border-radius: 10px;"></video>
                                    <div class="camera-controls" style="margin-top: 20px; display: flex; gap: 10px;">
                                        <button type="button" id="captureBtn" class="btn purple">
                                            <i class="material-icons left">camera_alt</i>
                                            Capturar
                                        </button>
                                        <button type="button" id="closeCamera" class="btn grey">
                                            <i class="material-icons left">close</i>
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quinta fila -->
                        <div class="row">
                            <!-- Descripción -->
                            <div class="input-field col s12">
                                <i class="material-icons prefix">edit</i>
                                <textarea id="descripcion" name="descripcion" class="materialize-textarea"></textarea>
                                <label for="descripcion">Descripción (opcional)</label>
                            </div>
                        </div>

                        <!-- Botón de Submit -->
                        <div class="row">
                            <div class="col s12 center">
                                <button class="btn btn-custom waves-effect" type="submit">Registrarse</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.waves.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="{{ asset('js/validations.js') }}"></script>
    <script>
    window.sweetAlertError = @json(session('error'));
    window.sweetAlertLoginError = @json(session('login_error'));
    </script>
</body>
</html>
