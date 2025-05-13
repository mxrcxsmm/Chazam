@include('layout.chatsHeader')

<div class="create-momentm-container">
    <a href="{{ url()->previous() }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Volver atrás
    </a>
    <h1 class="gradient-text">Crear nuevo Momentm</h1>

    <div class="editor-container">
        <div class="image-sources">
            <button id="uploadBtn" class="source-btn">
                <i class="fas fa-image"></i> Subir imagen
            </button>
            <button id="cameraBtn" class="source-btn">
                <i class="fas fa-camera"></i> Tomar foto
            </button>
            <input type="file" id="imageInput" accept="image/*" style="display: none">
        </div>

        <div class="editor-workspace">
            <div class="img-container">
                <img id="image" src="" alt="Imagen a editar">
                <div id="overlay-layer"></div>
            </div>
            <div class="editor-tools">
                <div class="tool-group">
                    <h3>Transformación</h3>
                    <div class="buttons-container">
                        <button class="tool-btn" data-action="rotate-left" title="Rotar izquierda">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button class="tool-btn" data-action="rotate-right" title="Rotar derecha">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button class="tool-btn" data-action="flip-horizontal" title="Voltear horizontal">
                            <i class="fas fa-arrows-alt-h"></i>
                        </button>
                        <button class="tool-btn" data-action="flip-vertical" title="Voltear vertical">
                            <i class="fas fa-arrows-alt-v"></i>
                        </button>
                    </div>
                </div>

                <div class="tool-group">
                    <h3>Ajustes</h3>
                    <div class="slider-container">
                        <label>Brillo</label>
                        <input type="range" class="slider" data-action="brightness" min="-100" max="100" value="0">
                    </div>
                    <div class="slider-container">
                        <label>Contraste</label>
                        <input type="range" class="slider" data-action="contrast" min="-100" max="100" value="0">
                    </div>
                    <div class="slider-container">
                        <label>Saturación</label>
                        <input type="range" class="slider" data-action="saturation" min="-100" max="100" value="0">
                    </div>
                </div>

                <div class="tool-group">
                    <h3>Filtros</h3>
                    <div class="buttons-container">
                        <button class="filter-btn" data-filter="none">Normal</button>
                        <button class="filter-btn" data-filter="grayscale">B/N</button>
                        <button class="filter-btn" data-filter="sepia">Sepia</button>
                        <button class="filter-btn" data-filter="blur">Blur</button>
                        <button class="filter-btn" data-filter="invert">Invert</button>
                        <button class="filter-btn" data-filter="vintage">Vintage</button>
                        <button class="filter-btn" data-filter="warm">Cálido</button>
                        <button class="filter-btn" data-filter="cool">Frío</button>
                        <button class="filter-btn" data-filter="dramatic">Dramático</button>
                    </div>
                </div>

                <div class="tool-group">
                    <h3>Texto</h3>
                    <div class="text-controls">
                        <input type="text" id="textInput" placeholder="Escribe algo..." class="text-input">
                        <select id="fontFamily" class="font-select">
                            <option value="Arial">Arial</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Helvetica">Helvetica</option>
                            <option value="Comic Sans MS">Comic Sans MS</option>
                            <option value="Impact">Impact</option>
                            <option value="Verdana">Verdana</option>
                        </select>
                        <input type="color" id="textColor" value="#ffffff" class="color-input">
                        <select id="fontSize" class="font-size-select">
                            <option value="20">Pequeño</option>
                            <option value="30" selected>Mediano</option>
                            <option value="40">Grande</option>
                        </select>
                        <button class="tool-btn" data-action="add-text">
                            <i class="fas fa-plus"></i> Añadir
                        </button>
                    </div>
                </div>

                <div class="tool-group">
                    <h3>Emojis</h3>
                    <div class="emoji-container">
                        <button class="emoji-btn" data-emoji="😊">😊</button>
                        <button class="emoji-btn" data-emoji="❤️">❤️</button>
                        <button class="emoji-btn" data-emoji="👍">👍</button>
                        <button class="emoji-btn" data-emoji="🎉">🎉</button>
                        <button class="emoji-btn" data-emoji="🌟">🌟</button>
                        <button class="emoji-btn" data-emoji="🔥">🔥</button>
                        <button class="emoji-btn" data-emoji="💯">💯</button>
                        <button class="emoji-btn" data-emoji="✨">✨</button>
                    </div>
                </div>

                <div class="tool-group">
                    <h3>Acciones</h3>
                    <div class="buttons-container">
                        <button class="tool-btn" data-action="reset" title="Restablecer">
                            <i class="fas fa-sync"></i>
                        </button>
                        <button class="tool-btn" data-action="download" title="Descargar">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="editor-controls">
            <button id="resetOverlaysBtn" class="control-btn">Resetear emojis</button>
            <button id="saveBtn" class="control-btn primary">Guardar Momentm</button>
        </div>
    </div>
