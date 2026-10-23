/**
 * admin-siaran-notifikasi.js
 * Interaksi frontend untuk halaman Kelola Notifikasi (Siaran Notifikasi) TaleMark
 * Mengatur counter karakter, toggle jadwal pengiriman, validasi form, toast simulasi,
 * serta panel riwayat siaran (filter tab, gunakan lagi, batalkan jadwal, & entri baru).
 */

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------------------------
    // 1. Inisialisasi Elemen DOM Form & Notifikasi
    // ----------------------------------------------------------------------
    const form = document.getElementById('formSiaranNotifikasi');
    const inputJudul = document.getElementById('judul');
    const counterJudul = document.getElementById('counterJudul');
    const inputIsiPesan = document.getElementById('isi_pesan');
    const selectJadwal = document.getElementById('jadwal');
    const scheduleFields = document.getElementById('scheduleFields');
    const inputTanggal = document.getElementById('tanggal');
    const inputJam = document.getElementById('jam');
    const btnSubmitText = document.getElementById('btnSubmitText');
    const toastContainer = document.getElementById('toastContainer');

    // Elemen pesan error
    const errorJudul = document.getElementById('errorJudul');
    const errorIsiPesan = document.getElementById('errorIsiPesan');
    const errorTanggal = document.getElementById('errorTanggal');
    const errorJam = document.getElementById('errorJam');
    const errorWaktu = document.getElementById('errorWaktu');

    // Elemen Panel Riwayat Siaran
    const historyTabs = document.querySelectorAll('.history-tab-btn');
    const historyList = document.getElementById('broadcastHistoryList');
    const historyEmptyState = document.getElementById('historyEmptyState');
    const btnAllHistory = document.getElementById('btnAllHistory');

    // ----------------------------------------------------------------------
    // 2. Helper: Tanggal & Sanitasi
    // ----------------------------------------------------------------------
    function setMinDateToday() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        if (inputTanggal) {
            inputTanggal.min = `${yyyy}-${mm}-${dd}`;
        }
    }

    function formatScheduleDateTime(dateStr, timeStr) {
        if (!dateStr) return timeStr || '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return `${dateStr}, ${timeStr}`;
        const y = parts[0];
        const m = parseInt(parts[1], 10);
        const d = parseInt(parts[2], 10);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const monthName = months[m - 1] || parts[1];
        return `${d} ${monthName} ${y}, ${timeStr}`;
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ----------------------------------------------------------------------
    // 3. Interaksi Counter Karakter Judul (Maks. 60)
    // ----------------------------------------------------------------------
    if (inputJudul) {
        inputJudul.addEventListener('input', () => {
            const length = inputJudul.value.length;
            if (length === 0) {
                counterJudul.textContent = 'Maks. 60 Karakter';
            } else {
                counterJudul.textContent = `${length}/60 Karakter`;
            }
            clearError(inputJudul, errorJudul);
        });
    }

    // ----------------------------------------------------------------------
    // 4. Toggle Opsi Jadwal Pengiriman
    // ----------------------------------------------------------------------
    function handleJadwalChange() {
        if (!selectJadwal || !scheduleFields) return;

        if (selectJadwal.value === 'jadwalkan') {
            scheduleFields.style.display = 'block';
            setMinDateToday();
            if (btnSubmitText) {
                btnSubmitText.textContent = 'Jadwalkan Siaran Notifikasi';
            }
        } else {
            scheduleFields.style.display = 'none';
            if (btnSubmitText) {
                btnSubmitText.textContent = 'Kirim Siaran Notifikasi Sekarang';
            }

            // Bersihkan error jika kembali ke kirim sekarang
            clearError(inputTanggal, errorTanggal);
            clearError(inputJam, errorJam);
            if (errorWaktu) errorWaktu.textContent = '';
        }
    }

    if (selectJadwal) {
        selectJadwal.addEventListener('change', handleJadwalChange);
        selectJadwal.addEventListener('input', handleJadwalChange);
    }

    // ----------------------------------------------------------------------
    // 5. Bersihkan Pesan Error Saat Pengguna Mengetik/Mengubah Nilai
    // ----------------------------------------------------------------------
    if (inputIsiPesan) {
        inputIsiPesan.addEventListener('input', () => {
            clearError(inputIsiPesan, errorIsiPesan);
        });
    }

    if (inputTanggal) {
        inputTanggal.addEventListener('input', () => {
            clearError(inputTanggal, errorTanggal);
            if (errorWaktu) errorWaktu.textContent = '';
            if (inputJam) inputJam.classList.remove('is-invalid');
        });
    }

    if (inputJam) {
        inputJam.addEventListener('input', () => {
            clearError(inputJam, errorJam);
            if (errorWaktu) errorWaktu.textContent = '';
            if (inputTanggal) inputTanggal.classList.remove('is-invalid');
        });
    }

    function showError(inputEl, errorEl, message) {
        if (inputEl) {
            inputEl.classList.add('is-invalid');
        }
        if (errorEl) {
            errorEl.textContent = message;
        }
    }

    function clearError(inputEl, errorEl) {
        if (inputEl) {
            inputEl.classList.remove('is-invalid');
        }
        if (errorEl) {
            errorEl.textContent = '';
        }
    }

    // ----------------------------------------------------------------------
    // 6. Tampilkan Notifikasi Toast
    // ----------------------------------------------------------------------
    function showToast(message) {
        if (!toastContainer) return;

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;

        toastContainer.appendChild(toast);

        // Animasi fade in & slide up
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Hilang otomatis setelah 3 detik
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // ----------------------------------------------------------------------
    // 7. Reset Form Setelah Berhasil Kirim
    // ----------------------------------------------------------------------
    function resetForm() {
        if (!form) return;
        form.reset();
        if (counterJudul) counterJudul.textContent = 'Maks. 60 Karakter';
        if (scheduleFields) scheduleFields.style.display = 'none';
        if (btnSubmitText) btnSubmitText.textContent = 'Kirim Siaran Notifikasi Sekarang';

        // Bersihkan seluruh status error
        [inputJudul, inputIsiPesan, selectJadwal, inputTanggal, inputJam].forEach((el) => {
            if (el) el.classList.remove('is-invalid');
        });
        [errorJudul, errorIsiPesan, errorTanggal, errorJam, errorWaktu].forEach((el) => {
            if (el) el.textContent = '';
        });
    }

    // ----------------------------------------------------------------------
    // 8. Logika Panel Riwayat Siaran (Tab Filter, Gunakan Lagi, Batalkan)
    // ----------------------------------------------------------------------

    // A. Filter Tab
    function applyHistoryFilter(filterValue) {
        if (!historyList) return;

        const items = historyList.querySelectorAll('.history-item');
        let visibleCount = 0;

        items.forEach((item) => {
            const itemStatus = item.getAttribute('data-status');
            let isMatch = false;

            if (filterValue === 'semua') {
                isMatch = true;
            } else if (filterValue === 'terkirim') {
                isMatch = (itemStatus === 'terkirim');
            } else if (filterValue === 'terjadwal') {
                isMatch = (itemStatus === 'terjadwal');
            }

            if (isMatch) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (historyEmptyState) {
            historyEmptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    historyTabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            historyTabs.forEach((t) => t.classList.remove('active'));
            tab.classList.add('active');
            const filter = tab.getAttribute('data-filter') || 'semua';
            applyHistoryFilter(filter);
        });
    });

    // B. Aksi "Gunakan Lagi"
    function handleReuseBroadcast(item) {
        if (!item || !inputJudul || !inputIsiPesan) return;

        const titleEl = item.querySelector('.history-item-title');
        const snippetEl = item.querySelector('.history-item-snippet');

        const titleText = titleEl ? titleEl.textContent.trim() : '';
        const bodyText = snippetEl ? (snippetEl.getAttribute('data-full-content') || snippetEl.textContent.trim()) : '';

        // Isi input form
        inputJudul.value = titleText;
        inputIsiPesan.value = bodyText;

        // Perbarui penghitung karakter
        const length = inputJudul.value.length;
        if (counterJudul) {
            if (length === 0) {
                counterJudul.textContent = 'Maks. 60 Karakter';
            } else {
                counterJudul.textContent = `${length}/60 Karakter`;
            }
        }

        clearError(inputJudul, errorJudul);
        clearError(inputIsiPesan, errorIsiPesan);

        // Gulir halus ke form (terutama berguna saat tampilan satu kolom)
        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Pindahkan fokus ke input judul
        setTimeout(() => {
            inputJudul.focus();
        }, 180);
    }

    // C. Aksi "Batalkan" (khusus TERJADWAL)
    function handleCancelBroadcast(btn, item) {
        if (!item) return;
        const broadcastId = btn.getAttribute('data-id') || item.getAttribute('data-id');

        // TODO: sambungkan ke route backend (id siaran: broadcastId) untuk membatalkan pengiriman terjadwal

        // Ubah status item menjadi dibatalkan
        item.setAttribute('data-status', 'dibatalkan');

        // Ubah pill status menjadi DIBATALKAN (latar abu, teks #666)
        const pill = item.querySelector('.history-status-pill');
        if (pill) {
            pill.className = 'history-status-pill pill-dibatalkan';
            pill.textContent = 'DIBATALKAN';
        }

        // Ganti keterangan jadwal menjadi "Dibatalkan"
        const scheduledInfo = item.querySelector('.history-scheduled-info');
        if (scheduledInfo) {
            scheduledInfo.className = 'history-cancelled-info';
            scheduledInfo.textContent = 'Dibatalkan';
        }

        // Sembunyikan/hapus tombol Batalkan
        btn.remove();

        // Tampilkan toast konfirmasi tanpa alert bawaan browser
        showToast('Siaran terjadwal berhasil dibatalkan');

        // Sinkronkan kembali dengan tab filter yang sedang aktif
        const activeTab = document.querySelector('.history-tab-btn.active');
        const activeFilter = activeTab ? activeTab.getAttribute('data-filter') : 'semua';
        applyHistoryFilter(activeFilter);
    }

    // D. Event Delegation untuk Tombol pada Item Riwayat
    if (historyList) {
        historyList.addEventListener('click', (e) => {
            const reuseBtn = e.target.closest('.btn-reuse');
            if (reuseBtn) {
                const item = reuseBtn.closest('.history-item');
                if (item) handleReuseBroadcast(item);
                return;
            }

            const cancelBtn = e.target.closest('.btn-cancel-broadcast');
            if (cancelBtn) {
                const item = cancelBtn.closest('.history-item');
                if (item) handleCancelBroadcast(cancelBtn, item);
                return;
            }
        });
    }

    // E. Tombol Footer Panel: Lihat Semua Riwayat
    if (btnAllHistory) {
        btnAllHistory.addEventListener('click', () => {
            // TODO: hubungkan ke rute backend arsip riwayat siaran lengkap
        });
    }

    // ----------------------------------------------------------------------
    // 9. Submit Form & Validasi di Sisi Browser
    // ----------------------------------------------------------------------
    if (form) {
        form.addEventListener('submit', (e) => {
            // Cegah reload halaman default
            e.preventDefault();

            let isValid = true;

            // Validasi Judul
            if (!inputJudul.value.trim()) {
                showError(inputJudul, errorJudul, 'Judul wajib diisi');
                isValid = false;
            }

            // Validasi Isi Pesan Editorial
            if (!inputIsiPesan.value.trim()) {
                showError(inputIsiPesan, errorIsiPesan, 'Isi pesan wajib diisi');
                isValid = false;
            }

            // Validasi Mode Jadwal
            const isScheduled = selectJadwal.value === 'jadwalkan';
            if (isScheduled) {
                let scheduleFieldMissing = false;

                if (!inputTanggal.value) {
                    showError(inputTanggal, errorTanggal, 'Tanggal wajib diisi');
                    isValid = false;
                    scheduleFieldMissing = true;
                }

                if (!inputJam.value) {
                    showError(inputJam, errorJam, 'Jam wajib diisi');
                    isValid = false;
                    scheduleFieldMissing = true;
                }

                // Jika tanggal dan jam sudah diisi, periksa apakah waktunya setelah sekarang
                if (!scheduleFieldMissing) {
                    const selectedDateTime = new Date(`${inputTanggal.value}T${inputJam.value}`);
                    const now = new Date();

                    if (isNaN(selectedDateTime.getTime()) || selectedDateTime <= now) {
                        showError(null, errorWaktu, 'Waktu pengiriman harus setelah waktu sekarang');
                        inputTanggal.classList.add('is-invalid');
                        inputJam.classList.add('is-invalid');
                        isValid = false;
                    }
                }
            }

            // Jika form tidak valid, hentikan proses
            if (!isValid) {
                return;
            }

            // Simpan data input sebelum form di-reset
            const judulVal = inputJudul.value.trim();
            const pesanVal = inputIsiPesan.value.trim();
            const tanggalVal = inputTanggal.value;
            const jamVal = inputJam.value;
            const newBroadcastId = 'BRD-' + Date.now();

            // TODO: sambungkan ke route backend (judul, isi pesan, jadwal, tanggal, jam)
            if (isScheduled) {
                showToast('Siaran notifikasi dijadwalkan (simulasi tampilan)');
            } else {
                showToast('Siaran notifikasi dikirim (simulasi tampilan)');
            }

            // Tambahkan item baru ke paling atas panel riwayat siaran
            if (historyList) {
                const newItem = document.createElement('div');
                newItem.className = 'history-item';
                newItem.setAttribute('data-id', newBroadcastId);

                if (isScheduled) {
                    newItem.setAttribute('data-status', 'terjadwal');
                    const formattedDate = formatScheduleDateTime(tanggalVal, jamVal);

                    newItem.innerHTML = `
                        <div class="history-item-top">
                            <span class="history-status-pill pill-terjadwal">TERJADWAL</span>
                            <span class="history-item-time">Terjadwal</span>
                        </div>
                        <h3 class="history-item-title">${escapeHtml(judulVal)}</h3>
                        <p class="history-item-snippet" data-full-content="${escapeHtml(pesanVal)}">${escapeHtml(pesanVal)}</p>
                        <p class="history-scheduled-info">Dikirim pada ${escapeHtml(formattedDate)}</p>
                        <div class="history-item-actions">
                            <button type="button" class="btn-history-action btn-reuse" data-id="${newBroadcastId}">
                                Gunakan Lagi
                            </button>
                            <button type="button" class="btn-history-action btn-cancel-broadcast" data-id="${newBroadcastId}">
                                Batalkan
                            </button>
                        </div>
                    `;
                } else {
                    newItem.setAttribute('data-status', 'terkirim');

                    newItem.innerHTML = `
                        <div class="history-item-top">
                            <span class="history-status-pill pill-terkirim">TERKIRIM</span>
                            <span class="history-item-time">Baru saja</span>
                        </div>
                        <h3 class="history-item-title">${escapeHtml(judulVal)}</h3>
                        <p class="history-item-snippet" data-full-content="${escapeHtml(pesanVal)}">${escapeHtml(pesanVal)}</p>
                        <p class="history-recent-info">Baru saja dikirim</p>
                        <div class="history-item-actions">
                            <button type="button" class="btn-history-action btn-reuse" data-id="${newBroadcastId}">
                                Gunakan Lagi
                            </button>
                        </div>
                    `;
                }

                historyList.prepend(newItem);

                // Sinkronkan filter tab yang sedang aktif
                const activeTab = document.querySelector('.history-tab-btn.active');
                const activeFilter = activeTab ? activeTab.getAttribute('data-filter') : 'semua';
                applyHistoryFilter(activeFilter);
            }

            // Kosongkan form setelah pengiriman berhasil disimulasikan
            resetForm();
        });
    }
});
