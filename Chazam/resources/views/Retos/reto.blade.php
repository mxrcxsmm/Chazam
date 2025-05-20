@include('layout.chatsHeader')

<meta name="user-id" content="{{ Auth::user()->id_usuario }}">

<!-- Sidebar del reto-->
<div id="sidebar2" class="bg-purple text-white p-4">
    <div class="reto-header mb-3">
        <h5 class="text-center mb-0">Reto del Día</h5>
    </div>
    <div class="reto-content">
        <h2 class="titulo_reto h5 mb-3">{{ $reto->nom_reto }}</h2>
        <p class="desc_reto mb-4">{{ $reto->desc_reto }}</p>
        
        <!-- Botón de instrucciones -->
        <div class="text-center mb-3">
            <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#instruccionesModal">
                <i class="fas fa-info-circle me-1"></i> Instrucciones
            </button>
        </div>
        
        <!-- Contador de puntos diarios -->
        <div class="puntos-diarios-container mb-4 text-center">
            <span class="text-white small">Puntos del día:</span>
            <div class="puntos-diarios fw-bold fs-5">
                <span id="puntos-diarios-actuales">0</span>/300
            </div>
        </div>

        <button type="submit" class="btn btn-warning w-100 rounded-pill skip-btn">
            Skip
            <span class="triangle"></span>
            <span class="triangle tight"></span>
        </button>
    </div>
</div>

<!-- Modal de Instrucciones -->
<div class="modal fade" id="instruccionesModal" tabindex="-1" aria-labelledby="instruccionesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-purple text-white">
                <h5 class="modal-title" id="instruccionesModalLabel">Instrucciones del Reto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($reto->id_reto == 1)
                    <h5 class="text-center mb-3">Reto de Emojis</h5>
                    <div class="text-center mb-3">
                        <img src="{{ asset('img/instrucciones_reto/reto1.gif') }}" alt="Instrucciones Reto de Emojis" class="img-fluid rounded" style="width: 100%; height: 200px; object-fit: contain;">
                    </div>
                    <p>En este reto:</p>
                    <ul>
                        <li>Utiliza emojis en tus mensajes</li>
                        <li>Ganarás puntos sólo por mensajes que contengan emojis</li>
                        <li>Intenta mantener una conversación divertida usando emojis creativamente</li>
                    </ul>
                    <p class="mt-3 text-center"><strong>¡Diviértete expresándote con emojis!</strong></p>
                @elseif($reto->id_reto == 2)
                    <h5 class="text-center mb-3">Reto de Texto Encriptado</h5>
                    <div class="text-center mb-3">
                        <img src="{{ asset('img/instrucciones_reto/reto2.gif') }}" alt="Instrucciones Reto de Texto Encriptado" class="img-fluid rounded" style="width: 100%; height: 200px; object-fit: contain;">
                    </div>
                    <p>En este reto:</p>
                    <ul>
                        <li>Tus mensajes se enviarán parcialmente encriptados</li>
                        <li>Intenta mantener una conversación coherente a pesar de los caracteres ocultos</li>
                        <li>Ganarás puntos por comunicarte a través de mensajes con caracteres ocultos</li>
                    </ul>
                    <p class="mt-3 text-center"><strong>¡Comunícate a través del misterio!</strong></p>
                @elseif($reto->id_reto == 3)
                    <h5 class="text-center mb-3">Reto de Palabras Desordenadas</h5>
                    <div class="text-center mb-3">
                        <img src="{{ asset('img/instrucciones_reto/reto3.gif') }}" alt="Instrucciones Reto de Palabras Desordenadas" class="img-fluid rounded" style="width: 100%; height: 200px; object-fit: contain;">
                    </div>
                    <p>En este reto:</p>
                    <ul>
                        <li>Tus mensajes se enviarán con las palabras en orden aleatorio</li>
                        <li>Intenta entender lo que tu compañero quiere decir a pesar del desorden</li>
                        <li>Solo ganarás puntos con mensajes que tengan al menos 60 caracteres</li>
                        <li>El límite máximo es de 500 caracteres por mensaje</li>
                    </ul>
                    <p class="mt-3 text-center"><strong>¡Descifra el desorden y comunícate!</strong></p>
                @elseif($reto->id_reto == 4)
                    <h5 class="text-center mb-3">Reto de Texto Invertido</h5>
                    <div class="text-center mb-3">
                        <img src="{{ asset('img/instrucciones_reto/reto4.gif') }}" alt="Instrucciones Reto de Texto Invertido" class="img-fluid rounded" style="width: 100%; height: 200px; object-fit: contain;">
                    </div>
                    <p>En este reto:</p>
                    <ul>
                        <li>Tus mensajes se enviarán con el texto invertido (boca abajo)</li>
                        <li>Intenta leer y entender los mensajes invertidos de tu compañero</li>
                        <li>¡Buena suerte!</li>
                    </ul>
                    <p class="mt-3 text-center"><strong>¡Da la vuelta a tu forma de comunicarte!</strong></p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Chat Container -->