</div>

<!-- Cámara modal -->
<div id="cameraModal" class="modal">
    <div class="modal-content">
        <div class="camera-container">
            <video id="video" autoplay playsinline></video>
            <div class="camera-controls">
                <button id="captureBtn" class="camera-btn">Capturar</button>
                <button id="closeCamera" class="camera-btn">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
body {
    overflow-y: auto !important;
}

.create-momentm-container {
    background-color: #9400D3;
    min-height: calc(100vh - 60px);
    padding: 20px;
    overflow-y: auto;
}

.gradient-text {
    background: linear-gradient(to right, rgba(255, 128, 0, 1), rgba(255, 0, 111, 1));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    text-align: center;
    margin-bottom: 30px;
}

.editor-container {
    max-width: 800px;
    margin: 0 auto 40px auto;
    background: #8B008B;
    padding: 20px;
    border-radius: 10px;
}

.image-sources {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.source-btn {
    background: #FFD700;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: bold;
}

.source-btn:hover {
    background: #FFC000;
    transform: scale(1.05);
}

.editor-workspace {
    background: #fff;
    margin-bottom: 20px;
    border-radius: 5px;
    overflow: hidden;
    height: auto;
    position: relative;
    display: flex;
    flex-direction: column;
}

.img-container {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    background: #000;
}

.img-container img {
    max-width: 100%;
    max-height: 100%;
    transition: filter 0.3s ease;
}

.editor-tools {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    padding: 10px;
    background: rgba(0,0,0,0.1);
}

.tool-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 8px;
    background: rgba(255,255,255,0.1);
    border-radius: 5px;
    min-width: 200px;
}

.tool-group h3 {
    color: #fff;
    margin: 0;
    font-size: 0.9rem;
    text-align: center;
    padding-bottom: 5px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.tool-group .buttons-container {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    justify-content: center;
}

.slider-container {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin: 10px 0;
}

.slider-container label {
    color: #fff;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.slider {
    width: 100%;
    height: 4px;
    -webkit-appearance: none;
    background: #ddd;
    outline: none;
    border-radius: 5px;
    margin: 5px 0;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 15px;
    height: 15px;
    background: #FFD700;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
}

.slider::-webkit-slider-thumb:hover {
    transform: scale(1.2);
}

.filter-btn {
    background: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    flex: 1;
    min-width: 60px;
    text-align: center;
}

.filter-btn:hover {
    background: #FFD700;
    transform: scale(1.05);
}

.filter-btn.active {
    background: #FFD700;
    color: #000;
    font-weight: bold;
}

.tool-btn {
    background: #fff;
    border: none;
    padding: 5px 8px;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}

.tool-btn:hover {
    background: #FFD700;
    transform: scale(1.05);
}

.editor-controls {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
}

.control-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    background: #fff;
    font-weight: bold;
    transition: all 0.3s ease;
}

.control-btn:hover {
    transform: scale(1.05);
}

.control-btn.primary {
    background: #FFD700;
    color: #000;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 1000;
    overflow-y: auto;
}

.modal-content {
    position: relative;
    width: 90%;
    max-width: 800px;
    margin: 20px auto;
    background: transparent;
}

.camera-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

#video {
    width: 100%;
    max-width: 800px;
    border-radius: 10px;
    background: #000;
}

.camera-controls {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.camera-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    background: #FFD700;
    color: #000;
    font-weight: bold;
    transition: all 0.3s ease;
}

