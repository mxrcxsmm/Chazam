document.addEventListener('DOMContentLoaded', function () {
    const thumbs = document.querySelectorAll('.marco-option');
    const input = document.getElementById('borde_overlay');
    const avatarWrapper = document.querySelector('.marco-externo');
    const picker = document.getElementById('glowColorPicker');
    const hiddenInput = document.getElementById('glow_color');
    const label = document.getElementById('colorValueLabel');
    const sidebarPicker = document.getElementById('sidebarColorPicker');
const sidebarLabel = document.getElementById('sidebarColorValueLabel');
const sidebar = document.querySelector('.sidebar');

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
            avatarWrapper.style.backgroundImage = `url('/IMG/bordes/${file}')`;
        });
    });
    

    const actual = input.value;
    document.querySelector(`.marco-option[data-marco="${actual}"]`)?.classList.add('selected');

    // Color dinámico
    picker.addEventListener('input', () => {
        const color = picker.value;
        avatarWrapper.style.setProperty('--glow-color', color);
        hiddenInput.value = color;
        label.textContent = color;
    });

    // Animación rotatoria (solo frontend)
    document.querySelectorAll('input[name="rotativo_temp"]').forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === "1" && radio.checked) {
                avatarWrapper.classList.add('marco-rotate');
            } else if (radio.value === "0" && radio.checked) {
                avatarWrapper.classList.remove('marco-rotate');
            }
        });
    });
});