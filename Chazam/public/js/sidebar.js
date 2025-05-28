document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.style.left = '0';
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.style.left = '-260px';
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    function checkSidebar() {
        if (window.innerWidth < 768) {
            sidebar.style.left = '-260px';
        } else {
            sidebar.style.left = '0';
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    checkSidebar();

    toggleBtn.addEventListener('click', openSidebar);
    overlay.addEventListener('click', closeSidebar);
    window.addEventListener('resize', checkSidebar);
});