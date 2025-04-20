@include('layout.chatsHeader')

<!-- Sidebar del reto-->
<div id="sidebar2" class="position-fixed bottom-0 end-0 bg-purple text-white p-4" style="width: 260px; height: 92.5vh; z-index: 1040;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5>Reto del Día</h5> <br><br>
    </div>
    <ul class="list-unstyled">
        <li class="titulo_reto">{{ $reto->nom_reto }}</li>
        <li class="desc_reto">Descripción del reto: {{ $reto->desc_reto }}</li>

        <button type="submit" class="btn btn-warning w-100 rounded-pill skip-btn mt-3">
            Skip
            <span class="triangle"></span>
            <span class="triangle tight"></span>
        </button>          
    </ul>
</div>

<!-- Incluir el archivo JavaScript para actualizar el tiempo en tiempo real -->
@push('scripts')
    <script src="{{ asset('js/Reto.js') }}"></script>
@endpush
