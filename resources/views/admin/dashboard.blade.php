{{-- resources/views/admin/dashboard.blade.php
     Halaman Ringkasan (Dashboard) Admin TaleMark
     Data memakai dummy langsung di sini — backend dikerjakan terpisah
--}}
@extends('layouts.admin')

@section('title', 'Ringkasan')

{{-- CSS khusus dashboard --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endsection

@section('content')

{{-- =============================================
     DATA DUMMY — nanti diganti dari controller
     ============================================= --}}
@php
    /* Empat kartu statistik */
    $statsCards = [
        [
            'label'    => 'Total Pengguna Aktif',
            'value'    => '128.450',
            'desc'     => '+12% bulan ini &bull; 4.210 baru pekan ini',
            'badge'    => null,
            'progress' => 72,
            'icon'     => 'users',
        ],
        [
            'label'    => 'Total Buku Terdaftar',
            'value'    => '412.890',
            'desc'     => '94% katalog resmi &bull; 6% antrean',
            'badge'    => null,
            'progress' => 94,
            'icon'     => 'book',
        ],
        [
            'label'    => 'Halaman Terbaca',
            'value'    => '18.420.100',
            'desc'     => '+340,2 rb sejak kemarin',
            'badge'    => 'up',
            'progress' => 58,
            'icon'     => 'pages',
        ],
        [
            'label'    => 'Sesi Hening Hari Ini',
            'value'    => '8.940',
            'desc'     => '1.218 sedang aktif &bull; fokus saat ini',
            'badge'    => null,
            'progress' => 45,
            'icon'     => 'hourglass',
        ],
    ];

    /* Data genre untuk kartu distribusi */
    $genres = [
        ['name' => 'Sastra Klasik & Kanon',       'count' => '156.898', 'pct' => 38, 'color' => '#111111'],
        ['name' => 'Fiksi Filsafat & Realisme',   'count' => '111.480', 'pct' => 27, 'color' => '#555555'],
        ['name' => 'Catatan Esai & Humaniora',    'count' => '82.578',  'pct' => 20, 'color' => '#999999'],
        ['name' => 'Antologi Puisi & Sajak',      'count' => '61.934',  'pct' => 15, 'color' => '#cccccc'],
    ];
@endphp

{{-- =============================================
     HEADER HALAMAN
     ============================================= --}}
<div class="page-header">
    <div class="page-header-left">
        <p class="page-label">IKHTISAR KURATORIAL &bull; Folio Warta Redaksi No. 89</p>
        <h1 class="page-title">Ringkasan Pustaka &amp; Statistik Platform</h1>
        <p class="page-subtitle">Arsip data aktivitas pembaca dan pertumbuhan katalog buku per November 2024</p>
    </div>

    <div class="page-header-actions">
        {{-- Tombol abu: Unduh Lembar Statistik --}}
        <button class="btn btn-secondary" id="btn-unduh-statistik">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Unduh Lembar Statistik
        </button>

        {{-- Tombol hitam: Kurasi Judul Baru --}}
        <button class="btn btn-primary" id="btn-kurasi-baru">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Kurasi Judul Baru
        </button>
    </div>
</div>

{{-- =============================================
     EMPAT KARTU STATISTIK
     ============================================= --}}
<div class="stats-grid">

    @foreach ($statsCards as $stat)
    <div class="card stat-card">

        {{-- Label kartu --}}
        <p class="stat-card-label">{{ $stat['label'] }}</p>

        {{-- Ikon pojok kanan atas --}}
        <div class="stat-card-icon">
            @if ($stat['icon'] === 'users')
                {{-- Ikon pengguna --}}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            @elseif ($stat['icon'] === 'book' || $stat['icon'] === 'pages')
                {{-- Ikon buku terbuka --}}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
            @else
                {{-- Ikon jam pasir --}}
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 22h14"/><path d="M5 2h14"/>
                    <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"/>
                    <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"/>
                </svg>
            @endif
        </div>

        {{-- Angka besar --}}
        <p class="stat-card-value">{{ $stat['value'] }}</p>

        {{-- Deskripsi --}}
        <div class="stat-card-desc">
            @if ($stat['badge'] === 'up')
                {{-- Ikon panah naik --}}
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                </svg>
            @elseif ($stat['badge'])
                <span class="badge-positive">{{ $stat['badge'] }}</span>
            @endif
            <span>{!! $stat['desc'] !!}</span>
        </div>

        {{-- Progress bar --}}
        <div class="stat-progress">
            <div class="stat-progress-fill" style="width: {{ $stat['progress'] }}%"></div>
        </div>

    </div>
    @endforeach

</div>
{{-- /stats-grid --}}

{{-- =============================================
     BARIS TENGAH: kolom kiri (lebar) + kolom kanan (sempit)
     ============================================= --}}
<div class="mid-grid">

    {{-- =========================================
         KOLOM KIRI: grafik + moderasi
         ========================================= --}}
    <div class="mid-left">

        {{-- ----- KARTU GRAFIK AKTIVITAS ----- --}}
        <div class="card chart-card">

            {{-- Header grafik --}}
            <div class="chart-card-header">
                <div class="chart-card-header-left">
                    <p class="chart-section-label">Pergerakan Pembaca</p>
                    <h2 class="chart-card-title">Aktivitas Membaca &amp;<br>Pendaftaran Buku</h2>
                </div>
                <div class="chart-card-header-right">
                    {{-- Legenda --}}
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-dot black"></span>
                            <span>Jam Membaca (K-hrs)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot gray"></span>
                            <span>Buku Terindeks</span>
                        </div>
                    </div>
                    {{-- Badge minggu --}}
                    <span class="badge-week">Minggu 43–47</span>
                </div>
            </div>

            {{-- Grafik SVG inline statis --}}
            {{-- Garis hitam = jam membaca, area abu = buku terindeks --}}
            <div class="chart-svg-wrapper">
                <svg viewBox="0 0 560 160" xmlns="http://www.w3.org/2000/svg">

                    {{-- Area buku terindeks (kurva halus abu muda) --}}
                    <defs>
                        <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#cccccc" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#ffffff" stop-opacity="0.02"/>
                        </linearGradient>
                    </defs>
                    <path d="M 16 142 C 160 138, 360 114, 544 92 L 544 158 L 16 158 Z"
                          fill="url(#areaGrad)" />
                    <path d="M 16 142 C 160 138, 360 114, 544 92"
                          fill="none" stroke="#cfcfcb" stroke-width="1.8"/>

                    {{-- Garis jam membaca: kurva bezier halus dengan 4 titik --}}
                    <path d="M 16 125 C 70 120, 110 98, 155 98 C 200 98, 230 116, 275 116 C 325 116, 365 82, 410 82 C 455 82, 495 62, 544 62"
                          fill="none" stroke="#111111" stroke-width="2.5" stroke-linecap="round"/>

                    {{-- 4 titik sesuai desain --}}
                    <circle cx="155" cy="98"  r="4" fill="#111111"/>
                    <circle cx="275" cy="116" r="4" fill="#111111"/>
                    <circle cx="410" cy="82"  r="4" fill="#111111"/>
                    <circle cx="544" cy="62"  r="4" fill="#111111"/>

                </svg>
            </div>

            {{-- Label sumbu X --}}
            <div class="chart-x-labels">
                <span class="chart-x-label">Sen (18)</span>
                <span class="chart-x-label">Sel (19)</span>
                <span class="chart-x-label">Rab (20)</span>
                <span class="chart-x-label">Kam (21)</span>
                <span class="chart-x-label">Jum (22)</span>
                <span class="chart-x-label">Sab (23)</span>
                <span class="chart-x-label">Min (24)</span>
            </div>

            {{-- Kotak info analitik di bawah grafik --}}
            <div class="chart-info-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p class="chart-info-text">
                    Rata-rata durasi per sesi tercatat <strong>42,6 menit</strong>, meningkat 8,3% pada segmen sastra terjemahan.
                </p>
                <a href="#" class="chart-info-link">DETAIL LOG ANALITIK &rarr;</a>
            </div>

        </div>
        {{-- /kartu grafik --}}

        {{-- ----- KARTU MODERASI ----- --}}
        <div class="card moderation-card">

            {{-- Header moderasi --}}
            <div class="moderation-card-header">
                <div>
                    <div class="moderation-alert-label">
                        <span class="alert-dot"></span>
                        PERHATIAN REDAKSI
                    </div>
                    <h2 class="moderation-card-title">Antrean Moderasi Butuh Tindakan Segera</h2>
                </div>
                <span class="badge-pending">4 TERTUNDA</span>
            </div>

            {{-- ---- Sub-bagian 1: Usulan Katalog Manual ---- --}}
            <div class="moderation-section">
                <div class="moderation-section-header">
                    <span class="moderation-section-title">Usulan Katalog Manual (2 Permohonan)</span>
                    <a href="#" class="moderation-link">Lihat semua (18)</a>
                </div>

                {{-- Grid dua kartu buku --}}
                <div class="book-cards-grid">

                    {{-- Buku 1 --}}
                    <div class="book-item">
                        <div class="book-item-top">
                            {{-- Placeholder sampul buku --}}
                            <div class="book-cover">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="1.5">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                            </div>
                            <div class="book-info">
                                <p class="book-isbn">ISBN 978-602-03-8821</p>
                                <p class="book-title">Ziarah Karya Iwan Simatupang</p>
                                <p class="book-proposer">Folio Restorasi</p>
                            </div>
                        </div>
                        <div class="book-item-actions">
                            <button class="btn btn-primary btn-sm">Setujui</button>
                            <button class="btn btn-secondary btn-sm">Periksa</button>
                        </div>
                    </div>

                    {{-- Buku 2 --}}
                    <div class="book-item">
                        <div class="book-item-top">
                            <div class="book-cover">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#aaa" stroke-width="1.5">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                            </div>
                            <div class="book-info">
                                <p class="book-isbn">ISBN 978-623-71-3310</p>
                                <p class="book-title">Hujan Bulan Juni: Antologi Lengkap</p>
                                <p class="book-proposer">@SastraSenja</p>
                            </div>
                        </div>
                        <div class="book-item-actions">
                            <button class="btn btn-primary btn-sm">Setujui</button>
                            <button class="btn btn-secondary btn-sm">Periksa</button>
                        </div>
                    </div>

                </div>
            </div>
            {{-- /sub-bagian 1 --}}

            <div class="moderation-divider"></div>

            {{-- ---- Sub-bagian 2: Laporan Pelanggaran ---- --}}
            <div class="moderation-section">
                <div class="moderation-section-header">
                    <span class="moderation-section-title">Laporan Pelanggaran Komunitas (2 Akun)</span>
                    <a href="#" class="moderation-link">Lihat investigasi (5)</a>
                </div>

                {{-- Akun 1: spam ulasan --}}
                <div class="violation-item">
                    <div class="violation-avatar">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="violation-info">
                        <div class="violation-user-row">
                            <span class="violation-username">@raden_kolektor</span>
                            <span class="badge-violation">SPAM ULASAN</span>
                        </div>
                        <p class="violation-desc">Membuat 14 ulasan generik AI identik dalam rentang 3 menit</p>
                        <div class="violation-actions">
                            <button class="btn btn-secondary btn-sm">Bekukan</button>
                            <button class="btn btn-secondary btn-sm">Abaikan</button>
                        </div>
                    </div>
                </div>

                {{-- Akun 2: hak cipta --}}
                <div class="violation-item">
                    <div class="violation-avatar">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="violation-info">
                        <div class="violation-user-row">
                            <span class="violation-username">@buku_bajakan_id</span>
                            <span class="badge-violation">HAK CIPTA</span>
                        </div>
                        <p class="violation-desc">Mencantumkan domain eksternal penjualan PDF tidak berizin</p>
                        <div class="violation-actions">
                            <button class="btn btn-danger btn-sm">Blokir Permanen</button>
                            <button class="btn btn-secondary btn-sm">Audit</button>
                        </div>
                    </div>
                </div>

            </div>
            {{-- /sub-bagian 2 --}}

        </div>
        {{-- /kartu moderasi --}}

    </div>
    {{-- /mid-left --}}

    {{-- =========================================
         KOLOM KANAN: notifikasi + genre + sistem
         ========================================= --}}
    <div class="mid-right">

        {{-- ----- KARTU SIARAN NOTIFIKASI ----- --}}
        <div class="card notif-card">
            <div class="notif-card-header">
                <div>
                    <p class="notif-section-label">Warta Publik</p>
                    <h2 class="notif-card-title">Siaran Notifikasi Aktif</h2>
                </div>
                {{-- Badge LIVE --}}
                <div class="badge-live">
                    <span class="live-dot"></span>
                    LIVE
                </div>
            </div>

            {{-- Konten siaran --}}
            <div class="notif-content-box">
                <div class="notif-type-label">
                    {{-- Ikon sinyal --}}
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 6c0 0 4-4 11-4s11 4 11 4"/><path d="M5 10c0 0 3-3 7-3s7 3 7 3"/>
                        <path d="M9 14c0 0 1-1 3-1s3 1 3 1"/><circle cx="12" cy="18" r="1"/>
                    </svg>
                    SIARAN NASIONAL
                </div>
                <p class="notif-title">Selamat Hari Buku Nasional 2024</p>
                <p class="notif-quote">
                    "Buku adalah jembatan sunyi yang menghubungkan dua jiwa yang tak pernah bersua."
                    Nikmati kurasi esai eksklusif selama pekan literasi.
                </p>
                <div class="notif-stats">
                    <div class="notif-stat"><strong>94.200</strong><br>pembaca terjangkau</div>
                    <div class="notif-stat"><strong>88,4%</strong><br>CTR</div>
                </div>
            </div>

            {{-- Tombol kelola --}}
            <button class="btn btn-secondary" style="width:100%; justify-content:center;" id="btn-kelola-siaran">
                Kelola Distribusi Siaran
            </button>
        </div>
        {{-- /kartu siaran --}}

        {{-- ----- KARTU DISTRIBUSI GENRE ----- --}}
        <div class="card genre-card">
            <div class="genre-card-header">
                <div>
                    <p class="genre-section-label">Katalog Platform</p>
                    <h2 class="genre-card-title">Distribusi Genre Terpopuler</h2>
                </div>
                <span class="genre-period">Bulan Ini</span>
            </div>

            {{-- Bar segmen horizontal --}}
            <div class="genre-bar">
                @foreach ($genres as $genre)
                    <div class="genre-bar-segment" style="width: {{ $genre['pct'] }}%; background: {{ $genre['color'] }};"></div>
                @endforeach
            </div>

            {{-- Daftar genre --}}
            <div class="genre-list">
                @foreach ($genres as $genre)
                    <div class="genre-item">
                        <span class="genre-dot" style="background: {{ $genre['color'] }};"></span>
                        <div class="genre-item-info">
                            <p class="genre-item-name">{{ $genre['name'] }}</p>
                            <p class="genre-item-meta">{{ $genre['count'] }} judul</p>
                        </div>
                        <span class="genre-item-pct">{{ $genre['pct'] }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
        {{-- /kartu genre --}}

        {{-- ----- KARTU SISTEM & KEAMANAN ----- --}}
        <div class="card system-card">
            <div class="system-card-header">
                <div>
                    <p class="system-section-label">Infrastruktur</p>
                    <h2 class="system-card-title">Sistem &amp; Keamanan</h2>
                </div>
                {{-- Ikon perisai --}}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>

            {{-- Baris status --}}
            <div class="system-status-list">
                <div class="system-status-item">
                    <span class="system-status-dot"></span>
                    <div class="system-status-text">
                        <strong>Status Server Repositori</strong>
                        <span>Optimal (99.98%)</span>
                    </div>
                </div>
                <div class="system-status-item">
                    <span class="system-status-dot"></span>
                    <div class="system-status-text">
                        <strong>Insiden Dilaporkan</strong>
                        <span>0 Kasus Hari Ini</span>
                    </div>
                </div>
                <div class="system-status-item">
                    <span class="system-status-dot"></span>
                    <div class="system-status-text">
                        <strong>Sinkronisasi Metadata ISBN</strong>
                        <span>12 mnt lalu</span>
                    </div>
                </div>
            </div>

            {{-- Footer kartu sistem --}}
            <div class="system-card-footer">
                <span class="system-node-label">Versi Node Kurasi: ID-JKT-04</span>
                <a href="#" class="system-log-link">LOG SISTEM &rarr;</a>
            </div>
        </div>
        {{-- /kartu sistem --}}

    </div>
    {{-- /mid-right --}}

</div>
{{-- /mid-grid --}}

@endsection
