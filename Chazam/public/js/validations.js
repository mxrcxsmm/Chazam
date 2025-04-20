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
                if (value.length > 10) return 'El nombre de usuario no puede tener más de 10 caracteres';
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
    const datepickers = document.querySelectorAll('.datepicker');
    const hoy = new Date();
    const edadMinima = new Date(hoy.getFullYear() - 13, hoy.getMonth(), hoy.getDate());

    M.Datepicker.init(datepickers, {
        format: 'yyyy-mm-dd',
        yearRange: [edadMinima.getFullYear() - 100, edadMinima.getFullYear()],
        maxDate: edadMinima,
        defaultDate: edadMinima,
        setDefaultDate: true,
        autoClose: true,
        showClearBtn: false,
        i18n: {
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            weekdays: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
            weekdaysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
            weekdaysAbbrev: ["D", "L", "M", "M", "J", "V", "S"],
            cancel: 'Cancelar',
            clear: 'Limpiar',
            done: 'Aceptar'
        }
    });
}

// Exportar las funciones para uso global
window.validateSignUpForm = validateSignUpForm;
window.initDatepicker = initDatepicker; 