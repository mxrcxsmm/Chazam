@extends('layout.chatsHeader')

@section('title', 'Crear Comunidad')

@section('content')
<div class="main-container">
    <div class="section-title">
        <h2>Crear Nueva Comunidad</h2>
    </div>

    <div class="create-form-container">
        <form action="{{ route('comunidades.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="nombre">Nombre de la Comunidad</label>
                <input type="text" id="nombre" name="nombre" required class="form-control">
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" required class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="tipocomunidad">Tipo de Comunidad</label>
                <select id="tipocomunidad" name="tipocomunidad" required class="form-control">
                    <option value="publica">Pública</option>
                    <option value="privada">Privada</option>
                </select>
            </div>

            <div class="form-group" id="codigo-group" style="display: none;">
                <label for="codigo">Código de Acceso</label>
                <input type="text" id="codigo" name="codigo" class="form-control">
            </div>

            <div class="form-group">
                <label for="img">Imagen de la Comunidad</label>
                <input type="file" id="img" name="img" accept="image/*" required class="form-control">
            </div>

            <div class="form-actions">
                <button type="submit" class="create-btn">Crear Comunidad</button>
            </div>
        </form>
    </div>
</div>

<style>
.create-form-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background: #2d2d2d;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #fff;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #444;
    border-radius: 5px;
    background: #1a1a1a;
    color: #fff;
}

textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

.form-actions {
    text-align: right;
    margin-top: 20px;
}

.create-btn {
    background-color: #9147ff;
    color: white;
    border: none;
    padding: 10px 30px;
    border-radius: 25px;
    font-size: 1.2em;
    cursor: pointer;
    transition: all 0.3s ease;
}

.create-btn:hover {
    background-color: #7a30dd;
    transform: scale(1.05);
}
</style>

<script>
document.getElementById('tipocomunidad').addEventListener('change', function() {
    const codigoGroup = document.getElementById('codigo-group');
    if (this.value === 'privada') {
        codigoGroup.style.display = 'block';
        document.getElementById('codigo').required = true;
    } else {
        codigoGroup.style.display = 'none';
        document.getElementById('codigo').required = false;
    }
});
</script>
@endsection
