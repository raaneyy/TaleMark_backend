{{-- resources/views/admin/profil.blade.php
     Halaman Setting Akun (Profil Admin) TaleMark
     Data diambil dari AdminProfilController (tabel `admin`)
--}}
@extends('layouts.admin')

@section('title', 'Setting Akun')

@section('styles')
    <style>
        /* =============================================
           SETTING AKUN
           ============================================= */
        .profil-wrap {
            max-width: 720px;
            margin: 0 auto;
        }
        .profil-card {
            position: relative;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 18px;
            padding: 28px 32px 32px;
        }

        /* Header kartu: judul kiri, EDIT kanan */
        .profil-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }
        .profil-kicker {
            margin: 0;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9CA3AF;
        }
        .profil-title {
            margin: 4px 0 0;
            font-family: 'Newsreader', Georgia, serif;
            font-size: 28px;
            font-weight: 400;
            color: #111827;
        }
        .profil-edit-btn {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            border: 0;
            background: transparent;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            color: #111827;
            cursor: pointer;
            padding: 0;
        }
        .profil-edit-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #E5E7EB;
            border-radius: 50%;
            background: #FFFFFF;
            transition: background .15s, color .15s, border-color .15s;
        }
        .profil-edit-btn:hover .profil-edit-icon { background: #F3F4F6; }
        .profil-form.sedang-edit .profil-edit-icon {
            background: #111827;
            border-color: #111827;
            color: #FFFFFF;
        }

        /* Foto profil */
        .profil-avatar-wrap {
            display: flex;
            justify-content: center;
            margin: 8px 0 28px;
        }
        .profil-avatar {
            position: relative;
            width: 132px;
            height: 132px;
        }
        .profil-avatar-img,
        .profil-avatar-kosong {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 1px solid #E5E7EB;
            object-fit: cover;
            background: #F3F4F6;
        }
        .profil-avatar-kosong {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9CA3AF;
        }
        .profil-avatar-kamera {
            position: absolute;
            right: 2px;
            bottom: 2px;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #FFFFFF;
            border-radius: 50%;
            background: #111827;
            color: #FFFFFF;
            cursor: pointer;
            transition: background .15s;
        }
        .profil-avatar-kamera:hover { background: #374151; }

        /* Baris data: label  :  nilai */
        .profil-list {
            margin: 0;
            border-top: 1px solid #F3F4F6;
        }
        .profil-row {
            display: grid;
            grid-template-columns: 180px 16px minmax(0, 1fr);
            align-items: start;
            gap: 8px;
            padding: 14px 0;
            border-bottom: 1px solid #F3F4F6;
        }
        .profil-row dt {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #6B7280;
        }
        .profil-titik {
            font-family: 'Inter', system-ui, sans-serif;
            color: #9CA3AF;
            text-align: center;
        }
        /* Label & titik dua sejajar dengan baris pertama isian */
        .profil-row dt,
        .profil-titik { padding-top: 11px; }
        .profil-form.sedang-edit .profil-pass-group { padding-top: 11px; }
        .profil-form.sedang-edit .profil-row:last-child dt,
        .profil-form.sedang-edit .profil-row:last-child .profil-titik { padding-top: 11px; }
        .profil-row dd { margin: 0; min-width: 0; }

        .profil-input {
            width: 100%;
            box-sizing: border-box;
            height: 42px;
            padding: 0 12px;
            border: 1px solid transparent;
            border-radius: 10px;
            background: transparent;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 15px;
            font-weight: 500;
            color: #111827;
            outline: none;
            text-overflow: ellipsis;
            transition: border-color .15s, background .15s, box-shadow .15s;
        }
        /* Mode lihat: tampil seperti teks biasa */
        .profil-input[readonly] {
            padding-left: 0;
            cursor: default;
        }
        /* Mode edit: tampil sebagai kolom isian */
        .profil-form.sedang-edit .profil-input:not([readonly]) {
            border-color: #E5E7EB;
            background: #FFFFFF;
        }
        .profil-form.sedang-edit .profil-input:not([readonly]):focus {
            border-color: #111827;
            box-shadow: 0 0 0 3px rgba(17, 24, 39, .08);
        }
        .profil-input.is-invalid { border-color: #DC2626 !important; }
        .profil-error {
            margin: 4px 0 0;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 12px;
            color: #DC2626;
        }
        .profil-error:empty { display: none; }

        /* Kolom password (muncul di mode edit) */
        .profil-pass-view { font-size: 18px; letter-spacing: 3px; }
        .profil-pass-group {
            display: none;
            flex-direction: column;
            gap: 10px;
        }
        .profil-form.sedang-edit .profil-pass-view { display: none; }
        .profil-form.sedang-edit .profil-pass-group { display: flex; }
        .profil-pass-field { position: relative; }
        .profil-pass-field .profil-input { padding-right: 42px; }
        .profil-pass-toggle {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            background: transparent;
            color: #9CA3AF;
            cursor: pointer;
        }
        .profil-pass-toggle:hover { color: #111827; }
        .profil-hint {
            margin: 0;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 12px;
            color: #9CA3AF;
        }

        /* Tombol Save & Logout */
        .profil-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-top: 32px;
        }
        .profil-btn {
            width: 100%;
            max-width: 360px;
            height: 48px;
            border-radius: 999px;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, opacity .15s;
        }
        .profil-btn-save {
            border: 0;
            background: #111827;
            color: #FFFFFF;
        }
        .profil-btn-save:hover:not(:disabled) { background: #1F2937; }
        .profil-btn-save:disabled { opacity: .35; cursor: not-allowed; }
        .profil-btn-logout {
            border: 1.5px solid #E5E7EB;
            background: #FFFFFF;
            color: #B91C1C;
        }
        .profil-btn-logout:hover { background: #FEF2F2; border-color: #FECACA; }

        /* Pesan berhasil / gagal dari server */
        .profil-alert {
            margin-bottom: 16px;
            padding: 12px 16px;
            border-radius: 12px;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 13px;
        }
        .profil-alert-sukses { background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; }
        .profil-alert-gagal  { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }

        /* Pop-up konfirmasi logout */
        .profil-modal {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .profil-modal[hidden] { display: none !important; }
        .profil-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, .45);
            backdrop-filter: blur(2px);
        }
        .profil-modal-box {
            position: relative;
            width: min(400px, 100%);
            background: #FFFFFF;
            border-radius: 18px;
            padding: 28px 24px 24px;
            text-align: center;
            box-shadow: 0 24px 60px rgba(17, 24, 39, .18);
            animation: profilMuncul .2s ease;
        }
        .profil-modal-box h2 {
            margin: 0;
            font-family: 'Newsreader', Georgia, serif;
            font-size: 24px;
            font-weight: 400;
            color: #111827;
        }
        .profil-modal-box p {
            margin: 8px 0 22px;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 14px;
            color: #6B7280;
        }
        .profil-modal-actions { display: flex; gap: 10px; }
        .profil-modal-actions .profil-btn { height: 44px; }
        .profil-btn-batal { border: 1px solid #E5E7EB; background: #FFFFFF; color: #374151; }
        .profil-btn-batal:hover { background: #F3F4F6; }
        .profil-btn-keluar { border: 0; background: #B91C1C; color: #FFFFFF; }
        .profil-btn-keluar:hover { background: #991B1B; }
        @keyframes profilMuncul {
            from { opacity: 0; transform: translateY(10px) scale(.98); }
            to   { opacity: 1; transform: none; }
        }

        /* HP */
        @media (max-width: 640px) {
            .profil-card { padding: 22px 18px 24px; }
            .profil-title { font-size: 24px; }
            .profil-avatar { width: 112px; height: 112px; }
            .profil-row {
                grid-template-columns: minmax(0, 1fr);
                gap: 4px;
            }
            .profil-titik { display: none; }
            .profil-row dt { font-size: 12px; padding-top: 0; }
            .profil-form.sedang-edit .profil-pass-group { padding-top: 4px; }
        }
    </style>
@endsection

@section('content')

@php
    /* Data admin dari controller. Fallback dummy jika belum tersambung. */
    $admin = $admin ?? (object) [
        'nama'     => 'Agus',
        'username' => 'agus',
        'email'    => 'agus123@gmail.com',
        'foto'     => null,
    ];

    /* Jika ada error validasi dari server, halaman langsung dibuka dalam mode edit */
    $modeEdit = $errors->any();
    $fotoUrl  = !empty($admin->foto) ? asset('storage/' . $admin->foto) : null;
@endphp

<div class="profil-wrap">

    {{-- Pesan dari server --}}
    @if (session('success'))
        <div class="profil-alert profil-alert-sukses" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="profil-alert profil-alert-gagal" role="alert">Perubahan belum disimpan. Periksa kembali kolom yang ditandai merah.</div>
    @endif

    {{-- TODO: pastikan route admin.profil.update sudah dibuat (lihat routes/web.php) --}}
    <form method="POST"
          action="{{ route('admin.profil.update') }}"
          enctype="multipart/form-data"
          class="profil-card profil-form {{ $modeEdit ? 'sedang-edit' : '' }}"
          id="formProfil"
          novalidate>
        @csrf
        @method('PUT')

        {{-- Header: judul & tombol EDIT --}}
        <div class="profil-head">
            <div>
                <p class="profil-kicker">Profil Admin</p>
                <h1 class="profil-title">Setting Akun</h1>
            </div>
            <button type="button" class="profil-edit-btn" id="btnEditProfil" aria-pressed="{{ $modeEdit ? 'true' : 'false' }}">
                <span class="profil-edit-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                </span>
                <span id="teksEditProfil">{{ $modeEdit ? 'BATAL' : 'EDIT' }}</span>
            </button>
        </div>

        {{-- Foto profil + tombol kamera --}}
        <div class="profil-avatar-wrap">
            <div class="profil-avatar">
                <div id="avatarPreview" style="width:100%;height:100%">
                    @if ($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="Foto profil {{ $admin->nama }}" class="profil-avatar-img">
                    @else
                        <div class="profil-avatar-kosong" aria-label="Belum ada foto profil">
                            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <label for="inputFoto" class="profil-avatar-kamera" title="Ganti foto profil" aria-label="Ganti foto profil">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                    </svg>
                </label>
                <input type="file" id="inputFoto" name="foto" accept="image/png,image/jpeg,image/webp" hidden>
            </div>
        </div>
        <p class="profil-error" id="errorFoto" style="text-align:center;margin:-16px 0 16px">@error('foto'){{ $message }}@enderror</p>

        {{-- Data akun --}}
        <dl class="profil-list">

            <div class="profil-row">
                <dt><label for="inputNama">Nama</label></dt>
                <span class="profil-titik">:</span>
                <dd>
                    <input type="text" id="inputNama" name="nama" maxlength="100"
                           class="profil-input @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $admin->nama) }}" {{ $modeEdit ? '' : 'readonly' }} data-edit>
                    <p class="profil-error" data-error="nama">@error('nama'){{ $message }}@enderror</p>
                </dd>
            </div>

            <div class="profil-row">
                <dt><label for="inputUsername">Nama Pengguna</label></dt>
                <span class="profil-titik">:</span>
                <dd>
                    <input type="text" id="inputUsername" name="username" maxlength="50" autocomplete="username"
                           class="profil-input @error('username') is-invalid @enderror"
                           value="{{ old('username', $admin->username) }}" {{ $modeEdit ? '' : 'readonly' }} data-edit>
                    <p class="profil-error" data-error="username">@error('username'){{ $message }}@enderror</p>
                </dd>
            </div>

            <div class="profil-row">
                <dt><label for="inputEmail">Email</label></dt>
                <span class="profil-titik">:</span>
                <dd>
                    <input type="email" id="inputEmail" name="email" maxlength="150" autocomplete="email"
                           class="profil-input @error('email') is-invalid @enderror"
                           value="{{ old('email', $admin->email) }}" {{ $modeEdit ? '' : 'readonly' }} data-edit>
                    <p class="profil-error" data-error="email">@error('email'){{ $message }}@enderror</p>
                </dd>
            </div>

            <div class="profil-row">
                <dt><label for="inputPasswordLama">Ubah Password</label></dt>
                <span class="profil-titik">:</span>
                <dd>
                    {{-- Mode lihat: password tidak pernah ditampilkan --}}
                    <input type="text" class="profil-input profil-pass-view" value="••••••••" readonly tabindex="-1" aria-label="Password tersembunyi">

                    {{-- Mode edit: isi hanya jika ingin mengganti password --}}
                    <div class="profil-pass-group">
                        <p class="profil-hint">Kosongkan jika tidak ingin mengganti password.</p>

                        <div class="profil-pass-field">
                            <input type="password" id="inputPasswordLama" name="password_lama" autocomplete="current-password"
                                   class="profil-input @error('password_lama') is-invalid @enderror" placeholder="Password saat ini" data-edit>
                            <button type="button" class="profil-pass-toggle" data-toggle-pass aria-label="Tampilkan password">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <p class="profil-error" data-error="password_lama">@error('password_lama'){{ $message }}@enderror</p>

                        <div class="profil-pass-field">
                            <input type="password" id="inputPassword" name="password" autocomplete="new-password"
                                   class="profil-input @error('password') is-invalid @enderror" placeholder="Password baru (min. 8 karakter)" data-edit>
                            <button type="button" class="profil-pass-toggle" data-toggle-pass aria-label="Tampilkan password">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <p class="profil-error" data-error="password">@error('password'){{ $message }}@enderror</p>

                        <div class="profil-pass-field">
                            <input type="password" id="inputPasswordKonfirmasi" name="password_confirmation" autocomplete="new-password"
                                   class="profil-input" placeholder="Ulangi password baru" data-edit>
                            <button type="button" class="profil-pass-toggle" data-toggle-pass aria-label="Tampilkan password">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <p class="profil-error" data-error="password_confirmation"></p>
                    </div>
                </dd>
            </div>

        </dl>

        {{-- Tombol Save & Logout --}}
        <div class="profil-actions">
            <button type="submit" class="profil-btn profil-btn-save" id="btnSaveProfil" {{ $modeEdit ? '' : 'disabled' }}>Save</button>
            <button type="button" class="profil-btn profil-btn-logout" id="btnLogout">Logout</button>
        </div>
    </form>

</div>

@endsection

@push('modals')
{{-- Pop-up konfirmasi logout --}}
<div class="profil-modal" id="modalLogout" hidden>
    <div class="profil-modal-backdrop" data-tutup-logout></div>
    <div class="profil-modal-box" role="dialog" aria-modal="true" aria-labelledby="judulLogout">
        <h2 id="judulLogout">Keluar dari akun?</h2>
        <p>Kamu perlu masuk kembali untuk membuka panel admin.</p>

        {{-- TODO: pastikan route admin.logout (POST) sudah ada --}}
        <form method="POST" action="{{ route('admin.logout') }}" class="profil-modal-actions">
            @csrf
            <button type="button" class="profil-btn profil-btn-batal" data-tutup-logout>Batal</button>
            <button type="submit" class="profil-btn profil-btn-keluar">Ya, Logout</button>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form       = document.getElementById('formProfil');
    const btnEdit    = document.getElementById('btnEditProfil');
    const teksEdit   = document.getElementById('teksEditProfil');
    const btnSave    = document.getElementById('btnSaveProfil');
    const inputFoto  = document.getElementById('inputFoto');
    const preview    = document.getElementById('avatarPreview');
    const errorFoto  = document.getElementById('errorFoto');
    const kolomEdit  = form.querySelectorAll('[data-edit]');
    const previewAwal = preview.innerHTML;

    // Simpan nilai awal untuk tombol BATAL
    const nilaiAwal = {};
    kolomEdit.forEach(i => { nilaiAwal[i.id] = i.defaultValue; });

    /* ---------- Mode edit ---------- */
    function setEdit(aktif) {
        form.classList.toggle('sedang-edit', aktif);
        kolomEdit.forEach(i => { i.readOnly = !aktif; });
        btnSave.disabled = !aktif;
        teksEdit.textContent = aktif ? 'BATAL' : 'EDIT';
        btnEdit.setAttribute('aria-pressed', aktif ? 'true' : 'false');
        if (aktif) {
            document.getElementById('inputNama').focus();
        } else {
            // Kembalikan semua isian ke nilai awal
            kolomEdit.forEach(i => { i.value = i.type === 'password' ? '' : nilaiAwal[i.id]; i.classList.remove('is-invalid'); });
            form.querySelectorAll('[data-error]').forEach(p => { p.textContent = ''; });
            inputFoto.value = '';
            preview.innerHTML = previewAwal;
            errorFoto.textContent = '';
        }
    }
    btnEdit.addEventListener('click', () => setEdit(!form.classList.contains('sedang-edit')));

    /* ---------- Ganti foto (otomatis masuk mode edit) ---------- */
    inputFoto.addEventListener('change', () => {
        errorFoto.textContent = '';
        const file = inputFoto.files[0];
        if (!file) return;
        if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
            inputFoto.value = '';
            errorFoto.textContent = 'Foto harus berformat JPG, PNG, atau WEBP.';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            inputFoto.value = '';
            errorFoto.textContent = 'Ukuran foto maksimal 2 MB.';
            return;
        }
        const url = URL.createObjectURL(file);
        if (!form.classList.contains('sedang-edit')) setEdit(true);
        preview.innerHTML = '<img src="' + url + '" alt="Pratinjau foto profil" class="profil-avatar-img">';
    });

    /* ---------- Lihat / sembunyikan password ---------- */
    form.querySelectorAll('[data-toggle-pass]').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.previousElementSibling;
            const lihat = input.type === 'password';
            input.type = lihat ? 'text' : 'password';
            btn.setAttribute('aria-label', lihat ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    /* ---------- Validasi sebelum dikirim ---------- */
    function setError(nama, pesan) {
        const p = form.querySelector('[data-error="' + nama + '"]');
        if (p) p.textContent = pesan;
        const input = form.querySelector('[name="' + nama + '"]');
        if (input) input.classList.toggle('is-invalid', !!pesan);
        return !pesan;
    }
    kolomEdit.forEach(i => i.addEventListener('input', () => setError(i.name, '')));

    form.addEventListener('submit', (e) => {
        const v = (id) => document.getElementById(id).value.trim();
        const passLama = v('inputPasswordLama'), passBaru = v('inputPassword'), passUlang = v('inputPasswordKonfirmasi');
        const hasil = [
            setError('nama', v('inputNama') ? '' : 'Nama wajib diisi.'),
            setError('username',
                !v('inputUsername') ? 'Nama pengguna wajib diisi.'
                : !/^[A-Za-z0-9_.-]+$/.test(v('inputUsername')) ? 'Hanya huruf, angka, titik, garis bawah, dan tanda hubung.'
                : ''),
            setError('email',
                !v('inputEmail') ? 'Email wajib diisi.'
                : !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v('inputEmail')) ? 'Format email tidak valid.'
                : ''),
            setError('password_lama', (passBaru || passUlang) && !passLama ? 'Masukkan password saat ini.' : ''),
            setError('password', passBaru && passBaru.length < 8 ? 'Password baru minimal 8 karakter.' : (passLama && !passBaru ? 'Masukkan password baru.' : '')),
            setError('password_confirmation', passBaru && passBaru !== passUlang ? 'Konfirmasi password tidak sama.' : ''),
        ];
        if (hasil.includes(false)) {
            e.preventDefault();
            const salah = form.querySelector('.is-invalid');
            if (salah) salah.focus();
        }
    });

    /* ---------- Pop-up logout ---------- */
    const modalLogout = document.getElementById('modalLogout');
    const btnLogout   = document.getElementById('btnLogout');
    const tutupLogout = () => { modalLogout.hidden = true; btnLogout.focus(); };
    btnLogout.addEventListener('click', () => {
        modalLogout.hidden = false;
        modalLogout.querySelector('.profil-btn-batal').focus();
    });
    modalLogout.querySelectorAll('[data-tutup-logout]').forEach(el => el.addEventListener('click', tutupLogout));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modalLogout.hidden) tutupLogout();
    });
});
</script>
@endpush