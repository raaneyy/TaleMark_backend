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
    const statCards = document.querySelectorAll('.stat-card');
    const filterBar = document.querySelector('.report-filter-bar');

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

    // Elemen Pop-up Konfirmasi Buka Blokir
    const unbanOverlay = document.getElementById('unbanOverlay');
    const unbanModal = document.getElementById('unbanModal');
    const unbanTargetUsername = document.getElementById('unbanTargetUsername');
    const btnUnbanBack = document.getElementById('btnUnbanBack');
    const btnCancelUnban = document.getElementById('btnCancelUnban');
    const btnConfirmUnban = document.getElementById('btnConfirmUnban');

    // Elemen Pop-up Kirim Peringatan (Kasih Warning)
    const warnOverlay = document.getElementById('warnOverlay');
    const warnModal = document.getElementById('warnModal');
    const warnTargetUsername = document.getElementById('warnTargetUsername');
    const btnWarnBack = document.getElementById('btnWarnBack');
    const btnCancelWarn = document.getElementById('btnCancelWarn');
    const warnForm = document.getElementById('warnForm');
    const warnMessage = document.getElementById('warnMessage');
    const warnMessageError = document.getElementById('warnMessageError');

    // Kontrol Filter Tambahan
    const selectUrutan = document.getElementById('selectUrutan');
    const selectKanal = document.getElementById('selectKanal');

    // --------------------------------------------------------------------------
    // 2. Filter Tab Kategori, Status, dan Interaksi Kartu Statistik
    // --------------------------------------------------------------------------

    // Helper: Sinkronisasi kartu statistik yang sedang aktif
    const syncActiveStatCard = (filterType) => {
        statCards.forEach((card, index) => {
            let target = card.getAttribute('data-stat-target');
            if (!target) {
                target = (index === 0) ? 'perlu-ditinjau' : (index === 1 ? 'banned' : 'selesai');
            }
            if (target === filterType) {
                card.classList.add('is-active');
            } else {
                card.classList.remove('is-active');
            }
        });
    };

    // Fungsi pemfilteran kartu laporan
    const applyFilter = (filterType) => {
        let visibleCount = 0;

        reportCards.forEach(card => {
            const kategori = card.getAttribute('data-kategori');
            const status = card.getAttribute('data-status');

            let isMatch = false;

            if (filterType === 'semua') {
                // Tab "Semua Laporan" menampilkan seluruh laporan yang ada tanpa terkecuali
                isMatch = true;
            } else if (filterType === 'perlu-ditinjau' || filterType === 'review') {
                // Kartu / Tab "Perlu Ditinjau" menampilkan laporan baru yang butuh review
                isMatch = (status === 'baru');
            } else if (filterType === 'spam') {
                // Tab "Spam & Bot"
                isMatch = (kategori === 'spam');
            } else if (filterType === 'sara') {
                // Tab "Kutipan SARA / Pelecehan"
                isMatch = (kategori === 'sara');
            } else if (filterType === 'banned') {
                // Tab "Akun Banned"
                isMatch = (status === 'banned');
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

        // Perbarui teks informasi berkas di footer sesuai hasil filter
        const footerInfo = document.querySelector('.report-footer-info');
        if (footerInfo) {
            footerInfo.innerHTML = `Menampilkan ${visibleCount} dari ${reportCards.length} berkas &bull; Pembaruan otomatis tiap 60 detik`;
        }
    };

    // Event listener pada tombol-tombol tab filter
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Ubah class active pada tombol tab
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const filterType = button.getAttribute('data-filter');
            applyFilter(filterType);
            syncActiveStatCard(filterType);
        });
    });

    // Interaksi klik pada 3 kartu statistik di bagian atas
    statCards.forEach((card, index) => {
        const handleStatCardClick = () => {
            let targetFilter = card.getAttribute('data-stat-target');
            if (!targetFilter) {
                targetFilter = (index === 0) ? 'perlu-ditinjau' : (index === 1 ? 'banned' : 'selesai');
            }
            const matchingTab = document.querySelector(`.report-tab-btn[data-filter="${targetFilter}"]`);

            if (matchingTab) {
                matchingTab.click();
            } else {
                applyFilter(targetFilter);
                syncActiveStatCard(targetFilter);
            }

            // Arahkan / scroll halaman secara halus ke bagian daftar di bawah
            if (filterBar) {
                const headerOffset = 80;
                const elementPosition = filterBar.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        };

        card.addEventListener('click', handleStatCardClick);

        // Aksesibilitas keyboard (Enter atau Spasi)
        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                handleStatCardClick();
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
        if (e.key === 'Escape') {
            if (banOverlay && banOverlay.classList.contains('is-active')) {
                closeBanModal();
            }
            if (unbanOverlay && unbanOverlay.classList.contains('is-active')) {
                closeUnbanModal();
            }
            if (warnOverlay && warnOverlay.classList.contains('is-active')) {
                closeWarnModal();
            }
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

            // Update status kartu di frontend secara reaktif
            if (targetUserId) {
                const targetCard = document.querySelector(`.report-card[data-id="${targetUserId}"]`);
                if (targetCard) {
                    targetCard.setAttribute('data-status', 'banned');
                    const actionsRow = targetCard.querySelector('.report-actions-row');
                    if (actionsRow) {
                        actionsRow.innerHTML = `
                            <div class="report-banned-container">
                                <div class="report-banned-text">
                                    <span class="report-banned-badge">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                        BANNED SEMENTARA
                                    </span>
                                    <span>Akses dibekukan hingga: <strong>${dateValue}, ${timeValue} WIB</strong></span>
                                </div>
                                <button type="button" class="btn-action btn-ignore btn-unban" data-id="${targetUserId}" data-username="${targetUsername || ''}" title="Buka penangguhan akun ini">
                                    Buka Blokir
                                </button>
                            </div>
                        `;
                        const newUnbanBtn = actionsRow.querySelector('.btn-unban');
                        if (newUnbanBtn) attachUnbanListener(newUnbanBtn);
                    }
                    const avatar = targetCard.querySelector('.report-avatar');
                    if (avatar) {
                        avatar.className = 'report-avatar avatar-banned';
                    }

                    // Sinkronkan kembali dengan filter yang sedang aktif
                    const activeTab = document.querySelector('.report-tab-btn.active');
                    const currentFilter = activeTab ? activeTab.getAttribute('data-filter') : 'semua';
                    applyFilter(currentFilter);
                }
            }

            // Tutup pop-up
            closeBanModal();
        });
    }

    // --------------------------------------------------------------------------
    // 4. Tombol Abaikan Laporan & Pop-up Kirim Peringatan (Kasih Warning)
    // --------------------------------------------------------------------------
    const ignoreButtons = document.querySelectorAll('.btn-ignore');
    ignoreButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.getAttribute('data-id');
            // TODO: sambungkan ke route backend untuk mengabaikan laporan akun ini (ID: userId)
        });
    });

    // Helper membersihkan error form peringatan
    const clearWarnErrors = () => {
        if (warnMessageError) warnMessageError.textContent = '';
        if (warnMessage) warnMessage.style.boxShadow = '';
    };

    // Fungsi membuka pop-up Kirim Peringatan
    const openWarnModal = (id, username) => {
        if (!warnOverlay || !warnModal) return;

        warnModal.setAttribute('data-id', id || '');
        warnModal.setAttribute('data-username', username || '');

        const formattedUsername = username ? (username.startsWith('@') ? username : `@${username}`) : '@pengguna';
        if (warnTargetUsername) {
            warnTargetUsername.textContent = formattedUsername;
        }

        if (warnMessage) warnMessage.value = '';
        clearWarnErrors();

        warnOverlay.classList.add('is-active');
        warnOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (warnMessage) warnMessage.focus();
        }, 60);
    };

    // Fungsi menutup pop-up Kirim Peringatan
    const closeWarnModal = () => {
        if (!warnOverlay) return;

        warnOverlay.classList.remove('is-active');
        warnOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        clearWarnErrors();
    };

    // Buka pop-up saat tombol "Kasih warning" diklik
    const warnButtons = document.querySelectorAll('.btn-warn');
    warnButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.report-card');
            const userId = btn.getAttribute('data-id') || (card ? card.getAttribute('data-id') : '');
            const username = btn.getAttribute('data-username') || (card ? card.getAttribute('data-username') : '');
            openWarnModal(userId, username);
        });
    });

    // Tutup pop-up lewat tombol panah kembali di pojok kiri atas
    if (btnWarnBack) {
        btnWarnBack.addEventListener('click', closeWarnModal);
    }

    // Tutup pop-up lewat tombol Batal
    if (btnCancelWarn) {
        btnCancelWarn.addEventListener('click', closeWarnModal);
    }

    // Tutup pop-up saat mengklik area latar buram di luar kotak modal
    if (warnOverlay) {
        warnOverlay.addEventListener('click', (e) => {
            if (e.target === warnOverlay) {
                closeWarnModal();
            }
        });

        // Focus trap: Tab berputar di dalam modal Kirim Peringatan
        warnOverlay.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab' || !warnOverlay.classList.contains('is-active')) return;

            const focusables = warnModal.querySelectorAll('button, textarea, input');
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

    // Bersihkan pesan error saat textarea mulai diubah
    if (warnMessage) {
        warnMessage.addEventListener('input', () => {
            clearWarnErrors();
        });
    }

    // Validasi form Kirim Peringatan saat submit
    if (warnForm) {
        warnForm.addEventListener('submit', (e) => {
            e.preventDefault();
            clearWarnErrors();

            const messageValue = warnMessage ? warnMessage.value.trim() : '';

            // Validasi di browser: pesan wajib diisi
            if (!messageValue) {
                if (warnMessageError) warnMessageError.textContent = 'Pesan peringatan wajib diisi';
                if (warnMessage) warnMessage.style.boxShadow = '0 0 0 2px var(--color-danger)';
                if (warnMessage) warnMessage.focus();
                return;
            }

            const targetUserId = warnModal.getAttribute('data-id');
            const targetUsername = warnModal.getAttribute('data-username');

            // TODO: sambungkan ke route backend (id akun, pesan peringatan)

            if (warnMessage) warnMessage.value = '';
            closeWarnModal();
        });
    }

    // --------------------------------------------------------------------------
    // 5. Pop-up Konfirmasi Buka Blokir (Unban)
    // --------------------------------------------------------------------------
    const openUnbanModal = (userId, username) => {
        if (!unbanOverlay || !unbanModal) return;

        unbanModal.setAttribute('data-id', userId || '');
        unbanModal.setAttribute('data-username', username || '');

        const formattedUsername = username ? (username.startsWith('@') ? username : `@${username}`) : '@pengguna';
        if (unbanTargetUsername) {
            unbanTargetUsername.textContent = formattedUsername;
        }

        unbanOverlay.classList.add('is-active');
        unbanOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (btnCancelUnban) btnCancelUnban.focus();
        }, 60);
    };

    const closeUnbanModal = () => {
        if (!unbanOverlay) return;

        unbanOverlay.classList.remove('is-active');
        unbanOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    function attachUnbanListener(btn) {
        if (!btn) return;
        btn.addEventListener('click', () => {
            const card = btn.closest('.report-card');
            const userId = btn.getAttribute('data-id') || (card ? card.getAttribute('data-id') : '');
            const username = btn.getAttribute('data-username') || (card ? card.getAttribute('data-username') : '');
            openUnbanModal(userId, username);
        });
    }

    document.querySelectorAll('.btn-unban').forEach(attachUnbanListener);

    // Tutup pop-up lewat tombol Kembali atau Batal
    if (btnUnbanBack) {
        btnUnbanBack.addEventListener('click', closeUnbanModal);
    }

    if (btnCancelUnban) {
        btnCancelUnban.addEventListener('click', closeUnbanModal);
    }

    // Tutup pop-up saat mengklik area latar buram di luar kotak modal
    if (unbanOverlay) {
        unbanOverlay.addEventListener('click', (e) => {
            if (e.target === unbanOverlay) {
                closeUnbanModal();
            }
        });
    }

    // Tombol "Ya, Buka Blokir"
    if (btnConfirmUnban) {
        btnConfirmUnban.addEventListener('click', () => {
            const targetUserId = unbanModal.getAttribute('data-id');
            const targetUsername = unbanModal.getAttribute('data-username');

            // TODO: sambungkan ke route backend (id akun, kosongkan banned_until, status_akun aktif)

            closeUnbanModal();
        });
    }

    // --------------------------------------------------------------------------
    // 6. Select Filter Pengurutan & Kanal Forum
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
