{{-- resources/views/layouts/admin.blade.php
     Kerangka halaman admin: sidebar + topbar + konten + footer
     Dipakai oleh semua halaman admin dengan @extends('layouts.admin')
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Judul tab diisi dari masing-masing halaman --}}
    <title>@yield('title', 'Admin') — TaleMark</title>
    <meta name="description" content="TaleMark Curatorial Desk — Panel administrasi kurasi buku dan statistik platform.">

    {{-- Google Fonts: Newsreader + Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,400;1,6..72,400&display=swap" rel="stylesheet">

    {{-- CSS Layout (sidebar, topbar, footer, variabel) --}}
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}">

    {{-- CSS tambahan dari halaman anak --}}
    @yield('styles')
</head>
<body>

{{-- Overlay gelap saat sidebar terbuka di mobile --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-wrapper">

    {{-- =============================================
         SIDEBAR
         ============================================= --}}
    <aside class="sidebar" id="sidebar" role="navigation" aria-label="Menu admin">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <img src="{{ asset('images/logo-talemark-icon.png') }}" alt="TaleMark" class="sidebar-logo-img">
            <div class="sidebar-logo-text">
                <span class="brand-name">TaleMark</span>
                <span class="brand-sub">Curatorial Desk</span>
            </div>
        </div>

        {{-- Navigasi --}}
        <nav class="sidebar-nav">
            <p class="sidebar-group-label">Sistem Redaksi</p>
            <ul class="sidebar-menu">

                {{-- Menu statistik --}}
                <li class="sidebar-menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" id="menu-statistik">
                        <span class="menu-icon">
                            {{-- Ikon buku terbuka --}}
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                        </span>
                        Statistik 
                    </a>
                </li>

                {{-- Menu Validasi Buku --}}
                <li class="sidebar-menu-item {{ request()->routeIs('admin.validasi-buku') ? 'active' : '' }}">
                    <a href="{{ route('admin.validasi-buku') }}" id="menu-validasi">
                        <span class="menu-icon">
                            {{-- Ikon daftar centang --}}
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2.5"/>
                                <line x1="7" y1="8" x2="11" y2="8"/>
                                <line x1="7" y1="12" x2="11" y2="12"/>
                                <line x1="7" y1="16" x2="11" y2="16"/>
                                <path d="M14 12.5l2 2.5 4-4.5"/>
                            </svg>
                        </span>
                        Validasi Buku
                    </a>
                </li>

                {{-- Menu Laporan Akun --}}
                <li class="sidebar-menu-item {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
                    <a href="#" id="menu-laporan">
                        <span class="menu-icon">
                            {{-- Ikon perisai akun --}}
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3a12 12 0 0 0 7 2.5V11c0 4.8-3.2 9.2-7 10.5A12 12 0 0 1 5 11V5.5A12 12 0 0 0 12 3z"/>
                                <circle cx="12" cy="10" r="2.8"/>
                                <path d="M7 16.5c1.2 2 3 3 5 3s3.8-1 5-3"/>
                            </svg>
                        </span>
                        Laporan Akun
                    </a>
                </li>

                {{-- Menu Siaran Notifikasi --}}
                <li class="sidebar-menu-item {{ request()->routeIs('admin.siaran*') ? 'active' : '' }}">
                    <a href="#" id="menu-siaran">
                        <span class="menu-icon">
                            {{-- Ikon megafon siaran --}}
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 11v2a1 1 0 0 0 1 1h2l6 3.5v-11L6 10H4a1 1 0 0 0-1 1z"/>
                                <path d="M8 14.5v3.5a1 1 0 0 0 1 1h.5"/>
                                <line x1="17" y1="7.5" x2="20" y2="4.5"/>
                                <line x1="18" y1="12" x2="22" y2="12"/>
                                <line x1="17" y1="16.5" x2="20" y2="19.5"/>
                            </svg>
                        </span>
                        Siaran Notifikasi
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Badge versi di footer sidebar --}}
        <div class="sidebar-footer">
            <div class="sidebar-version-badge">
                <div class="sidebar-version-info">
                    <span class="sidebar-version-label">EDISI INDEKS</span>
                    <span class="sidebar-version-val">v2.4.0 &bull; Folio Stable</span>
                </div>
                {{-- Ikon centang/status --}}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
        </div>

    </aside>
    {{-- /sidebar --}}

    {{-- =============================================
         AREA UTAMA (topbar + konten + footer)
         ============================================= --}}
    <div class="main-area">

        {{-- TOPBAR --}}
        <header class="topbar" role="banner">

            {{-- Tombol hamburger (hanya muncul di layar kecil) --}}
            <button class="topbar-hamburger" id="btnHamburger" aria-label="Buka menu">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            {{-- Info tanggal --}}
            <div class="topbar-date">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span>Senin, 21 Oktober 2024</span>
            </div>

            {{-- Info pengguna di kanan --}}
            <div class="topbar-right">
                {{-- Kolom pencarian opsional untuk halaman tertentu --}}
                @yield('topbar_search')

                <span class="topbar-username">AGUS</span>
                <button class="topbar-user-btn" aria-label="Profil pengguna">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </button>
            </div>

        </header>
        {{-- /topbar --}}

        {{-- KONTEN HALAMAN (diisi oleh halaman anak) --}}
        <main class="page-content" role="main">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        <footer class="page-footer" role="contentinfo">
            <div class="footer-brand">
                <strong>TaleMark</strong>
                <span> — Arsip Indeks Kurasi Sastra &amp; Preservasi Teks Digital.</span>
            </div>
            <nav class="footer-links" aria-label="Tautan footer">
                <a href="#">Protokol Kurasi</a>
                <a href="#">Daftar Istilah ISBN</a>
                <a href="#">Bantuan Meja Redaksi</a>
            </nav>
        </footer>

    </div>
    {{-- /main-area --}}

</div>
{{-- /admin-wrapper --}}

{{-- JavaScript toggle sidebar --}}
<script src="{{ asset('js/admin-layout.js') }}"></script>

{{-- Script tambahan dari halaman anak --}}
@yield('scripts')

</body>
</html>
