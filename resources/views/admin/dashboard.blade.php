{{-- resources/views/admin/dashboard.blade.php
     Halaman Ringkasan (Dashboard) Admin TaleMark
     Data memakai dummy langsung di sini — backend dikerjakan terpisah
--}}
@extends('layouts.admin')

@section('title', 'Ringkasan')

{{-- CSS khusus dashboard --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <style>
        /* Filter periode (Minggu / Bulan / Tahun) di atas grafik */
        .chart-filter {
            display: inline-flex;
            gap: 2px;
            padding: 3px;
            background: #F3F4F6;
            border: 1px solid #E5E7EB;
            border-radius: 999px;
        }
        .chart-filter-btn {
            border: 0;
            background: transparent;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 11px;
            font-weight: 500;
            color: #6B7280;
            padding: 5px 12px;
            border-radius: 999px;
            cursor: pointer;
            transition: background .15s, color .15s;
        }
        .chart-filter-btn:hover { color: #111827; }
        .chart-filter-btn.active {
            background: #111827;
            color: #FFFFFF;
        }

        /* =============================================
           RESPONSIF — konten selalu muat di lebar layar
           (tidak ada geser ke kanan)
           ============================================= */
        .main-area,
        .page-content {
            min-width: 0;
            max-width: 100%;
        }

        /* Header halaman: tombol turun ke bawah kalau tidak muat */
        .page-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }
        .page-header-left {
            flex: 1 1 320px;
            min-width: 0;
        }
        .page-header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Grid utama: satu kolom penuh, anak grid boleh menyusut */
        .mid-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) !important;
            gap: 24px;
            width: 100%;
        }
        .mid-left {
            display: flex;
            flex-direction: column;
            gap: 24px;
            min-width: 0;
        }

        /* Dua grafik: berdampingan di layar lebar, bertumpuk di layar kecil */
        .chart-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }
        .chart-card,
        .genre-card {
            min-width: 0;
            max-width: 100%;
            margin: 0;
        }
        .chart-apex-wrapper {
            width: 100%;
            min-width: 0;
        }

        /* Header kartu grafik */
        .chart-card-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px 16px;
        }
        .chart-card-header-left { min-width: 0; }
        .chart-card-header-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        /* Kotak info di bawah grafik */
        .chart-info-box {
            flex-wrap: wrap;
            gap: 10px;
        }
        .chart-info-text {
            flex: 1 1 200px;
            min-width: 0;
            white-space: normal;
            overflow-wrap: anywhere;
        }
        .genre-item-info { min-width: 0; }

        /* Tablet & laptop kecil: grafik bertumpuk */
        @media (max-width: 1200px) {
            .chart-row { grid-template-columns: minmax(0, 1fr); }
        }

        /* HP */
        @media (max-width: 640px) {
            .page-title { font-size: 26px; }
            .page-header-actions { width: 100%; }
            .page-header-actions .btn {
                flex: 1 1 auto;
                justify-content: center;
            }
            .chart-card,
            .genre-card { padding: 18px; }
            .chart-card-header-right {
                align-items: flex-start;
                width: 100%;
            }
            .chart-filter { width: 100%; }
            .chart-filter-btn { flex: 1; }
            .chart-info-link { margin-left: 26px; }
        }

        /* =============================================
           DETAIL LOG ANALITIK
           ============================================= */
        .log-modal {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .log-modal[hidden] { display: none !important; }
        .log-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, .45);
            backdrop-filter: blur(2px);
            animation: logFade .2s ease;
        }
        .log-modal-dialog {
            position: relative;
            width: min(1100px, 100%);
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            overflow-x: hidden;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(17, 24, 39, .18);
            padding: 28px;
            animation: logMuncul .22s ease;
        }
        .log-close {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #E5E7EB;
            border-radius: 50%;
            background: #FFFFFF;
            color: #374151;
            cursor: pointer;
            z-index: 1;
        }
        .log-close:hover { background: #F3F4F6; }
        .log-modal-dialog .log-header { padding-right: 44px; }
        body.log-terbuka { overflow: hidden; }
        @keyframes logFade { from { opacity: 0; } to { opacity: 1; } }
        @keyframes logMuncul {
            from { opacity: 0; transform: translateY(12px) scale(.98); }
            to   { opacity: 1; transform: none; }
        }
        .log-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px 16px;
            margin-bottom: 20px;
        }
        .log-header-left { min-width: 0; }
        .log-periode-text {
            margin-top: 4px;
            font-size: 13px;
            color: #6B7280;
        }
        .log-header-right {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
        }

        /* Ringkasan angka */
        .log-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .log-summary-item {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 14px;
            padding: 14px 16px;
            min-width: 0;
        }
        .log-summary-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #9CA3AF;
        }
        .log-summary-value {
            margin-top: 6px;
            font-family: 'Newsreader', Georgia, serif;
            font-size: 28px;
            line-height: 1.1;
            color: #111827;
        }
        .log-summary-note {
            margin-top: 2px;
            font-size: 12px;
            color: #6B7280;
        }

        /* Pencarian, filter status, ekspor */
        .log-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }
        .log-search {
            flex: 1 1 240px;
            min-width: 0;
            position: relative;
        }
        .log-search svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            pointer-events: none;
        }
        .log-input,
        .log-select {
            width: 100%;
            height: 38px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            background: #FFFFFF;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 13px;
            color: #111827;
            padding: 0 12px;
            outline: none;
        }
        .log-input { padding-left: 34px; }
        .log-input:focus,
        .log-select:focus { border-color: #111827; }
        .log-select { width: auto; min-width: 150px; cursor: pointer; }
        .log-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 38px;
            padding: 0 14px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            background: #FFFFFF;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 13px;
            font-weight: 500;
            color: #111827;
            cursor: pointer;
            white-space: nowrap;
        }
        .log-btn:hover { background: #F3F4F6; }

        /* Tabel */
        .log-table-wrap {
            border: 1px solid #E5E7EB;
            border-radius: 14px;
            overflow: hidden;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: fixed;   /* lebar kolom tetap, tidak ikut isi */
        }
        .log-table th {
            background: #F9FAFB;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #6B7280;
            padding: 12px 16px;
            border-bottom: 1px solid #E5E7EB;
        }
        .log-table td {
            height: 64px;          /* tinggi baris tetap di semua halaman */
            padding: 0 16px;
            border-bottom: 1px solid #F3F4F6;
            color: #374151;
            vertical-align: middle;
        }
        /* Teks panjang dipotong "…" supaya baris tidak membesar */
        .log-cell,
        .log-main,
        .log-sub {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .log-table tbody tr:last-child td { border-bottom: 0; }
        .log-table tbody tr:hover { background: #FAFAFA; }
        /* Baris pengisi: tak terlihat, hanya menjaga tinggi tabel tetap */
        .log-table tbody tr.log-filler:hover { background: transparent; }
        .log-table tbody tr.log-filler td { border-bottom-color: transparent; }
        .log-table td.log-empty {
            height: 64px;
            padding: 0 16px !important;
        }
        .log-cell { min-width: 0; }
        .log-main { font-weight: 600; color: #111827; line-height: 1.45; }
        .log-sub { font-size: 12px; color: #9CA3AF; line-height: 1.45; }
        .log-empty {
            text-align: center;
            padding: 36px 16px !important;
            color: #9CA3AF;
        }

        /* Label status */
        .log-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .log-badge.aktif,
        .log-badge.disetujui { background: #111827; color: #FFFFFF; }
        .log-badge.nonaktif,
        .log-badge.menunggu  { background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB; }
        .log-badge.dibanned,
        .log-badge.ditolak   { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }

        /* Halaman */
        .log-footer {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
            font-size: 12px;
            color: #6B7280;
        }
        .log-pages { display: flex; flex-wrap: wrap; gap: 4px; }
        .log-page-btn {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            background: #FFFFFF;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 12px;
            color: #374151;
            cursor: pointer;
        }
        .log-page-btn.active { background: #111827; border-color: #111827; color: #FFFFFF; }
        .log-page-btn:disabled { opacity: .4; cursor: default; }

        @media (max-width: 900px) {
            .log-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        /* HP: tabel berubah jadi kartu */
        @media (max-width: 640px) {
            /* Pop-up jadi layar penuh di HP */
            .log-modal { padding: 0; }
            .log-modal-dialog {
                width: 100%;
                height: 100%;
                max-height: 100%;
                border-radius: 0;
                border: 0;
                padding: 20px 16px;
            }
            .log-header-right { width: 100%; justify-content: flex-start; }
            .log-header-right .chart-filter { width: 100%; }
            .log-summary-value { font-size: 24px; }
            .log-select { flex: 1 1 auto; }
            .log-btn { flex: 1 1 auto; justify-content: center; }
            .log-table-wrap { border: 0; border-radius: 0; }
            .log-table thead { display: none; }
            .log-table,
            .log-table tbody,
            .log-table tr,
            .log-table td { display: block; width: 100%; }
            .log-table tr {
                border: 1px solid #E5E7EB;
                border-radius: 12px;
                padding: 6px 0;
                margin-bottom: 10px;
                background: #FFFFFF;
            }
            .log-table td {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
                border-bottom: 0;
                padding: 6px 14px;
                text-align: right;
            }
            .log-table td::before {
                content: attr(data-label);
                flex-shrink: 0;
                font-size: 11px;
                font-weight: 600;
                letter-spacing: .04em;
                text-transform: uppercase;
                color: #9CA3AF;
                text-align: left;
                padding-top: 2px;
            }
            .log-table td.log-empty { display: block; text-align: center; }
            .log-table tr.log-filler { display: none; }
            .log-table { table-layout: auto; }
            .log-table td { height: auto; }
            .log-cell,
            .log-main,
            .log-sub {
                white-space: normal;
                overflow: visible;
                overflow-wrap: anywhere;
            }
            .log-table td.log-empty::before { content: none; }
        }
    </style>
@endsection

@section('content')

{{-- =============================================
     DATA DUMMY — nanti diganti dari controller
     ============================================= --}}
{{-- TODO: Variabel $statistik di bawah ini adalah data fallback dummy.
     Hubungkan dengan controller backend (AdminDashboardController) saat siap.
     Struktur:
     $statistik['pengguna' | 'buku']['minggu' | 'bulan' | 'tahun'] = [
         'labels' => [...],   // label sumbu X
         'data'   => [...],   // total pada akhir tiap titik
     ]
     $logPengguna: daftar akun  -> nama, username, daftar (Y-m-d), login (Y-m-d H:i), status (aktif|nonaktif|dibanned)
     $logBuku: daftar buku      -> judul, penulis, genre, pengaju, tanggal (Y-m-d), status (disetujui|menunggu|ditolak)
     $tanggalAcuan: tanggal hari ini (Y-m-d), dasar perhitungan minggu/bulan/tahun berjalan --}}
@php
    $statistik = $statistik ?? [
        'pengguna' => [
            'minggu' => [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'data'   => [14, 18, 17, 22, 21, 26, 30],
            ],
            'bulan' => [
                'labels' => ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                'data'   => [9, 16, 14, 30],
            ],
            'tahun' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                'data'   => [2, 4, 3, 7, 9, 8, 12, 15, 14, 19, 24, 30],
            ],
        ],
        'buku' => [
            'minggu' => [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'data'   => [28, 31, 35, 34, 41, 46, 50],
            ],
            'bulan' => [
                'labels' => ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                'data'   => [18, 31, 27, 50],
            ],
            'tahun' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                'data'   => [5, 8, 7, 12, 16, 15, 21, 26, 25, 33, 41, 50],
            ],
        ],
    ];

    /* Tanggal acuan "hari ini" untuk detail log (dari controller: now()->toDateString()) */
    $tanggalAcuan = $tanggalAcuan ?? '2024-10-27';

    /* Data detail log analitik */
    $logPengguna = $logPengguna ?? array_map(fn ($r) => array_combine(['nama', 'username', 'daftar', 'login', 'status'], $r), [
        ['Nadia Putri', 'nadiaputri', '2024-10-27', '2024-10-27 14:20', 'aktif'],
        ['Raka Pratama', 'rakapratama', '2024-10-27', '2024-10-27 09:12', 'aktif'],
        ['Salsabila Anwar', 'salsa.anwar', '2024-10-26', '2024-10-27 08:45', 'aktif'],
        ['Dimas Saputra', 'dimas_s', '2024-10-26', '2024-10-26 21:03', 'aktif'],
        ['Ayu Lestari', 'ayulestari', '2024-10-25', '2024-10-27 07:30', 'aktif'],
        ['Fajar Nugroho', 'fajarn', '2024-10-25', '2024-10-25 19:40', 'aktif'],
        ['Intan Permata', 'intanpermata', '2024-10-25', '2024-10-26 16:22', 'nonaktif'],
        ['Bagas Wicaksono', 'bagasw', '2024-10-24', '2024-10-27 10:05', 'aktif'],
        ['Citra Maharani', 'citramaharani', '2024-10-24', '2024-10-24 20:11', 'aktif'],
        ['Yoga Firmansyah', 'yogaf', '2024-10-23', '2024-10-26 13:47', 'aktif'],
        ['Laras Ningrum', 'larasningrum', '2024-10-23', '2024-10-25 22:18', 'aktif'],
        ['Reza Maulana', 'rezamaulana', '2024-10-22', '2024-10-27 11:36', 'aktif'],
        ['Putri Amelia', 'putriamelia', '2024-10-22', '2024-10-23 18:09', 'aktif'],
        ['Hendra Kusuma', 'hendrak', '2024-10-21', '2024-10-24 15:52', 'aktif'],
        ['Wulan Sari', 'wulansari', '2024-10-21', '2024-10-27 06:58', 'aktif'],
        ['Arif Hidayat', 'arifhidayat', '2024-10-21', '2024-10-22 12:30', 'aktif'],
        ['Meisya Rahma', 'meisyarahma', '2024-10-21', '2024-10-26 20:44', 'aktif'],
        ['Galih Ramadhan', 'galihr', '2024-10-18', '2024-10-25 09:20', 'aktif'],
        ['Anisa Fitri', 'anisafitri', '2024-10-15', '2024-10-27 13:15', 'aktif'],
        ['Bima Setiawan', 'bimasetiawan', '2024-10-12', '2024-10-14 17:02', 'nonaktif'],
        ['Kirana Dewi', 'kiranadewi', '2024-10-09', '2024-10-26 10:48', 'aktif'],
        ['Rizky Akbar', 'rizkyakbar', '2024-10-05', '2024-10-24 08:33', 'aktif'],
        ['Tiara Anggraini', 'tiaraang', '2024-10-02', '2024-10-27 12:01', 'aktif'],
        ['Farhan Syahputra', 'farhans', '2024-09-21', '2024-10-23 19:27', 'aktif'],
        ['Dewi Kartika', 'dewikartika', '2024-09-03', '2024-10-27 15:10', 'aktif'],
        ['Adi Gunawan', 'adigunawan', '2024-08-17', '2024-10-21 07:44', 'dibanned'],
        ['Sekar Ayuningtyas', 'sekarayu', '2024-07-29', '2024-10-26 22:35', 'aktif'],
        ['Yusuf Ibrahim', 'yusufibrahim', '2024-06-11', '2024-10-25 11:58', 'aktif'],
        ['Maya Safitri', 'mayasafitri', '2024-05-02', '2024-10-27 16:40', 'aktif'],
        ['Gilang Prakoso', 'gilangp', '2024-04-14', '2024-10-20 09:09', 'aktif'],
        ['Rina Marlina', 'rinamarlina', '2024-03-08', '2024-10-27 17:25', 'aktif'],
        ['Taufik Hakim', 'taufikhakim', '2024-02-19', '2024-10-22 14:14', 'aktif'],
        ['Agus Salim', 'agussalim', '2024-01-06', '2024-10-27 18:02', 'aktif'],
    ]);

    $logBuku = $logBuku ?? array_map(fn ($r) => array_combine(['judul', 'penulis', 'genre', 'pengaju', 'tanggal', 'status'], $r), [
        ['Anna Karenina', 'Leo Tolstoy', 'Sastra Klasik & Kanon', 'kiranadewi', '2024-10-27', 'disetujui'],
        ['Siti Nurbaya', 'Marah Rusli', 'Sastra Klasik & Kanon', 'rezamaulana', '2024-10-27', 'disetujui'],
        ['Anak Semua Bangsa', 'Pramoedya Ananta Toer', 'Fiksi Filsafat & Realisme', 'rizkyakbar', '2024-10-27', 'disetujui'],
        ['Great Expectations', 'Charles Dickens', 'Sastra Klasik & Kanon', 'kiranadewi', '2024-10-27', 'disetujui'],
        ['Madilog', 'Tan Malaka', 'Catatan Esai & Humaniora', 'sekarayu', '2024-10-26', 'disetujui'],
        ['Hujan Bulan Juni', 'Sapardi Djoko Damono', 'Antologi Puisi & Sajak', 'ayulestari', '2024-10-26', 'disetujui'],
        ['Orang-Orang Bloomington', 'Budi Darma', 'Fiksi Filsafat & Realisme', 'galihr', '2024-10-25', 'disetujui'],
        ['Senja di Jakarta', 'Mochtar Lubis', 'Fiksi Filsafat & Realisme', 'hendrak', '2024-10-25', 'disetujui'],
        ['Buah Rindu', 'Amir Hamzah', 'Antologi Puisi & Sajak', 'bimasetiawan', '2024-10-25', 'disetujui'],
        ['Wuthering Heights', 'Emily Brontë', 'Sastra Klasik & Kanon', 'intanpermata', '2024-10-25', 'disetujui'],
        ['Catatan dari Bawah Tanah', 'Fyodor Dostoyevsky', 'Fiksi Filsafat & Realisme', 'mayasafitri', '2024-10-24', 'disetujui'],
        ['Ronggeng Dukuh Paruk', 'Ahmad Tohari', 'Fiksi Filsafat & Realisme', 'adigunawan', '2024-10-24', 'menunggu'],
        ['Empat Kumpulan Sajak', 'W.S. Rendra', 'Antologi Puisi & Sajak', 'anisafitri', '2024-10-24', 'disetujui'],
        ['Robohnya Surau Kami', 'A.A. Navis', 'Sastra Klasik & Kanon', 'meisyarahma', '2024-10-24', 'disetujui'],
        ['Manusia Indonesia', 'Mochtar Lubis', 'Catatan Esai & Humaniora', 'putriamelia', '2024-10-24', 'menunggu'],
        ['Gitanjali', 'Rabindranath Tagore', 'Antologi Puisi & Sajak', 'citramaharani', '2024-10-24', 'disetujui'],
        ['O Amuk Kapak', 'Sutardji Calzoum Bachri', 'Antologi Puisi & Sajak', 'adigunawan', '2024-10-24', 'disetujui'],
        ['Proses', 'Franz Kafka', 'Fiksi Filsafat & Realisme', 'meisyarahma', '2024-10-23', 'disetujui'],
        ['Madame Bovary', 'Gustave Flaubert', 'Sastra Klasik & Kanon', 'agussalim', '2024-10-23', 'disetujui'],
        ['Blues untuk Bonnie', 'W.S. Rendra', 'Antologi Puisi & Sajak', 'taufikhakim', '2024-10-23', 'disetujui'],
        ['Deru Campur Debu', 'Chairil Anwar', 'Antologi Puisi & Sajak', 'mayasafitri', '2024-10-23', 'disetujui'],
        ['Di Bawah Bendera Revolusi', 'Soekarno', 'Catatan Esai & Humaniora', 'hendrak', '2024-10-22', 'disetujui'],
        ['Polemik Kebudayaan', 'Achdiat K. Mihardja', 'Catatan Esai & Humaniora', 'sekarayu', '2024-10-22', 'disetujui'],
        ['Daun Rumput', 'Walt Whitman', 'Antologi Puisi & Sajak', 'gilangp', '2024-10-21', 'disetujui'],
        ['Siddhartha', 'Hermann Hesse', 'Fiksi Filsafat & Realisme', 'taufikhakim', '2024-10-19', 'disetujui'],
        ['Metamorfosis', 'Franz Kafka', 'Fiksi Filsafat & Realisme', 'gilangp', '2024-10-16', 'disetujui'],
        ['Kejahatan dan Hukuman', 'Fyodor Dostoyevsky', 'Sastra Klasik & Kanon', 'bimasetiawan', '2024-10-15', 'disetujui'],
        ['Moby-Dick', 'Herman Melville', 'Sastra Klasik & Kanon', 'gilangp', '2024-10-13', 'disetujui'],
        ['Bumi Manusia', 'Pramoedya Ananta Toer', 'Fiksi Filsafat & Realisme', 'rinamarlina', '2024-10-10', 'disetujui'],
        ['Sang Nabi', 'Kahlil Gibran', 'Antologi Puisi & Sajak', 'taufikhakim', '2024-10-10', 'disetujui'],
        ['Walden', 'Henry David Thoreau', 'Catatan Esai & Humaniora', 'sekarayu', '2024-10-03', 'disetujui'],
        ['Salah Asuhan', 'Abdoel Moeis', 'Sastra Klasik & Kanon', 'tiaraang', '2024-10-03', 'ditolak'],
        ['Kerikil Tajam', 'Chairil Anwar', 'Antologi Puisi & Sajak', 'rinamarlina', '2024-08-28', 'disetujui'],
        ['Layar Terkembang', 'Sutan Takdir Alisjahbana', 'Sastra Klasik & Kanon', 'taufikhakim', '2024-08-21', 'disetujui'],
        ['Catatan Pinggir 1', 'Goenawan Mohamad', 'Catatan Esai & Humaniora', 'agussalim', '2024-08-12', 'disetujui'],
        ['Sampar', 'Albert Camus', 'Fiksi Filsafat & Realisme', 'rinamarlina', '2024-07-23', 'disetujui'],
        ['Burung-Burung Manyar', 'Y.B. Mangunwijaya', 'Fiksi Filsafat & Realisme', 'mayasafitri', '2024-07-17', 'disetujui'],
        ['Jalan Tak Ada Ujung', 'Mochtar Lubis', 'Fiksi Filsafat & Realisme', 'taufikhakim', '2024-07-12', 'disetujui'],
        ['Kebudayaan, Mentalitas dan Pembangunan', 'Koentjaraningrat', 'Catatan Esai & Humaniora', 'agussalim', '2024-07-06', 'disetujui'],
        ['Belenggu', 'Armijn Pane', 'Sastra Klasik & Kanon', 'yusufibrahim', '2024-06-30', 'disetujui'],
        ['Tirani dan Benteng', 'Taufiq Ismail', 'Antologi Puisi & Sajak', 'gilangp', '2024-05-31', 'disetujui'],
        ['Olenka', 'Budi Darma', 'Fiksi Filsafat & Realisme', 'mayasafitri', '2024-05-28', 'disetujui'],
        ['Don Quixote', 'Miguel de Cervantes', 'Sastra Klasik & Kanon', 'gilangp', '2024-05-26', 'disetujui'],
        ['Nyanyi Sunyi', 'Amir Hamzah', 'Antologi Puisi & Sajak', 'taufikhakim', '2024-05-26', 'disetujui'],
        ['Orang Asing', 'Albert Camus', 'Fiksi Filsafat & Realisme', 'taufikhakim', '2024-05-10', 'disetujui'],
        ['Tenggelamnya Kapal Van der Wijck', 'Hamka', 'Sastra Klasik & Kanon', 'gilangp', '2024-05-02', 'disetujui'],
        ['Sebuah Kamar Sendiri', 'Virginia Woolf', 'Catatan Esai & Humaniora', 'taufikhakim', '2024-04-07', 'disetujui'],
        ['Habis Gelap Terbitlah Terang', 'R.A. Kartini', 'Catatan Esai & Humaniora', 'rinamarlina', '2024-04-04', 'disetujui'],
        ['Meditasi', 'Marcus Aurelius', 'Catatan Esai & Humaniora', 'agussalim', '2024-03-31', 'disetujui'],
        ['Atheis', 'Achdiat K. Mihardja', 'Sastra Klasik & Kanon', 'rinamarlina', '2024-03-22', 'ditolak'],
        ['Pride and Prejudice', 'Jane Austen', 'Sastra Klasik & Kanon', 'taufikhakim', '2024-03-19', 'disetujui'],
        ['Esai-Esai', 'Michel de Montaigne', 'Catatan Esai & Humaniora', 'agussalim', '2024-03-04', 'disetujui'],
        ['Di Bawah Lindungan Ka\'bah', 'Hamka', 'Sastra Klasik & Kanon', 'agussalim', '2024-02-15', 'menunggu'],
        ['Jane Eyre', 'Charlotte Brontë', 'Sastra Klasik & Kanon', 'agussalim', '2024-02-04', 'disetujui'],
        ['Azab dan Sengsara', 'Merari Siregar', 'Sastra Klasik & Kanon', 'agussalim', '2024-01-11', 'disetujui'],
    ]);

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
        <h1 class="page-title">Ringkasan Pustaka &amp; Statistik Platform</h1>
        <p class="page-subtitle">Arsip data aktivitas pembaca dan pertumbuhan katalog buku per November 2024</p>
    </div>

{{-- =============================================
     BARIS TENGAH: kolom kiri (lebar) + kolom kanan (sempit)
     ============================================= --}}
<div class="mid-grid">

    {{-- =========================================
         KOLOM KIRI: grafik + moderasi
         ========================================= --}}
    <div class="mid-left">

        {{-- Baris dua grafik (berdampingan di layar lebar) --}}
        <div class="chart-row">

        {{-- ----- KARTU GRAFIK TOTAL PENGGUNA AKTIF ----- --}}
        <div class="card chart-card">

            {{-- Header grafik --}}
            <div class="chart-card-header">
                <div class="chart-card-header-left">
                    <p class="chart-section-label">Pertumbuhan Pengguna</p>
                    <h2 class="chart-card-title">Total Pengguna Aktif</h2>
                </div>
                <div class="chart-card-header-right">
                    {{-- Legenda --}}
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-dot black"></span>
                            <span>Pengguna Aktif</span>
                        </div>
                    </div>
                    {{-- Filter periode --}}
                    <div class="chart-filter" data-chart="pengguna">
                        <button type="button" class="chart-filter-btn active" data-periode="minggu">Minggu</button>
                        <button type="button" class="chart-filter-btn" data-periode="bulan">Bulan</button>
                        <button type="button" class="chart-filter-btn" data-periode="tahun">Tahun</button>
                    </div>
                </div>
            </div>

            {{-- Grafik Garis ApexCharts --}}
            <div id="chartPengguna" class="chart-apex-wrapper"></div>

            {{-- Kotak info di bawah grafik (diisi otomatis oleh JavaScript) --}}
            <div class="chart-info-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p class="chart-info-text" id="infoPengguna"></p>
                <a href="#detail-log" class="chart-info-link" data-log="pengguna">DETAIL LOG ANALITIK &rarr;</a>
            </div>

        </div>
        {{-- /kartu grafik pengguna --}}

        {{-- ----- KARTU GRAFIK TOTAL BUKU ----- --}}
        <div class="card chart-card">

            {{-- Header grafik --}}
            <div class="chart-card-header">
                <div class="chart-card-header-left">
                    <p class="chart-section-label">Pertumbuhan Katalog</p>
                    <h2 class="chart-card-title">Total Buku</h2>
                </div>
                <div class="chart-card-header-right">
                    {{-- Legenda --}}
                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="legend-dot gray"></span>
                            <span>Total Buku</span>
                        </div>
                    </div>
                    {{-- Filter periode --}}
                    <div class="chart-filter" data-chart="buku">
                        <button type="button" class="chart-filter-btn active" data-periode="minggu">Minggu</button>
                        <button type="button" class="chart-filter-btn" data-periode="bulan">Bulan</button>
                        <button type="button" class="chart-filter-btn" data-periode="tahun">Tahun</button>
                    </div>
                </div>
            </div>

            {{-- Grafik Garis ApexCharts --}}
            <div id="chartBuku" class="chart-apex-wrapper"></div>

            {{-- Kotak info di bawah grafik (diisi otomatis oleh JavaScript) --}}
            <div class="chart-info-box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p class="chart-info-text" id="infoBuku"></p>
                <a href="#detail-log" class="chart-info-link" data-log="buku">DETAIL LOG ANALITIK &rarr;</a>
            </div>

        </div>
        {{-- /kartu grafik buku --}}

        </div>
        {{-- /chart-row --}}


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

       </div>
    {{-- /mid-right --}}

</div>
{{-- /mid-grid --}}

@endsection

@push('modals')
{{-- =============================================
     POP-UP DETAIL LOG ANALITIK
     Muncul hanya saat tombol "DETAIL LOG ANALITIK" ditekan
     ============================================= --}}
<div class="log-modal" id="detail-log" hidden>
    <div class="log-modal-backdrop" data-tutup-log></div>

    <div class="log-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="logJudul">

            {{-- Tombol tutup --}}
            <button type="button" class="log-close" data-tutup-log aria-label="Tutup detail log">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>

            {{-- Header --}}
            <div class="log-header">
                <div class="log-header-left">
                    <p class="chart-section-label">Detail Log Analitik</p>
                    <h2 class="chart-card-title" id="logJudul">Riwayat Pengguna &amp; Buku</h2>
                    <p class="log-periode-text" id="logPeriodeText"></p>
                </div>
                <div class="log-header-right">
                    {{-- Tab data --}}
                    <div class="chart-filter" id="logTab">
                        <button type="button" class="chart-filter-btn active" data-tab="pengguna">Pengguna</button>
                        <button type="button" class="chart-filter-btn" data-tab="buku">Buku</button>
                    </div>
                    {{-- Filter periode --}}
                    <div class="chart-filter" id="logPeriode">
                        <button type="button" class="chart-filter-btn active" data-periode="minggu">Minggu</button>
                        <button type="button" class="chart-filter-btn" data-periode="bulan">Bulan</button>
                        <button type="button" class="chart-filter-btn" data-periode="tahun">Tahun</button>
                    </div>
                </div>
            </div>

            {{-- Ringkasan angka (diisi JavaScript) --}}
            <div class="log-summary" id="logSummary"></div>

            {{-- Pencarian, filter status, ekspor --}}
            <div class="log-toolbar">
                <label class="log-search">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="search" class="log-input" id="logCari" placeholder="Cari…" aria-label="Cari data log">
                </label>
                <select class="log-select" id="logStatus" aria-label="Filter status"></select>
                <button type="button" class="log-btn" id="logEkspor">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Ekspor CSV
                </button>
            </div>

            {{-- Tabel log --}}
            <div class="log-table-wrap">
                <table class="log-table">
                    <colgroup id="logCols"></colgroup>
                    <thead id="logHead"></thead>
                    <tbody id="logBody"></tbody>
                </table>
            </div>

            {{-- Info jumlah & halaman --}}
            <div class="log-footer">
                <span id="logInfo"></span>
                <div class="log-pages" id="logPages"></div>
            </div>

    </div>
</div>
{{-- /pop-up detail log --}}
@endpush

@push('scripts')
{{-- Library ApexCharts via CDN jsdelivr --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statistik = @json($statistik);

        // Format angka gaya Indonesia: 1250 -> 1.250
        const formatAngka = (val) => (val === null || val === undefined)
            ? '-'
            : new Intl.NumberFormat('id-ID').format(Math.round(val));

        const teksPeriode = { minggu: 'minggu ini', bulan: 'bulan ini', tahun: 'tahun ini' };

        // Pengaturan tiap grafik
        const daftarGrafik = {
            pengguna: { selector: '#chartPengguna', info: '#infoPengguna', nama: 'Pengguna Aktif', satuan: 'pengguna aktif', warna: '#111827' },
            buku:     { selector: '#chartBuku',     info: '#infoBuku',     nama: 'Total Buku',     satuan: 'judul buku',     warna: '#9CA3AF' },
        };

        // Isi kotak info: total terakhir + perubahan dalam periode
        function perbaruiInfo(kunci, periode) {
            const cfg  = daftarGrafik[kunci];
            const el   = document.querySelector(cfg.info);
            const data = (statistik[kunci][periode].data || []).filter(v => v !== null);
            if (!el || data.length === 0) return;

            const total   = data[data.length - 1];
            const selisih = total - data[0];
            const arah    = selisih >= 0 ? 'bertambah' : 'berkurang';

            el.innerHTML = 'Total saat ini <strong>' + formatAngka(total) + ' ' + cfg.satuan + '</strong>, '
                + arah + ' <strong>' + formatAngka(Math.abs(selisih)) + '</strong> dalam ' + teksPeriode[periode] + '.';
        }

        // Membuat satu grafik garis
        function buatGrafik(kunci) {
            const cfg = daftarGrafik[kunci];
            const chartElement = document.querySelector(cfg.selector);
            if (!chartElement || !statistik[kunci]) return null;

            const awal = statistik[kunci].minggu;

            const options = {
                chart: {
                    type: 'line',
                    height: 260,
                    fontFamily: 'Inter, system-ui, sans-serif',
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 900,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 450
                        }
                    },
                    zoom: {
                        enabled: false
                    },
                    // Grafik menyesuaikan lebar kartu saat layar berubah
                    redrawOnParentResize: true,
                    redrawOnWindowResize: true
                },
                // Pengaturan khusus layar HP
                responsive: [
                    {
                        breakpoint: 640,
                        options: {
                            chart: { height: 220 },
                            markers: { size: 3 },
                            xaxis: {
                                labels: {
                                    rotate: -45,
                                    hideOverlappingLabels: true
                                }
                            }
                        }
                    }
                ],
                series: [
                    {
                        name: cfg.nama,
                        data: awal.data
                    }
                ],
                xaxis: {
                    categories: awal.labels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#9CA3AF',
                            fontSize: '11px',
                            fontFamily: 'Inter, system-ui, sans-serif'
                        }
                    }
                },
                // Sumbu Y selalu dimulai dari 0, angka bulat
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    tickAmount: 5,
                    labels: {
                        style: {
                            colors: '#9CA3AF',
                            fontSize: '11px',
                            fontFamily: 'Inter, system-ui, sans-serif'
                        },
                        formatter: formatAngka
                    }
                },
                colors: [cfg.warna],
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                markers: {
                    size: 4,
                    colors: [cfg.warna],
                    strokeColors: '#FFFFFF',
                    strokeWidth: 2,
                    hover: {
                        size: 6
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: '#E5E7EB',
                    strokeDashArray: 4,
                    padding: {
                        top: 0,
                        right: 8,
                        bottom: 0,
                        left: 8
                    }
                },
                legend: {
                    show: false
                },
                tooltip: {
                    theme: 'light',
                    x: {
                        show: true
                    },
                    y: {
                        formatter: formatAngka
                    },
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Inter, system-ui, sans-serif'
                    }
                }
            };

            const chart = new ApexCharts(chartElement, options);
            chart.render();
            perbaruiInfo(kunci, 'minggu');
            return chart;
        }

        const chartAktif = {
            pengguna: buatGrafik('pengguna'),
            buku: buatGrafik('buku'),
        };

        // Klik filter Minggu / Bulan / Tahun
        document.querySelectorAll('.chart-filter').forEach(function (grup) {
            const kunci = grup.dataset.chart;

            grup.querySelectorAll('.chart-filter-btn').forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    const periode = tombol.dataset.periode;
                    const sumber  = statistik[kunci] && statistik[kunci][periode];
                    if (!chartAktif[kunci] || !sumber) return;

                    grup.querySelectorAll('.chart-filter-btn').forEach(b => b.classList.remove('active'));
                    tombol.classList.add('active');

                    chartAktif[kunci].updateOptions({
                        xaxis: { categories: sumber.labels },
                        series: [{ name: daftarGrafik[kunci].nama, data: sumber.data }]
                    });

                    perbaruiInfo(kunci, periode);
                });
            });
        });

        /* =============================================
           DETAIL LOG ANALITIK
           ============================================= */
        const logData = {
            pengguna: @json(array_values($logPengguna)),
            buku: @json(array_values($logBuku)),
        };
        const tanggalAcuan = new Date(@json($tanggalAcuan) + 'T00:00:00');
        const PER_HALAMAN = 8;

        const logState = { tab: 'pengguna', periode: 'minggu', cari: '', status: 'semua', halaman: 1 };

        // Cegah HTML dari data (keamanan saat data dari database)
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        const keTanggal = (s) => new Date(String(s).replace(' ', 'T') + (String(s).length <= 10 ? 'T00:00:00' : ''));
        const fmtTanggal = (s) => keTanggal(s).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        const fmtWaktu = (s) => {
            const d = keTanggal(s);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ', '
                + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        };
        const badge = (status) => '<span class="log-badge ' + esc(status) + '">' + esc(status.charAt(0).toUpperCase() + status.slice(1)) + '</span>';

        // Susunan kolom & status untuk tiap tab
        const logConfig = {
            pengguna: {
                kolomTanggal: 'daftar',
                placeholder: 'Cari nama atau username…',
                status: ['aktif', 'nonaktif', 'dibanned'],
                lebar: ['34%', '22%', '26%', '18%'],
                kolom: [
                    { label: 'Pengguna',       isi: r => '<div class="log-main" title="' + esc(r.nama) + '">' + esc(r.nama) + '</div><div class="log-sub" title="@' + esc(r.username) + '">@' + esc(r.username) + '</div>' },
                    { label: 'Tanggal Daftar', isi: r => fmtTanggal(r.daftar) },
                    { label: 'Aktif Terakhir', isi: r => fmtWaktu(r.login) },
                    { label: 'Status',         isi: r => badge(r.status) },
                ],
                cari: r => (r.nama + ' ' + r.username).toLowerCase(),
                csv: { header: ['Nama', 'Username', 'Tanggal Daftar', 'Aktif Terakhir', 'Status'], baris: r => [r.nama, r.username, r.daftar, r.login, r.status] },
                ringkasan: (rows) => [
                    { label: 'Pendaftar Baru',  nilai: rows.length, catatan: 'akun terdaftar' },
                    { label: 'Masih Aktif',     nilai: rows.filter(r => r.status === 'aktif').length, catatan: 'dari pendaftar baru' },
                    { label: 'Nonaktif / Banned', nilai: rows.filter(r => r.status !== 'aktif').length, catatan: 'perlu ditinjau' },
                    { label: 'Total Pengguna Aktif', nilai: logData.pengguna.filter(r => r.status === 'aktif').length, catatan: 'seluruh platform' },
                ],
            },
            buku: {
                kolomTanggal: 'tanggal',
                placeholder: 'Cari judul, penulis, atau pengaju…',
                status: ['disetujui', 'menunggu', 'ditolak'],
                lebar: ['32%', '23%', '17%', '15%', '13%'],
                kolom: [
                    { label: 'Judul Buku',    isi: r => '<div class="log-main" title="' + esc(r.judul) + '">' + esc(r.judul) + '</div><div class="log-sub" title="' + esc(r.penulis) + '">' + esc(r.penulis) + '</div>' },
                    { label: 'Genre',         isi: r => '<span title="' + esc(r.genre) + '">' + esc(r.genre) + '</span>' },
                    { label: 'Pengaju',       isi: r => '@' + esc(r.pengaju) },
                    { label: 'Tanggal Masuk', isi: r => fmtTanggal(r.tanggal) },
                    { label: 'Status',        isi: r => badge(r.status) },
                ],
                cari: r => (r.judul + ' ' + r.penulis + ' ' + r.pengaju).toLowerCase(),
                csv: { header: ['Judul', 'Penulis', 'Genre', 'Pengaju', 'Tanggal Masuk', 'Status'], baris: r => [r.judul, r.penulis, r.genre, r.pengaju, r.tanggal, r.status] },
                ringkasan: (rows) => [
                    { label: 'Buku Masuk',        nilai: rows.length, catatan: 'pengajuan judul' },
                    { label: 'Disetujui',         nilai: rows.filter(r => r.status === 'disetujui').length, catatan: 'masuk katalog' },
                    { label: 'Menunggu Validasi', nilai: rows.filter(r => r.status === 'menunggu').length, catatan: 'belum diperiksa' },
                    { label: 'Ditolak',           nilai: rows.filter(r => r.status === 'ditolak').length, catatan: 'tidak lolos kurasi' },
                ],
            },
        };

        // Rentang tanggal untuk minggu / bulan / tahun berjalan
        function rentangPeriode(periode) {
            const a = new Date(tanggalAcuan);
            let mulai, akhir;
            if (periode === 'minggu') {
                const geser = (a.getDay() + 6) % 7; // Senin = 0
                mulai = new Date(a.getFullYear(), a.getMonth(), a.getDate() - geser);
                akhir = new Date(mulai.getFullYear(), mulai.getMonth(), mulai.getDate() + 6, 23, 59, 59);
            } else if (periode === 'bulan') {
                mulai = new Date(a.getFullYear(), a.getMonth(), 1);
                akhir = new Date(a.getFullYear(), a.getMonth() + 1, 0, 23, 59, 59);
            } else {
                mulai = new Date(a.getFullYear(), 0, 1);
                akhir = new Date(a.getFullYear(), 11, 31, 23, 59, 59);
            }
            return { mulai, akhir };
        }

        function teksRentang(periode) {
            const { mulai, akhir } = rentangPeriode(periode);
            const f = (d) => d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            return 'Periode ' + f(mulai) + ' – ' + f(akhir);
        }

        function dataPeriode() {
            const cfg = logConfig[logState.tab];
            const { mulai, akhir } = rentangPeriode(logState.periode);
            return logData[logState.tab]
                .filter(r => { const d = keTanggal(r[cfg.kolomTanggal]); return d >= mulai && d <= akhir; })
                .sort((x, y) => keTanggal(y[cfg.kolomTanggal]) - keTanggal(x[cfg.kolomTanggal]));
        }

        function dataTersaring() {
            const cfg = logConfig[logState.tab];
            const kata = logState.cari.trim().toLowerCase();
            return dataPeriode().filter(r =>
                (logState.status === 'semua' || r.status === logState.status) &&
                (!kata || cfg.cari(r).includes(kata))
            );
        }

        function isiStatusSelect() {
            const cfg = logConfig[logState.tab];
            const sel = document.getElementById('logStatus');
            sel.innerHTML = '<option value="semua">Semua status</option>'
                + cfg.status.map(s => '<option value="' + s + '">' + s.charAt(0).toUpperCase() + s.slice(1) + '</option>').join('');
            sel.value = logState.status;
            document.getElementById('logCari').placeholder = cfg.placeholder;
        }

        function aktifkanTombol(grupId, attr, nilai) {
            document.querySelectorAll('#' + grupId + ' .chart-filter-btn').forEach(b => {
                b.classList.toggle('active', b.dataset[attr] === nilai);
            });
        }

        function renderLog() {
            const cfg = logConfig[logState.tab];

            // Teks periode
            document.getElementById('logPeriodeText').textContent = teksRentang(logState.periode);

            // Ringkasan
            document.getElementById('logSummary').innerHTML = cfg.ringkasan(dataPeriode()).map(s =>
                '<div class="log-summary-item">'
                + '<p class="log-summary-label">' + esc(s.label) + '</p>'
                + '<p class="log-summary-value">' + formatAngka(s.nilai) + '</p>'
                + '<p class="log-summary-note">' + esc(s.catatan) + '</p>'
                + '</div>'
            ).join('');

            // Lebar kolom tetap (sama di semua halaman)
            document.getElementById('logCols').innerHTML =
                cfg.lebar.map(w => '<col style="width:' + w + '">').join('');

            // Header tabel
            document.getElementById('logHead').innerHTML =
                '<tr>' + cfg.kolom.map(k => '<th>' + esc(k.label) + '</th>').join('') + '</tr>';

            // Isi tabel + halaman
            const rows = dataTersaring();
            const totalHalaman = Math.max(1, Math.ceil(rows.length / PER_HALAMAN));
            logState.halaman = Math.min(logState.halaman, totalHalaman);
            const awal = (logState.halaman - 1) * PER_HALAMAN;
            const tampil = rows.slice(awal, awal + PER_HALAMAN);

            let isiTabel = tampil.length
                ? tampil.map(r => '<tr>' + cfg.kolom.map(k => '<td data-label="' + esc(k.label) + '"><div class="log-cell">' + k.isi(r) + '</div></td>').join('') + '</tr>').join('')
                : '<tr><td class="log-empty" colspan="' + cfg.kolom.length + '">Tidak ada data pada periode atau filter ini.</td></tr>';

            // Baris kosong pengisi supaya tinggi tabel selalu sama (PER_HALAMAN baris)
            // di halaman terakhir yang datanya kurang dari PER_HALAMAN
            const sisa = PER_HALAMAN - Math.max(tampil.length, 1);
            const barisKosong = '<tr class="log-filler" aria-hidden="true">'
                + '<td><div class="log-cell"><div class="log-main">&nbsp;</div><div class="log-sub">&nbsp;</div></div></td>'
                + '<td></td>'.repeat(cfg.kolom.length - 1)
                + '</tr>';
            isiTabel += barisKosong.repeat(Math.max(0, sisa));

            document.getElementById('logBody').innerHTML = isiTabel;

            document.getElementById('logInfo').textContent = rows.length
                ? 'Menampilkan ' + (awal + 1) + '–' + (awal + tampil.length) + ' dari ' + rows.length + ' data'
                : '0 data';

            // Tombol halaman
            let tombol = '<button type="button" class="log-page-btn" data-halaman="' + (logState.halaman - 1) + '" ' + (logState.halaman === 1 ? 'disabled' : '') + ' aria-label="Sebelumnya">&lsaquo;</button>';
            for (let i = 1; i <= totalHalaman; i++) {
                tombol += '<button type="button" class="log-page-btn ' + (i === logState.halaman ? 'active' : '') + '" data-halaman="' + i + '">' + i + '</button>';
            }
            tombol += '<button type="button" class="log-page-btn" data-halaman="' + (logState.halaman + 1) + '" ' + (logState.halaman === totalHalaman ? 'disabled' : '') + ' aria-label="Berikutnya">&rsaquo;</button>';
            document.getElementById('logPages').innerHTML = tombol;
        }

        function gantiTab(tab) {
            logState.tab = tab;
            logState.status = 'semua';
            logState.cari = '';
            logState.halaman = 1;
            document.getElementById('logCari').value = '';
            aktifkanTombol('logTab', 'tab', tab);
            isiStatusSelect();
            renderLog();
        }

        function gantiPeriode(periode) {
            logState.periode = periode;
            logState.halaman = 1;
            aktifkanTombol('logPeriode', 'periode', periode);
            renderLog();
        }

        // Buka & tutup pop-up detail log
        let pemicuLog = null;
        function bukaLog(pemicu) {
            const modal = document.getElementById('detail-log');
            pemicuLog = pemicu || null;
            modal.hidden = false;
            document.body.classList.add('log-terbuka');
            modal.querySelector('.log-modal-dialog').scrollTop = 0;
            modal.querySelector('.log-close').focus();
        }
        function tutupLog() {
            document.getElementById('detail-log').hidden = true;
            document.body.classList.remove('log-terbuka');
            if (pemicuLog) pemicuLog.focus();
        }

        if (document.getElementById('detail-log')) {
            document.querySelectorAll('#logTab .chart-filter-btn').forEach(b =>
                b.addEventListener('click', () => gantiTab(b.dataset.tab)));

            document.querySelectorAll('#logPeriode .chart-filter-btn').forEach(b =>
                b.addEventListener('click', () => gantiPeriode(b.dataset.periode)));

            document.getElementById('logCari').addEventListener('input', (e) => {
                logState.cari = e.target.value;
                logState.halaman = 1;
                renderLog();
            });

            document.getElementById('logStatus').addEventListener('change', (e) => {
                logState.status = e.target.value;
                logState.halaman = 1;
                renderLog();
            });

            document.getElementById('logPages').addEventListener('click', (e) => {
                const b = e.target.closest('.log-page-btn');
                if (!b || b.disabled) return;
                logState.halaman = parseInt(b.dataset.halaman, 10);
                renderLog();
            });

            // Ekspor data yang sedang tampil (sesuai periode & filter) ke CSV
            document.getElementById('logEkspor').addEventListener('click', () => {
                const cfg = logConfig[logState.tab];
                const sel = (v) => '"' + String(v ?? '').replace(/"/g, '""') + '"';
                const isi = [cfg.csv.header, ...dataTersaring().map(cfg.csv.baris)]
                    .map(baris => baris.map(sel).join(','))
                    .join('\r\n');
                const blob = new Blob(['﻿' + isi], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'log-' + logState.tab + '-' + logState.periode + '.csv';
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            });

            // Tombol "DETAIL LOG ANALITIK" di kartu grafik:
            // buka tab yang sesuai dengan periode yang sama seperti grafiknya
            document.querySelectorAll('.chart-info-link[data-log]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tab = link.dataset.log;
                    const aktif = document.querySelector('.chart-filter[data-chart="' + tab + '"] .chart-filter-btn.active');
                    gantiTab(tab);
                    gantiPeriode(aktif ? aktif.dataset.periode : 'minggu');
                    bukaLog(link);
                });
            });

            // Tutup lewat tombol X atau klik area gelap
            document.querySelectorAll('[data-tutup-log]').forEach(el =>
                el.addEventListener('click', tutupLog));

            // Tutup dengan tombol Esc
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !document.getElementById('detail-log').hidden) tutupLog();
            });

            isiStatusSelect();
            renderLog();
        }
    });
</script>
@endpush