/**
 * public/js/admin-laporan-akun.js
 * Logika frontend untuk halaman Laporan Akun & Tinjau Komunitas TaleMark
 * Mengelola kartu pemilih (Laporan Akun & Postingan), filter status,
 * pop-up Banned (dengan checkbox permanen), Kasih Warning, Buka Blokir, dan Toast notifikasi.
 */

document.addEventListener('DOMContentLoaded', () => {
    // --------------------------------------------------------------------------
    // 1. Elemen DOM Utama
    // --------------------------------------------------------------------------
    // Dua kartu pemilih
    const tabLaporanAkun = document.getElementById('tabLaporanAkun');
    const tabLaporanPostingan = document.getElementById('tabLaporanPostingan');
    const sectionLaporanAkun = document.getElementById('sectionLaporanAkun');
    const sectionLaporanPostingan = document.getElementById('sectionLaporanPostingan');

    // Filter status & pesan kosong
    const selectStatus = document.getElementById('selectStatus');
    const selectUrutan = document.getElementById('selectUrutan');
    const selectKanal = document.getElementById('selectKanal');
    const emptyState = document.getElementById('reportEmptyState');

    // Toast Notifikasi
    const toastNotification = document.getElementById('toastNotification');
    const toastMessage = document.getElementById('toastMessage');
    let toastTimeout = null;

    /**
     * Tampilkan notifikasi toast di pojok kanan atas
     * @param {string} message
     */
    const showToast = (message) => {
        if (!toastNotification || !toastMessage) return;
        toastMessage.textContent = message;
        toastNotification.classList.add('is-visible');

        if (toastTimeout) {
            clearTimeout(toastTimeout);
        }
        toastTimeout = setTimeout(() => {
            toastNotification.classList.remove('is-visible');
        }, 3200);
    };

    // Elemen Pop-up Banned Duration (dengan checkbox permanen)
    const banOverlay = document.getElementById('banOverlay');
    const banModal = document.getElementById('banModal');
    const btnBanBack = document.getElementById('btnBanBack');
    const banForm = document.getElementById('banForm');
    const banDate = document.getElementById('banDate');
    const banTime = document.getElementById('banTime');
    const banPermanent = document.getElementById('banPermanent');
    const banFieldsGrid = document.getElementById('banFieldsGrid');
    const dateError = document.getElementById('dateError');
    const timeError = document.getElementById('timeError');

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

    // --------------------------------------------------------------------------
    // 2. Pemfilteran Kartu Berdasarkan Status
    // --------------------------------------------------------------------------
    const applyStatusFilter = () => {
        const isAkunActive = tabLaporanAkun && tabLaporanAkun.classList.contains('is-active');
        const activeContainer = isAkunActive ? sectionLaporanAkun : sectionLaporanPostingan;
        const inactiveContainer = isAkunActive ? sectionLaporanPostingan : sectionLaporanAkun;

        if (inactiveContainer) {
            inactiveContainer.style.display = 'none';
        }
        if (activeContainer) {
            activeContainer.style.display = 'flex';
        }

        const selectedValue = selectStatus ? selectStatus.value : 'semua';
        const cards = activeContainer ? activeContainer.querySelectorAll('.report-card') : [];
        let visibleCount = 0;

        cards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            let isMatch = false;

            if (selectedValue === 'semua') {
                isMatch = true;
            } else if (selectedValue === 'baru') {
                isMatch = (cardStatus === 'baru');
            } else if (selectedValue === 'banned') {
                isMatch = (cardStatus === 'banned');
            } else if (selectedValue === 'selesai') {
                isMatch = (cardStatus === 'selesai');
            }

            if (isMatch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    };

    // Jalankan pemfilteran awal saat halaman pertama kali dimuat
    applyStatusFilter();

    // Event listener saat dropdown status diubah
    if (selectStatus) {
        selectStatus.addEventListener('change', applyStatusFilter);
    }

    // --------------------------------------------------------------------------
    // 3. Pengalihan Dua Kartu Pemilih (Laporan Akun & Laporan Postingan)
    // --------------------------------------------------------------------------
    const switchTab = (targetTab) => {
        if (!tabLaporanAkun || !tabLaporanPostingan) return;

        if (targetTab === 'akun') {
            tabLaporanAkun.classList.add('is-active');
            tabLaporanAkun.setAttribute('aria-selected', 'true');

            tabLaporanPostingan.classList.remove('is-active');
            tabLaporanPostingan.setAttribute('aria-selected', 'false');
        } else {
            tabLaporanPostingan.classList.add('is-active');
            tabLaporanPostingan.setAttribute('aria-selected', 'true');

            tabLaporanAkun.classList.remove('is-active');
            tabLaporanAkun.setAttribute('aria-selected', 'false');
        }

        applyStatusFilter();
    };

    if (tabLaporanAkun) {
        tabLaporanAkun.addEventListener('click', () => switchTab('akun'));
    }

    if (tabLaporanPostingan) {
        tabLaporanPostingan.addEventListener('click', () => switchTab('postingan'));
    }

    // --------------------------------------------------------------------------
    // 4. Pop-up Banned Duration (Interaksi, Checkbox Permanen, & Validasi)
    // --------------------------------------------------------------------------
    const clearBanErrors = () => {
        if (dateError) dateError.textContent = '';
        if (timeError) timeError.textContent = '';
        if (banDate) banDate.style.boxShadow = '';
        if (banTime) banTime.style.boxShadow = '';
    };

    const openBanModal = (id, username) => {
        if (!banOverlay || !banModal) return;

        banModal.setAttribute('data-id', id || '');
        banModal.setAttribute('data-username', username || '');

        if (banDate) {
            banDate.value = '';
            banDate.disabled = false;
        }
        if (banTime) {
            banTime.value = '';
            banTime.disabled = false;
        }
        if (banPermanent) {
            banPermanent.checked = false;
        }
        if (banFieldsGrid) {
            banFieldsGrid.classList.remove('is-disabled');
        }
        clearBanErrors();

        // Tetapkan tanggal hari ini sebagai batas minimum
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        if (banDate) {
            banDate.min = `${year}-${month}-${day}`;
        }

        banOverlay.classList.add('is-active');
        banOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (banDate) banDate.focus();
        }, 60);
    };

    const closeBanModal = () => {
        if (!banOverlay) return;

        banOverlay.classList.remove('is-active');
        banOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        clearBanErrors();
    };

    // Toggle disable date/time saat checkbox permanen dicentang
    if (banPermanent) {
        banPermanent.addEventListener('change', () => {
            const isPermanent = banPermanent.checked;
            clearBanErrors();

            if (banDate) banDate.disabled = isPermanent;
            if (banTime) banTime.disabled = isPermanent;

            if (banFieldsGrid) {
                if (isPermanent) {
                    banFieldsGrid.classList.add('is-disabled');
                } else {
                    banFieldsGrid.classList.remove('is-disabled');
                }
            }
        });
    }

    // Buka pop-up saat tombol Banned (durasi) diklik
    const attachBanListeners = () => {
        document.querySelectorAll('.btn-ban').forEach(btn => {
            btn.addEventListener('click', () => {
                const card = btn.closest('.report-card');
                const userId = btn.getAttribute('data-id') || (card ? card.getAttribute('data-id') : '');
                const username = btn.getAttribute('data-username') || (card ? card.getAttribute('data-username') : '');
                openBanModal(userId, username);
            });
        });
    };
    attachBanListeners();

    if (btnBanBack) {
        btnBanBack.addEventListener('click', closeBanModal);
    }

    if (banOverlay) {
        banOverlay.addEventListener('click', (e) => {
            if (e.target === banOverlay) {
                closeBanModal();
            }
        });
    }

    // Pembersihan error real-time
    if (banDate) {
        banDate.addEventListener('input', () => {
            if (dateError) dateError.textContent = '';
            banDate.style.boxShadow = '';
            if (timeError && timeError.textContent.includes('setelah')) {
                timeError.textContent = '';
                if (banTime) banTime.style.boxShadow = '';
            }
        });
    }

    if (banTime) {
        banTime.addEventListener('input', () => {
            if (timeError) timeError.textContent = '';
            banTime.style.boxShadow = '';
        });
    }

    // Submit form banned
    if (banForm) {
        banForm.addEventListener('submit', (e) => {
            e.preventDefault();
            clearBanErrors();

            const isPermanent = banPermanent && banPermanent.checked;
            const dateValue = banDate ? banDate.value.trim() : '';
            const timeValue = banTime ? banTime.value.trim() : '';
            const targetUserId = banModal.getAttribute('data-id');
            const targetUsername = banModal.getAttribute('data-username');

            if (!isPermanent) {
                let hasError = false;

                if (!dateValue) {
                    if (dateError) dateError.textContent = 'Tanggal wajib diisi';
                    if (banDate) banDate.style.boxShadow = '0 0 0 2px var(--color-danger)';
                    hasError = true;
                }

                if (!timeValue) {
                    if (timeError) timeError.textContent = 'Jam wajib diisi';
                    if (banTime) banTime.style.boxShadow = '0 0 0 2px var(--color-danger)';
                    hasError = true;
                }

                if (hasError) return;

                const targetDateTime = new Date(`${dateValue}T${timeValue}`);
                const currentDateTime = new Date();

                if (isNaN(targetDateTime.getTime()) || targetDateTime <= currentDateTime) {
                    if (timeError) timeError.textContent = 'Waktu banned harus setelah waktu sekarang';
                    if (banTime) banTime.style.boxShadow = '0 0 0 2px var(--color-danger)';
                    return;
                }
            }

            // TODO: sambungkan ke route backend (id akun: targetUserId, is_permanent: isPermanent, banned_until: `${dateValue} ${timeValue}`)

            // Update kartu akun di UI
            if (targetUserId) {
                const targetCard = document.querySelector(`#sectionLaporanAkun .report-card[data-id="${targetUserId}"]`);
                if (targetCard) {
                    targetCard.setAttribute('data-status', 'banned');
                    targetCard.classList.add('report-card--banned');

                    // Hilangkan risk pill jika ada
                    const riskBadge = targetCard.querySelector('.risk-badge');
                    if (riskBadge) riskBadge.remove();

                    // Jadikan avatar abu muda
                    const avatar = targetCard.querySelector('.report-avatar');
                    if (avatar) {
                        avatar.className = 'report-avatar avatar-neutral';
                    }

                    const actionsRow = targetCard.querySelector('.report-actions-row');
                    if (actionsRow) {
                        const banTextDesc = isPermanent
                            ? 'Akses dibekukan secara: <strong>Permanen</strong>'
                            : `Akses dibekukan hingga: <strong>${dateValue}, ${timeValue} WIB</strong>`;

                        actionsRow.innerHTML = `
                            <div class="report-banned-container">
                                <div class="report-banned-text">
                                    <span class="report-banned-badge">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                        </svg>
                                        ${isPermanent ? 'BANNED PERMANEN' : 'BANNED SEMENTARA'}
                                    </span>
                                    <span>${banTextDesc}</span>
                                </div>
                                <button type="button" class="btn-action btn-ignore btn-unban" data-id="${targetUserId}" data-username="${targetUsername || ''}" title="Buka penangguhan akun ini">
                                    Buka Blokir
                                </button>
                            </div>
                        `;

                        const newUnbanBtn = actionsRow.querySelector('.btn-unban');
                        if (newUnbanBtn) attachUnbanListener(newUnbanBtn);
                    }
                }
            }

            closeBanModal();
            applyStatusFilter();

            const toastMsg = isPermanent
                ? `Akun ${targetUsername || ''} berhasil di-banned permanen (simulasi tampilan)`
                : `Akun ${targetUsername || ''} berhasil di-banned sementara (simulasi tampilan)`;
            showToast(toastMsg);
        });
    }

    // --------------------------------------------------------------------------
    // 5. Pop-up Kirim Peringatan (Kasih Warning)
    // --------------------------------------------------------------------------
    const clearWarnErrors = () => {
        if (warnMessageError) warnMessageError.textContent = '';
        if (warnMessage) warnMessage.style.boxShadow = '';
    };

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

    const closeWarnModal = () => {
        if (!warnOverlay) return;

        warnOverlay.classList.remove('is-active');
        warnOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        clearWarnErrors();
    };

    document.querySelectorAll('#sectionLaporanAkun .btn-warn').forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.report-card');
            const userId = btn.getAttribute('data-id') || (card ? card.getAttribute('data-id') : '');
            const username = btn.getAttribute('data-username') || (card ? card.getAttribute('data-username') : '');
            openWarnModal(userId, username);
        });
    });

    if (btnWarnBack) btnWarnBack.addEventListener('click', closeWarnModal);
    if (btnCancelWarn) btnCancelWarn.addEventListener('click', closeWarnModal);

    if (warnOverlay) {
        warnOverlay.addEventListener('click', (e) => {
            if (e.target === warnOverlay) closeWarnModal();
        });
    }

    if (warnMessage) {
        warnMessage.addEventListener('input', clearWarnErrors);
    }

    if (warnForm) {
        warnForm.addEventListener('submit', (e) => {
            e.preventDefault();
            clearWarnErrors();

            const messageValue = warnMessage ? warnMessage.value.trim() : '';
            if (!messageValue) {
                if (warnMessageError) warnMessageError.textContent = 'Pesan peringatan wajib diisi';
                if (warnMessage) {
                    warnMessage.style.boxShadow = '0 0 0 2px var(--color-danger)';
                    warnMessage.focus();
                }
                return;
            }

            const targetUserId = warnModal.getAttribute('data-id');
            const targetUsername = warnModal.getAttribute('data-username');

            // TODO: sambungkan ke route backend untuk kirim warning (id akun: targetUserId, pesan: messageValue)

            closeWarnModal();
            showToast(`Peringatan berhasil dikirim ke ${targetUsername || 'pengguna'} (simulasi tampilan)`);
        });
    }

    // --------------------------------------------------------------------------
    // 6. Pop-up Konfirmasi Buka Blokir (Unban)
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

    if (btnUnbanBack) btnUnbanBack.addEventListener('click', closeUnbanModal);
    if (btnCancelUnban) btnCancelUnban.addEventListener('click', closeUnbanModal);

    if (unbanOverlay) {
        unbanOverlay.addEventListener('click', (e) => {
            if (e.target === unbanOverlay) closeUnbanModal();
        });
    }

    if (btnConfirmUnban) {
        btnConfirmUnban.addEventListener('click', () => {
            const targetUserId = unbanModal.getAttribute('data-id');
            const targetUsername = unbanModal.getAttribute('data-username');

            // TODO: sambungkan ke route backend (id akun, unban)

            closeUnbanModal();
            showToast(`Blokir akun ${targetUsername || ''} berhasil dibuka (simulasi tampilan)`);
        });
    }

    // --------------------------------------------------------------------------
    // 7. Pop-up Konfirmasi Sembunyikan Postingan
    // --------------------------------------------------------------------------
    const hidePostOverlay = document.getElementById('hidePostOverlay');
    const hidePostModal = document.getElementById('hidePostModal');
    const hidePostTargetId = document.getElementById('hidePostTargetId');
    const hidePostTargetAuthor = document.getElementById('hidePostTargetAuthor');
    const btnHidePostBack = document.getElementById('btnHidePostBack');
    const btnCancelHidePost = document.getElementById('btnCancelHidePost');
    const btnConfirmHidePost = document.getElementById('btnConfirmHidePost');

    const openHidePostModal = (postId, author) => {
        if (!hidePostOverlay || !hidePostModal) return;

        hidePostModal.setAttribute('data-post-id', postId || '');
        hidePostModal.setAttribute('data-author', author || '');

        if (hidePostTargetId) {
            hidePostTargetId.textContent = postId ? (postId.startsWith('#') ? postId : `#${postId}`) : '#PST';
        }
        if (hidePostTargetAuthor) {
            hidePostTargetAuthor.textContent = author || '@penulis';
        }

        hidePostOverlay.classList.add('is-active');
        hidePostOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (btnCancelHidePost) btnCancelHidePost.focus();
        }, 60);
    };

    const closeHidePostModal = () => {
        if (!hidePostOverlay) return;

        hidePostOverlay.classList.remove('is-active');
        hidePostOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    document.querySelectorAll('.btn-hide-post').forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.report-card');
            const postId = btn.getAttribute('data-post-id') || (card ? card.getAttribute('data-id') : '');
            const author = btn.getAttribute('data-author') || (card ? card.getAttribute('data-username') : '');
            openHidePostModal(postId, author);
        });
    });

    if (btnHidePostBack) btnHidePostBack.addEventListener('click', closeHidePostModal);
    if (btnCancelHidePost) btnCancelHidePost.addEventListener('click', closeHidePostModal);

    if (hidePostOverlay) {
        hidePostOverlay.addEventListener('click', (e) => {
            if (e.target === hidePostOverlay) closeHidePostModal();
        });
    }

    if (btnConfirmHidePost) {
        btnConfirmHidePost.addEventListener('click', () => {
            const targetPostId = hidePostModal.getAttribute('data-post-id');

            // TODO: sambungkan ke route backend (id postingan: targetPostId, status: disembunyikan)

            // Update kartu postingan di UI
            if (targetPostId) {
                const targetCard = document.querySelector(`#sectionLaporanPostingan .report-card[data-id="${targetPostId}"]`);
                if (targetCard) {
                    targetCard.setAttribute('data-status', 'selesai');
                    const riskBadge = targetCard.querySelector('.risk-badge');
                    if (riskBadge) riskBadge.remove();

                    const actionsRow = targetCard.querySelector('.report-actions-row');
                    if (actionsRow) {
                        actionsRow.innerHTML = `
                            <p class="report-completed-text">
                                Laporan selesai: postingan sudah disembunyikan.
                            </p>
                        `;
                    }
                }
            }

            closeHidePostModal();
            applyStatusFilter();
            showToast(`Postingan #${targetPostId || ''} berhasil disembunyikan (simulasi tampilan)`);
        });
    }

    // --------------------------------------------------------------------------
    // 8. Pop-up Konfirmasi Hapus Postingan
    // --------------------------------------------------------------------------
    const deletePostOverlay = document.getElementById('deletePostOverlay');
    const deletePostModal = document.getElementById('deletePostModal');
    const deletePostTargetId = document.getElementById('deletePostTargetId');
    const deletePostTargetAuthor = document.getElementById('deletePostTargetAuthor');
    const btnDeletePostBack = document.getElementById('btnDeletePostBack');
    const btnCancelDeletePost = document.getElementById('btnCancelDeletePost');
    const btnConfirmDeletePost = document.getElementById('btnConfirmDeletePost');

    const openDeletePostModal = (postId, author) => {
        if (!deletePostOverlay || !deletePostModal) return;

        deletePostModal.setAttribute('data-post-id', postId || '');
        deletePostModal.setAttribute('data-author', author || '');

        if (deletePostTargetId) {
            deletePostTargetId.textContent = postId ? (postId.startsWith('#') ? postId : `#${postId}`) : '#PST';
        }
        if (deletePostTargetAuthor) {
            deletePostTargetAuthor.textContent = author || '@penulis';
        }

        deletePostOverlay.classList.add('is-active');
        deletePostOverlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (btnCancelDeletePost) btnCancelDeletePost.focus();
        }, 60);
    };

    const closeDeletePostModal = () => {
        if (!deletePostOverlay) return;

        deletePostOverlay.classList.remove('is-active');
        deletePostOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    document.querySelectorAll('.btn-delete-post').forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.report-card');
            const postId = btn.getAttribute('data-post-id') || (card ? card.getAttribute('data-id') : '');
            const author = btn.getAttribute('data-author') || (card ? card.getAttribute('data-username') : '');
            openDeletePostModal(postId, author);
        });
    });

    if (btnDeletePostBack) btnDeletePostBack.addEventListener('click', closeDeletePostModal);
    if (btnCancelDeletePost) btnCancelDeletePost.addEventListener('click', closeDeletePostModal);

    if (deletePostOverlay) {
        deletePostOverlay.addEventListener('click', (e) => {
            if (e.target === deletePostOverlay) closeDeletePostModal();
        });
    }

    if (btnConfirmDeletePost) {
        btnConfirmDeletePost.addEventListener('click', () => {
            const targetPostId = deletePostModal.getAttribute('data-post-id');

            // TODO: sambungkan ke route backend (id postingan: targetPostId, aksi: hapus permanen)

            // Update kartu postingan di UI
            if (targetPostId) {
                const targetCard = document.querySelector(`#sectionLaporanPostingan .report-card[data-id="${targetPostId}"]`);
                if (targetCard) {
                    targetCard.setAttribute('data-status', 'selesai');
                    const riskBadge = targetCard.querySelector('.risk-badge');
                    if (riskBadge) riskBadge.remove();

                    const actionsRow = targetCard.querySelector('.report-actions-row');
                    if (actionsRow) {
                        actionsRow.innerHTML = `
                            <p class="report-completed-text">
                                Laporan selesai: postingan telah dihapus permanen.
                            </p>
                        `;
                    }
                }
            }

            closeDeletePostModal();
            applyStatusFilter();
            showToast(`Postingan #${targetPostId || ''} berhasil dihapus permanen (simulasi tampilan)`);
        });
    }

    // --------------------------------------------------------------------------
    // 9. Tombol Abaikan Laporan (Akun & Postingan)
    // --------------------------------------------------------------------------
    // Abaikan Laporan Akun
    document.querySelectorAll('#sectionLaporanAkun .btn-ignore:not(.btn-unban)').forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.getAttribute('data-id');
            // TODO: sambungkan ke route backend untuk abaikan laporan akun (ID: userId)
            showToast('Laporan akun diabaikan (simulasi tampilan)');
        });
    });

    // Abaikan Laporan Postingan
    document.querySelectorAll('.btn-ignore-post').forEach(btn => {
        btn.addEventListener('click', () => {
            const postId = btn.getAttribute('data-post-id');
            // TODO: sambungkan ke route backend (id postingan)
            showToast(`Laporan postingan #${postId} diabaikan (simulasi tampilan)`);
        });
    });

    // --------------------------------------------------------------------------
    // 10. Select Filter Tambahan (Urutan & Kanal)
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

    // --------------------------------------------------------------------------
    // 11. Aksesibilitas Keyboard Global (Esc & Focus Trap)
    // --------------------------------------------------------------------------
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (banOverlay && banOverlay.classList.contains('is-active')) closeBanModal();
            if (unbanOverlay && unbanOverlay.classList.contains('is-active')) closeUnbanModal();
            if (warnOverlay && warnOverlay.classList.contains('is-active')) closeWarnModal();
            if (hidePostOverlay && hidePostOverlay.classList.contains('is-active')) closeHidePostModal();
            if (deletePostOverlay && deletePostOverlay.classList.contains('is-active')) closeDeletePostModal();
        }
    });
});
