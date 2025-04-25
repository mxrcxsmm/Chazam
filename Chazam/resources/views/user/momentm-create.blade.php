@include('layout.chatsHeader')

<div class="create-momentm-container">
    <h1>Crear nuevo Momentm</h1>
    
    <form action="{{ route('momentms.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="contenido">Contenido (Imagen o Video)</label>
            <input type="file" id="contenido" name="contenido" accept="image/*,video/mp4" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción (opcional)</label>
            <textarea id="descripcion" name="descripcion" maxlength="255"></textarea>
        </div>

        <button type="submit" class="submit-btn">Crear Momentm</button>
    </form>
</div>

<style>
.create-momentm-container {
    background-color: #9400D3;
    min-height: calc(100vh - 60px);
    padding: 40px;
}

.create-momentm-container h1 {
    color: #FFD700;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: white;
    margin-bottom: 10px;
}

.form-group input[type="file"],
.form-group textarea {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: none;
}

.form-group textarea {
    height: 100px;
    resize: vertical;
}

.submit-btn {
    background-color: #FFD700;
    color: #000;
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    background-color: #FFC000;
    transform: scale(1.05);
}
</style> 