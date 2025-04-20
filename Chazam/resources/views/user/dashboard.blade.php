<!-- filepath: c:\wamp64\www\DAW2\MP12\Chazam\Chazam\resources\views\user\dashboard.blade.php -->
@extends('layout.chatsHeader')

@section('title', 'Dashboard Usuario')

@section('content')
    <div class="container">
        <h1>Bienvenido, {{ isset($username) ? $username : 'Usuario' }}</h1>
        <p>Esta es la página principal para usuarios normales.</p>
        
        <!-- Aquí puedes agregar más contenido para el dashboard -->
    </div>
@endsection