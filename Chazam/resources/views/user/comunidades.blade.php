<div class="main-container">
    <div class="comunidades-list" id="comunidades-list">
         @foreach($comunidades as $comunidad)
            <div class="comunidad-item">
                <div class="comunidad-info">
                    <h3>{{ $comunidad->nombre }}</h3>
                    <p>{{ $comunidad->descripcion }}</p>
                    <button class="join-btn" data-id="{{ $comunidad->id_comunidad }}">Unirse</button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Añadir el JavaScript personalizado -->
<script src="{{ asset('js/comunidades.js') }}"></script>

<!-- Añadir el CSS personalizado -->
<link rel="stylesheet" href="{{ asset('css/comunidades.css') }}">