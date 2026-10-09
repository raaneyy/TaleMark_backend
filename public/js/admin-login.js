/**
 * admin-login.js — Logika tampilan halaman login admin TaleMark
 *
 * Fungsi yang ada di sini:
 * 1. Toggle show/hide password
 * 2. Validasi form sederhana di browser (cek field kosong)
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ===== Referensi elemen ===== */
    const form          = document.getElementById('login-form');
    const inputUsername = document.getElementById('username');
    const inputPassword = document.getElementById('password');
    const btnToggle     = document.getElementById('btn-toggle-password');
    const iconEye       = document.getElementById('icon-eye');
    const iconEyeOff    = document.getElementById('icon-eye-off');
    const errorUsername = document.getElementById('error-username');
    const errorPassword = document.getElementById('error-password');


    /* ===== 1. Toggle show/hide password ===== */
    btnToggle.addEventListener('click', function () {
        const isHidden = inputPassword.type === 'password';

        // Ganti tipe input
        inputPassword.type = isHidden ? 'text' : 'password';

        // Saat password terlihat (isHidden=true): tampilkan ikon mata biasa, sembunyikan mata dicoret
        // Saat password tersembunyi (isHidden=false): tampilkan ikon mata dicoret, sembunyikan mata biasa
        iconEye.style.display    = isHidden ? 'block' : 'none';
        iconEyeOff.style.display = isHidden ? 'none'  : 'block';
    });


    /* ===== 2. Validasi form di browser ===== */
    form.addEventListener('submit', function (event) {
        let isValid = true;

        // Bersihkan pesan error sebelumnya
        tampilkanError(errorUsername, '');
        tampilkanError(errorPassword, '');

        // Cek username kosong
        if (inputUsername.value.trim() === '') {
            tampilkanError(errorUsername, 'Username wajib diisi');
            isValid = false;
        }

        // Cek password kosong
        if (inputPassword.value.trim() === '') {
            tampilkanError(errorPassword, 'Kata sandi wajib diisi');
            isValid = false;
        }

        // Batalkan submit jika ada field yang kosong
        if (!isValid) {
            event.preventDefault();
        }
    });


    /* ===== Helper: tampilkan pesan error pada elemen span ===== */
    function tampilkanError(elemenError, pesan) {
        elemenError.textContent = pesan;
    }

});
