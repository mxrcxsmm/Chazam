@extends('layout.user')

@section('content')
<div class="form-container">
    <h4 class="titulo">Mis datos</h4>
    <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="formulario">
        @csrf
        @method('PUT')

        {{-- Campos personales --}}
        <div>
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $user->nombre) }}">
        </div>
        <div>
            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $user->apellido) }}">
        </div>
        <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}">
        </div>
        <div>
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($user->fecha_nacimiento)->format('Y-m-d')) }}">
        </div>
        <div class="full">
            <label for="descripcion">Descripción</label>
            <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion', $user->descripcion) }}">
        </div>

                {{-- <div class="full text-center">
            <label for="img">Imagen de perfil</label>

            <div id="previewContainer" class="mb-3">
                @if($user->img)
                    <img src="{{ asset($user->img) }}" id="profilePreview" alt="Foto de perfil" class="rounded-circle shadow" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #8750B2;">
                @else
                    <img src="" id="profilePreview" class="rounded-circle shadow hidden" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #8750B2;">
                @endif
            </div>

            <div class="photo-actions">
                <button type="button" id="changePhotoBtn" class="custom-button">Cambiar foto</button>

                <div id="photoOptions" class="photo-options hidden">
                    <label for="img" class="option">Subir desde archivos</label>
                    <button type="button" class="option" id="takePhotoBtn">Tomar una foto</button>
                </div>

                <input type="file" id="img" name="img" accept="image/*" class="hidden">
            </div>

            <!-- Contenedor para la cámara -->
            <div id="cameraContainer" class="hidden">
                <video id="camera" autoplay></video>
                <button type="button" id="captureBtn">Capturar</button>
                <canvas id="snapshot" class="hidden"></canvas>
            </div>
        </div> --}}

        {{-- Imagen de perfil --}}
        <div class="full text-center">
            <label class="form-label fw-bold fs-5">Imagen de perfil</label>
            <div id="previewContainer" class="position-relative mb-3">
                <img src="{{ asset($user->img ?? '') }}" id="profilePreview" alt="Foto de perfil"
                     class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #8750B2;">
                <a id="downloadBtn" class="position-absolute top-0 end-0 btn btn-sm btn-light shadow d-none" download="foto_webcam.jpg" title="Descargar" style="margin-top: -10px; margin-right: -10px;">
                    💾
                </a>
                <button id="discardBtn" type="button" class="position-absolute top-0 start-0 btn btn-sm btn-danger shadow d-none" title="Descartar" style="margin-top: -10px; margin-left: -10px;">
                    ❌
                </button>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn btn-outline-secondary" id="uploadBtn">Subir archivo</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cameraModal">
                    Tomar foto
                </button>
            </div>
            <input type="file" id="img" name="img" accept="image/*" class="d-none">
        </div>

        {{-- Modal cámara --}}
        <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center" style="background-color: #f0d9ff;">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold text-purple" id="cameraModalLabel">Tomar Foto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <video id="camera" autoplay style="width: 100%; border-radius: 10px; border: 3px solid #8750B2;"></video>
                        <button id="captureBtn" type="button" class="btn btn-capture mt-4 px-4 py-2 rounded-pill text-white fw-bold" style="background-color: #4B0082;">CAPTURAR</button>
                        <canvas id="snapshot" class="d-none"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botón guardar --}}
        <div class="full center mt-4">
            <button type="submit">GUARDAR</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const video = document.getElementById('camera');
    const canvas = document.getElementById('snapshot');
    const captureBtn = document.getElementById('captureBtn');
    const fileInput = document.getElementById('img');
    const previewImg = document.getElementById('profilePreview');
    const uploadBtn = document.getElementById('uploadBtn');
    const modal = document.getElementById('cameraModal');
    const discardBtn = document.getElementById('discardBtn');
    const downloadBtn = document.getElementById('downloadBtn');

    let stream;
    let originalImage = previewImg.src;

    modal.addEventListener('shown.bs.modal', async () => {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    });

    modal.addEventListener('hidden.bs.modal', () => {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });

    captureBtn.addEventListener('click', () => {
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataURL = canvas.toDataURL('image/jpeg');
        previewImg.src = dataURL;

        // Mostrar botones de acción
        discardBtn.classList.remove('d-none');
        downloadBtn.classList.remove('d-none');
        downloadBtn.href = dataURL;

        canvas.toBlob(blob => {
            const file = new File([blob], "foto_webcam.jpg", { type: "image/jpeg" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
        }, "image/jpeg");

        bootstrap.Modal.getInstance(modal).hide();
    });

    uploadBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                downloadBtn.classList.add('d-none');
                discardBtn.classList.add('d-none');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    discardBtn.addEventListener('click', () => {
        previewImg.src = originalImage;
        discardBtn.classList.add('d-none');
        downloadBtn.classList.add('d-none');
        fileInput.value = ''; // limpia el archivo del input
    });
});
</script>
@endpush