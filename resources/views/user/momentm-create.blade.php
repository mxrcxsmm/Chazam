@include('layout.chatsHeader')

<div class="create-momentm-container">
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
            <canvas id="canvas"></canvas>
        </div>

        <div class="text-controls">
            <input type="text" id="textInput" placeholder="Añade texto...">
            <select id="fontFamily">
                <option value="Arial">Arial</option>
                <option value="Times New Roman">Times New Roman</option>
                <option value="Helvetica">Helvetica</option>
                <option value="Comic Sans MS">Comic Sans MS</option>
            </select>
            <input type="number" id="fontSize" value="30" min="12" max="72">
            <input type="color" id="textColor" value="#ffffff">
            <button id="addTextBtn" class="control-btn">Añadir texto</button>
        </div>

        <div class="editor-controls">
            <button id="deleteSelectedBtn" class="control-btn">Eliminar selección</button>
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
    height: 400px; /* Altura fija */
    display: flex;
    justify-content: center;
    align-items: center;
}

#canvas {
    max-width: 100%;
    max-height: 100%;
}

.text-controls {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    flex-wrap: wrap;
    align-items: center;
    background: rgba(255,255,255,0.1);
    padding: 10px;
    border-radius: 5px;
}

.text-controls input,
.text-controls select {
    padding: 8px;
    border: none;
    border-radius: 4px;
}

#fontFamily {
    min-width: 150px;
}

#fontSize {
    width: 70px;
}

#textColor {
    width: 50px;
    height: 38px;
    padding: 0;
}

.editor-controls {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.control-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
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

.image-preview {
    background: rgba(0,0,0,0.2);
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    display: none;
}

.preview-scroll {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 5px;
}

.preview-image {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.preview-image.active {
    border-color: #FFD700;
    transform: scale(1.05);
}

.preview-image:hover {
    transform: scale(1.05);
}
</style>

<!-- Fabric.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let canvas = new fabric.Canvas('canvas', {
        width: 400,
        height: 400,
        backgroundColor: '#ffffff',
        enableRetinaScaling: false,
        selection: true
    });

    // Función para añadir imagen al canvas
    function addImageToCanvas(imgSrc) {
        fabric.Image.fromURL(imgSrc, function(fabricImage) {
            const scale = Math.min(
                canvas.width / fabricImage.width,
                canvas.height / fabricImage.height
            );
            
            fabricImage.scale(scale);
            canvas.clear();
            canvas.add(fabricImage);
            canvas.centerObject(fabricImage);
            canvas.renderAll();
        });
    }

    // Subir imagen
    document.getElementById('uploadBtn').addEventListener('click', function() {
        document.getElementById('imageInput').click();
    });

    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                addImageToCanvas(event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Cámara
    document.getElementById('captureBtn').addEventListener('click', function() {
        const video = document.getElementById('video');
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = video.videoWidth;
        tempCanvas.height = video.videoHeight;
        const ctx = tempCanvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        const imgData = tempCanvas.toDataURL('image/jpeg');
        addImageToCanvas(imgData);
        
        // Cerrar modal de la cámara
        document.getElementById('cameraModal').style.display = 'none';
        stopCamera();
    });

    // Añadir texto
    document.getElementById('addTextBtn').addEventListener('click', function() {
        const text = document.getElementById('textInput').value;
        if (text) {
            const textObj = new fabric.Text(text, {
                left: canvas.width / 2,
                top: canvas.height / 2,
                fontSize: parseInt(document.getElementById('fontSize').value),
                fill: document.getElementById('textColor').value,
                fontFamily: document.getElementById('fontFamily').value,
                stroke: '#000000',
                strokeWidth: 0.5,
                textAlign: 'center',
                originX: 'center',
                originY: 'center'
            });
            
            canvas.add(textObj);
            canvas.setActiveObject(textObj);
            document.getElementById('textInput').value = '';
            canvas.renderAll();
        }
    });

    // Eliminar objeto seleccionado
    document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
        const activeObject = canvas.getActiveObject();
        if (activeObject) {
            canvas.remove(activeObject);
            canvas.renderAll();
        }
    });

    // Guardar Momentm
    document.getElementById('saveBtn').addEventListener('click', function() {
        if (canvas.isEmpty()) {
            alert('Por favor, añade una imagen primero');
            return;
        }

        // Mostrar indicador de carga
        this.disabled = true;
        this.textContent = 'Guardando...';

        const dataUrl = canvas.toDataURL({
            format: 'jpeg',
            quality: 0.8
        });

        fetch('{{ route("momentms.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                contenido: dataUrl
            })
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
            console.error('Error:', error);
            alert(error.message || 'Error al guardar el Momentm');
            this.disabled = false;
            this.textContent = 'Guardar Momentm';
        });
    });

    // Variables para la cámara
    let video = document.getElementById('video');
    let cameraModal = document.getElementById('cameraModal');
    let stream = null;

    // Manejador para la cámara
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
            console.error('Error:', err);
            alert('No se pudo acceder a la cámara');
        }
    };

    // Cerrar cámara
    document.getElementById('closeCamera').onclick = stopCamera;

    // Detener la cámara
    function stopCamera() {
        cameraModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        video.srcObject = null;
    }

    // Añadir estas opciones para el manejo de eventos
    canvas.on('mouse:wheel', function(opt) {
        var delta = opt.e.deltaY;
        var zoom = canvas.getZoom();
        zoom *= 0.999 ** delta;
        if (zoom > 20) zoom = 20;
        if (zoom < 0.01) zoom = 0.01;
        canvas.setZoom(zoom);
        opt.e.preventDefault();
        opt.e.stopPropagation();
    });
});
</script> 