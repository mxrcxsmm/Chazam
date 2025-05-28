// public/js/reto_specific.js

document.addEventListener('DOMContentLoaded', function() {
    // Las variables window.userId y window.retoId deberían estar definidas antes de cargar este script
    console.log('DOMContentLoaded en reto_specific.js');
    console.log('User ID (desde reto_specific.js):', window.userId);
    console.log('Reto ID (desde reto_specific.js):', window.retoId);

    // Verificar si ya se mostró el disclaimer hoy
    fetch('/retos/verificar-disclaimer')
        .then(response => response.json())
        .then(data => {
            if (!data.mostrado) {
                Swal.fire({
                    title: 'Aviso',
                    html: `
                        <div class="text-start">
                            <p>Por favor, tenga en cuenta que:</p>
                            <ul>
                                <li>Si recarga o cierra esta página, perderá el chat actual</li>
                                <li>El usuario con el que está emparejado también perderá la conexión</li>
                            </ul>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="noMostrarHoy">
                                <label class="form-check-label" for="noMostrarHoy">
                                    No volver a mostrar el día de hoy
                                </label>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#4B0082',
                    showCancelButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        document.getElementById('noMostrarHoy').addEventListener('change', function() {
                            if (this.checked) {
                                fetch('/retos/guardar-disclaimer', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });

    // Configuración específica según el reto
    switch (window.retoId) {
        case 1:
            // Configuración del selector de emojis solo para el reto 1
            const emojiButton = document.getElementById('emojiButton');
            const emojiPicker = document.querySelector('emoji-picker');

            if (emojiButton && emojiPicker) {
                emojiButton.addEventListener('click', () => {
                    emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
                });

                document.querySelector('emoji-picker')
                    .addEventListener('emoji-click', event => {
                        const emoji = event.detail.unicode;
                        const input = document.getElementById('mensajeInput');
                        if (input) {
                            input.value += emoji;
                        }
                    });
            }
            break;
        case 3:
            // Lógica específica del contador para el reto 3 (ya incluida en la lógica general del contador)
            break;
        default:
            // Mostrar mensaje cuando intentan usar emojis en otros retos
            const emojiButtonDisabled = document.getElementById('emojiButtonDisabled');
            if(emojiButtonDisabled) { // Añadir verificación
                emojiButtonDisabled.addEventListener('click', () => {
                    Swal.fire({
                        title: 'No disponible',
                        text: 'Los emojis solo están disponibles en los retos de emojis 😊',
                        icon: 'info',
                        confirmButtonText: 'Entendido'
                    });
                });
            }
            break;
    }

    // === Lógica del contador de caracteres (para todos los retos) ===
    const mensajeInput = document.getElementById('mensajeInput');
    const inputGroup = document.querySelector('.input-group');

    if (mensajeInput && inputGroup) {
        // Crear elemento contador si no existe
        let contadorContainer = document.getElementById('contador-caracteres');
        if (!contadorContainer) {
            contadorContainer = document.createElement('div');
            contadorContainer.id = 'contador-caracteres';
            contadorContainer.style.fontSize = '12px';
            contadorContainer.style.color = '#6c757d';
            contadorContainer.style.marginTop = '5px';
            contadorContainer.style.textAlign = 'right';
            inputGroup.insertAdjacentElement('afterend', contadorContainer);
        }

        // Actualizar contador al escribir
        mensajeInput.addEventListener('input', function() {
            const longitud = this.value.trim().length;
            const caracteresRestantes = 500 - longitud;
            let textoContador = `${longitud}/500 caracteres`;

            // Lógica específica para el reto 3
            if (window.retoId == 3) {
                if (longitud < 60) {
                    const caracteresParaPuntos = 60 - longitud;
                    textoContador += ` (Faltan ${caracteresParaPuntos} para ganar puntos)`;
                    contadorContainer.style.color = '#dc3545'; // Color de advertencia
                } else {
                    contadorContainer.style.color = '#28a745'; // Color de éxito
                    textoContador += ' (¡Suficiente para puntuar!)';
                }
            } else { // Lógica para otros retos
                if (caracteresRestantes < 50) {
                    contadorContainer.style.color = '#dc3545'; // Color de advertencia
                } else {
                    contadorContainer.style.color = '#6c757d'; // Color normal
                }
            }

            contadorContainer.textContent = textoContador;
        });

        // Inicializar el contador
        mensajeInput.dispatchEvent(new Event('input'));
    }
    // === Fin Lógica del contador de caracteres ===

}); 