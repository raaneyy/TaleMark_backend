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
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}?v={{ file_exists(public_path('css/admin-layout.css')) ? filemtime(public_path('css/admin-layout.css')) : time() }}">

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
            <img src="{{ asset('images/talemark.jpeg') }}" alt="TaleMark" class="sidebar-logo-img">
            <div class="sidebar-logo-text">
                <span class="brand-name">TaleMark</span>
                <span class="brand-sub">Dashboard </span>
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2.5" y="2.5" width="19" height="19" rx="2.5"/>
                                <line x1="6" y1="7.5" x2="9.5" y2="7.5"/>
                                <line x1="6" y1="12" x2="9.5" y2="12"/>
                                <line x1="6" y1="16.5" x2="9.5" y2="16.5"/>
                                <path d="M13 12.5L15.5 15L19 9.5"/>
                            </svg>
                        </span>
                        Validasi Buku
                    </a>
                </li>

                {{-- Menu Laporan Akun --}}
                <li class="sidebar-menu-item {{ request()->routeIs('admin.laporan-akun') ? 'active' : '' }}">
                    <a href="{{ route('admin.laporan-akun') }}" id="menu-laporan">
                        <span class="menu-icon">
                            <svg width="20" height="20" viewBox="2.5 1 19 23" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3L5 5.5V11.5C5 16.2 8.5 19.8 12 21.5C15.5 19.8 19 16.2 19 11.5V5.5L12 3Z"/>
                                <circle cx="12" cy="9.5" r="2.4"/>
                                <path d="M6.5 16.5C8.5 14.8 10.2 14.5 12 14.5C13.8 14.5 15.5 14.8 17.5 16.5"/>
                            </svg>
                        </span>
                        Laporan Akun
                    </a>
                </li>

                {{-- Menu Siaran Notifikasi --}}
                <li class="sidebar-menu-item {{ request()->routeIs('admin.siaran-notifikasi') ? 'active' : '' }}">
                    <a href="{{ route('admin.siaran-notifikasi') }}" id="menu-siaran">
                        <span class="menu-icon">
                            <svg width="20" height="20" viewBox="0.5 1.5 23 21" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.5 11C2.5 10 3.2 9.5 4 9.5H6L13.5 6V18L6 14.5H4C3.2 14.5 2.5 14 2.5 13V11Z"/>
                                <path d="M13.5 8C15 9.5 15.5 11 15.5 12C15.5 13 15 14.5 13.5 16"/>
                                <line x1="7" y1="14.5" x2="7" y2="20"/>
                                <line x1="17" y1="6.5" x2="20.5" y2="4"/>
                                <line x1="18" y1="12" x2="22.5" y2="12"/>
                                <line x1="17" y1="17.5" x2="20.5" y2="20"/>
                            </svg>
                        </span>
                        Siaran Notifikasi
                    </a>
                </li>
            </ul>
        </nav>


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
        <main role="main">
            <div class="page-content">
                @yield('content')
            </div>
        </main>

    </div>
    {{-- /main-area --}}

</div>
{{-- /admin-wrapper --}}

{{-- =============================================
     POP-UP & MODAL DIALOGS (Langsung di bawah body)
     Menjamin posisi fixed 100% dead-center dan menutup penuh layar
     ============================================= --}}
@yield('modals')
@stack('modals')

{{-- JavaScript toggle sidebar & modal teleport --}}
<script src="{{ asset('js/admin-layout.js') }}?v={{ file_exists(public_path('js/admin-layout.js')) ? filemtime(public_path('js/admin-layout.js')) : time() }}"></script>

{{-- Script tambahan dari halaman anak --}}
@yield('scripts')
@stack('scripts')

</body>
</html>
