<!-- personalizacion.blade.php -->
@extends('layout.user')

@section('content')
<div class="form-container">
    <h4 class="titulo">Mis datos</h4>
    <form action="{{ route('perfil.update') }}" method="POST" class="formulario">
        @csrf
        @method('PUT')

        <div>
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $user->nombre) }}">
        </div>
        <div>
            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $user->apellido) }}">
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
        </div>
        <div>
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($user->fecha_nacimiento)->format('Y-m-d')) }}">
        </div>

        <div class="full">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion', $user->descripcion) }}">
        </div>        

        <div class="full">
            <label for="img">Ruta de imagen (temporal)</label>
            <input type="text" id="img" name="img" value="{{ old('img', $user->img) }}">
        </div>

        <div class="full text-center">
            <label for="img">Imagen de perfil</label>
            
            @if($imagen_perfil)
                <div class="mb-3">
                    <img src="{{ asset($imagen_perfil) }}" alt="Foto de perfil" class="rounded-circle shadow" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #8750B2;">
                </div>
            @endif
        
            <input type="file" name="img" id="img" accept="image/*" class="form-control text-center">
        </div>        

        <div class="full center">
            <button type="submit">GUARDAR</button>
        </div>
    </form>
</div>
@endsection
