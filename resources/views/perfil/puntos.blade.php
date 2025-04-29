<!-- resources/views/perfil/puntos.blade.php --> 
@extends('layout.user')

@section('content')
<div class="perfil-container">
    <h1 class="titulo">Comprar Puntos</h1>
    <div class="puntos-grid">
        @foreach ([
            ['cantidad' => '1.000', 'precio' => '0,99€'],
            ['cantidad' => '5.000', 'precio' => '3,99€'],
            ['cantidad' => '10.000', 'precio' => '7,99€'],
            ['cantidad' => '20.000', 'precio' => '14,99€'],
            ['cantidad' => '50.000', 'precio' => '24,99€']
        ] as $pack)
            <div class="puntos-card">
                <img src="{{ asset('img/coin.png') }}" alt="Puntos">
                <p>{{ $pack['cantidad'] }} Puntos</p>
                <span>{{ $pack['precio'] }}</span>
            </div>
        @endforeach
    </div>
</div>
@endsection