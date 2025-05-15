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
        return path.startsWith('http') ? path : '/storage/' + path;
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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