document.addEventListener('DOMContentLoaded', function() {

    // VANTA FOG
    VANTA.FOG({
        el: "#vanta-bg",
        mouseControls: true,
        touchControls: true,
        gyroControls: false,
        minHeight: 200.00,
        minWidth: 200.00,
        highlightColor: 0x6600ff,
        midtoneColor: 0x9300ff,
        lowlightColor: 0xff005f,
        baseColor: 0xaa91ff,
        speed: 2.50
    });
    
    const modal = document.getElementById('momentmModal');
    const closeBtn = document.querySelector('.close-modal');
    const prevBtn = document.getElementById('prevMomentm');
    const nextBtn = document.getElementById('nextMomentm');
    const progressBar = document.querySelector('.progress');
    let currentMomentms = [];
    let currentIndex = 0;
    let progressTimer;
    const MOMENTM_DURATION = 5000; // 5 segundos por momentm

    // Asegura que una ruta de imagen sea válida.
    // Si empieza por http o /, la devuelve igual.
    // Si no, antepone / para que funcione como ruta absoluta.
    // Esto es importante para que las imágenes se carguen correctamente.
    function getAssetUrl(path) {
        if (path.startsWith('http')) return path;
        if (path.startsWith('/')) return path;
        return '/' + path;
    }
    // Pillar bien la imagen de perfil.
    function getProfileImgPath(img) {
        if (!img || img === 'avatar-default.png' || img === '/img/profile_img/avatar-default.png') {
            return `${window.location.origin}/img/profile_img/avatar-default.png`;
        }
        if (img.startsWith('http')) {
            return img;
        }
        const cleanImg = img.replace(/^\/?img\/profile_img\//, '');
        return `${window.location.origin}/img/profile_img/${cleanImg}`;
    }

    // Obtener todos los momentms al cargar
    // Recolecta todos los elementos .momentm-card del DOM.
    // Llena el array currentMomentms con los IDs de los Momentms disponibles.
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
        document.querySelector('.momentm-user-avatar').src = getProfileImgPath(momentm.usuario.img);
        document.querySelector('.momentm-user-name').textContent = momentm.usuario.username;
        document.querySelector('.momentm-time').textContent = momentm.fecha_inicio_diff;
        document.querySelector('.momentm-full-image').src = getAssetUrl(momentm.img);
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        resetProgress();
    }

    // Navegación de Momentms Modal (flechita prev)
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

    const searchInput = document.getElementById('momentmsSearchInput');
    const searchFilter = document.getElementById('momentmsSearchFilter');
    const orderSelect = document.getElementById('momentmsOrder');
    const section = document.querySelector('.momentms-section');

    let lastController = null;

    function renderMomentms(momentms) {
        section.innerHTML = '';
        if (momentms.length === 0) {
            section.innerHTML = '<div class="no-content-message"><p>No hay Momentms que coincidan.</p></div>';
            currentMomentms = [];
            return;
        }
        // Actualiza el array global con los nuevos momentms
        currentMomentms = momentms.map(m => ({ id: m.id }));

        momentms.forEach(m => {
            section.innerHTML += `
                <div class="momentm-card" data-momentm-id="${m.id}">
                    <div class="momentm-preview">
                        <img src="${getAssetUrl(m.img)}" alt="Momentm de ${m.usuario.username}">
                    </div>
                    <div class="momentm-info">
                        <div class="momentm-avatar">
                            <img src="${getProfileImgPath(m.usuario.img)}" alt="Avatar de ${m.usuario.username}">
                        </div>
                        <p class="momentm-username">${m.usuario.username}</p>
                        <p class="momentm-time">${m.fecha_inicio}</p>
                        ${m.usuario.id_usuario == window.authUserId ? `
                            <button class="delete-momentm-btn" data-id="${m.id}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        // Reasigna el evento a las nuevas tarjetas
        document.querySelectorAll('.momentm-card').forEach(card => {
            card.onclick = function(e) {
                e.preventDefault();
                const momentmId = this.getAttribute('data-momentm-id');
                // Calcula el índice sobre el array actualizado
                currentIndex = currentMomentms.findIndex(m => m.id == momentmId);
                openModal(momentmId);
            };
        });

        // Reasigna eventos a los botones de eliminar
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
    }

    function buscarMomentms() {
        const q = searchInput.value.trim();
        const filtro = searchFilter.value;
        let orden = orderSelect.value;
        
        // Si el orden es "default", usar "fecha_desc"
        if (orden === 'default') {
            orden = 'fecha_desc';
        }

        // Cancelar fetch anterior si existe
        if (lastController) lastController.abort();
        lastController = new AbortController();

        fetch(`/momentms/search?q=${encodeURIComponent(q)}&filtro=${filtro}&orden=${orden}`, {
            signal: lastController.signal
        })
        .then(res => res.json())
        .then(data => renderMomentms(data))
        .catch(e => { /* Ignorar abortos */ });
    }

    searchInput.addEventListener('input', buscarMomentms);
    searchFilter.addEventListener('change', buscarMomentms);
    orderSelect.addEventListener('change', buscarMomentms);
}); 
