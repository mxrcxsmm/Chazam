document.addEventListener('DOMContentLoaded', () => {
    const hamburgerButton = document.getElementById('hamburgerButton');
    const navbarNav = document.getElementById('navbarNav');

    hamburgerButton.addEventListener('click', () => {
        navbarNav.classList.toggle('show'); // Alternar la clase 'show' para abrir/cerrar el menú
    });
});