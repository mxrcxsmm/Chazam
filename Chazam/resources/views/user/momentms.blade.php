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
                    <div class="momentm-card" data-momentm-id="{{ $momentm->id_historia }}" onclick="window.location.href='{{ route('momentms.show', $momentm->id_historia) }}'">
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
                    <div class="momentm-card" data-momentm-id="{{ $momentm->id_historia }}" onclick="window.location.href='{{ route('momentms.show', $momentm->id_historia) }}'">
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

<!-- Añadir el modal al final del archivo, justo antes de cerrar el body -->
<div id="momentmModal" class="momentm-modal">
    <div class="momentm-modal-content">
        <span class="close-modal">&times;</span>
        
        <!-- Navegación -->
        <button class="nav-btn prev-btn" id="prevMomentm">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="nav-btn next-btn" id="nextMomentm">
            <i class="fas fa-chevron-right"></i>
        </button>

        <!-- Contenido del Momentm -->
        <div class="momentm-view">
            <!-- Barra de progreso -->
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            
            <div class="momentm-view-header">
                <div class="momentm-user-info">
                    <img class="momentm-user-avatar" src="" alt="Avatar">
                    <span class="momentm-user-name"></span>
                </div>
                <span class="momentm-time"></span>
            </div>
            <div class="momentm-image-container">
                <img class="momentm-full-image" src="" alt="Momentm">
            </div>
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

/* Estilos para el modal */
.momentm-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    z-index: 1000;
    overflow: hidden;
}

.momentm-modal-content {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.close-modal {
    position: absolute;
    top: 20px;
    right: 20px;
    color: white;
    font-size: 30px;
    cursor: pointer;
    z-index: 1001;
}

.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 15px;
    cursor: pointer;
    border-radius: 50%;
    transition: background-color 0.3s;
}

.nav-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.prev-btn {
    left: 20px;
}

.next-btn {
    right: 20px;
}

.momentm-view {
    position: relative;
    max-width: 90%;
    max-height: 90vh;
    background: #8B008B;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.momentm-view-header {
    margin-top: 2px;
}

.momentm-user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.momentm-user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.momentm-user-name {
    color: white;
    font-weight: bold;
}

.momentm-time {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.8rem;
}

.momentm-image-container {
    position: relative;
    width: 100%;
    height: calc(90vh - 70px);
}

.momentm-full-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.progress-bar {
    width: 100%;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    position: relative;
    top: 0;
    left: 0;
    margin: 0;
    padding: 0;
}

.progress {
    height: 100%;
    background: #FFD700;
    width: 0;
    transition: width linear;
    position: absolute;
    top: 0;
    left: 0;
}

/* Mejorar visibilidad de los botones de navegación */
.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.5);
    border: none;
    color: white;
    padding: 20px;
    cursor: pointer;
    border-radius: 50%;
    transition: background-color 0.3s;
    z-index: 1002;
}

.nav-btn:hover {
    background: rgba(0, 0, 0, 0.8);
}
</style>

<!-- Añadir Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('momentmModal');
    const closeBtn = document.querySelector('.close-modal');
    const prevBtn = document.getElementById('prevMomentm');
    const nextBtn = document.getElementById('nextMomentm');
    const progressBar = document.querySelector('.progress');
    let currentMomentms = [];
    let currentIndex = 0;
    let progressTimer;
    const MOMENTM_DURATION = 5000; // 5 segundos por momentm

    function getAssetUrl(path) {
        return path.startsWith('http') ? path : '{{ asset("") }}' + path;
    }

    // Obtener todos los momentms al cargar
    function getAllMomentms() {
        const cards = document.querySelectorAll('.momentm-card');
        currentMomentms = Array.from(cards).map(card => ({
            id: card.getAttribute('data-momentm-id')
        }));
    }

    // Iniciar el temporizador de progreso
    function startProgress() {
        const progressElement = document.querySelector('.progress');
        if (progressElement) {
            // Reiniciar la barra de progreso
            progressElement.style.transition = 'none';
            progressElement.style.width = '0';
            
            // Forzar un reflow
            progressElement.offsetHeight;
            
            // Iniciar la animación
            progressElement.style.transition = `width ${MOMENTM_DURATION}ms linear`;
            progressElement.style.width = '100%';
        }

        if (progressTimer) {
            clearTimeout(progressTimer);
        }

        progressTimer = setTimeout(() => {
            if (currentIndex < currentMomentms.length - 1) {
                nextMomentm();
            } else {
                closeModal();
            }
        }, MOMENTM_DURATION);
    }

    function resetProgress() {
        if (progressTimer) {
            clearTimeout(progressTimer);
        }
        const progressElement = document.querySelector('.progress');
        if (progressElement) {
            progressElement.style.transition = 'none';
            progressElement.style.width = '0';
        }
    }

    // Modificar los onclick de las tarjetas para usar el modal
    document.querySelectorAll('.momentm-card').forEach(card => {
        card.onclick = function(e) {
            e.preventDefault();
            const momentmId = this.getAttribute('data-momentm-id');
            currentIndex = currentMomentms.findIndex(m => m.id === momentmId);
            openModal(momentmId);
        };
    });

    function openModal(momentmId) {
        resetProgress(); // Añadir esta línea para reiniciar la barra antes de abrir
        fetch(`/momentms/${momentmId}/data`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                updateModalContent(data);
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                startProgress();
            })
            .catch(error => {
                console.error('Error al cargar el momentm:', error);
            });
    }

    function updateModalContent(momentm) {
        document.querySelector('.momentm-user-avatar').src = getAssetUrl(momentm.usuario.img);
        document.querySelector('.momentm-user-name').textContent = momentm.usuario.username;
        document.querySelector('.momentm-time').textContent = momentm.fecha_inicio_diff;
        document.querySelector('.momentm-full-image').src = getAssetUrl(momentm.img);
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        resetProgress();
    }

    // Navegación
    function prevMomentm() {
        if (currentIndex > 0) {
            currentIndex--;
            openModal(currentMomentms[currentIndex].id);
        }
    }

    function nextMomentm() {
        if (currentIndex < currentMomentms.length - 1) {
            currentIndex++;
            openModal(currentMomentms[currentIndex].id);
        } else {
            closeModal();
        }
    }

    // Event Listeners
    closeBtn.onclick = closeModal;
    prevBtn.onclick = function(e) {
        e.stopPropagation();
        prevMomentm();
    };
    nextBtn.onclick = function(e) {
        e.stopPropagation();
        nextMomentm();
    };

    // Cerrar con Escape
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });

    // Inicializar los momentms
    getAllMomentms();
});
</script>