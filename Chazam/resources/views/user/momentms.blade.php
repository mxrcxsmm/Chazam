@include('layout.chatsHeader')

<div class="momentms-container">
    <div class="momentms-header">
        <h1>MOMENTMS DEL DIA</h1>
        <a href="{{ route('momentms.create') }}" class="create-momentm-btn">Crear Momentm</a>
    </div>

    <div class="momentms-grid">
        @if($amigos->isEmpty())
            <div class="no-content-message">
                <p>No tienes amigos agregados aún. ¡Agrega amigos para ver sus Momentms!</p>
            </div>
        @elseif($momentms->isEmpty())
            <div class="no-content-message">
                <p>No hay Momentms disponibles. ¡Sé el primero en crear uno!</p>
            </div>
        @else
            <!-- Tu Momentm -->
            @if($momentms->where('id_usuario', Auth::id())->isNotEmpty())
                <div class="momentm-card" onclick="window.location.href='{{ route('momentms.show', $momentms->where('id_usuario', Auth::id())->first()->id) }}'">
                    <div class="momentm-avatar">
                        <img src="{{ asset('storage/img/profile_img/' . Auth::user()->img) }}" alt="Tu avatar">
                    </div>
                    <p class="momentm-username">Tú</p>
                </div>
            @endif

            <!-- Momentms de amigos -->
            @foreach($momentms->where('id_usuario', '!=', Auth::id()) as $momentm)
                <div class="momentm-card" onclick="window.location.href='{{ route('momentms.show', $momentm->id) }}'">
                    <div class="momentm-avatar">
                        <img src="{{ asset('storage/img/profile_img/' . $momentm->usuario->img) }}" alt="Avatar de {{ $momentm->usuario->username }}">
                    </div>
                    <p class="momentm-username">{{ $momentm->usuario->username }}</p>
                </div>
            @endforeach
        @endif
    </div>
</div>

<style>
.momentms-container {
    background-color: #9400D3;
    min-height: calc(100vh - 60px);
    padding: 20px;
}

.momentms-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.momentms-header h1 {
    color: #FFD700;
    font-size: 2rem;
    margin: 0;
}

.create-momentm-btn {
    background-color: #FFD700;
    color: #000;
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.create-momentm-btn:hover {
    background-color: #FFC000;
    transform: scale(1.05);
}

.momentms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    padding: 20px;
}

.momentm-card {
    background-color: #8B008B;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.momentm-card:hover {
    transform: scale(1.05);
}

.momentm-avatar {
    width: 100px;
    height: 100px;
    margin: 0 auto 15px;
}

.momentm-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.momentm-username {
    color: white;
    margin: 0;
    font-size: 1.2rem;
}

.no-content-message {
    grid-column: 1 / -1;
    text-align: center;
    color: white;
    font-size: 1.2rem;
    padding: 40px;
}
</style>

<!-- Añadir Font Awesome y el CSS personalizado -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/chatamig.css') }}">

<!-- Añadir el JavaScript personalizado -->
<script src="{{ asset('js/chatamig.js') }}"></script> 