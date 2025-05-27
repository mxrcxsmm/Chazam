@include('layout.chatsHeader')
<link rel="stylesheet" href="{{ asset('css/momentm/momentm-create.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="{{ asset('js/momentms/momentms-create.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>


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
            <div class="img-container" style="position: relative;">
                <img id="image" src="{{ asset('img/Logo_Chazam.png') }}" alt="Imagen a editar" class="editor-logo">
                <div id="overlay-layer"></div>
                <div id="editor-spinner" class="spinner-border text-warning" role="status" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 2000; width: 3rem; height: 3rem; border-width: 0.25em;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
            <div class="editor-tools">
                <div class="tool-group">
                    <h3>Transformación</h3>
                    <div class="buttons-container" data-group="transformacion">
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
                    <div class="buttons-container" data-group="filtros">
                        <button class="filter-btn" data-filter="none">Normal</button>
                        <button class="filter-btn" data-filter="grayscale">B/N</button>
                        <button class="filter-btn" data-filter="sepia">Sepia</button>
                        <button class="filter-btn" data-filter="blur">Blur</button>
                        <button class="filter-btn" data-filter="invert">Invert</button>
                        <button class="filter-btn" data-filter="vintage">Vintage</button>
                        <button class="filter-btn" data-filter="warm">Cálido</button>
                        <button class="filter-btn" data-filter="cool">Frío</button>
                        <button class="filter-btn" data-filter="dramatic">Drama</button>
                        <button class="filter-btn" data-filter="bright">Brillo+</button>
                        <button class="filter-btn" data-filter="soft-bw">B/N-</button>
                        <button class="filter-btn" data-filter="strong-blur">Blur+</button>
                    </div>
                </div>

                <div class="tool-group">
                    <h3>Acciones</h3>
                    <div class="buttons-container" data-group="acciones">
                        <button class="tool-btn" data-action="reset" title="Restablecer">
                            <i class="fas fa-sync"></i>
                        </button>
                        <button class="tool-btn" data-action="download" title="Descargar">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <div class="tool-group tool-group-mensaje">
                    <h3>Mensaje</h3>
                    <div class="text-controls" style="position: relative;">
                        <input type="text" id="textInput" placeholder="Escribe un mensaje..." class="text-input">
                        <i class="far fa-smile" id="emojiBtn" style="position: absolute; right: 10px; top: 10px; cursor: pointer;"></i>
                        <div style="display: flex; gap: 8px; margin: 10px 0;">
                            <label style="color: #fff; font-size: 0.9em;">Color:
                                <input type="color" id="textColor" value="#ffffff" class="color-input" style="vertical-align: middle;">
                            </label>
                            <label style="color: #fff; font-size: 0.9em;">Fuente:
                                <select id="fontFamily" class="font-select">
                                    <option value="Arial">Arial</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Helvetica">Helvetica</option>
                                    <option value="Comic Sans MS">Comic Sans MS</option>
                                    <option value="Impact">Impact</option>
                                    <option value="Verdana">Verdana</option>
                                </select>
                            </label>
                            <label style="color: #fff; font-size: 0.9em;">Tamaño:
                                <select id="fontSize" class="font-size-select">
                                    <option value="20">Pequeño</option>
                                    <option value="30" selected>Mediano</option>
                                    <option value="40">Grande</option>
                                </select>
                            </label>
                        </div>
                        <button class="tool-btn" data-action="add-text">
                            <i class="fas fa-plus"></i> Añadir
                        </button>
                        <emoji-picker style="position: absolute; bottom: 50px; left: 0; display: none; z-index: 999;"></emoji-picker>
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

