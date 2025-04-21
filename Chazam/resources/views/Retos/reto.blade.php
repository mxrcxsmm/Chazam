@include('layout.chatsHeader')

<!-- Sidebar del reto-->
<div id="sidebar2" class="bg-purple text-white p-4">
    <div class="reto-header mb-3">
        <h5 class="text-center mb-0">Reto del Día</h5>
    </div>
    <div class="reto-content">
        <h2 class="titulo_reto h5 mb-3">{{ $reto->nom_reto }}</h2>
        <p class="desc_reto mb-4">{{ $reto->desc_reto }}</p>
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
                    <h5 class="mb-0">Chat del Reto</h5>
                </div>
            </div>
        </div>

        <div class="reto-messages-container p-3">
            <!-- Los mensajes irán aquí -->
        </div>

        <div class="reto-message-input p-3 bg-light">
            <div class="input-group">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="far fa-smile"></i>
                </button>
                <input type="text" class="form-control" placeholder="Escribe un mensaje aquí">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Añadir Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Incluir el archivo JavaScript para actualizar el tiempo en tiempo real -->
@push('scripts')
    <script src="{{ asset('js/Reto.js') }}"></script>
@endpush