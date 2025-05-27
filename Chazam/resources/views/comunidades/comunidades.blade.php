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
        @if($comunidadesCreadas->isEmpty() && $comunidadesUnidas->isEmpty())
            <div class="no-comunidades">
                <p>No perteneces a ninguna comunidad aún</p>
            </div>
        @else
            @foreach($comunidadesCreadas as $comunidad)
                <div class="comunidad-item">
                    <div class="comunidad-imagen">
                        <img src="{{ asset('img/comunidades/' . $comunidad->img) }}" alt="{{ $comunidad->nombre }}">
                    </div>
                    <div class="comunidad-info">
                        <h3>{{ $comunidad->nombre }}</h3>
                        <p>{{ Str::limit($comunidad->descripcion, 60, '...') }}</p>
                        <div class="comunidad-meta">
                            <span style="font-size: 1.1em;">{{ $comunidad->chat_usuarios_count }} miembros</span><br>
                            @if($comunidad->tipocomunidad === 'privada')
                                <span style="font-size: 1.1em;">Código: <span class="codigo-privado" onclick="toggleCodigo(this, '{{ $comunidad->codigo }}')" style="cursor: pointer;">••••••••••</span></span>
                            @endif
                        </div>
                    </div>
                    <div class="comunidad-actions">
                        <a href="{{ route('comunidades.edit', ['id' => $comunidad->id_chat]) }}" class="join-btn">
                            {{ $comunidad->creator == Auth::id() ? 'Editar' : 'Detalles' }}
                        </a>
                        <a href="{{ route('comunidades.show', ['id' => $comunidad->id_chat]) }}" class="join-btn join-btn-success">Entrar</a>
                    </div>
                </div>
            @endforeach

            @foreach($comunidadesUnidas as $comunidad)
                <div class="comunidad-item">
                    <div class="comunidad-imagen">
                        <img src="{{ asset('img/comunidades/' . $comunidad->img) }}" alt="{{ $comunidad->nombre }}">
                    </div>
                    <div class="comunidad-info">
                        <h3>{{ $comunidad->nombre }}</h3>
                        <p>{{ Str::limit($comunidad->descripcion, 60, '...') }}</p>
                        <div class="comunidad-meta">
                            <span style="font-size: 1.1em;">{{ $comunidad->chat_usuarios_count }} miembros</span><br>
                            <span style="font-size: 1.1em;">Creado por: {{ $comunidad->creador->username }}</span>
                        </div>
                    </div>
                    <div class="comunidad-actions">
                        <a href="{{ route('comunidades.edit', ['id' => $comunidad->id_chat]) }}" class="join-btn">Detalles</a>
                        <a href="{{ route('comunidades.show', ['id' => $comunidad->id_chat]) }}" class="join-btn join-btn-success">Entrar</a>
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
                        <p>{{ Str::limit($comunidad->descripcion, 60, '...') }}</p>
                        <div class="comunidad-meta">
                            <span style="font-size: 1.1em;">{{ $comunidad->chat_usuarios_count }} miembros</span> <br>
                            <span style="font-size: 1.1em;">Creado por: {{ $comunidad->creador->username }}</span>
                        </div>
                    </div>
                    <div class="comunidad-actions">
                        <button class="join-btn" onclick="unirseComunidad({{ $comunidad->id_chat }})">Unirse</button>
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
                        <p>{{ Str::limit($comunidad->descripcion, 60, '...') }}</p>
                        <div class="comunidad-meta">
                            <span style="font-size: 1.1em;">{{ $comunidad->chat_usuarios_count }} miembros</span> <br>
                            <span style="font-size: 1.1em;">Creado por: {{ $comunidad->creador->username }}</span>
                        </div>
                    </div>
                    <div class="comunidad-actions">
                        <button class="join-btn" onclick="unirseComunidad({{ $comunidad->id_chat }}, true)">Unirse</button>
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

    function unirseComunidad(id, esPrivada = false) {
        if (esPrivada) {
            Swal.fire({
                title: 'Código de Acceso',
                text: 'Ingresa el código de la comunidad privada',
                input: 'text',
                inputPlaceholder: 'Código de acceso',
                showCancelButton: true,
                confirmButtonColor: '#9147ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Unirme',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debes ingresar el código';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/comunidades/${id}/join`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ codigo: result.value })
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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Código incorrecto',
                                confirmButtonColor: '#9147ff'
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
            });
        } else {
            Swal.fire({
                title: '¿Unirse a la comunidad?',
                text: '¿Estás seguro de que quieres unirte a esta comunidad?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9147ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, unirme',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Hubo un error al unirse a la comunidad',
                                confirmButtonColor: '#9147ff'
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
            });
        }
    }
</script>
@endsection
