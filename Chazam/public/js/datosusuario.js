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
    const form = document.querySelector('.formulario');
    const removePhotoBtn = document.getElementById('removePhotoBtn');
    const removeImgInput = document.getElementById('remove_img');

    let stream;
    let originalImage = previewImg.src;

    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Tu imagen de perfil será eliminada.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    removeImgInput.value = '1';
                    form.dataset.skipValidation = 'true';

                    const defaultAvatar = '/img/profile_img/avatar-default.png';
                    previewImg.src = defaultAvatar;
                    fileInput.value = '';
                    discardBtn.classList.add('d-none');
                    downloadBtn.classList.add('d-none');

                    const layoutImg = document.querySelector('.sidebar img');
                    if (layoutImg) layoutImg.src = defaultAvatar;

                    form.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            });
        });
    }

    modal.addEventListener('shown.bs.modal', async () => {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    });

    modal.addEventListener('hidden.bs.modal', () => {
        if (stream) stream.getTracks().forEach(track => track.stop());
    });

    captureBtn.addEventListener('click', () => {
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataURL = canvas.toDataURL('image/jpeg');
        previewImg.src = dataURL;

        discardBtn.classList.remove('d-none');
        downloadBtn.classList.remove('d-none');
        downloadBtn.href = dataURL;

        canvas.toBlob(blob => {
            const file = new File([blob], "foto_webcam.jpg", { type: "image/jpeg" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
            removeImgInput.value = '0';
        }, "image/jpeg");

        bootstrap.Modal.getInstance(modal).hide();
    });

    uploadBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            removeImgInput.value = '0';
            
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                discardBtn.classList.remove('d-none');
                downloadBtn.classList.add('d-none');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    discardBtn.addEventListener('click', () => {
        previewImg.src = originalImage;
        discardBtn.classList.add('d-none');
        downloadBtn.classList.add('d-none');
        fileInput.value = '';
    });

// Función reutilizable para mostrar errores
function mostrarError(campo, mensaje) {
    const existente = campo.parentElement.querySelector('.error-message');
    if (existente) existente.remove();

    const error = document.createElement('div');
    error.className = 'error-message';
    error.textContent = mensaje;
    campo.insertAdjacentElement('afterend', error);
}

// Validación asincrónica de disponibilidad de username
async function checkUsernameAvailability(username, usernameActual) {
    if (username === usernameActual) return true;

    try {
        const response = await fetch('/check-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ type: 'username', value: username })
        });
        const data = await response.json();
        return data.available;
    } catch (error) {
        console.error('Error al verificar disponibilidad:', error);
        return true;
    }
}

// Validación en tiempo real del campo username
const usernameInput = form.querySelector('input[name="username"]');
const usernameActual = document.getElementById('username_actual').value;

usernameInput.addEventListener('blur', async () => {
    const username = usernameInput.value.trim();

    if (!username || username.length > 30) {
        mostrarError(usernameInput, 'Username obligatorio y máximo 30 caracteres.');
        return;
    }

    const disponible = await checkUsernameAvailability(username, usernameActual);
    if (!disponible) {
        mostrarError(usernameInput, 'Este nombre de usuario ya está en uso.');
    }
});

usernameInput.addEventListener('input', () => {
    const existente = usernameInput.parentElement.querySelector('.error-message');
    if (existente) existente.remove();
});

// Validación en tiempo real de nombre y apellido
const nombreInput = form.querySelector('input[name="nombre"]');
const apellidoInput = form.querySelector('input[name="apellido"]');

function validarTextoAlfanumerico(input, campoNombre) {
    const valor = input.value.trim();
    const regex = /^[\p{L}\s\-]+$/u;

    if (valor && !regex.test(valor)) {
        mostrarError(input, `${campoNombre} inválido. Solo letras, espacios y guiones.`);
    }
}

[nombreInput, apellidoInput].forEach((input, i) => {
    const campoNombre = i === 0 ? 'Nombre' : 'Apellido';

    input.addEventListener('blur', () => validarTextoAlfanumerico(input, campoNombre));
    input.addEventListener('input', () => {
        const existente = input.parentElement.querySelector('.error-message');
        if (existente) existente.remove();
    });
});

// Validación en tiempo real de fecha de nacimiento
const fechaInput = form.querySelector('input[name="fecha_nacimiento"]');
fechaInput.addEventListener('blur', () => {
    const fechaNacimiento = fechaInput.value;
    const hoy = new Date();
    const fechaMinima = new Date(hoy.getFullYear() - 13, hoy.getMonth(), hoy.getDate());

    if (fechaNacimiento) {
        const fecha = new Date(fechaNacimiento);
        if (fecha > fechaMinima) {
            mostrarError(fechaInput, 'Debes tener al menos 13 años.');
        }
    }
});
fechaInput.addEventListener('input', () => {
    const existente = fechaInput.parentElement.querySelector('.error-message');
    if (existente) existente.remove();
});

// Validación en tiempo real de descripción
const descripcionInput = form.querySelector('textarea[name="descripcion"]');
if (descripcionInput) {
    descripcionInput.addEventListener('input', () => {
        const existente = descripcionInput.parentElement.querySelector('.error-message');
        if (existente) existente.remove();

        if (descripcionInput.value.length > 1000) {
            mostrarError(descripcionInput, 'Máximo 1000 caracteres.');
        }
    });
}



    form.addEventListener('submit', async function (e) {
        e.preventDefault();
    
        if (form.dataset.skipValidation === 'true') {
            form.dataset.skipValidation = 'false';
            sendFormAjax(form);
            return;
        }
    
        document.querySelectorAll('.error-message').forEach(el => el.remove());
    
        const nombre = form.nombre.value.trim();
        const apellido = form.apellido.value.trim();
        const username = form.username.value.trim();
        const fechaNacimiento = form.fecha_nacimiento.value;
        const descripcion = form.descripcion.value.trim();
        const hoy = new Date();
        const fechaMinima = new Date(hoy.getFullYear() - 13, hoy.getMonth(), hoy.getDate());
    
        let valido = true;

        if (!nombre.match(/^[\p{L}\s\-]+$/u)) {
            mostrarError(form.nombre, 'Nombre inválido.');
            valido = false;
        }
        if (!apellido.match(/^[\p{L}\s\-]+$/u)) {
            mostrarError(form.apellido, 'Apellido inválido.');
            valido = false;
        }
        if (!username || username.length > 30) {
            mostrarError(form.username, 'Username obligatorio y máximo 30 caracteres.');
            valido = false;
        }
        if (fechaNacimiento) {
            const fecha = new Date(fechaNacimiento);
            if (fecha > fechaMinima) {
                mostrarError(form.fecha_nacimiento, 'Debes tener al menos 13 años.');
                valido = false;
            }
        }
        if (descripcion.length > 1000) {
            mostrarError(form.descripcion, 'Máximo 1000 caracteres.');
            valido = false;
        }        
    
        // Validación asincrónica del username solo si pasó validaciones anteriores
        if (valido) {
            const disponible = await checkUsernameAvailability(username, usernameActual);
            if (!disponible) {
                mostrarError(form.username, 'Este nombre de usuario ya está en uso.');
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Corrige los errores del formulario.' });
                return;
            }
        } else {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Corrige los errores del formulario.' });
            return;
        }        
    
        sendFormAjax(form);
    });    

    function sendFormAjax(form) {
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            if (!response.ok) {
                if (response.status === 422) {
                    const errorData = await response.json();
                    if (errorData.errors && errorData.errors.username) {
                        mostrarError(form.username, errorData.errors.username[0]);
                    }
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Corrige los errores del formulario.' });
                    return;
                }
                throw new Error('Error en la respuesta');
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;

            Swal.fire({ icon: 'success', title: '¡Éxito!', text: data.message, showConfirmButton: false, timer: 2000 });

            if (data.img !== undefined) {
                const newImgSrc = data.img ? '/' + data.img : '/img/profile_img/avatar-default.png';
                previewImg.src = newImgSrc;
                originalImage = newImgSrc;
                const layoutImg = document.querySelector('.sidebar img');
                if (layoutImg) layoutImg.src = newImgSrc;
            
                // Verificar si el botón existe
                let btn = document.getElementById('removePhotoBtn');
                if (data.img) {
                    if (!btn) {
                        btn = document.createElement('button');
                        btn.type = 'button';
                        btn.id = 'removePhotoBtn';
                        btn.className = 'btn btn-outline-secondary';
                        btn.textContent = 'Quitar foto';
            
                        btn.addEventListener('click', () => {
                            Swal.fire({
                                title: '¿Estás seguro?',
                                text: "Tu imagen de perfil será eliminada.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Sí, quitar',
                                cancelButtonText: 'Cancelar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    removeImgInput.value = '1';
                                    form.dataset.skipValidation = 'true';
            
                                    const defaultAvatar = '/img/profile_img/avatar-default.png';
                                    previewImg.src = defaultAvatar;
                                    fileInput.value = '';
                                    discardBtn.classList.add('d-none');
                                    downloadBtn.classList.add('d-none');
            
                                    const layoutImg = document.querySelector('.sidebar img');
                                    if (layoutImg) layoutImg.src = defaultAvatar;
            
                                    btn.remove(); // Ocultar el botón tras quitar
                                    form.dispatchEvent(new Event('submit', { cancelable: true }));
                                }
                            });
                        });
            
                        // Insertar el botón junto a los otros
                        const container = uploadBtn.parentElement;
                        container.appendChild(btn);
                    } else {
                        btn.classList.remove('d-none');
                    }
                } else if (btn) {
                    btn.remove(); // Si no hay imagen, ocultar el botón
                }
            }            

            if (data.username) {
                const layoutUsername = document.querySelector('.sidebar div:nth-child(2)');
                if (layoutUsername) layoutUsername.textContent = data.username;
            }

            if (data.nombre_completo) {
                const layoutNombre = document.querySelector('.sidebar .small');
                if (layoutNombre) layoutNombre.textContent = data.nombre_completo;
            }

            discardBtn.classList.add('d-none');
            downloadBtn.classList.add('d-none');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Hubo un error al actualizar los datos.' });
        });
    }
});
