@extends('layout.chatsHeader')

@section('title', 'Comunidades')

@section('content')
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="main-container">
    <!-- Sección de comunidades creadas -->
    <div class="section-title">
        <h2>Mis Comunidades</h2>
    </div>
    <div style="margin-bottom: 20px; text-align: right;">
        <a href="{{ route('comunidades.create') }}" class="create-btn" style="padding: 12px 20px;">Crear comunidad</a>
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
                        <p>{{ Str::limit($comunidad->descripcion, 120, '...') }}</p>
                        <div class="comunidad-meta">
                            <span style="font-size: 1.1em;">{{ $comunidad->chat_usuarios_count }} miembros</span><br>
                            @if($comunidad->tipocomunidad === 'privada')
                                <span style="font-size: 1.1em;">Código: <span class="codigo-privado" onclick="toggleCodigo(this, '{{ $comunidad->codigo }}')" style="cursor: pointer;">••••••••••</span></span>
                            @endif
                        </div>
                    </div>
                    <div class="comunidad-actions">
                        <button class="join-btn" data-id="{{ $comunidad->id }}">Gestionar</button>
                        <button class="join-btn" style="margin-top: 10px; background-color: #43b581; width: 100%;" data-id="{{ $comunidad->id }}">Entrar</button>
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
                        <p>{{ Str::limit($comunidad->descripcion, 120, '...') }}</p>
                        <div class="comunidad-meta">
                            <span style="font-size: 1.1em;">{{ $comunidad->chat_usuarios_count }} miembros</span> <br>
                            <span style="font-size: 1.1em;">Creado por: {{ $comunidad->creador->username }}</span>
                        </div>
                    </div>
                    <div class="comunidad-actions">
                        <button class="join-btn" data-id="{{ $comunidad->id }}">Unirse</button>
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
                        <p>{{ Str::limit($comunidad->descripcion, 120, '...') }}</p>
                        <div class="comunidad-meta">
                            <span style="font-size: 1.1em;">{{ $comunidad->chat_usuarios_count }} miembros</span> <br>
                            <span style="font-size: 1.1em;">Creado por: {{ $comunidad->creador->username }}</span>
                        </div>
                    </div>
                    <div class="comunidad-actions">
                        <button class="join-btn" data-id="{{ $comunidad->id }}">Unirse</button>
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
    // Mostrar SweetAlert si hay mensaje de éxito
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#9147ff'
        });
    @endif

    function toggleCodigo(element, codigo) {
        if (element.textContent === '••••••••••') {
            element.textContent = codigo;
        } else {
            element.textContent = '••••••••••';
        }
    }

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
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Te has unido a la comunidad exitosamente',
                    confirmButtonColor: '#9147ff'
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al unirse a la comunidad',
                confirmButtonColor: '#9147ff'
            });
        });
    }
</script>
@endsection
