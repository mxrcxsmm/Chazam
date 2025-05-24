document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('personalizacionForm');
    const guardarBtn = document.getElementById('guardarCambios');
    const avatarWrapper = document.querySelector('.marco-externo');
    const input = document.getElementById('borde_overlay');
    const picker = document.getElementById('glowColorPicker');
    const hiddenInput = document.getElementById('glow_color');
    const label = document.getElementById('colorValueLabel');
    const sidebarPicker = document.getElementById('sidebarColorPicker');
    const sidebarLabel = document.getElementById('sidebarColorValueLabel');
    const sidebar = document.querySelector('.sidebar');
    const thumbs = document.querySelectorAll('.marco-option');
    const restablecerBtn = document.getElementById('restablecerBtn');
    const restablecerForm = document.getElementById('restablecerForm');

    // Actualiza color del sidebar en tiempo real
    sidebarPicker.addEventListener('input', () => {
        const color = sidebarPicker.value;
        sidebar.style.backgroundColor = color;
        sidebarLabel.textContent = color;
    });

    // Selección de marco
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function () {
            document.querySelectorAll('.marco-option').forEach(el => el.classList.remove('selected'));
            this.classList.add('selected');
            const file = this.dataset.marco;
            input.value = file;
            avatarWrapper.style.backgroundImage = `url('/img/bordes/${file}')`;
        });
    });

    // Color dinámico del brillo
    picker.addEventListener('input', () => {
        const color = picker.value;
        avatarWrapper.style.setProperty('--glow-color', color);
        hiddenInput.value = color;
        label.textContent = color;
    });

    // Rotación del marco en tiempo real
    document.querySelectorAll('input[name="rotacion"]').forEach(radio => {
        radio.addEventListener('change', () => {
            avatarWrapper.classList.toggle('marco-rotate', radio.value === "1");
        });
    });

    // Aplicar rotación si ya estaba activada
    const selectedRotation = document.querySelector('input[name="rotacion"]:checked');
    if (selectedRotation?.value === "1") {
        avatarWrapper.classList.add('marco-rotate');
    }

    // Envío asincrónico (guardar)
    guardarBtn.addEventListener('click', async () => {
        const csrfToken = document.querySelector('input[name="_token"]').value;
        const marco = input.value;
        const brillo = hiddenInput.value;
        const rotacion = document.querySelector('input[name="rotacion"]:checked')?.value || '0';
        const sidebarColor = sidebarPicker.value;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'PUT',
                    marco,
                    brillo,
                    rotacion,
                    sidebar: sidebarColor
                })
            });

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: 'Los cambios se guardaron correctamente.',
                    confirmButtonColor: '#4B0082'
                });
            } else {
                throw new Error();
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron guardar los cambios.',
                confirmButtonColor: '#4B0082'
            });
        }
    });

    // Restablecer valores por defecto
    restablecerBtn?.addEventListener('click', () => {
        Swal.fire({
            title: '¿Restablecer personalización?',
            text: 'Esto restaurará los valores por defecto. ¿Deseas continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, restablecer',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(async (result) => {
            if (result.isConfirmed) {
                const csrfToken = document.querySelector('input[name="_token"]').value;
    
                try {
                    const response = await fetch(restablecerForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'PUT' })
                    });
    
                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Restablecido',
                            text: 'Los valores han sido restaurados.',
                            confirmButtonColor: '#4B0082'
                        })/*.then(() => location.reload())*/;
    
                        // Actualizar visualmente los valores por defecto sin recargar
                        avatarWrapper.style.setProperty('--glow-color', '#FFD700');
                        avatarWrapper.style.backgroundImage = "url('/img/bordes/default.svg')";
                        input.value = 'default.svg';
                        hiddenInput.value = '#FFD700';
                        picker.value = '#FFD700';
                        label.textContent = '#FFD700';
                        sidebarPicker.value = '#4B0082';
                        sidebar.style.backgroundColor = '#4B0082';
                        sidebarLabel.textContent = '#4B0082';
    
                        // Reset rotación
                        avatarWrapper.classList.remove('marco-rotate');
                        document.querySelector('input[name="rotacion"][value="0"]').checked = true;
    
                        // Reset selección de marco visual
                        document.querySelectorAll('.marco-option').forEach(el => el.classList.remove('selected'));
                        document.querySelector('.marco-option[data-marco="default.svg"]')?.classList.add('selected');
                    } else {
                        throw new Error();
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo restablecer la personalización.',
                        confirmButtonColor: '#4B0082'
                    });
                }
            }
        });
    });    
});