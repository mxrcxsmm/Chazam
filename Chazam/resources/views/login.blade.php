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
</head>
<body>
    <div id="vanta-bg"></div>
    <div class="login-container">
        <img src="{{ asset('img/logo.png') }}" alt="" style="" class="logo">

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
                        <input id="email" name="email" type="email" class="validate" required autocomplete="email">
                        <label for="email">Email</label>
                    </div>

                    <div class="input-field">
                        <input id="password" name="password" type="password" class="validate" required autocomplete="current-password">
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
                        <div class="file-field input-field col s12">
                            <div class="btn btn-small purple">
                                <span>Foto</span>
                                <input type="file" name="img" accept="image/jpeg,image/png">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text">
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

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.waves.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="{{ asset('js/validations.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar mensaje de error si existe
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '¡Cuenta baneada!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#703ea3'
                });
            @endif

            const loginContainer = document.querySelector('.login-container');
            
            // Función para manejar el cambio de ancho
            function handleWidthChange() {
                const activeTab = document.querySelector('.tabs .tab a.active');
                if (window.innerWidth > 600) {
                    if (activeTab && activeTab.getAttribute('href') === '#signup') {
                        loginContainer.classList.add('wider');
                    } else {
                        loginContainer.classList.remove('wider');
                    }
                } else {
                    loginContainer.classList.remove('wider');
                }
            }

            // Inicializar tabs de Materialize
            const materializeTabs = M.Tabs.init(document.querySelectorAll('.tabs'), {
                onShow: function(tab) {
                    handleWidthChange();
                    
                    // Reiniciar componentes
                    setTimeout(() => {
                        M.updateTextFields();
                        M.FormSelect.init(document.querySelectorAll('select'));
                    }, 50);
                }
            });

            // Manejar cambios de tamaño de ventana
            window.addEventListener('resize', handleWidthChange);

            // Aplicar el ancho inicial
            handleWidthChange();

            // Inicializar componentes
            initDatepicker();
            M.FormSelect.init(document.querySelectorAll('select'));
            M.CharacterCounter.init(document.querySelectorAll('textarea'));

            // Inicializar validaciones
            validateSignUpForm();

            // Inicializar Vanta.js
            VANTA.WAVES({
                el: "#vanta-bg",
                color: 0x703ea3,
                backgroundColor: 0xaa00ff
            });
        });
    </script>
</body>
</html>
