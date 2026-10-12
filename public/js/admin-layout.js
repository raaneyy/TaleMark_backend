/**
 * admin-layout.js
 * Toggle sidebar di layar kecil (hamburguer menu)
 */

document.addEventListener('DOMContentLoaded', function () {

    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const btnOpen  = document.getElementById('btnHamburger');

    // Buka/tutup sidebar saat tombol hamburger diklik
    if (btnOpen) {
        btnOpen.addEventListener('click', function () {
            toggleSidebar();
        });
    }

    // Tutup sidebar saat overlay diklik
    if (overlay) {
        overlay.addEventListener('click', function () {
            closeSidebar();
        });
    }

    function toggleSidebar() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }

});
