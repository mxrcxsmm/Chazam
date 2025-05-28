// Función para validar el formulario de registro

function validateSignUpForm() {
    const formSignup = document.querySelector('.form-signup');
    if (!formSignup) return;

    // Función para verificar disponibilidad
    async function checkAvailability(type, value) {
        try {
            const response = await fetch('/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type, value })
            });
            const data = await response.json();
            return data.available;
        } catch (error) {
            console.error('Error checking availability:', error);
            return false;
        }
    }

    // Configurar validaciones para cada campo
    const fields = {
        username: {
            element: document.getElementById('username'),
            validate: async (value) => {
                if (!value) return 'El nombre de usuario es requerido';
                if (value.length > 15) return 'El nombre de usuario no puede tener más de 15 caracteres';
                const available = await checkAvailability('username', value);
                if (!available) return 'Este nombre de usuario ya está en uso';
                return null;
            }
        },
        nombre: {
            element: document.getElementById('nombre'),
            validate: (value) => {
                if (!value) return 'El nombre es requerido';
                if (!/^[\p{L}\s\-]+$/u.test(value)) return 'El nombre solo puede contener letras, espacios y guiones';
                return null;
            }
        },
        apellido: {
            element: document.getElementById('apellido'),
            validate: (value) => {
                if (!value) return 'El apellido es requerido';
                if (!/^[\p{L}\s\-]+$/u.test(value)) return 'El apellido solo puede contener letras, espacios y guiones';
                return null;
            }
        },
        fecha_nacimiento: {
            element: document.getElementById('fecha_nacimiento'),
            validate: (value) => {
                if (!value) return 'La fecha de nacimiento es requerida';
                const fecha = new Date(value);
                const hoy = new Date();
                const edadMinima = new Date(hoy.getFullYear() - 13, hoy.getMonth(), hoy.getDate());
                if (fecha > edadMinima) return 'Debes tener al menos 13 años para registrarte';
                return null;
            }
        },
        email: {
            element: document.getElementById('email_signup'),
            validate: async (value) => {
                if (!value) return 'El email es requerido';
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'El email no es válido';
                const available = await checkAvailability('email', value);
                if (!available) return 'Este email ya está registrado';
                return null;
            }
        },
        password: {
            element: document.getElementById('password_signup'),
            validate: (value) => {
                if (!value) return 'La contraseña es requerida';
                if (value.length < 8) return 'La contraseña debe tener al menos 8 caracteres';
                if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(value)) return 'La contraseña debe contener al menos una mayúscula, una minúscula y un número';
                return null;
            }
        },
        password_confirmation: {
            element: document.getElementById('password_confirm'),
            validate: (value) => {
                if (!value) return 'Por favor, repite la contraseña';
                const password = document.getElementById('password_signup').value;
                if (value !== password) return 'Las contraseñas no coinciden';
                return null;
            }
        },
        nacionalidad: {
            element: document.getElementById('id_nacionalidad'),
            validate: (value) => {
                if (!value) return 'La nacionalidad es requerida';
                return null;
            }
        },
        genero: {
            element: document.getElementById('genero'),
            validate: (value) => {
                if (!value) return 'El género es requerido';
                if (!['hombre', 'mujer'].includes(value)) return 'El género debe ser Hombre o Mujer';
                return null;
            }
        }
    };

    // Función para mostrar errores
    function showError(input, message) {
        input.classList.add('invalid');
        
        let errorElement = input.parentElement.querySelector('.helper-text');
        if (!errorElement) {
            errorElement = document.createElement('span');
            errorElement.className = 'helper-text';
            input.parentElement.appendChild(errorElement);
        }
        errorElement.setAttribute('data-error', message);
    }

    // Función para limpiar errores
    function clearError(input) {
        input.classList.remove('invalid');
        const errorElement = input.parentElement.querySelector('.helper-text');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // Función para validar un campo
    async function validateField(fieldName) {
        const field = fields[fieldName];
        const value = field.element.value;
        const error = await field.validate(value);
        
        if (error) {
            showError(field.element, error);
            return false;
        } else {
            clearError(field.element);
            return true;
        }
    }

    // Añadir eventos de validación a cada campo
    Object.keys(fields).forEach(fieldName => {
        const field = fields[fieldName];
        
        // Validación al perder el foco
        field.element.addEventListener('blur', () => validateField(fieldName));
        
        // Validación mientras se escribe
        field.element.addEventListener('keyup', () => validateField(fieldName));
    });

    // Validación del formulario al enviar
    formSignup.addEventListener('submit', async function(e) {
        e.preventDefault();
        let isValid = true;
        
        // Validar todos los campos
        for (const fieldName of Object.keys(fields)) {
            if (!(await validateField(fieldName))) {
                isValid = false;
            }
        }

        if (isValid) {
            formSignup.submit();
        }
    });
}

// Inicializar el datepicker con las restricciones
function initDatepicker() {
    var today = new Date();
    var maxDate = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate() - 1);
    var elems = document.querySelectorAll('.datepicker');
    M.Datepicker.init(elems, {
        format: 'yyyy-mm-dd',
        yearRange: [1900, maxDate.getFullYear()],
        maxDate: maxDate,
        setDefaultDate: false,
        defaultDate: maxDate,
        i18n: {
            cancel: 'Cancelar',
            clear: 'Limpiar',
            done: 'Aceptar',
            months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            weekdays: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            weekdaysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
            weekdaysAbbrev: ['D','L','M','M','J','V','S']
        },
        onSelect: function(date) {
            // Formatea la fecha seleccionada y la pone en el input
            if (this.el) {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                this.el.value = `${yyyy}-${mm}-${dd}`;
            }
        }
    });
}

// Exportar las funciones para uso global
window.validateSignUpForm = validateSignUpForm;
window.initDatepicker = initDatepicker;

