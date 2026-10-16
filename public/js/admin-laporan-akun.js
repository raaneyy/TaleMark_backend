/**
 * public/js/admin-laporan-akun.js
 * Logika frontend sederhana untuk halaman Laporan Akun & Tinjau Komunitas
 * TaleMark Curatorial Desk (Dibuat untuk kebutuhan tugas TRPL)
 */

document.addEventListener('DOMContentLoaded', () => {
    // --------------------------------------------------------------------------
    // 1. Elemen DOM Utama
    // --------------------------------------------------------------------------
    const tabButtons = document.querySelectorAll('.report-tab-btn');
    const reportCards = document.querySelectorAll('.report-card');
    const emptyState = document.getElementById('reportEmptyState');

    // Elemen Pop-up Banned Duration Baru
    const banOverlay = document.getElementById('banOverlay');
    const banModal = document.getElementById('banModal');
    const btnBanBack = document.getElementById('btnBanBack');
    const banForm = document.getElementById('banForm');
    const banDate = document.getElementById('banDate');
    const banTime = document.getElementById('banTime');
    const dateError = document.getElementById('dateError');
    const timeError = document.getElementById('timeError');
    const banButtons = document.querySelectorAll('.btn-ban');

    // Kontrol Filter Tambahan
    const selectUrutan = document.getElementById('selectUrutan');
    const selectKanal = document.getElementById('selectKanal');

    // --------------------------------------------------------------------------
    // 2. Filter Tab Kategori dan Status
    // --------------------------------------------------------------------------
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Ubah class active pada tombol tab
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const filterType = button.getAttribute('data-filter');
            let visibleCount = 0;

            reportCards.forEach(card => {
                const kategori = card.getAttribute('data-kategori');
                const status = card.getAttribute('data-status');

                let isMatch = false;

                if (filterType === 'semua') {
                    // Tab "Semua" menampilkan seluruh laporan yang masih berstatus baru
                    isMatch = (status === 'baru');
                } else if (filterType === 'spam') {
                    // Tab "Spam & Bot"
                    isMatch = (kategori === 'spam' && status === 'baru');
                } else if (filterType === 'sara') {
                    // Tab "Kutipan SARA / Pelecehan"
                    isMatch = (kategori === 'sara' && status === 'baru');
                } else if (filterType === 'selesai') {
                    // Tab "Selesai"
                    isMatch = (status === 'selesai');
                }

                if (isMatch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Tampilkan pesan kosong jika tidak ada laporan pada kategori ini
            if (emptyState) {
                emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        });
    });

    // --------------------------------------------------------------------------
    // 3. Pop-up Banned Duration (Interaksi & Validasi)
    // --------------------------------------------------------------------------

    // Helper: Membersihkan pesan error validasi
    const clearErrors = () => {
        if (dateError) dateError.textContent = '';
        if (timeError) timeError.textContent = '';
        if (banDate) banDate.style.boxShadow = '';
        if (banTime) banTime.style.boxShadow = '';
    };

    // Fungsi membuka pop-up
    const openBanModal = (id, username) => {
        if (!banOverlay || !banModal) return;

        // Simpan username dan ID akun di atribut data modal
        banModal.setAttribute('data-id', id || '');
        banModal.setAttribute('data-username', username || '');

        // Kosongkan isian tanggal dan jam setiap kali pop-up dibuka
        if (banDate) banDate.value = '';
        if (banTime) banTime.value = '';
        clearErrors();

        // Hitung tanggal hari ini dalam format YYYY-MM-DD untuk atribut min
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        if (banDate) {
            banDate.min = `${year}-${month}-${day}`;
        }

        // Tampilkan pop-up dengan efek latar buram
        banOverlay.classList.add('is-active');
        banOverlay.setAttribute('aria-hidden', 'false');

        // Kunci scroll halaman belakang
        document.body.style.overflow = 'hidden';

        // Pindahkan fokus otomatis ke input tanggal
        setTimeout(() => {
            if (banDate) banDate.focus();
        }, 60);
    };

    // Fungsi menutup pop-up
    const closeBanModal = () => {
        if (!banOverlay) return;

        banOverlay.classList.remove('is-active');
        banOverlay.setAttribute('aria-hidden', 'true');

        // Kembalikan scroll halaman
        document.body.style.overflow = '';
        clearErrors();
    };

    // Buka pop-up saat tombol "Banned (durasi)" diklik pada kartu
    banButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.getAttribute('data-id') || '';
            const username = btn.getAttribute('data-username') || '';
            openBanModal(userId, username);
        });
    });

    // Tutup pop-up lewat tombol panah kiri di pojok kiri atas
    if (btnBanBack) {
        btnBanBack.addEventListener('click', closeBanModal);
    }

    // Tutup pop-up saat mengklik area latar buram di luar kotak modal
    if (banOverlay) {
        banOverlay.addEventListener('click', (e) => {
            if (e.target === banOverlay) {
                closeBanModal();
            }
        });
    }

    // Tutup pop-up saat menekan tombol Escape (Esc)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && banOverlay && banOverlay.classList.contains('is-active')) {
            closeBanModal();
        }
    });

    // Focus trap sederhana: Tab tetap berputar di dalam modal selama pop-up terbuka
    if (banOverlay) {
        banOverlay.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab' || !banOverlay.classList.contains('is-active')) return;

            const focusables = banModal.querySelectorAll('button, input');
            if (focusables.length === 0) return;

            const firstElement = focusables[0];
            const lastElement = focusables[focusables.length - 1];

            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        });
    }

    // Hilangkan pesan error saat user mulai mengetik / mengubah input tanggal
    if (banDate) {
        banDate.addEventListener('input', () => {
            if (dateError) dateError.textContent = '';
            banDate.style.boxShadow = '';
            // Jika sebelumnya ada error waktu lewat, bersihkan juga
            if (timeError && timeError.textContent.includes('setelah')) {
                timeError.textContent = '';
                if (banTime) banTime.style.boxShadow = '';
            }
        });
    }

    // Hilangkan pesan error saat user mulai mengetik / mengubah input jam
    if (banTime) {
        banTime.addEventListener('input', () => {
            if (timeError) timeError.textContent = '';
            banTime.style.boxShadow = '';
        });
    }

    // Validasi form saat tombol "Simpan" ditekan
    if (banForm) {
        banForm.addEventListener('submit', (e) => {
            e.preventDefault();
            clearErrors();

            const dateValue = banDate ? banDate.value.trim() : '';
            const timeValue = banTime ? banTime.value.trim() : '';
            let hasError = false;

            // 1. Cek apakah tanggal kosong
            if (!dateValue) {
                if (dateError) dateError.textContent = 'Tanggal wajib diisi';
                if (banDate) banDate.style.boxShadow = '0 0 0 2px var(--color-danger)';
                hasError = true;
            }

            // 2. Cek apakah jam kosong
            if (!timeValue) {
                if (timeError) timeError.textContent = 'Jam wajib diisi';
                if (banTime) banTime.style.boxShadow = '0 0 0 2px var(--color-danger)';
                hasError = true;
            }

            // Jika ada field kosong, jangan tutup pop-up
            if (hasError) return;

            // 3. Cek apakah gabungan tanggal dan jam sudah lewat dari waktu sekarang
            const targetDateTime = new Date(`${dateValue}T${timeValue}`);
            const currentDateTime = new Date();

            if (isNaN(targetDateTime.getTime()) || targetDateTime <= currentDateTime) {
                if (timeError) timeError.textContent = 'Waktu banned harus setelah waktu sekarang';
                if (banTime) banTime.style.boxShadow = '0 0 0 2px var(--color-danger)';
                return;
            }

            // Data valid: simpan data target
            const targetUserId = banModal.getAttribute('data-id');
            const targetUsername = banModal.getAttribute('data-username');

            // TODO: sambungkan ke route backend (id akun: targetUserId, username: targetUsername, tanggal banned_until: dateValue, jam: timeValue)

            // Tutup pop-up
            closeBanModal();
        });
    }

    // --------------------------------------------------------------------------
    // 4. Tombol Abaikan Laporan & Kasih Warning
    // --------------------------------------------------------------------------
    const ignoreButtons = document.querySelectorAll('.btn-ignore');
    ignoreButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.getAttribute('data-id');
            // TODO: sambungkan ke route backend untuk mengabaikan laporan akun ini (ID: userId)
        });
    });

    const warnButtons = document.querySelectorAll('.btn-warn');
    warnButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.getAttribute('data-id');
            // TODO: sambungkan ke route backend untuk mengirimkan warning ke akun ini (ID: userId)
        });
    });

    // --------------------------------------------------------------------------
    // 5. Select Filter Pengurutan & Kanal Forum
    // --------------------------------------------------------------------------
    if (selectUrutan) {
        selectUrutan.addEventListener('change', () => {
            // TODO: sambungkan fitur pengurutan (prioritas, terbaru, terbanyak dilaporkan) ke backend
        });
    }

    if (selectKanal) {
        selectKanal.addEventListener('change', () => {
            // TODO: sambungkan filter kanal forum (semua kanal, feed sastra, dsb) ke backend
        });
    }
});
