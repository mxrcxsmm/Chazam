document.addEventListener('DOMContentLoaded', function() {
    const comunidadesList = document.getElementById('comunidades-list');

    function renderComunidades(comunidades) {
        comunidadesList.innerHTML = '';
        comunidades.forEach(comunidad => {
            const comunidadItem = document.createElement('div');
            comunidadItem.className = 'comunidad-item';
            comunidadItem.innerHTML = `
                <div class="comunidad-info">
                    <h3>${comunidad.nombre}</h3>
                    <p>${comunidad.descripcion}</p>
                    <button class="join-btn" data-id="${comunidad.id}">Unirse</button>
                </div>
            `;
            comunidadesList.appendChild(comunidadItem);
        });
    }

    function loadComunidades() {
        fetch('/api/comunidades')
            .then(res => res.json())
            .then(data => {
                renderComunidades(data);
            });
    }

    comunidadesList.addEventListener('click', function(e) {
        if (e.target.classList.contains('join-btn')) {
            const comunidadId = e.target.dataset.id;
            fetch(`/api/comunidades/${comunidadId}/join`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Te has unido a la comunidad');
                }
            });
        }
    });

    loadComunidades();
});