document.addEventListener('DOMContentLoaded', function() {
    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-content').forEach(el => el.style.display = 'none');
    }

    const loginContainer = document.querySelector('.login-container');
    
    // Función para manejar el cambio de ancho
    function handleWidthChange() {
        const activeTab = document.querySelector('.tabs .tab a.active');
        if (window.innerWidth > 600) {
            if (activeTab && activeTab.getAttribute('href') === '#signup') {
                loginContainer.classList.add('wider');
            } else {
                loginContainer.classList.remove('wider');
            }
        } else {
            loginContainer.classList.remove('wider');
        }
    }

    // Inicializar tabs de Materialize
    const materializeTabs = M.Tabs.init(document.querySelectorAll('.tabs'), {
        onShow: function(tab) {
            handleWidthChange();
            
            // Reiniciar componentes de Materialize
            setTimeout(() => {
                M.updateTextFields();
                M.FormSelect.init(document.querySelectorAll('select'));
            }, 50);
        }
    });

    // Manejar cambios de tamaño de ventana
    window.addEventListener('resize', handleWidthChange);

    // Aplicar el ancho inicial
    handleWidthChange();

    // Inicializar componentes
    initDatepicker();
    M.FormSelect.init(document.querySelectorAll('select'));
    M.CharacterCounter.init(document.querySelectorAll('textarea'));

    // Inicializar validaciones
    validateSignUpForm();

    // Inicializar Vanta.js
    VANTA.WAVES({
        el: "#vanta-bg",
        color: 0x703ea3,
        backgroundColor: 0xaa00ff
    });

    // Oculta los dropdowns de Materialize cuando sale un SweetAlert
    const observer = new MutationObserver(function(mutations) {
        const swalVisible = !!document.querySelector('.swal2-container');
        document.querySelectorAll('.dropdown-content').forEach(el => {
            el.style.display = swalVisible ? 'none' : '';
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // Forzar repaint REAL de autofill al cambiar de tab
    document.querySelectorAll('.tabs .tab a').forEach(tab => {
        tab.addEventListener('click', () => {
            setTimeout(() => {
                document.querySelectorAll('input').forEach(input => {
                    // Forzar reflow y trigger de autofill
                    input.value = input.value; // trigger repaint
                    
                    void input.offsetHeight;
                    input.style.display = '';
                });
            }, 100);
        });
    });

    // Mostrar SweetAlert si hay mensajes de error de sesión

    // --- SUBIR O TOMAR FOTO ---
    const uploadBtn = document.getElementById('uploadBtn');
    const cameraBtn = document.getElementById('cameraBtn');
    const imageInput = document.getElementById('imageInput');
    const previewImg = document.getElementById('preview-img');
    let videoStream = null;
    let modalInstance = null;

    // Inicializar el modal de la cámara
    const cameraModal = document.getElementById('cameraModal');
    if (cameraModal) {
        modalInstance = M.Modal.init(cameraModal, {
            dismissible: false,
            onCloseEnd: function() {
                if (videoStream) {
                    videoStream.getTracks().forEach(track => track.stop());
                }
            }
        });
    }

    // Función para mostrar la previsualización
    function showPreview(file) {
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '';
            previewImg.style.display = 'none';
        }
    }

    // Botón de subir foto
    if (uploadBtn && imageInput) {
        uploadBtn.addEventListener('click', (e) => {
            e.preventDefault();
            imageInput.click();
        });

        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                showPreview(file);
            }
        });
    }

    // Botón de tomar foto
    if (cameraBtn && modalInstance) {
        cameraBtn.addEventListener('click', (e) => {
            e.preventDefault();
            modalInstance.open();
            const video = document.getElementById('video');
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    videoStream = stream;
                    video.srcObject = stream;
                })
                .catch(err => {
                    console.error("Error al acceder a la cámara:", err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de cámara',
                        text: 'No se pudo acceder a la cámara. Por favor, asegúrate de dar los permisos necesarios.',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#703ea3'
                    });
                    modalInstance.close();
                });
        });

        // Botón de cerrar cámara
        document.getElementById('closeCamera').addEventListener('click', () => {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }
            modalInstance.close();
        });

        // Botón de capturar foto
        document.getElementById('captureBtn').addEventListener('click', () => {
            const video = document.getElementById('video');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            
            canvas.toBlob(blob => {
                // Crear un archivo para el input
                const file = new File([blob], 'foto_perfil.jpg', {type: 'image/jpeg'});
                
                // Asignar al input de archivo
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;

                // Actualizar nombre y previsualización
                showPreview(file);

                // Cerrar cámara
                if (videoStream) {
                    videoStream.getTracks().forEach(track => track.stop());
                }
                modalInstance.close();
            }, 'image/jpeg', 0.95);
        });
    }

    // Validación del formulario al enviar
    const formSignup = document.querySelector('.form-signup');
    if (formSignup) {
        formSignup.addEventListener('submit', function(e) {
            const file = imageInput.files[0];
            let errorMsg = '';
            if (file) {
                const validTypes = ['image/jpeg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    errorMsg = 'La imagen debe ser JPG o PNG.';
                } else if (file.size > 2 * 1024 * 1024) {
                    errorMsg = 'La imagen no puede superar los 2MB.';
                }
            }
            if (errorMsg) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Imagen no válida',
                    text: errorMsg,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#703ea3'
                });
                return false;
            }
        });
    }

    console.log('sweetAlertLoginError:', window.sweetAlertLoginError);

    // Mostrar SweetAlert si hay error de credenciales
    if (window.sweetAlertLoginError) {
        Swal.fire({
            icon: 'error',
            title: 'Error de acceso',
            text: window.sweetAlertLoginError,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#703ea3'
        });
    }
});