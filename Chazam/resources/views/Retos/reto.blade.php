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
        
        <!-- Contador de puntos diarios -->
        <div class="puntos-diarios-container mb-4 text-center">
            <span class="text-muted small">Puntos del día:</span>
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

<!-- Chat Container -->
<div class="reto-chat-container">
    <div class="reto-chat-main h-100">
        <div class="reto-chat-header bg-purple text-white p-3">
            <div class="d-flex align-items-center">
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
</script>
<script src="{{ asset('js/estados.js') }}"></script>
<script src="{{ asset('js/chatrandom.js') }}"></script>
<script src="{{ asset('js/reto' . $reto->id_reto . '.js') }}"></script>