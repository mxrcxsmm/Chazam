<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@include('layout.chatsHeader')

<div class="center-container">
    <img src="{{ asset('img/Logo_Chazam.png') }}" alt="Logo" class="logo2">
    <p class="startingtext">¿Preparado para conocer a gente nueva?</p>
    <div class="button-container">
        <a href="{{ route('retos.reto') }}"><button class="startbutton">Comenzar</button></a>
        <a href="{{ route('user.friendchat') }}"><button class="startbutton">Amigos</button></a>
        <a href="{{ route('comunidades.index') }}"><button class="startbutton">Comunidades</button></a>
    </div>
</div>

<style>
.button-container {
    display: flex;
    gap: 20px;
    justify-content: center;
}

.startbutton {
    background-color: #9147ff;
    color: white;
    border: none;
    padding: 10px 30px;
    border-radius: 25px;
    font-size: 1.2em;
    cursor: pointer;
    transition: all 0.3s ease;
}

.startbutton:hover {
    background-color: #7a30dd;
    transform: scale(1.05);
}
</style>

<script src="{{ asset('js/hamburger.js') }}"></script>

