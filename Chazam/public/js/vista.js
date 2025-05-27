document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('personalizacionForm');
    const guardarBtn = document.getElementById('guardarCambios');
    const avatarWrapper = document.getElementById('previewAvatar'); // Solo afecta al preview
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

    // Actualiza color del sidebar en tiempo real (solo visual, antes de guardar)
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
                document.querySelectorAll('.marco-option').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');
                const file = this.dataset.marco;
                input.value = file;
                avatarWrapper.style.backgroundImage = `url('/img/bordes/${file}')`;
            });
        });

        // Inicialmente elimina el brillo si el valor es null
        if (!hiddenInput.value || hiddenInput.value === "null") {
            avatarWrapper.style.removeProperty('--glow-color');
            hiddenInput.value = '';
        }

        // Color dinámico del brillo
        if (picker && hiddenInput && label) {
            picker.addEventListener('input', () => {
                const color = picker.value;
                avatarWrapper.style.setProperty('--glow-color', color);
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

        const selectedRotation = document.querySelector('input[name="rotacion"]:checked');
        if (selectedRotation?.value === "1") {
            avatarWrapper.classList.add('marco-rotate');
        }
    }

    // Guardar cambios
    if (guardarBtn && form) {
    guardarBtn.addEventListener('click', async () => {
        const csrfToken = document.querySelector('input[name="_token"]').value;
        const marco = input.value;
        const brillo = hiddenInput.value || null;
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

                // Actualizar el sidebar visualmente sin recargar
                const sidebarAvatar = document.getElementById('sidebarAvatar');
                if (sidebarAvatar && avatarWrapper) {
                    // Marco
                    sidebarAvatar.style.backgroundImage = avatarWrapper.style.backgroundImage;

                    // Brillo
                    if (brillo && brillo !== 'null') {
                        sidebarAvatar.style.setProperty('--glow-color', brillo);
                    } else {
                        sidebarAvatar.style.removeProperty('--glow-color');
                    }

                    // Rotación
                    sidebarAvatar.classList.toggle('marco-rotate', rotacion === '1');
                }

                // Color de fondo del sidebar
                if (sidebar) {
                    sidebar.style.backgroundColor = sidebarColor;
                }

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
                            });

                            // Actualización visual del preview
                            avatarWrapper.style.removeProperty('--glow-color');
                            avatarWrapper.style.backgroundImage = "url('/img/bordes/default.svg')";
                            avatarWrapper.classList.remove('marco-rotate');
                            input.value = 'default.svg';
                            hiddenInput.value = '';
                            picker.value = '#FFD700';
                            label.textContent = '#FFD700';

                            const sidebarAvatar = document.getElementById('sidebarAvatar');
                            if (sidebarAvatar) {
                                sidebarAvatar.style.backgroundImage = "url('/img/bordes/default.svg')";
                                sidebarAvatar.style.removeProperty('--glow-color');
                                sidebarAvatar.classList.remove('marco-rotate');
                            }

                            // Sidebar
                            sidebarPicker.value = '#4B0082';
                            sidebar.style.backgroundColor = '#4B0082';
                            sidebarLabel.textContent = '#4B0082';

                            // Rotación
                            document.querySelector('input[name="rotacion"][value="0"]').checked = true;

                            // Marco seleccionado
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
    }
});
