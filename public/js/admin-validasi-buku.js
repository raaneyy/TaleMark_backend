/**
 * admin-validasi-buku.js
 * Interaksi frontend untuk halaman Validasi & Kurasi Buku Manual
 * Mengatur filter tab, pencarian judul, dan pilih semua checkbox.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------------------------
    // 1. Inisialisasi Elemen DOM
    // ----------------------------------------------------------------------
    const tabButtons = document.querySelectorAll('.tab-btn');
    const searchInput = document.getElementById('searchBookTitle');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const bookCards = document.querySelectorAll('.book-card');
    const emptyState = document.getElementById('emptyState');

    // Kolom pencarian topbar (opsional/TODO backend)
    const topbarSearchInput = document.querySelector('.topbar-search-input');
    if (topbarSearchInput) {
        topbarSearchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                // TODO: sambungkan fitur pencarian global topbar ke route backend pencarian
            }
        });
    }

    // Variabel state filter aktif
    let currentStatus = 'all';
    let currentKeyword = '';

    // ----------------------------------------------------------------------
    // 2. Fungsi Utama Filter & Pencarian
    // ----------------------------------------------------------------------
    function applyFilters() {
        let visibleCount = 0;

        bookCards.forEach((card) => {
            const cardStatus = card.dataset.status || '';
            const cardTitleEl = card.querySelector('.book-title');
            const cardTitle = cardTitleEl ? cardTitleEl.textContent.toLowerCase() : '';

            // Cek kecocokan status tab
            const matchStatus = (currentStatus === 'all') || (cardStatus === currentStatus);

            // Cek kecocokan kata kunci judul (tidak peka huruf besar/kecil)
            const matchKeyword = currentKeyword === '' || cardTitle.includes(currentKeyword);

            if (matchStatus && matchKeyword) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Tampilkan pesan kosong jika tidak ada kartu yang cocok
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        // Sinkronkan kembali status checkbox pilih semua terhadap kartu yang tampak
        syncSelectAllState();
    }

    // ----------------------------------------------------------------------
    // 3. Event Listener: Tab Filter (Semua, Menunggu Review, Disetujui)
    // ----------------------------------------------------------------------
    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            // Hapus kelas aktif dari semua tab
            tabButtons.forEach((b) => b.classList.remove('active'));

            // Aktifkan tab yang diklik
            btn.classList.add('active');

            // Ambil nilai status dari atribut data-status
            currentStatus = btn.dataset.status || 'all';

            // Jalankan filter
            applyFilters();
        });
    });

    // ----------------------------------------------------------------------
    // 4. Event Listener: Pencarian Berdasarkan Judul
    // ----------------------------------------------------------------------
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentKeyword = e.target.value.trim().toLowerCase();
            applyFilters();
        });
    }

    // ----------------------------------------------------------------------
    // 5. Checkbox "Pilih Semua"
    // ----------------------------------------------------------------------
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            const isChecked = selectAllCheckbox.checked;

            // Centang/batalkan hanya checkbox pada kartu yang sedang tampil
            bookCards.forEach((card) => {
                if (card.style.display !== 'none') {
                    const cb = card.querySelector('.item-checkbox');
                    if (cb) {
                        cb.checked = isChecked;
                    }
                }
            });
        });
    }

    // Sinkronkan status "Pilih Semua" saat checkbox satuan diklik
    function syncSelectAllState() {
        if (!selectAllCheckbox) return;

        const visibleCheckboxes = [];
        bookCards.forEach((card) => {
            if (card.style.display !== 'none') {
                const cb = card.querySelector('.item-checkbox');
                if (cb) visibleCheckboxes.push(cb);
            }
        });

        if (visibleCheckboxes.length === 0) {
            selectAllCheckbox.checked = false;
            return;
        }

        const allChecked = visibleCheckboxes.every((cb) => cb.checked);
        selectAllCheckbox.checked = allChecked;
    }

    // Event listener untuk masing-masing checkbox buku
    bookCards.forEach((card) => {
        const cb = card.querySelector('.item-checkbox');
        if (cb) {
            cb.addEventListener('change', syncSelectAllState);
        }
    });

    // ----------------------------------------------------------------------
    // 6. Tombol Aksi (Tolak, Setujui, Bandingkan)
    //    Catatan: Sesuai instruksi, tombol belum dieksekusi ke backend
    // ----------------------------------------------------------------------
    bookCards.forEach((card) => {
        const bookId = card.dataset.id;

        // Tombol Bandingkan Duplikat
        const btnCompare = card.querySelector('.btn-compare');
        if (btnCompare) {
            btnCompare.addEventListener('click', (e) => {
                e.preventDefault();
                // TODO: sambungkan ke route backend untuk fitur perbandingan duplikat (id: bookId)
            });
        }

        // Tombol Tolak
        const btnReject = card.querySelector('.btn-reject');
        if (btnReject) {
            btnReject.addEventListener('click', (e) => {
                e.preventDefault();
                // TODO: sambungkan ke route backend untuk tolak pengajuan (id: bookId)
            });
        }

        // Tombol Setujui & Tambah ke Katalog
        const btnApprove = card.querySelector('.btn-approve');
        if (btnApprove) {
            btnApprove.addEventListener('click', (e) => {
                e.preventDefault();
                // TODO: sambungkan ke route backend untuk setujui & tambah ke katalog (id: bookId)
            });
        }
    });
});
