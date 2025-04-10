<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- Material Icons (opcional) -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    
    <div id="vanta-bg"></div>
    <div class="login-container z-depth-2">
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
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="input-field">
                        <input id="email" name="email" type="email" class="validate" required>
                        <label for="email">Email</label>
                    </div>

                    <div class="input-field">
                        <input id="password" name="password" type="password" class="validate" required>
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
            <!-- Sign Up -->
            <div id="signup" class="col s12">
                <form action="{{ route('login') }}" method="POST" class="form-signup">
                    @csrf
                    <!-- Primera columna -->
                    <div class="row">
                        <!-- Username -->
                        <div class="input-field col s12">
                            <i class="material-icons prefix">person_outline</i>
                            <input id="username" name="username" type="text" class="validate" required>
                            <label for="username">Username</label>
                        </div>
                        
                        <!-- Nombre y Apellido -->
                        <div class="input-field col s6">
                            <i class="material-icons prefix">badge</i>
                            <input id="nombre" name="nombre" type="text" required>
                            <label for="nombre">Nombre</label>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix">people</i>
                            <input id="apellido" name="apellido" type="text" required>
                            <label for="apellido">Apellido</label>
                        </div>
                        
                        <!-- Fecha de Nacimiento (Datepicker) -->
                        <div class="input-field col s12">
                            <i class="material-icons prefix">event</i>
                            <input id="fecha_nacimiento" name="fecha_nacimiento" type="text" class="datepicker" required>
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        </div>
                    </div>

                    <!-- Segunda columna (Scroll) -->
                    <div class="signup-scroll" style="max-height: 300px; overflow-y: auto;">
                        <!-- Email -->
                        <div class="input-field col s12">
                            <i class="material-icons prefix">email</i>
                            <input id="email_signup" name="email" type="email" required>
                            <label for="email_signup">Email</label>
                        </div>
                        
                        <!-- Password -->
                        <div class="input-field col s12">
                            <i class="material-icons prefix">lock</i>
                            <input id="password_signup" name="password" type="password" required>
                            <label for="password_signup">Password</label>
                        </div>
                        
                        <!-- Nacionalidad (Select) -->
                        <div class="input-field col s12">
                            <i class="material-icons prefix">public</i>
                            <select id="id_nacionalidad" name="id_nacionalidad" required>
                                <option value="" disabled selected>Elige tu país</option>
                                {{-- @foreach($paises as $pais)
                                    <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                                @endforeach --}}
                            </select>
                            <label>Nacionalidad</label>
                        </div>
                        
                        <!-- Imagen (Opcional) -->
                        <div class="file-field input-field col s12">
                            <div class="btn btn-small purple">
                                <span>Foto</span>
                                <input type="file" name="img">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path" type="text">
                            </div>
                        </div>
                        
                        <!-- Descripción (Textarea) -->
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
    


<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanta/dist/vanta.waves.min.js"></script>
<script>

</script>
    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tabs
    var tabs = M.Tabs.init(document.querySelectorAll('.tabs'), {
        onShow: function(tab) {
            setTimeout(() => M.updateTextFields(), 50);
        }
    });

    // Datepicker
    var datepickers = document.querySelectorAll('.datepicker');
    M.Datepicker.init(datepickers, {
        format: 'yyyy-mm-dd',
        yearRange: [1900, new Date().getFullYear()],
        i18n: {
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"], // Personaliza si es necesario
            cancel: 'Cancelar'
        }
    });

    // Select
    var selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);

    // Textarea (auto-resize)
    var textareas = document.querySelectorAll('textarea');
    M.CharacterCounter.init(textareas);
});
    VANTA.WAVES({
    el: "#vanta-bg",
    color: 0x703ea3,
    backgroundColor: 0xaa00ff
  });
    </script>
    
</body>

</html>