.camera-btn:hover {
    background: #FFC000;
    transform: scale(1.05);
}

.font-select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.9rem;
    width: 100%;
    background: #fff;
}

.text-controls {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 10px 0;
}

.text-input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.9rem;
    width: 100%;
}

.color-input {
    width: 100%;
    height: 40px;
    padding: 0;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.font-size-select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.9rem;
    width: 100%;
    background: #fff;
}

.emoji-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin: 10px 0;
}

.emoji-btn {
    background: #fff;
    border: none;
    padding: 8px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;
}

.emoji-btn:hover {
    background: #FFD700;
    transform: scale(1.1);
}

/* Estilos para los filtros */
.filter-grayscale {
    filter: grayscale(100%) !important;
}

.filter-sepia {
    filter: sepia(100%) !important;
}

.filter-blur {
    filter: blur(5px) !important;
}

.filter-invert {
    filter: invert(100%) !important;
}

/* Estilos para los filtros adicionales */
.filter-vintage {
    filter: sepia(50%) contrast(120%) brightness(90%) !important;
}

.filter-warm {
    filter: sepia(30%) saturate(150%) brightness(110%) !important;
}

.filter-cool {
    filter: saturate(80%) hue-rotate(30deg) brightness(110%) !important;
}

.filter-dramatic {
    filter: contrast(150%) brightness(90%) saturate(120%) !important;
}

/* Mejoras para elementos arrastrables */
.draggable {
    position: absolute;
    cursor: move;
    user-select: none;
    pointer-events: auto;
    z-index: 1000;
    padding: 5px;
    border-radius: 3px;
    transform: translate(-50%, -50%);
}

.draggable.dragging {
    opacity: 0.8;
    z-index: 1001;
}

#overlay-layer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.back-btn {
    display: inline-block;
    background: #FFD700;
    color: #000;
    padding: 10px 22px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 18px;
    margin-right: 20px;
    transition: background 0.2s, transform 0.2s;
}
.back-btn i {
    margin-right: 8px;
}
.back-btn:hover {
    background: #FFC000;
    transform: scale(1.05);
}
</style>

<!-- Cropper.js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    const emojiButtons = document.querySelectorAll('.emoji-btn');
    // Cámara
    let video = document.getElementById('video');
    let cameraModal = document.getElementById('cameraModal');
    let stream = null;

    // Estado global
    let originalImage = null;
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
            }
            ctx.filter = filterStr.trim();
            ctx.drawImage(img, 0, 0);
            ctx.filter = 'none';
            // NO dibujar overlays aquí
            image.src = canvas.toDataURL();
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
                alert('Por favor, sube una imagen primero');
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
    // Función para añadir texto
    function addText(text, options = {}) {
        const textEl = document.createElement('div');
        textEl.className = 'draggable text-overlay';
        textEl.textContent = text;
        textEl.style.color = options.color || '#ffffff';
        textEl.style.fontSize = (options.size || 30) + 'px';
        textEl.style.fontFamily = options.font || 'Arial';
        
        // Posicionar en el centro
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

    // Event Listeners para texto y emojis
    document.querySelector('[data-action="add-text"]').addEventListener('click', () => {
        if (!cropper) {
            alert('Por favor, sube una imagen primero');
            return;
        }
        
        const text = textInput.value.trim();
        if (!text) {
            alert('Por favor, escribe algún texto');
            return;
        }
        
        addText(text, {
            color: textColor.value,
            size: parseInt(fontSize.value),
            font: fontFamily.value
        });
        
        textInput.value = '';
    });

    emojiButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (!cropper) {
                alert('Por favor, sube una imagen primero');
                return;
            }
            
            const emoji = button.dataset.emoji;
            addEmoji(emoji);
        });
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
            alert('Por favor, añade una imagen primero');
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
            fetch('{{ route("momentms.store") }}', {
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
                    window.location.href = '{{ route("user.momentms") }}';
                } else {
                    throw new Error(data.message || 'Error al guardar el Momentm');
                }
            })
            .catch(error => {
                alert(error.message || 'Error al guardar el Momentm');
                this.disabled = false;
                this.textContent = 'Guardar Momentm';
            });
        };
        img.src = originalImage;
    });
});
</script> 