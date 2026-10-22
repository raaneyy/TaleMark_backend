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

    // Fungsi reset centang semua checkbox
    function resetSelection() {
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }
        bookCards.forEach((card) => {
            const cb = card.querySelector('.item-checkbox');
            if (cb) {
                cb.checked = false;
            }
        });
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

            // Reset centang pilihan saat berpindah tab
            resetSelection();

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
    // 6. Toast Notifikasi
    // ----------------------------------------------------------------------
    let toastTimeout = null;
    const toastNotification = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');

    function showToast(message) {
        if (!toastNotification || !toastMessage) return;

        toastMessage.textContent = message;
        toastNotification.classList.add('is-visible');

        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }

        toastTimeout = setTimeout(() => {
            toastNotification.classList.remove('is-visible');
        }, 3000);
    }

    // Helper pembaruan angka badge pada tab filter
    function updateBadgeCount(status, change) {
        const tab = document.querySelector(`.tab-btn[data-status="${status}"] .tab-badge`);
        if (tab) {
            const currentVal = parseInt(tab.textContent.trim(), 10) || 0;
            tab.textContent = Math.max(0, currentVal + change);
        }
    }

    // ----------------------------------------------------------------------
    // 7. Elemen & Logika Pop-up "Setujui & Tambah ke Katalog"
    // ----------------------------------------------------------------------
    const approveModalOverlay = document.getElementById('approveModalOverlay');
    const approveModal = document.getElementById('approveModal');
    const btnApproveBack = document.getElementById('btnApproveBack');
    const btnApproveCancel = document.getElementById('btnApproveCancel');
    const btnApproveConfirm = document.getElementById('btnApproveConfirm');
    const approveSummaryCover = document.getElementById('approveSummaryCover');
    const approveSummaryTitle = document.getElementById('approveSummaryTitle');
    const approveSummaryAuthor = document.getElementById('approveSummaryAuthor');
    const approveSummaryIsbn = document.getElementById('approveSummaryIsbn');
    const approveSummaryPengaju = document.getElementById('approveSummaryPengaju');

    let currentApproveCard = null;

    function openApproveModal(card) {
        if (!approveModalOverlay || !approveModal) return;
        currentApproveCard = card;

        const id = card.dataset.id || '';
        const judul = card.dataset.judul || card.querySelector('.book-title')?.textContent.trim() || '';
        const penulis = card.dataset.penulis || card.querySelector('.book-author')?.textContent.trim() || '';
        const isbn = card.dataset.isbn || card.querySelector('.info-isbn')?.textContent.trim() || '';
        const pengaju = card.dataset.pengaju || card.querySelectorAll('.info-col')[1]?.querySelector('.info-value')?.textContent.trim() || '';
        const sampul = card.dataset.sampul || '';

        approveModal.setAttribute('data-id', id);

        if (approveSummaryTitle) approveSummaryTitle.textContent = judul;
        if (approveSummaryAuthor) approveSummaryAuthor.textContent = penulis;
        if (approveSummaryIsbn) approveSummaryIsbn.textContent = isbn;
        if (approveSummaryPengaju) approveSummaryPengaju.textContent = pengaju;

        if (approveSummaryCover) {
            if (sampul) {
                approveSummaryCover.innerHTML = `<img src="${sampul}" alt="Sampul ${judul}">`;
            } else {
                approveSummaryCover.innerHTML = `<div class="book-summary-cover-placeholder"><span>${judul}</span></div>`;
            }
        }

        approveModalOverlay.classList.add('is-active');
        approveModalOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (btnApproveCancel) btnApproveCancel.focus();
        }, 60);
    }

    function closeApproveModal() {
        if (!approveModalOverlay) return;
        approveModalOverlay.classList.remove('is-active');
        approveModalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        currentApproveCard = null;
    }

    // ----------------------------------------------------------------------
    // 8. Elemen & Logika Pop-up "Tolak Pengajuan"
    // ----------------------------------------------------------------------
    const rejectModalOverlay = document.getElementById('rejectModalOverlay');
    const rejectModal = document.getElementById('rejectModal');
    const btnRejectBack = document.getElementById('btnRejectBack');
    const btnRejectCancel = document.getElementById('btnRejectCancel');
    const rejectForm = document.getElementById('rejectForm');
    const rejectReason = document.getElementById('rejectReason');
    const rejectReasonError = document.getElementById('rejectReasonError');
    const rejectNotes = document.getElementById('rejectNotes');
    const rejectSummaryTitle = document.getElementById('rejectSummaryTitle');
    const rejectSummaryPengaju = document.getElementById('rejectSummaryPengaju');

    let currentRejectCard = null;

    function openRejectModal(card) {
        if (!rejectModalOverlay || !rejectModal) return;
        currentRejectCard = card;

        const id = card.dataset.id || '';
        const judul = card.dataset.judul || card.querySelector('.book-title')?.textContent.trim() || '';
        const pengaju = card.dataset.pengaju || card.querySelectorAll('.info-col')[1]?.querySelector('.info-value')?.textContent.trim() || '';

        rejectModal.setAttribute('data-id', id);

        if (rejectSummaryTitle) rejectSummaryTitle.textContent = judul;
        if (rejectSummaryPengaju) rejectSummaryPengaju.textContent = pengaju;

        if (rejectReason) rejectReason.value = '';
        if (rejectNotes) rejectNotes.value = '';
        if (rejectReasonError) rejectReasonError.textContent = '';
        if (rejectReason) rejectReason.style.boxShadow = '';

        rejectModalOverlay.classList.add('is-active');
        rejectModalOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (rejectReason) rejectReason.focus();
        }, 60);
    }

    function closeRejectModal() {
        if (!rejectModalOverlay) return;
        rejectModalOverlay.classList.remove('is-active');
        rejectModalOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        currentRejectCard = null;
        if (rejectReasonError) rejectReasonError.textContent = '';
        if (rejectReason) rejectReason.style.boxShadow = '';
    }

    // ----------------------------------------------------------------------
    // 9. Event Listener Tombol Kartu & Modal Action Handlers
    // ----------------------------------------------------------------------
    bookCards.forEach((card) => {
        // Tombol Bandingkan Duplikat (tetap seperti sekarang)
        const btnCompare = card.querySelector('.btn-compare');
        if (btnCompare) {
            btnCompare.addEventListener('click', (e) => {
                e.preventDefault();
                // TODO: sambungkan ke route backend untuk fitur perbandingan duplikat (id: card.dataset.id)
            });
        }

        // Tombol Tolak
        const btnReject = card.querySelector('.btn-reject');
        if (btnReject) {
            btnReject.addEventListener('click', (e) => {
                e.preventDefault();
                openRejectModal(card);
            });
        }

        // Tombol Setujui & Tambah ke Katalog
        const btnApprove = card.querySelector('.btn-approve');
        if (btnApprove) {
            btnApprove.addEventListener('click', (e) => {
                e.preventDefault();
                openApproveModal(card);
            });
        }
    });

    // Eksekusi Konfirmasi "Ya, Tambahkan"
    if (btnApproveConfirm) {
        btnApproveConfirm.addEventListener('click', () => {
            if (!currentApproveCard) return;

            const card = currentApproveCard;

            // Tutup pop-up
            closeApproveModal();

            // Ubah kartu menjadi status disetujui
            card.dataset.status = 'disetujui';
            card.setAttribute('data-status', 'disetujui');

            // Badge jadi "DISETUJUI" hijau
            const pill = card.querySelector('.status-pill');
            if (pill) {
                pill.textContent = 'DISETUJUI';
                pill.className = 'status-pill status-approved';
            }

            // Sembunyikan tombol Tolak, Setujui, dan Bandingkan Duplikat, ganti dengan teks abu
            const actionLeft = card.querySelector('.action-left');
            if (actionLeft) actionLeft.style.display = 'none';

            const actionRight = card.querySelector('.action-right');
            if (actionRight) {
                actionRight.innerHTML = `
                    <span class="text-approved">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Sudah ditambahkan ke katalog
                    </span>
                `;
            }

            // Kurangi angka tab "Menunggu Review" dan tambah angka tab "Disetujui"
            updateBadgeCount('menunggu', -1);
            updateBadgeCount('disetujui', 1);

            // Tampilkan toast kecil di pojok kanan atas
            showToast('Buku ditambahkan ke katalog (simulasi tampilan)');

            // Perbarui filter kartu jika sedang di tab aktif
            applyFilters();

            // TODO: sambungkan ke route backend (id buku, status_validasi = disetujui)
        });
    }

    // Validasi & Eksekusi "Tolak Pengajuan"
    if (rejectReason) {
        rejectReason.addEventListener('change', () => {
            if (rejectReasonError) rejectReasonError.textContent = '';
            rejectReason.style.boxShadow = '';
        });
    }

    if (rejectForm) {
        rejectForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // Validasi di browser: alasan wajib dipilih
            if (!rejectReason || !rejectReason.value) {
                if (rejectReasonError) rejectReasonError.textContent = 'Pilih alasan penolakan';
                if (rejectReason) {
                    rejectReason.style.boxShadow = '0 0 0 2px var(--color-danger)';
                    rejectReason.focus();
                }
                return;
            }

            if (!currentRejectCard) return;

            const card = currentRejectCard;
            const selectedReason = rejectReason.value;

            // Tutup pop-up
            closeRejectModal();

            // Ubah kartu menjadi status ditolak
            card.dataset.status = 'ditolak';
            card.setAttribute('data-status', 'ditolak');

            // Badge "DITOLAK" merah lembut
            const pill = card.querySelector('.status-pill');
            if (pill) {
                pill.textContent = 'DITOLAK';
                pill.className = 'status-pill status-rejected';
            }

            // Sembunyikan tombol aksi, tampilkan teks abu "Ditolak: {alasan}"
            const actionLeft = card.querySelector('.action-left');
            if (actionLeft) actionLeft.style.display = 'none';

            const actionRight = card.querySelector('.action-right');
            if (actionRight) {
                const escapedReason = document.createElement('div');
                escapedReason.textContent = selectedReason;

                actionRight.innerHTML = `
                    <span class="text-rejected">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                        Ditolak: ${escapedReason.innerHTML}
                    </span>
                `;
            }

            // Kurangi angka tab "Menunggu Review"
            updateBadgeCount('menunggu', -1);

            // Tampilkan toast
            showToast('Pengajuan ditolak (simulasi tampilan)');

            // Perbarui filter kartu (kartu ditolak tidak tampil di "Menunggu Review" atau "Disetujui", tetap di "Semua")
            applyFilters();

            // TODO: sambungkan ke route backend (id buku, alasan, catatan)
        });
    }

    // ----------------------------------------------------------------------
    // 10. Kontrol Penutupan Modal & Focus Trap Aksesibilitas
    // ----------------------------------------------------------------------
    if (btnApproveBack) btnApproveBack.addEventListener('click', closeApproveModal);
    if (btnApproveCancel) btnApproveCancel.addEventListener('click', closeApproveModal);
    if (btnRejectBack) btnRejectBack.addEventListener('click', closeRejectModal);
    if (btnRejectCancel) btnRejectCancel.addEventListener('click', closeRejectModal);

    // Tutup saat klik latar buram di luar modal
    if (approveModalOverlay) {
        approveModalOverlay.addEventListener('click', (e) => {
            if (e.target === approveModalOverlay) closeApproveModal();
        });
    }

    if (rejectModalOverlay) {
        rejectModalOverlay.addEventListener('click', (e) => {
            if (e.target === rejectModalOverlay) closeRejectModal();
        });
    }

    // Tutup saat menekan tombol Escape (Esc)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (approveModalOverlay && approveModalOverlay.classList.contains('is-active')) {
                closeApproveModal();
            }
            if (rejectModalOverlay && rejectModalOverlay.classList.contains('is-active')) {
                closeRejectModal();
            }
        }
    });

    // Focus trap untuk kedua modal
    function setupFocusTrap(overlay, modal) {
        if (!overlay || !modal) return;
        overlay.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab' || !overlay.classList.contains('is-active')) return;

            const focusables = modal.querySelectorAll('button, select, textarea, input');
            if (focusables.length === 0) return;

            const firstEl = focusables[0];
            const lastEl = focusables[focusables.length - 1];

            if (e.shiftKey && document.activeElement === firstEl) {
                e.preventDefault();
                lastEl.focus();
            } else if (!e.shiftKey && document.activeElement === lastEl) {
                e.preventDefault();
                firstEl.focus();
            }
        });
    }

    setupFocusTrap(approveModalOverlay, approveModal);
    setupFocusTrap(rejectModalOverlay, rejectModal);
});
