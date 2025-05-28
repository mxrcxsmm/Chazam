@extends('layout.user')

@section('content')
<div class="perfil-container">
    <h1 class="titulo">Personalizar</h1>

    <div id="previewAvatar" class="marco-externo marco-glow"
    style="
        background-image: url('{{ asset('img/bordes/' . ($personalizacion->marco ?? 'default.svg')) }}');
        @if($personalizacion->brillo)
            --glow-color: {{ $personalizacion->brillo }};
        @endif
    ">
    <img src="{{ asset($user->imagen_perfil) }}" class="avatar-img">
    </div>

    <form id="personalizacionForm" action="{{ route('perfil.vista.actualizar') }}" method="POST">
        @csrf
        @method('PUT')

        <div id="marcoForm">
            <h5>Elige tu marco:</h5>
            <div class="d-flex flex-wrap gap-3">
                @php
                    $marcos = [
                        'default.svg' => 'Clásico',
                        'azuul.svg' => 'Azul Suave',
                        'azul-champions.svg' => 'Azul Champions',
                        'circle-rojonegro.svg' => 'Rojinegro',
                        'cromado.svg' => 'Cromado',
                        'cromado-normal.svg' => 'Cromado Normal',
                        'cromado-peque.svg' => 'Cromado Pequeño',
                        'cutre-estrellas.svg' => 'Estrellas Cutres',
                        'golden-champions.svg' => 'Golden Champions',
                        'wave-haikei.svg' => 'Wave Haikei',
                        'estrellas-haikei.svg' => 'Estrellas Haikei'
                    ];
                @endphp

                @foreach ($marcos as $file => $nombre)
                    <div class="marco-option {{ $personalizacion->marco === $file ? 'selected' : '' }}" data-marco="{{ $file }}">
                        <div class="marco-thumb">
                            <img src="{{ asset('img/bordes/' . $file) }}" alt="{{ $nombre }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <input type="hidden" name="borde_overlay" id="borde_overlay" value="{{ $personalizacion->marco ?? 'default.svg' }}">

            <h5 class="mt-4">Animación del marco</h5>
            <div class="radio-group">
                <label class="form-check form-check-label">
                    <input class="form-check-input" type="radio" name="rotacion" value="0" {{ !$personalizacion->rotacion ? 'checked' : '' }}>
                    Estático
                </label>
                <label class="form-check form-check-label">
                    <input class="form-check-input" type="radio" name="rotacion" value="1" {{ $personalizacion->rotacion ? 'checked' : '' }}>
                    Rotatorio
                </label>
            </div>
        </div>

        <div class="mt-5">
            <h5>Color del brillo en tiempo real</h5>
            <div class="d-flex align-items-center gap-3 mb-3">
                <input type="color" id="glowColorPicker" value="{{ $personalizacion->brillo ?? '#FFD700' }}"
                    style="width: 80px; height: 40px; border: none; cursor: pointer;">
                <span id="colorValueLabel" style="font-weight: bold;">{{ $personalizacion->brillo ?? '#FFD700' }}</span>
            </div>
            <input type="hidden" name="glow_color" id="glow_color" value="{{ $personalizacion->brillo ?? '#FFD700' }}" disabled>
        </div>

        <div class="mt-5">
            <h5>Color del fondo del menú lateral</h5>
            <div class="d-flex align-items-center gap-3 mb-3">
                <input type="color" id="sidebarColorPicker"
                       value="{{ $personalizacion->sidebar ?? '#4B0082' }}"
                       style="width: 80px; height: 40px; border: none; cursor: pointer;">
                <span id="sidebarColorValueLabel" style="font-weight: bold;">{{ $personalizacion->sidebar ?? '#4B0082' }}</span>
            </div>
        </div>

        <button type="button" id="guardarCambios" class="btn btn-outline-secondary">Guardar Cambios</button>
        <button type="button" id="restablecerBtn" class="btn btn-outline-secondary">Restablecer Personalización</button>
    </form>

    <form id="restablecerForm" action="{{ route('perfil.vista.restablecer') }}" method="POST" style="display:none;">
        @csrf
        @method('PUT')
    </form>
    
</div>

@push('scripts')
    <script src="{{ asset('js/vista.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection
