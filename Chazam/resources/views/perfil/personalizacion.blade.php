<!-- resources/views/perfil/personalizacion.blade.php -->
@extends('layout.user')

@section('content')
<div class="form-container">
    <h1 class="titulo">Mis datos</h1>

    <form action="{{ route('perfil.update') }}" method="POST" class="formulario">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div>
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $user->nombre) }}">
        </div>

        <!-- Apellido -->
        <div>
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" value="{{ old('apellido', $user->apellido) }}">
        </div>

        <!-- Email -->
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}">
        </div>

        <!-- Fecha de nacimiento -->
        <div>
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($user->fecha_nacimiento)->format('Y-m-d')) }}">
        </div>

        <!-- Descripción -->
        <div class="full">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="4">{{ old('descripcion', $user->descripcion) }}</textarea>
        </div>

        <!-- Imagen -->
        <div class="full">
            <label for="img">Ruta de imagen (temporal)</label>
            <input type="text" name="img" id="img" value="{{ old('img', $user->img) }}">
        </div>

        <!-- Botón -->
        <div class="full text-center">
            <button type="submit">GUARDAR</button>
        </div>
    </form>
</div>
@endsection