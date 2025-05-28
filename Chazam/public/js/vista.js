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

    let brilloTocado = false; // Bandera para saber si el usuario tocó el color

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
                document.querySelectorAll('.marco-option').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');
                const file = this.dataset.marco;
                input.value = file;
                avatarWrapper.style.backgroundImage = `url('/img/bordes/${file}')`;
            });
        });

        // Inicialmente elimina el brillo si es null
        if (!hiddenInput.value || hiddenInput.value === "null") {
            avatarWrapper.style.removeProperty('--glow-color');
            hiddenInput.value = '';
        }

        // Cambios en el brillo (picker)
        if (picker && hiddenInput && label) {
            picker.addEventListener('input', () => {
                const color = picker.value;
                avatarWrapper.style.setProperty('--glow-color', color);
                hiddenInput.value = color;
                label.textContent = color;
                brilloTocado = true; // ✅ Usuario tocó el brillo
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
        
            // Nuevos valores seleccionados
            const marco = input.value;
            const rotacion = document.querySelector('input[name="rotacion"]:checked')?.value || '0';
            const sidebarColor = sidebarPicker.value;
            const brillo = hiddenInput.value;
        
            // Valores actuales guardados en data-attributes
            const marcoActual    = form.dataset.marcoActual;
            const rotacionActual = form.dataset.rotacionActual;
            const sidebarActual  = form.dataset.sidebarActual;
            const brilloActual   = form.dataset.brilloActual;
            const isPremium      = form.dataset.isPremium === '1';
        
            const cambios = [];
            let puntosTotales = 0;
        
            // Precios configurados
            const precios = {
                marcos: {
                    'azuul.svg': 120,
                    'azul-champions.svg': 120,
                    'circle-rojonegro.svg': 130,
                    'cromado.svg': 150,
                    'cromado-normal.svg': 140,
                    'cromado-peque.svg': 130,
                    'cutre-estrellas.svg': 110,
                    'golden-champions.svg': 160,
                    'wave-haikei.svg': 125,
                    'estrellas-haikei.svg': 125,
                    'default.svg': 0
                },
                rotacion: {
                    '0': 0,
                    '1': 80
                },
                brillo: 90,
                sidebar: 100
            };
        
            // Detectar cambios reales
            if (marco !== marcoActual) {
                const costo = precios.marcos[marco] ?? 0;
                cambios.push(`🖼️ <strong>Marco</strong>: ${costo} puntos`);
                puntosTotales += costo;
            }
            if (rotacion !== rotacionActual) {
                const costo = precios.rotacion[rotacion] ?? 0;
                cambios.push(`🔄 <strong>Rotación</strong>: ${costo} puntos`);
                puntosTotales += costo;
            }
            if (sidebarColor.toLowerCase() !== sidebarActual.toLowerCase()) {
                const costo = (sidebarColor.toLowerCase() === '#4b0082') ? 0 : precios.sidebar;
                if (costo > 0) {
                    cambios.push(`📐 <strong>Color del menú lateral</strong>: ${costo} puntos`);
                    puntosTotales += costo;
                }
            }
            if (brilloTocado && brillo !== brilloActual) {
                const costo = precios.brillo;
                cambios.push(`✨ <strong>Brillo del marco</strong>: ${costo} puntos`);
                puntosTotales += costo;
            }
        
            // Si no hay cambios, informar
            if (cambios.length === 0) {
                Swal.fire('Sin cambios', 'No has modificado ningún elemento.', 'info');
                return;
            }
        
            // Configurar modal de confirmación
            const swalOpts = {
                title: '¿Confirmar personalización?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#4B0082'
            };
            if (!isPremium) {
                // Solo usuarios no premium ven el detalle de puntos
                swalOpts.html = cambios.join('<br>') + `<hr><strong>Total: ${puntosTotales} puntos</strong>`;
            }
        
            // Mostrar modal
            const confirmacion = await Swal.fire(swalOpts);
            if (!confirmacion.isConfirmed) return;
        
            // Armar payload con solo los campos cambiados
            const payload = { _method: 'PUT' };
            if (marco !== marcoActual)    payload.marco    = marco;
            if (rotacion !== rotacionActual) payload.rotacion = rotacion;
            if (sidebarColor.toLowerCase() !== sidebarActual.toLowerCase()) payload.sidebar = sidebarColor;
            if (brilloTocado && brillo !== brilloActual) payload.brillo = brillo;
        
            // 👉 Ver en consola el JSON que se envía
            console.log('Payload enviado al backend:', JSON.stringify(payload, null, 2));
        
            // Enviar al backend
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
        
                if (response.status === 403) {
                    const data = await response.json();
                    return Swal.fire({
                        icon: 'error',
                        title: 'Sin puntos suficientes',
                        text: data.message,
                        confirmButtonColor: '#4B0082'
                    });
                }
                if (!response.ok) throw new Error();
        
                const data = await response.json();
                await Swal.fire({
                    icon: 'success',
                    title: '¡Guardado!',
                    text: data.message,
                    confirmButtonColor: '#4B0082'
                });
        
                // Actualizar puntos
                if (data.puntos_restantes !== undefined) {
                    const puntosEl = document.getElementById('userPuntos');
                    if (puntosEl) {
                        puntosEl.innerHTML = `<i class="bi bi-star-fill me-1"></i>${data.puntos_restantes} pts`;
                    }
                }
        
                // Actualizar preview en el sidebar
                const sidebarAvatar = document.getElementById('sidebarAvatar');
                if (sidebarAvatar) {
                    if (payload.marco) sidebarAvatar.style.backgroundImage = `url('/img/bordes/${marco}')`;
                    if (payload.brillo) sidebarAvatar.style.setProperty('--glow-color', brillo);
                    if (payload.rotacion !== undefined) {
                        sidebarAvatar.classList.remove('marco-rotate');
                        if (rotacion === '1') sidebarAvatar.classList.add('marco-rotate');
                    }
                }
                if (payload.sidebar) {
                    sidebar.style.backgroundColor = sidebarColor;
                }
        
                // Actualizar los valores actuales (dataset)
                if (payload.marco)    form.dataset.marcoActual    = marco;
                if (payload.rotacion) form.dataset.rotacionActual = rotacion;
                if (payload.sidebar)  form.dataset.sidebarActual  = sidebarColor;
                if (payload.brillo)   form.dataset.brilloActual   = brillo;
        
                brilloTocado = false;
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
    
                    const data = await response.json();
    
                    Swal.fire({
                        icon: 'success',
                        title: 'Restablecido',
                        text: data.message,
                        confirmButtonColor: '#4B0082'
                    });
    
                    // === Reset visual
                    avatarWrapper.style.removeProperty('--glow-color');
                    avatarWrapper.style.backgroundImage = "url('/img/bordes/default.svg')";
                    avatarWrapper.classList.remove('marco-rotate');
                    input.value = 'default.svg';
                    hiddenInput.value = '';
                    picker.value = '#FFD700';
                    label.textContent = '#FFD700';
                    brilloTocado = false;
    
                    // Sidebar avatar
                    const sidebarAvatar = document.getElementById('sidebarAvatar');
                    if (sidebarAvatar) {
                        sidebarAvatar.style.backgroundImage = "url('/img/bordes/default.svg')";
                        sidebarAvatar.style.removeProperty('--glow-color');
                        sidebarAvatar.classList.remove('marco-rotate');
                    }
    
                    // Sidebar color
                    sidebarPicker.value = '#4B0082';
                    sidebar.style.backgroundColor = '#4B0082';
                    sidebarLabel.textContent = '#4B0082';
    
                    // Puntos
                    if (data.puntos_restantes !== undefined) {
                        const puntosEl = document.getElementById('userPuntos');
                        if (puntosEl) {
                            puntosEl.innerHTML = `<i class="bi bi-star-fill me-1"></i>${data.puntos_restantes} pts`;
                        }
                    }
    
                    // Actualizar radio rotación
                    document.querySelector('input[name="rotacion"][value="0"]').checked = true;
    
                    // Reset marco seleccionado
                    document.querySelectorAll('.marco-option').forEach(el => el.classList.remove('selected'));
                    document.querySelector('.marco-option[data-marco="default.svg"]')?.classList.add('selected');
    
                    // Actualizar data-atributos
                    form.dataset.marcoActual = 'default.svg';
                    form.dataset.rotacionActual = '0';
                    form.dataset.sidebarActual = '#4B0082';
                    form.dataset.brilloActual = '#FFD700';
    
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
