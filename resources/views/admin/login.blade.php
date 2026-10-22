<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Halaman login admin TaleMark - aplikasi pelacak bacaan">
    <title>Login Admin — TaleMark</title>

    {{-- Google Fonts: Newsreader + Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,400;1,6..72,400&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">

    {{-- CSS halaman login --}}
    <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
</head>
<body>

    {{-- Kontainer utama — kartu login di tengah layar --}}
    <main class="login-wrapper">
        <div class="login-card">

            {{-- ===== KOLOM KIRI: Logo ===== --}}
            <div class="login-brand">
                <img
                    src="{{ asset('images/logo-talemark.png') }}"
                    alt="Logo TaleMark"
                    class="brand-logo"
                >
            </div>

            {{-- ===== KOLOM KANAN: Form login ===== --}}
            <div class="login-form-section">

                <h1 class="form-title">Masuk Sebagai Admin</h1>

                {{-- Area pesan error dari server (dikosongkan dulu, backend yang mengisi) --}}
                @if(session('error'))
                    <div class="alert-error" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                {{--
                    TODO: Ganti action="#" dengan route login dari backend,
                    contoh: action="{{ route('admin.login.post') }}"
                --}}
                <form
                    id="login-form"
                    action="#"
                    method="POST"
                    novalidate
                >
                    @csrf

                    {{-- Field username --}}
                    <div class="form-group">
                        <label class="form-label" for="username">USERNAME</label>
                        <input
                            class="form-input"
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Username"
                            autocomplete="username"
                            value="{{ old('username') }}"
                        >
                        {{-- Pesan error validasi browser (diisi oleh JS) --}}
                        <span class="field-error" id="error-username" role="alert"></span>
                        {{-- Pesan error validasi server (diisi oleh backend) --}}
                        @error('username')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Field kata sandi --}}
                    <div class="form-group">
                        {{-- Baris label + link "Lupa?" --}}
                        <div class="label-row">
                            <label class="form-label" for="password">KATA SANDI</label>
                            {{-- TODO: Ganti href="#" dengan route lupa password dari backend --}}
                            <a href="#" class="link-forgot">Lupa?</a>
                        </div>

                        {{-- Input password dengan tombol show/hide --}}
                        <div class="input-password-wrapper">
                            <input
                                class="form-input"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                            >
                            {{-- Tombol toggle show/hide password --}}
                            <button
                                type="button"
                                class="btn-toggle-password"
                                id="btn-toggle-password"
                                aria-label="Tampilkan atau sembunyikan kata sandi"
                            >
                                {{-- Ikon mata biasa (password terlihat) — disembunyikan secara default --}}
                                <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                                    style="display: none;">
                                    <path d="M2 12C3.6 7.5 7.4 4.5 12 4.5C16.6 4.5 20.4 7.5 22 12C20.4 16.5 16.6 19.5 12 19.5C7.4 19.5 3.6 16.5 2 12Z"/>
                                    <circle cx="12" cy="12" r="3.2"/>
                                </svg>
                                {{-- Ikon mata dicoret (password tersembunyi) — tampil secara default --}}
                                <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12C3.6 7.5 7.4 4.5 12 4.5C16.6 4.5 20.4 7.5 22 12C20.4 16.5 16.6 19.5 12 19.5C7.4 19.5 3.6 16.5 2 12Z"/>
                                    <circle cx="12" cy="12" r="3.2"/>
                                    <line x1="3" y1="3" x2="21" y2="21"/>
                                </svg>
                            </button>
                        </div>
                        {{-- Pesan error validasi browser (diisi oleh JS) --}}
                        <span class="field-error" id="error-password" role="alert"></span>
                        {{-- Pesan error validasi server (diisi oleh backend) --}}
                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tombol submit --}}
                    <button type="submit" class="btn-submit" id="btn-submit">
                        Masuk
                    </button>

                </form>
            </div>
            {{-- akhir kolom kanan --}}

        </div>
        {{-- akhir login-card --}}
    </main>

    {{-- JavaScript halaman login --}}
    <script src="{{ asset('js/admin-login.js') }}"></script>
</body>
</html>
