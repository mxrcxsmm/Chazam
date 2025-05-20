@extends('layout.chatsHeader')

@section('title', 'Comunidades')

@section('content')
<div class="main-container">
    <!-- Sección de comunidades creadas -->
    <div class="section-title">
        <h2>Mis Comunidades</h2>
    </div>
    <div class="comunidades-list" id="comunidades-creadas">
        @if($comunidadesCreadas->isEmpty())
            <div class="no-comunidades">
                <p>No has creado ninguna comunidad aún</p>
            </div>
        @else
            @foreach($comunidadesCreadas as $comunidad)
                <div class="comunidad-item">
                    <div class="comunidad-imagen">
                        <img src="{{ asset('img/comunidades/' . $comunidad->img) }}" alt="{{ $comunidad->nombre }}">
                    </div>
                    <div class="comunidad-info">
                        <h3>{{ $comunidad->nombre }}</h3>
                        <p>{{ $comunidad->descripcion }}</p>
                        <div class="comunidad-meta">
                            <span>{{ $comunidad->chat_usuarios_count }} miembros</span>
                            <span>Código: {{ $comunidad->codigo }}</span>
                        </div>
                        <div class="comunidad-actions">
                            <button class="join-btn" data-id="{{ $comunidad->id }}">Gestionar</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Sección de comunidades públicas -->
    <div class="section-title">
        <h2>Comunidades Públicas</h2>
    </div>
    <div class="comunidades-list" id="comunidades-publicas">
        @if($comunidadesPublicas->isEmpty())
            <div class="no-comunidades">
                <p>No hay comunidades públicas disponibles</p>
            </div>
        @else
            @foreach($comunidadesPublicas as $comunidad)
                <div class="comunidad-item">
                    <div class="comunidad-imagen">
                        <img src="{{ asset('img/comunidades/' . $comunidad->img) }}" alt="{{ $comunidad->nombre }}">
                    </div>
                    <div class="comunidad-info">
                        <h3>{{ $comunidad->nombre }}</h3>
                        <p>{{ $comunidad->descripcion }}</p>
                        <div class="comunidad-meta">
                            <span>{{ $comunidad->chat_usuarios_count }} miembros</span> <br>
                            <span>Creado por: {{ $comunidad->creador->username }}</span>
                        </div>
                        <div class="comunidad-actions">
                            <button class="join-btn" data-id="{{ $comunidad->id }}">Unirse</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Sección de comunidades privadas -->
    <div class="section-title">
        <h2>Comunidades Privadas</h2>
    </div>
    <div class="comunidades-list" id="comunidades-privadas">
        @if($comunidadesPrivadas->isEmpty())
            <div class="no-comunidades">
                <p>No hay comunidades privadas disponibles</p>
            </div>
        @else
            @foreach($comunidadesPrivadas as $comunidad)
                <div class="comunidad-item">
                    <div class="comunidad-imagen">
                        <img src="{{ asset('img/comunidades/' . $comunidad->img) }}" alt="{{ $comunidad->nombre }}">
                    </div>
                    <div class="comunidad-info">
                        <h3>{{ $comunidad->nombre }}</h3>
                        <p>{{ $comunidad->descripcion }}</p>
                        <div class="comunidad-meta">
                            <span>{{ $comunidad->chat_usuarios_count }} miembros</span>
                            <span>Creado por: {{ $comunidad->creador->username }}</span>
                        </div>
                        <div class="comunidad-actions">
                            <button class="join-btn" data-id="{{ $comunidad->id }}">Unirse</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

<!-- Añadir el JavaScript personalizado -->
<script src="{{ asset('js/comunidades.js') }}"></script>

<!-- Añadir el CSS personalizado -->
<link rel="stylesheet" href="{{ asset('css/comunidades.css') }}">

<script>
function unirseComunidad(id) {
    fetch(`/comunidades/${id}/join`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Te has unido a la comunidad exitosamente');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al unirse a la comunidad');
    });
}
</script>
@endsection