<div class="reto-chat-container">
    <div class="reto-chat-main h-100">
        <div class="reto-chat-header bg-purple text-white p-3">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div>
                    <h5 class="mb-0" id="chatHeader">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            Buscando usuarios disponibles...
                        </div>
                    </h5>
                </div>
                <div class="dropdown ms-auto" id="chatOptions" style="display: none;">
                    <button class="btn btn-link text-white p-0" type="button" id="chatOptionsButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatOptionsButton">
                        <li><a class="dropdown-item" href="#" id="sendFriendRequest">
                            <i class="fas fa-user-plus me-2"></i>Enviar solicitud de amistad
                        </a></li>
                        <li><a class="dropdown-item" href="#" id="reportUser">
                            <i class="fas fa-flag me-2"></i>Reportar usuario
                        </a></li>
                        <li><a class="dropdown-item" href="#" id="blockUser">
                            <i class="fas fa-ban me-2"></i>Bloquear usuario
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="reto-messages-container p-3" id="mensajesContainer">
            <!-- Los mensajes irán aquí -->
        </div>

        <div class="reto-message-input p-3 bg-light">
            <div class="input-group">
                @if($reto->id_reto == 1)
                <button class="btn btn-outline-secondary" type="button" id="emojiButton">
                    <i class="far fa-smile"></i>
                </button>
                @else
                <button class="btn btn-outline-secondary" type="button" id="emojiButtonDisabled" title="Los emojis no están disponibles en este reto">
                    <i class="far fa-smile"></i>
                </button>
                @endif
                <input type="text" class="form-control" id="mensajeInput" placeholder="Escribe un mensaje aquí">
                <button class="btn btn-primary" type="button" id="enviarMensaje">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Añadir Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Añadir SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Añadir el selector de emojis solo para el reto 1 -->
@if($reto->id_reto == 1)
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
<emoji-picker style="position: absolute; bottom: 60px; left: 20px; display: none;"></emoji-picker>
@endif

<!-- Incluir los archivos JavaScript -->
<script>
    window.userId = {{ Auth::user()->id_usuario }};
    window.retoId = {{ $reto->id_reto }};
    console.log('=== INICIO DE LA VISTA RETO ===');
    console.log('User ID:', window.userId);
    console.log('Reto ID:', window.retoId);

    @if($reto->id_reto == 1)
    // Configuración del selector de emojis solo para el reto 1
    const emojiButton = document.getElementById('emojiButton');
    const emojiPicker = document.querySelector('emoji-picker');

    emojiButton.addEventListener('click', () => {
        emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
    });

    document.querySelector('emoji-picker')
        .addEventListener('emoji-click', event => {
            const emoji = event.detail.unicode;
            const input = document.getElementById('mensajeInput');
            input.value += emoji;
        });
    @elseif($reto->id_reto == 3)
    // Para el reto 3, agregar contador de caracteres
    const mensajeInput = document.getElementById('mensajeInput');
    
    // Crear elemento contador
    const contadorContainer = document.createElement('div');
    contadorContainer.id = 'contador-caracteres';
    contadorContainer.style.fontSize = '12px';
    contadorContainer.style.color = '#6c757d';
    contadorContainer.style.marginTop = '5px';
    contadorContainer.style.textAlign = 'right';
    document.querySelector('.input-group').insertAdjacentElement('afterend', contadorContainer);
    
    // Actualizar contador al escribir
    mensajeInput.addEventListener('input', function() {
        const longitud = this.value.trim().length;
        const caracteresRestantes = 500 - longitud;
        let textoContador = `${longitud}/500 caracteres`;
        
        // Si es menor a 60 caracteres, mostrar cuántos faltan para puntuar
        if (longitud < 60) {
            const caracteresParaPuntos = 60 - longitud;
            textoContador += ` (Faltan ${caracteresParaPuntos} para ganar puntos)`;
            contadorContainer.style.color = '#dc3545';
        } else {
            contadorContainer.style.color = '#28a745';
            textoContador += ' (¡Suficiente para puntuar!)';
        }
        
        if (caracteresRestantes < 50) {
            contadorContainer.style.color = '#dc3545';
        }
        
        contadorContainer.textContent = textoContador;
    });
    
    // Inicializar el contador
    mensajeInput.dispatchEvent(new Event('input'));
    @else
    // Mostrar mensaje cuando intentan usar emojis en otros retos
    const emojiButtonDisabled = document.getElementById('emojiButtonDisabled');
    emojiButtonDisabled.addEventListener('click', () => {
        Swal.fire({
            title: 'No disponible',
            text: 'Los emojis solo están disponibles en los retos de emojis 😊',
            icon: 'info',
            confirmButtonText: 'Entendido'
        });
    });
    @endif

    // Agregar un contador simple de caracteres para todos los retos
    @if($reto->id_reto != 3)
    const mensajeInput = document.getElementById('mensajeInput');
    
    // Crear elemento contador simple
    const contadorContainer = document.createElement('div');
    contadorContainer.id = 'contador-caracteres';
    contadorContainer.style.fontSize = '12px';
    contadorContainer.style.color = '#6c757d';
    contadorContainer.style.marginTop = '5px';
    contadorContainer.style.textAlign = 'right';
    document.querySelector('.input-group').insertAdjacentElement('afterend', contadorContainer);
    
    // Actualizar contador al escribir
    mensajeInput.addEventListener('input', function() {
        const longitud = this.value.trim().length;
        const caracteresRestantes = 500 - longitud;
        let textoContador = `${longitud}/500 caracteres`;
        
        if (caracteresRestantes < 50) {
            contadorContainer.style.color = '#dc3545';
        } else {
            contadorContainer.style.color = '#6c757d';
        }
        
        contadorContainer.textContent = textoContador;
    });
    
    // Inicializar el contador
    mensajeInput.dispatchEvent(new Event('input'));
    @endif
</script>
<script src="{{ asset('js/estados.js') }}"></script>
<script src="{{ asset('js/chatrandom.js') }}"></script>
<script src="{{ asset('js/reto' . $reto->id_reto . '.js') }}"></script>