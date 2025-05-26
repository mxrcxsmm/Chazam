@extends('layout.user')

@section('content')
<div class="perfil-container">
    <h1 class="titulo">Personalizar</h1>

    @php
        $claseMarco = 'marco-glow'; // Glow activo en pruebas
        $presets = [
            '#FFD700' => 'Dorado',
            '#FF0000' => 'Rojo Demonio',
            '#00FFFF' => 'Cian Eléctrico',
            '#9400D3' => 'Violeta Oscuro',
            '#00FF00' => 'Verde Alien',
            '#FF69B4' => 'Rosa Neon',
            '#00BFFF' => 'Azul Intenso'
        ];
    @endphp

    <div class="marco-externo {{ $claseMarco }}" style="
        --glow-color: {{ $user->borde_glow_color ?? '#FFD700' }};
        background-image: url('{{ asset('img/bordes/' . ($user->borde_overlay ?? 'default.png')) }}');
        {{--background-image: url('{{ $user->borde_overlay ? asset("img/bordes/{$user->borde_overlay}") : 'none' }}');--}}
    ">
    {{--<div class="marco-externo {{ $claseMarco }} marco-{{ $marcoNombre }}">--}}
        <img src="{{ asset($user->img) }}" class="avatar-img">
    </div>

    {{-- Selección de marco --}}
    <form method="POST" action="{{ route('perfil.cambiarMarco') }}" id="marcoForm" class="mt-4">
        @csrf
        <h5>Elige tu marco:</h5>
        <div class="d-flex flex-wrap gap-3">
            @php
                $marcos = [
                    'default.svg' => 'Clásico', /*eliminar cuando quiera*/
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
                <div class="marco-option {{ $user->borde_overlay === $file ? 'selected' : '' }}" data-marco="{{ $file }}">
                    <div class="marco-thumb">
                        <img src="{{ asset('img/bordes/' . $file) }}" alt="{{ $nombre }}">
                    </div>
                </div>
            @endforeach
        </div>

        <input type="hidden" name="borde_overlay" id="borde_overlay" value="{{ $user->borde_overlay ?? 'default.svg' }}">
        <button type="submit" class="btn btn-outline-secondary mt-4">Aplicar</button>
        
        <h5 class="mt-4">Animación del marco<h5>
        <div class="radio-group">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rotativo_temp" id="rotativo_temp_no" value="0" checked>
                <label class="form-check-label" for="rotativo_temp_no">Estático</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="rotativo_temp" id="rotativo_temp_si" value="1">
                <label class="form-check-label" for="rotativo_temp_si">Rotatorio</label>
            </div>
        </div>
    </form>

    {{-- Selector de color dinámico --}}
    <div class="mt-5">
        <h5>Color del brillo en tiempo real</h5>

        <div class="d-flex align-items-center gap-3 mb-3">
            <input type="color" id="glowColorPicker" value="{{ $user->borde_glow_color ?? '#FFD700' }}"
                style="width: 80px; height: 40px; border: none; cursor: pointer;">
            <span id="colorValueLabel" style="font-weight: bold;">{{ $user->borde_glow_color ?? '#FFD700' }}</span>
        </div>

        <form method="POST" action="{{ route('perfil.cambiarBrillo') }}">
            @csrf
            <input type="hidden" name="glow_color" id="glow_color" value="{{ $user->borde_glow_color ?? '#FFD700' }}">
            <button type="submit" class="btn btn-outline-secondary">Guardar color</button>
        </form>
    </div>

    <div class="mt-5">
        <h5>Color del fondo del menú lateral</h5>
        <div class="d-flex align-items-center gap-3 mb-3">
            <input type="color" id="sidebarColorPicker"
                   value="#4B0082"
                   style="width: 80px; height: 40px; border: none; cursor: pointer;">
            <span id="sidebarColorValueLabel" style="font-weight: bold;">#4B0082</span>
        </div>
    </div>    
</div>

@push('scripts')
<script src="{{ asset('js/vista.js') }}"></script>
@endpush
@endsection
