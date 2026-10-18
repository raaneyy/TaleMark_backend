/**
 * admin-siaran-notifikasi.js
 * Interaksi frontend untuk halaman Kelola Notifikasi (Siaran Notifikasi) TaleMark
 * Mengatur counter karakter, toggle jadwal pengiriman, validasi form, dan toast simulasi.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ----------------------------------------------------------------------
    // 1. Inisialisasi Elemen DOM
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

    // ----------------------------------------------------------------------
    // 2. Helper: Tanggal Minimal Hari Ini
    // ----------------------------------------------------------------------
    function setMinDateToday() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        inputTanggal.min = `${yyyy}-${mm}-${dd}`;
    }

    // ----------------------------------------------------------------------
    // 3. Interaksi Counter Karakter Judul (Maks. 60)
    // ----------------------------------------------------------------------
    inputJudul.addEventListener('input', () => {
        const length = inputJudul.value.length;
        if (length === 0) {
            counterJudul.textContent = 'Maks. 60 Karakter';
        } else {
            counterJudul.textContent = `${length}/60 Karakter`;
        }
        clearError(inputJudul, errorJudul);
    });

    // ----------------------------------------------------------------------
    // 4. Toggle Opsi Jadwal Pengiriman
    // ----------------------------------------------------------------------
    function handleJadwalChange() {
        if (selectJadwal.value === 'jadwalkan') {
            scheduleFields.style.display = 'block';
            setMinDateToday();
            btnSubmitText.textContent = 'Jadwalkan Siaran Notifikasi';
        } else {
            scheduleFields.style.display = 'none';
            btnSubmitText.textContent = 'Kirim Siaran Notifikasi Sekarang';

            // Bersihkan error jika kembali ke kirim sekarang
            clearError(inputTanggal, errorTanggal);
            clearError(inputJam, errorJam);
            if (errorWaktu) errorWaktu.textContent = '';
        }
    }

    selectJadwal.addEventListener('change', handleJadwalChange);
    selectJadwal.addEventListener('input', handleJadwalChange);

    // ----------------------------------------------------------------------
    // 5. Bersihkan Pesan Error Saat Pengguna Mengetik/Mengubah Nilai
    // ----------------------------------------------------------------------
    inputIsiPesan.addEventListener('input', () => {
        clearError(inputIsiPesan, errorIsiPesan);
    });

    inputTanggal.addEventListener('input', () => {
        clearError(inputTanggal, errorTanggal);
        if (errorWaktu) errorWaktu.textContent = '';
        inputJam.classList.remove('is-invalid');
    });

    inputJam.addEventListener('input', () => {
        clearError(inputJam, errorJam);
        if (errorWaktu) errorWaktu.textContent = '';
        inputTanggal.classList.remove('is-invalid');
    });

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
        form.reset();
        counterJudul.textContent = 'Maks. 60 Karakter';
        scheduleFields.style.display = 'none';
        btnSubmitText.textContent = 'Kirim Siaran Notifikasi Sekarang';

        // Bersihkan seluruh status error
        [inputJudul, inputIsiPesan, selectJadwal, inputTanggal, inputJam].forEach((el) => {
            if (el) el.classList.remove('is-invalid');
        });
        [errorJudul, errorIsiPesan, errorTanggal, errorJam, errorWaktu].forEach((el) => {
            if (el) el.textContent = '';
        });
    }

    // ----------------------------------------------------------------------
    // 8. Submit Form & Validasi di Sisi Browser
    // ----------------------------------------------------------------------
    form.addEventListener('submit', (e) => {
        // Cegah pengiriman default sampai route backend siap
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

        // TODO: sambungkan ke route backend (judul, isi pesan, jadwal, tanggal, jam)
        if (isScheduled) {
            showToast('Siaran notifikasi dijadwalkan (simulasi tampilan)');
        } else {
            showToast('Siaran notifikasi dikirim (simulasi tampilan)');
        }

        // Kosongkan form setelah pengiriman berhasil disimulasikan
        resetForm();
    });
});
