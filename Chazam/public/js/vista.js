document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('personalizacionForm');
    const guardarBtn = document.getElementById('guardarCambios');
    const avatarWrapper = document.getElementById('previewAvatar');
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

    // Guardamos el valor inicial del brillo
    const initialGlow = picker.value;

    // Actualiza color del sidebar en tiempo real (solo visual)
    if (sidebarPicker && sidebarLabel && sidebar) {
        sidebarPicker.addEventListener('input', () => {
            const color = sidebarPicker.value;
            sidebar.style.backgroundColor = color;
            sidebarLabel.textContent = color;
        });
    }

    if (avatarWrapper) {
        // Selección de marco
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function () {
                thumbs.forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');
                const file = this.dataset.marco;
                input.value = file;
                avatarWrapper.style.backgroundImage = `url('/img/bordes/${file}')`;
            });
        });

        // Inicialmente deshabilitamos el hidden si no hay valor
        if (!hiddenInput.value || hiddenInput.value === "null") {
            avatarWrapper.style.removeProperty('--glow-color');
            hiddenInput.value = '';
        }

        // Cuando el usuario mueve el picker, habilitamos y actualizamos el hidden
        if (picker && hiddenInput && label) {
            picker.addEventListener('input', () => {
                const color = picker.value;
                avatarWrapper.style.setProperty('--glow-color', color);
                hiddenInput.disabled = false;
                hiddenInput.value = color;
                label.textContent = color;
            });
        }

        // Rotación del marco en tiempo real
        document.querySelectorAll('input[name="rotacion"]').forEach(radio => {
            radio.addEventListener('change', () => {
                avatarWrapper.classList.toggle('marco-rotate', radio.value === "1");
            });
        });
        // Inicializamos la clase si estaba activada
        const selectedRotation = document.querySelector('input[name="rotacion"]:checked');
        if (selectedRotation?.value === "1") {
            avatarWrapper.classList.add('marco-rotate');
        }
    }

    // En el click de Guardar, deshabilitamos el hidden si no cambió
    if (guardarBtn && form) {
        guardarBtn.addEventListener('click', async () => {
            // Si el brillo no cambió, volvemos a deshabilitarlo
            if (picker.value === initialGlow) {
                hiddenInput.disabled = true;
            }
            // Ahora enviamos
            const csrfToken = document.querySelector('input[name="_token"]').value;
            const marco = input.value;
            const brillo = hiddenInput.disabled ? null : hiddenInput.value;
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

                    // Actualizar sidebar sin recarga
                    const sidebarAvatar = document.getElementById('sidebarAvatar');
                    if (sidebarAvatar && avatarWrapper) {
                        sidebarAvatar.style.backgroundImage = avatarWrapper.style.backgroundImage;
                        if (brillo) {
                            sidebarAvatar.style.setProperty('--glow-color', brillo);
                        } else {
                            sidebarAvatar.style.removeProperty('--glow-color');
                        }
                        sidebarAvatar.classList.toggle('marco-rotate', rotacion === '1');
                    }
                    if (sidebar) sidebar.style.backgroundColor = sidebarColor;

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
    }

    // Restablecer valores
    if (restablecerBtn && restablecerForm && avatarWrapper) {
        restablecerBtn.addEventListener('click', () => {
            Swal.fire({
                title: '¿Restablecer personalización?',
                text: 'Esto restaurará los valores por defecto. ¿Deseas continuar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, restablecer',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545'
            }).then(async (result) => {
                if (!result.isConfirmed) return;

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

                    if (!response.ok) throw new Error();

                    Swal.fire({
                        icon: 'success',
                        title: 'Restablecido',
                        text: 'Los valores han sido restaurados.',
                        confirmButtonColor: '#4B0082'
                    });

                    // Visual: brillo
                    avatarWrapper.style.removeProperty('--glow-color');
                    hiddenInput.disabled = true;
                    hiddenInput.value = '';
                    picker.value = initialGlow;
                    label.textContent = initialGlow;

                    // Visual: marco
                    avatarWrapper.style.backgroundImage = "url('/img/bordes/default.svg')";
                    avatarWrapper.classList.remove('marco-rotate');
                    input.value = 'default.svg';
                    thumbs.forEach(el => el.classList.remove('selected'));
                    document.querySelector('.marco-option[data-marco="default.svg"]')?.classList.add('selected');

                    // Visual: rotación
                    document.querySelector('input[name="rotacion"][value="0"]').checked = true;

                    // Visual: sidebar
                    sidebarPicker.value = '#4B0082';
                    sidebar.style.backgroundColor = '#4B0082';
                    sidebarLabel.textContent = '#4B0082';

                    const sidebarAvatar = document.getElementById('sidebarAvatar');
                    if (sidebarAvatar) {
                        sidebarAvatar.style.backgroundImage = "url('/img/bordes/default.svg')";
                        sidebarAvatar.style.removeProperty('--glow-color');
                        sidebarAvatar.classList.remove('marco-rotate');
                    }

                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo restablecer la personalización.',
                        confirmButtonColor: '#4B0082'
                    });
                }
            });
        });
    }
});
