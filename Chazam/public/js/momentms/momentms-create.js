// Configuración global de SweetAlert2 para tu paleta
const swalChazam = Swal.mixin({
    customClass: {
        popup: 'swal2-chazam-popup',
        confirmButton: 'swal2-chazam-confirm',
        cancelButton: 'swal2-chazam-cancel',
        title: 'swal2-chazam-title',
        content: 'swal2-chazam-content'
    },
    background: '#8B008B', // Fondo morado oscuro
    color: '#fff',
    confirmButtonColor: '#FFD700', // Dorado
    cancelButtonColor: '#9400D3', // Morado
    buttonsStyling: false
});

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
    let cropper = null;
    const image = document.getElementById('image');
    const imageInput = document.getElementById('imageInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const saveBtn = document.getElementById('saveBtn');
    const toolButtons = document.querySelectorAll('.tool-btn');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const sliders = document.querySelectorAll('.slider');
    const textInput = document.getElementById('textInput');
    const textColor = document.getElementById('textColor');
    const fontSize = document.getElementById('fontSize');
    const fontFamily = document.getElementById('fontFamily');
    const emojiBtn = document.getElementById('emojiBtn');
    const emojiPicker = document.querySelector('emoji-picker');
    // Cámara
    let video = document.getElementById('video');
    let cameraModal = document.getElementById('cameraModal');
    let stream = null;

    // Estado global
    let originalImage = document.getElementById('image').src;
    let currentFilter = 'none';
    let currentAdjust = { brightness: 0, contrast: 0, saturation: 0 };
    let overlays = []; // {type, value, x, y, color, size, font, el}

    const overlayLayer = document.getElementById('overlay-layer');
    const resetOverlaysBtn = document.getElementById('resetOverlaysBtn');

    // Renderiza la imagen con filtros, ajustes y overlays
    function renderImage(callback) {
        if (!originalImage) return;
        const img = new window.Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            let filterStr = '';
            filterStr += `brightness(${100 + parseInt(currentAdjust.brightness)}%) `;
            filterStr += `contrast(${100 + parseInt(currentAdjust.contrast)}%) `;
            filterStr += `saturate(${100 + parseInt(currentAdjust.saturation)}%)`;
            switch(currentFilter) {
                case 'grayscale': filterStr += ' grayscale(100%)'; break;
                case 'sepia': filterStr += ' sepia(100%)'; break;
                case 'blur': filterStr += ' blur(5px)'; break;
                case 'invert': filterStr += ' invert(100%)'; break;
                case 'vintage': filterStr += ' sepia(50%) contrast(120%) brightness(90%)'; break;
                case 'warm': filterStr += ' sepia(30%) saturate(150%) brightness(110%)'; break;
                case 'cool': filterStr += ' saturate(80%) hue-rotate(30deg) brightness(110%)'; break;
                case 'dramatic': filterStr += ' contrast(150%) brightness(90%) saturate(120%)'; break;
                case 'bright': filterStr += ' brightness(140%)'; break;
                case 'soft-bw': filterStr += ' grayscale(60%) contrast(110%)'; break;
                case 'strong-blur': filterStr += ' blur(12px)'; break;
            }
            ctx.filter = filterStr.trim();
            ctx.drawImage(img, 0, 0);
            ctx.filter = 'none';
            // NO dibujar overlays aquí
            image.src = canvas.toDataURL();
            image.classList.remove('editor-logo');
            setTimeout(() => {
                if (cropper) cropper.destroy();
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
                if (callback) callback();
            }, 100);
        };
        img.src = originalImage;
    }

    // --- FUNCIÓN PARA REDIMENSIONAR LA IMAGEN AL CARGAR ---
    function resizeImageIfNeeded(dataUrl, maxWidth = 1000, maxHeight = 1000, callback) {
        const img = new window.Image();
        img.onload = function() {
            let { width, height } = img;
            if (width > maxWidth || height > maxHeight) {
                const scale = Math.min(maxWidth / width, maxHeight / height);
                width = Math.round(width * scale);
                height = Math.round(height * scale);
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                callback(canvas.toDataURL('image/jpeg', 0.9));
            } else {
                callback(dataUrl);
            }
        };
        img.src = dataUrl;
    }

    // --- MODIFICAR CARGA DE IMAGEN DESDE ARCHIVO ---
    uploadBtn.addEventListener('click', () => {
        imageInput.click();
    });
    imageInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                resizeImageIfNeeded(event.target.result, 1000, 1000, (resizedDataUrl) => {
                    originalImage = resizedDataUrl;
                    overlays = [];
                    currentFilter = 'none';
                    currentAdjust = { brightness: 0, contrast: 0, saturation: 0 };
                    sliders.forEach(slider => slider.value = 0);
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    renderImage();
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // Cámara
    document.getElementById('cameraBtn').onclick = async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });
            video.srcObject = stream;
            cameraModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        } catch (err) {
            alert('No se pudo acceder a la cámara');
        }
    };

    document.getElementById('captureBtn').addEventListener('click', function() {
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = video.videoWidth;
        tempCanvas.height = video.videoHeight;
        const ctx = tempCanvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        resizeImageIfNeeded(tempCanvas.toDataURL('image/jpeg'), 1000, 1000, (resizedDataUrl) => {
            originalImage = resizedDataUrl;
            overlays = [];
            currentFilter = 'none';
            currentAdjust = { brightness: 0, contrast: 0, saturation: 0 };
            sliders.forEach(slider => slider.value = 0);
            filterButtons.forEach(btn => btn.classList.remove('active'));
            renderImage();
        });
        cameraModal.style.display = 'none';
        stopCamera();
    });

    document.getElementById('closeCamera').onclick = stopCamera;

    function stopCamera() {
        cameraModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
    }

    // Filtros visuales
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (!originalImage) {
                alert('Por favor, sube una imagen primero');
                return;
            }
            const filter = button.dataset.filter;
            filterButtons.forEach(btn => btn.classList.remove('active'));
            currentFilter = filter;
            if (filter !== 'none') button.classList.add('active');
            renderImage();
        });
    });

    // Sliders de ajustes
    sliders.forEach(slider => {
        slider.addEventListener('input', () => {
            if (!originalImage) return;
            const action = slider.dataset.action;
            currentAdjust[action] = parseInt(slider.value);
            renderImage();
        });
    });

    // Herramientas de edición básicas
    toolButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (!cropper) {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: 'Por favor, sube una imagen primero'
                });
                return;
            }
            const action = button.dataset.action;
            switch (action) {
                case 'rotate-left':
                    cropper.rotate(-90);
                    break;
                case 'rotate-right':
                    cropper.rotate(90);
                    break;
                case 'flip-horizontal':
                    cropper.scaleX(cropper.getData().scaleX * -1);
                    break;
                case 'flip-vertical':
                    cropper.scaleY(cropper.getData().scaleY * -1);
                    break;
                case 'reset':
                    overlays = [];
                    currentAdjust = { brightness: 0, contrast: 0, saturation: 0 };
                    sliders.forEach(slider => slider.value = 0);
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    currentFilter = 'none';
                    renderImage();
                    break;
                case 'download':
                    const canvas = cropper.getCroppedCanvas();
                    const link = document.createElement('a');
                    link.download = 'momentm.jpg';
                    link.href = canvas.toDataURL('image/jpeg');
                    link.click();
                    break;
            }
        });
    });

    // Función para añadir texto (con emojis) como overlay
    function addTextOverlay(text, options = {}) {
        const textEl = document.createElement('div');
        textEl.className = 'draggable text-overlay';
        textEl.textContent = text;
        textEl.style.color = options.color || '#ffffff';
        textEl.style.fontSize = (options.size || 30) + 'px';
        textEl.style.fontFamily = options.font || 'Arial';

        // Posicionar en el centro del overlay-layer
        const rect = overlayLayer.getBoundingClientRect();
        textEl.style.left = (rect.width / 2) + 'px';
        textEl.style.top = (rect.height / 2) + 'px';

        overlayLayer.appendChild(textEl);
        overlays.push({
            type: 'text',
            value: text,
            x: rect.width / 2,
            y: rect.height / 2,
            color: options.color,
            size: options.size,
            font: options.font,
            el: textEl
        });

        makeDraggable(textEl);
        renderImage();
    }

    // Evento para el botón "Añadir"
    document.querySelector('[data-action="add-text"]').addEventListener('click', () => {
        if (!cropper) {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Por favor, sube una imagen primero'
            });
            return;
        }

        const text = textInput.value.trim();
        if (!text) {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Por favor, escribe algún texto o añade emojis'
            });
            return;
        }

        addTextOverlay(text, {
            color: textColor ? textColor.value : '#ffffff',
            size: fontSize ? parseInt(fontSize.value) : 30,
            font: fontFamily ? fontFamily.value : 'Arial'
        });

        textInput.value = '';
    });

    // Función para añadir emoji
    function addEmoji(emoji) {
        const emojiEl = document.createElement('div');
        emojiEl.className = 'draggable emoji-overlay';
        emojiEl.textContent = emoji;
        emojiEl.style.fontSize = '40px';
        
        // Posicionar en el centro
        const rect = overlayLayer.getBoundingClientRect();
        emojiEl.style.left = (rect.width / 2) + 'px';
        emojiEl.style.top = (rect.height / 2) + 'px';

        overlayLayer.appendChild(emojiEl);
        overlays.push({
            type: 'emoji',
            value: emoji,
            x: rect.width / 2,
            y: rect.height / 2,
            el: emojiEl
        });

        makeDraggable(emojiEl);
        renderImage();
    }

    // Función para hacer elementos arrastrables
    function makeDraggable(element) {
        let isDragging = false;
        let offsetX, offsetY;

        element.addEventListener('mousedown', dragStart);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', dragEnd);

        function dragStart(e) {
            e.preventDefault();
            isDragging = true;
            element.classList.add('dragging');
            // Calcula el offset entre el cursor y la esquina del overlay
            const rect = element.getBoundingClientRect();
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;
        }

        function drag(e) {
            if (!isDragging) return;
            e.preventDefault();

            const rect = overlayLayer.getBoundingClientRect();
            const elementRect = element.getBoundingClientRect();

            // Nueva posición basada en el offset
            let newX = e.clientX - rect.left - offsetX;
            let newY = e.clientY - rect.top - offsetY;

            // Limitar al contenedor
            newX = Math.max(0, Math.min(newX, rect.width - elementRect.width));
            newY = Math.max(0, Math.min(newY, rect.height - elementRect.height));

            element.style.left = newX + 'px';
            element.style.top = newY + 'px';

            // Actualizar posición en el array de overlays
            const overlay = overlays.find(o => o.el === element);
            if (overlay) {
                overlay.x = newX;
                overlay.y = newY;
            }
        }

        function dragEnd() {
            isDragging = false;
            element.classList.remove('dragging');
        }
    }

    emojiBtn.addEventListener('click', () => {
        emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
    });

    emojiPicker.addEventListener('emoji-click', event => {
        const emoji = event.detail.unicode;
        // Inserta el emoji en la posición actual del cursor
        const start = textInput.selectionStart;
        const end = textInput.selectionEnd;
        const value = textInput.value;
        textInput.value = value.substring(0, start) + emoji + value.substring(end);
        // Mueve el cursor después del emoji insertado
        textInput.selectionStart = textInput.selectionEnd = start + emoji.length;
        textInput.focus();
        emojiPicker.style.display = 'none';
    });

    // Reemplazar el botón de fijar por el de resetear
    document.getElementById('resetOverlaysBtn').addEventListener('click', function() {
        // Limpiar overlays
        overlayLayer.innerHTML = '';
        overlays = [];
        renderImage();
    });

    // --- MODIFICAR FUNCIÓN DE GUARDADO ---
    saveBtn.addEventListener('click', function() {
        if (!cropper) {
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Por favor, añade una imagen primero'
            });
            return;
        }
        this.disabled = true;
        this.textContent = 'Guardando...';

        // 1. Obtén los datos de recorte de Cropper
        const cropData = cropper.getData(true); // datos reales
        const cropBoxData = cropper.getCropBoxData();
        const overlayRect = overlayLayer.getBoundingClientRect();

        // 2. Crea un canvas del tamaño del recorte
        const canvas = document.createElement('canvas');
        canvas.width = cropData.width;
        canvas.height = cropData.height;
        const ctx = canvas.getContext('2d');

        // 3. Carga la imagen original
        const img = new window.Image();
        img.onload = function() {
            // 4. Aplica los mismos filtros y ajustes que en el editor
            let filterStr = '';
            filterStr += `brightness(${100 + parseInt(currentAdjust.brightness)}%) `;
            filterStr += `contrast(${100 + parseInt(currentAdjust.contrast)}%) `;
            filterStr += `saturate(${100 + parseInt(currentAdjust.saturation)}%)`;
            switch(currentFilter) {
                case 'grayscale': filterStr += ' grayscale(100%)'; break;
                case 'sepia': filterStr += ' sepia(100%)'; break;
                case 'blur': filterStr += ' blur(5px)'; break;
                case 'invert': filterStr += ' invert(100%)'; break;
                case 'vintage': filterStr += ' sepia(50%) contrast(120%) brightness(90%)'; break;
                case 'warm': filterStr += ' sepia(30%) saturate(150%) brightness(110%)'; break;
                case 'cool': filterStr += ' saturate(80%) hue-rotate(30deg) brightness(110%)'; break;
                case 'dramatic': filterStr += ' contrast(150%) brightness(90%) saturate(120%)'; break;
                case 'bright': filterStr += ' brightness(140%)'; break;
                case 'soft-bw': filterStr += ' grayscale(60%) contrast(110%)'; break;
                case 'strong-blur': filterStr += ' blur(12px)'; break;
            }
            ctx.filter = filterStr.trim();

            // 5. Dibuja la imagen original recortada
            ctx.drawImage(
                img,
                cropData.x, cropData.y, cropData.width, cropData.height,
                0, 0, canvas.width, canvas.height
            );
            ctx.filter = 'none';

            // 6. Dibuja los overlays HTML en la posición y tamaño relativa al recorte
            overlays.forEach(overlay => {
                // overlay.x, overlay.y son en px relativos al overlayLayer
                // Calcula la posición relativa al área recortada dentro del overlayLayer
                const relX = ((overlay.x - cropBoxData.left) / cropBoxData.width) * canvas.width;
                const relY = ((overlay.y - cropBoxData.top) / cropBoxData.height) * canvas.height;

                // Calcula el tamaño proporcional
                let fontSize = overlay.size || 30;
                if (overlay.type === 'emoji') fontSize = 40;
                // El tamaño base es respecto al cropBox, no al overlayLayer completo
                const scale = canvas.width / cropBoxData.width;
                const scaledFontSize = fontSize * scale;

                if (overlay.type === 'text') {
                    ctx.font = `${scaledFontSize}px ${overlay.font}`;
                    ctx.fillStyle = overlay.color;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(overlay.value, relX, relY);
                } else if (overlay.type === 'emoji') {
                    ctx.font = `${scaledFontSize}px Arial`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(overlay.value, relX, relY);
                }
            });

            // 7. Envía la imagen final
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            fetch('/momentms', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ contenido: dataUrl })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.href = '/momentms';
                } else {
                    throw new Error(data.message || 'Error al guardar el Momentm');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: '¡Error!',
                    text: error.message || 'Error al guardar el Momentm'
                });
                this.disabled = false;
                this.textContent = 'Guardar Momentm';
            });
        };
        img.src = originalImage;
    });

    image.classList.add('editor-logo');
}); 