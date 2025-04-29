@include('layout.chatsHeader')

<div class="momentm-view-container">
    <div class="momentm-content">
        @if(Str::endsWith($momentm->contenido, ['.mp4']))
            <video controls autoplay>
                <source src="{{ asset('storage/momentms/' . $momentm->contenido) }}" type="video/mp4">
                Tu navegador no soporta el elemento de video.
            </video>
        @else
            <img src="{{ asset('storage/momentms/' . $momentm->contenido) }}" alt="Momentm">
        @endif
        
        @if($momentm->descripcion)
            <p class="momentm-description">{{ $momentm->descripcion }}</p>
        @endif
    </div>
</div>

<style>
.momentm-view-container {
    background-color: #9400D3;
    min-height: calc(100vh - 60px);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.momentm-content {
    max-width: 100%;
    max-height: 80vh;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}

.momentm-content img,
.momentm-content video {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
}

.momentm-description {
    color: white;
    padding: 15px;
    margin: 0;
    background-color: rgba(0,0,0,0.5);
}
</style> 