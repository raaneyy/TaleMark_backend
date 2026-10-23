/**
 * admin-layout.js
 * Toggle sidebar di layar kecil (hamburger menu) & Modal Teleport
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

    // Pindahkan semua elemen overlay/modal ke <body> agar posisinya mutlak relatif terhadap viewport layar penuh dan muncul tepat di tengah layar
    function teleportModalsToBody() {
        const modalSelectors = [
            '.ban-overlay',
            '.unban-overlay',
            '.warn-overlay',
            '.book-modal-overlay',
            '.modal-overlay',
            '.curation-modal'
        ];
        modalSelectors.forEach(function (selector) {
            document.querySelectorAll(selector).forEach(function (el) {
                if (el && el.parentElement !== document.body) {
                    document.body.appendChild(el);
                }
            });
        });
    }

    teleportModalsToBody();

});
