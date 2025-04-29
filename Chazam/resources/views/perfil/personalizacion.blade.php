@extends('layout.user')

@section('content')
<div class="form-container">
    <h4 class="titulo">Mis datos</h4>
    <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="formulario">
        @csrf
        @method('PUT')

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

        <div class="full text-center">
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
        </div>

        <div class="full center">
            <button type="submit">GUARDAR</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const changeBtn = document.getElementById('changePhotoBtn');
        const options = document.getElementById('photoOptions');
        const takePhotoBtn = document.getElementById('takePhotoBtn');
        const cameraContainer = document.getElementById('cameraContainer');
        const video = document.getElementById('camera');
        const captureBtn = document.getElementById('captureBtn');
        const canvas = document.getElementById('snapshot');
        const fileInput = document.getElementById('img');
        const previewImg = document.getElementById('profilePreview');

        changeBtn?.addEventListener('click', () => {
            options.classList.toggle('hidden');
        });

        takePhotoBtn?.addEventListener('click', async () => {
            options.classList.add('hidden');
            cameraContainer.classList.remove('hidden');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
            } catch (err) {
                alert('No se pudo acceder a la cámara');
                console.error(err);
            }
        });

        captureBtn?.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.classList.remove('hidden');

            // Mostrar preview inmediata en img
            const dataURL = canvas.toDataURL('image/jpeg');
            previewImg.src = dataURL;
            previewImg.classList.remove('hidden');

            canvas.toBlob(blob => {
                const file = new File([blob], "foto_webcam.jpg", { type: "image/jpeg" });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
            }, "image/jpeg");
        });

        // Preview si elige archivo desde documentos
        fileInput?.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush