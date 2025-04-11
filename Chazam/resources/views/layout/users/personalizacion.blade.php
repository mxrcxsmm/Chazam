<!-- resources/views/user/personalizacion.blade.php -->

@extends('layouts.user')

@section('content')
<div class="max-w-4xl mx-auto bg-[#8F00FF] text-white p-10 rounded shadow">
    <h1 class="text-3xl font-bold mb-10 text-center">Mis datos</h1>

    <form action="{{ route('user.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div>
            <label for="nombre" class="block mb-1">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="w-full rounded px-4 py-2 text-black" value="{{ old('nombre', $user->nombre) }}">
        </div>

        <!-- Apellido -->
        <div>
            <label for="apellido" class="block mb-1">Apellido</label>
            <input type="text" name="apellido" id="apellido" class="w-full rounded px-4 py-2 text-black" value="{{ old('apellido', $user->apellido) }}">
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block mb-1">Email</label>
            <input type="email" name="email" id="email" class="w-full rounded px-4 py-2 text-black" value="{{ old('email', $user->email) }}">
        </div>

        <!-- Fecha de nacimiento -->
        <div>
            <label for="fecha_nacimiento" class="block mb-1">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="w-full rounded px-4 py-2 text-black" value="{{ old('fecha_nacimiento', optional($user->fecha_nacimiento)->format('Y-m-d')) }}">
        </div>

        <!-- Descripción -->
        <div class="md:col-span-2">
            <label for="descripcion" class="block mb-1">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="4" class="w-full rounded px-4 py-2 text-black">{{ old('descripcion', $user->descripcion) }}</textarea>
        </div>

        <!-- Imagen (texto temporal) -->
        <div class="md:col-span-2">
            <label for="img" class="block mb-1">Ruta de imagen (temporal)</label>
            <input type="text" name="img" id="img" class="w-full rounded px-4 py-2 text-black" value="{{ old('img', $user->img) }}">
        </div>

        <!-- Botón guardar -->
        <div class="md:col-span-2 text-center mt-6">
            <button type="submit" class="bg-[#129401] hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-full">GUARDAR</button>
        </div>
    </form>
</div>
@endsection
