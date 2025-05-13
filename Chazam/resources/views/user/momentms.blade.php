@include('layout.chatsHeader')

<div class="momentms-container">
    <div class="momentms-header">
        <h1 class="gradient-text">MOMENTMS DEL DIA</h1>
        <a href="{{ route('momentms.create') }}" class="create-momentm-btn">Crear Momentm</a>
    </div>

    <div class="momentms-grid">
        <div class="momentms-section">
            @php
                use Carbon\Carbon;
                $ahora = Carbon::now();
                $momentms24h = $momentms->filter(function($m) use ($ahora) {
                    return $m->fecha_inicio && $ahora->diffInHours($m->fecha_inicio) < 24;
                });
            @endphp
            @forelse($momentms24h as $momentm)
                <div class="momentm-card" data-momentm-id="{{ $momentm->id_historia }}">
                    <div class="momentm-preview">
                        <img src="{{ asset($momentm->img) }}" alt="Momentm de {{ $momentm->usuario->username ?? 'Usuario' }}">
                    </div>
                    <div class="momentm-info">
                        <div class="momentm-avatar">
                            <img src="{{ asset('img/profile_img/' . ($momentm->usuario->img ?? 'default.png')) }}" alt="Avatar de {{ $momentm->usuario->username ?? 'Usuario' }}">
                        </div>
                        <p class="momentm-username">{{ $momentm->usuario->id_usuario == Auth::user()->id_usuario ? 'Tú' : ($momentm->usuario->username ?? 'Usuario') }}</p>
                        <p class="momentm-time">{{ $momentm->fecha_inicio->diffForHumans() }}</p>
                        @if($momentm->usuario->id_usuario == Auth::user()->id_usuario)
                            <button class="delete-momentm-btn" data-id="{{ $momentm->id_historia }}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="no-content-message">
                    <p>No hay Momentms recientes. ¡Crea uno nuevo!</p>
                </div>
            @endforelse
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
    min-height: 100vh;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}

.momentms-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 40px;
    margin-bottom: 30px;
    gap: 20px;
}

.gradient-text {
    background: linear-gradient(to right, #FF8000, #FF006F);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0;
    text-align: center;
}

.create-momentm-btn {
    background-color: #FFD700;
    color: #000;
    padding: 12px 32px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.1rem;
    border: none;
    transition: all 0.3s ease;
    margin-top: 10px;
}
.create-momentm-btn:hover {
    background-color: #FFC000;
    transform: scale(1.05);
}

.momentms-grid {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.momentms-section {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 40px;
    justify-content: center;
    align-items: center;
    margin-top: 30px;
    width: 90vw;
    max-width: 1100px;
}

.momentm-card {
    background-color: #A259D9;
    border-radius: 18px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    min-height: 280px;
    width: 200px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 2px solid #FFD700;
    transition: transform 0.2s;
    cursor: pointer;
    overflow: hidden;
}
.momentm-card:hover {
    transform: scale(1.04);
}

.momentm-preview {
    width: 100%;
    height: 140px;
    overflow: hidden;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.momentm-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.momentm-info {
    padding: 15px 10px 10px 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    width: 100%;
}

.momentm-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
    background: #fff;
    margin-bottom: 5px;
}

.momentm-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.momentm-username {
    color: #fff;
    font-size: 1.1rem;
    font-weight: 500;
    text-align: center;
    margin: 0;
}

.momentm-time {
    color: #FFD700;
    font-size: 0.95rem;
    margin: 0;
    text-align: center;
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
    width: 100vw;
    height: 100vh;
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
    max-width: 90vw;
    max-height: 90vh;
    background: #8B008B;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.momentm-view-header {
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
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
    color: #FFD700;
    font-size: 0.95rem;
}

.momentm-image-container {
    position: relative;
    width: 100%;
    height: 60vh;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
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

.delete-momentm-btn {
    background: #ff4d4d;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 14px;
    margin-top: 8px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background 0.2s;
}
.delete-momentm-btn:hover {
    background: #d90000;
}

.swal2-border-radius {
    border-radius: 16px !important;
}
</style>

<!-- Añadir Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    document.querySelectorAll('.delete-momentm-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.dataset.id;
            const card = this.closest('.momentm-card');
            Swal.fire({
                title: '¿Eliminar Momentm?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FFD700',
                cancelButtonColor: '#8B008B',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#8B008B',
                color: '#fff',
                customClass: {
                    popup: 'swal2-border-radius'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/momentms/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            card.remove();
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: 'Tu Momentm ha sido eliminado.',
                                icon: 'success',
                                confirmButtonColor: '#FFD700',
                                background: '#8B008B',
                                color: '#fff',
                                customClass: {
                                    popup: 'swal2-border-radius'
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudo eliminar el Momentm.',
                                icon: 'error',
                                confirmButtonColor: '#FFD700',
                                background: '#8B008B',
                                color: '#fff',
                                customClass: {
                                    popup: 'swal2-border-radius'
                                }
                            });
                        }
                    })
                    .catch(() => Swal.fire({
                        title: 'Error',
                        text: 'Error al eliminar el Momentm.',
                        icon: 'error',
                        confirmButtonColor: '#FFD700',
                        background: '#8B008B',
                        color: '#fff',
                        customClass: {
                            popup: 'swal2-border-radius'
                        }
                    }));
                }
            });
        });
    });
});
</script>

<meta name="csrf-token" content="{{ csrf_token() }}">