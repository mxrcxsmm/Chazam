@include('layout.chatsHeader')

<div class="center-container">
    <img src="{{ asset('img/Logo_Chazam.png') }}" alt="Logo" class="logo2">
    <p class="startingtext">¿Qué quieres hacer hoy?</p>
    <div class="button-container">
        <a href="{{ route('user.mis-comunidades') }}"><button class="startbutton">Mis Comunidades</button></a>
        <a href="{{ route('user.explorar-comunidades') }}"><button class="startbutton">Explorar</button></a>
    </div>
</div>

<div class="comunidades-list" id="comunidades-list">
    @foreach($chats as $chat)
        <div class="comunidad-item">
            <div class="comunidad-info">
                <h3>{{ $chat->nombre }}</h3>
                <p>{{ $chat->descripcion }}</p>
                <button class="join-btn" data-id="{{ $chat->id_chat }}">Unirse</button>
            </div>
        </div>
    @endforeach
</div>

<link rel="stylesheet" href="{{ asset('css/comunidades.css') }}">

<script src="{{ asset('js/comunidades.js') }}"></script>

