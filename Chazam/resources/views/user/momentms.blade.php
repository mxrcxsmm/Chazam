@include('layout.chatsHeader')

<div class="momentms-container">
    <div class="momentms-header">
        <h1 class="gradient-text">MOMENTMS DEL DIA</h1>
        <a href="{{ route('momentms.create') }}" class="create-momentm-btn">Crear Momentm</a>
    </div>

    <div class="momentms-grid">
        <!-- Sección: Tus Momentms -->
        <div class="section-header">
            <h2 class="section-title">Tus Momentms</h2>
        </div>
        <div class="momentms-section">
            @php
                $tusMomentms = $momentms->where('id_usuario', Auth::id());
            @endphp
            
            @if($tusMomentms->isEmpty())
                <div class="no-content-message">
                    <p>No tienes Momentms. ¡Crea uno nuevo!</p>
                </div>
            @else
                @foreach($tusMomentms as $momentm)
                    <div class="momentm-card" onclick="window.location.href='{{ route('momentms.show', $momentm->id_historia) }}'">
                        <div class="momentm-preview">
                            <img src="{{ asset($momentm->img) }}" alt="Tu Momentm">
                        </div>
                        <div class="momentm-info">
                            <div class="momentm-avatar">
                                <img src="{{ asset('img/profile_img/' . Auth::user()->img) }}" alt="Tu avatar">
                            </div>
                            <p class="momentm-username">Tú</p>
                            <p class="momentm-time">{{ $momentm->fecha_inicio->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Sección: Momentms de Amigos -->
        <div class="section-header">
            <h2 class="section-title">Momentms de Amigos</h2>
        </div>
        <div class="momentms-section">
            @php
                $momentmsAmigos = $momentms->where('id_usuario', '!=', Auth::id());
            @endphp
            
            @if($momentmsAmigos->isEmpty())
                <div class="no-content-message">
                    <p>No hay Momentms de amigos para mostrar.</p>
                </div>
            @else
                @foreach($momentmsAmigos as $momentm)
                    <div class="momentm-card" onclick="window.location.href='{{ route('momentms.show', $momentm->id_historia) }}'">
                        <div class="momentm-preview">
                            <img src="{{ asset($momentm->img) }}" alt="Momentm de {{ $momentm->usuario->username }}">
                        </div>
                        <div class="momentm-info">
                            <div class="momentm-avatar">
                                <img src="{{ asset('img/profile_img/' . $momentm->usuario->img) }}" alt="Avatar de {{ $momentm->usuario->username }}">
                            </div>
                            <p class="momentm-username">{{ $momentm->usuario->username }}</p>
                            <p class="momentm-time">{{ $momentm->fecha_inicio->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
/* Asegurar que el body y html permitan scroll */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow-y: auto !important;
}

.momentms-container {
    background-color: #9400D3;
    min-height: 100%;
    padding: 20px;
    padding-top: 160px; /* Espacio para el header fijo */
    position: relative;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch; /* Para mejor scroll en iOS */
}

.momentms-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    text-align: center;
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    z-index: 100;
    background-color: #9400D3;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Añadir sombra para separación visual */
}

.gradient-text {
    background: linear-gradient(to right, rgba(255, 128, 0, 1), rgba(255, 0, 111, 1));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0;
    padding: 10px 0;
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

.section-header {
    margin: 0 0 20px 0;
    padding: 0 20px;
    background-color: #9400D3;
}

.section-title {
    color: #FFD700;
    font-size: 1.5rem;
    margin: 0;
    padding: 10px 0;
    border-bottom: 2px solid #FFD700;
}

.momentms-grid {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 40px;
}

.momentms-section {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.momentm-card {
    background-color: #8B008B;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.momentm-card:hover {
    transform: scale(1.05);
}

.momentm-preview {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.momentm-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.momentm-info {
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.momentm-avatar {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
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
    font-weight: bold;
}

.momentm-time {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
    margin: 0;
    margin-left: auto;
}

.no-content-message {
    grid-column: 1 / -1;
    text-align: center;
    color: white;
    font-size: 1.2rem;
    padding: 40px;
}
</style>

<!-- Añadir Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> 