{{-- resources/views/admin/siaran-notifikasi.blade.php
     Halaman Kelola Notifikasi (Siaran Notifikasi) Admin TaleMark
     Tampilan frontend murni untuk pengumuman editorial (backend dikerjakan terpisah)
--}}
@extends('layouts.admin')

@section('title', 'Kelola Notifikasi')

{{-- CSS khusus halaman siaran notifikasi --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-siaran-notifikasi.css') }}?v={{ file_exists(public_path('css/admin-siaran-notifikasi.css')) ? filemtime(public_path('css/admin-siaran-notifikasi.css')) : time() }}">
@endsection

@section('content')

{{-- =============================================
     DATA DUMMY — array lokal riwayat siaran
     ============================================= --}}
@php
    $riwayatSiaran = [
        [
            'id'             => 'BRD-101',
            'status'         => 'terkirim',
            'label_status'   => 'TERKIRIM',
            'waktu'          => '21 Okt 2024 08:00',
            'judul'          => 'Selamat Hari Buku Nasional 2024',
            'isi'            => 'Buku adalah jembatan sunyi yang menghubungkan dua jiwa yang tak pernah bersua.',
            'terjangkau'     => '94.200',
            'dibaca'         => '88,4%',
        ],
        [
            'id'             => 'BRD-102',
            'status'         => 'terjadwal',
            'label_status'   => 'TERJADWAL',
            'waktu'          => '25 Okt 2024 09:00',
            'jadwal_kirim'   => 'Dikirim pada 25 Okt 2024, 09:00',
            'judul'          => 'Pekan Literasi Dimulai Besok',
            'isi'            => 'Nikmati kurasi esai eksklusif selama pekan literasi.',
        ],
        [
            'id'             => 'BRD-103',
            'status'         => 'terkirim',
            'label_status'   => 'TERKIRIM',
            'waktu'          => '18 Okt 2024 19:30',
            'judul'          => 'Fitur Baru: Rekap Bacaan Akhir Bulan',
            'isi'            => 'Pantau statistik membaca buku bulanan Anda dengan tampilan kalender interaktif.',
            'terjangkau'     => '91.870',
            'dibaca'         => '72,1%',
        ],
        [
            'id'             => 'BRD-104',
            'status'         => 'terkirim',
            'label_status'   => 'TERKIRIM',
            'waktu'          => '15 Okt 2024 20:00',
            'judul'          => 'Pengingat Sesi Hening Malam Ini',
            'isi'            => 'Sesi hening membaca bersama komunitas akan dimulai pukul 20:30 WIB. Siapkan buku favoritmu.',
            'terjangkau'     => '90.540',
            'dibaca'         => '65,3%',
        ],
    ];
@endphp

{{-- =============================================
     HEADER HALAMAN
     ============================================= --}}
<div class="notification-header">
    <h1 class="notification-title">Kelola Notifikasi</h1>
    <p class="notification-subtitle">Kirim pengumuman langsung ke pembaca TaleMark.</p>
</div>

{{-- =============================================
     GRID DUA KOLOM: Form (kiri) + Riwayat (kanan)
     ============================================= --}}
<div class="notification-layout-grid">

    {{-- =============================================
         KOLOM KIRI: KARTU FORM NOTIFIKASI
         ============================================= --}}
    <div class="notif-card">

        {{-- Header kartu: Kotak ikon + Judul --}}
        <div class="notif-card-header">
            <div class="notif-card-icon" aria-hidden="true">
                <svg viewBox="0 0 27 24" fill="currentColor"><rect x="0" y="0" width="17" height="3" rx="0.5"/><rect x="0" y="6" width="17" height="3" rx="0.5"/><rect x="0" y="12" width="11" height="3" rx="0.5"/><path d="M13.5 24V20.75L23.15 11.1Q23.75 10.5 24.35 11.1L26.4 13.15Q27 13.75 26.4 14.35L16.75 24Z"/><path d="M16.4 21.2L20.4 17.2" stroke="#fff" stroke-width="1.9" stroke-linecap="round" fill="none"/></svg>
            </div>
            <h2 class="notif-card-title">Form Pembuatan Notifikasi</h2>
        </div>

        {{-- Form input notifikasi --}}
        <form id="formSiaranNotifikasi" method="POST" action="#" novalidate>
            @csrf

            {{-- Field 1: Judul Notifikasi --}}
            <div class="form-field">
                <div class="field-label-row">
                    <label for="judul" class="field-label">Judul Notifikasi</label>
                    <span id="counterJudul" class="field-hint">Maks. 60 Karakter</span>
                </div>
                <input
                    type="text"
                    id="judul"
                    name="judul"
                    class="form-input"
                    placeholder="Hari Buku Nasional: Buka Lembaran Baru Hari Ini"
                    maxlength="60"
                    autocomplete="off"
                >
                <span class="field-error" id="errorJudul" aria-live="polite"></span>
            </div>

            {{-- Field 2: Isi Pesan Editorial --}}
            <div class="form-field">
                <div class="field-label-row">
                    <label for="isi_pesan" class="field-label">Isi Pesan Editorial</label>
                    {{-- TODO: fitur format kutipan ditentukan oleh tim backend/desain --}}
                    <span class="field-hint">Format Kutipan Tersedia</span>
                </div>
                <textarea
                    id="isi_pesan"
                    name="isi_pesan"
                    class="form-textarea"
                    rows="6"
                    placeholder="Hari ini adalah Hari Membaca Nasional! Luangkan 25 menit sesi hening di pojok favoritmu dan catat progres buku terbaru."
                ></textarea>
                <span class="field-error" id="errorIsiPesan" aria-live="polite"></span>
            </div>

            {{-- Field 3: Jadwal Pengiriman --}}
            <div class="form-field">
                <div class="field-label-row">
                    <label for="jadwal" class="field-label">Jadwal Pengiriman</label>
                </div>
                <div class="select-wrapper">
                    <select id="jadwal" name="jadwal" class="form-select">
                        <option value="sekarang" selected>Kirim Sekarang (Segera)</option>
                        <option value="jadwalkan">Jadwalkan Pengiriman</option>
                    </select>
                    {{-- Ikon jam --}}
                    <span class="select-icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </span>
                </div>
                <span class="field-error" id="errorJadwal" aria-live="polite"></span>
            </div>

            {{-- Field Tambahan: Tanggal & Waktu (muncul jika mode jadwal dipilih) --}}
            <div id="scheduleFields" class="schedule-fields" style="display: none;">
                <div class="schedule-grid">
                    <div class="form-field">
                        <label for="tanggal" class="field-label">Tanggal Pengiriman</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-input">
                        <span class="field-error" id="errorTanggal" aria-live="polite"></span>
                    </div>
                    <div class="form-field">
                        <label for="jam" class="field-label">Waktu Pengiriman</label>
                        <input type="time" id="jam" name="jam" class="form-input">
                        <span class="field-error" id="errorJam" aria-live="polite"></span>
                    </div>
                </div>
                <span class="field-error" id="errorWaktu" aria-live="polite"></span>
            </div>

            {{-- Tombol Aksi di Kanan Bawah --}}
            <div class="form-actions">
                <button type="submit" id="btnSubmit" class="btn-send">
                    <svg viewBox="0 0 24 20.3" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M0 0L24 10L0 20.3ZM2.8 4L17.5 10L2.8 16ZM2.8 8.3L10.5 10L2.8 12Z"/></svg>
                    <span id="btnSubmitText">Kirim Siaran Notifikasi Sekarang</span>
                </button>
            </div>

        </form>

    </div>

    {{-- =============================================
         KOLOM KANAN: PANEL RIWAYAT SIARAN
         ============================================= --}}
    <aside class="broadcast-history-panel" aria-label="Riwayat Siaran">

        {{-- Header Panel --}}
        <div class="broadcast-history-header">
            <p class="history-panel-label">ARSIP SIARAN</p>
            <h2 class="history-panel-title">Riwayat Siaran</h2>
        </div>

        {{-- Tab Filter Pill --}}
        <div class="history-tab-group" role="tablist" aria-label="Filter riwayat siaran">
            <button type="button" class="history-tab-btn active" data-filter="semua">Semua</button>
            <button type="button" class="history-tab-btn" data-filter="terkirim">Terkirim</button>
            <button type="button" class="history-tab-btn" data-filter="terjadwal">Terjadwal</button>
        </div>

        {{-- Daftar Item Riwayat (Scroll di dalam panel) --}}
        <div class="history-list" id="broadcastHistoryList">
            @foreach ($riwayatSiaran as $item)
                <div class="history-item" data-id="{{ $item['id'] }}" data-status="{{ $item['status'] }}">
                    <div class="history-item-top">
                        <span class="history-status-pill pill-{{ $item['status'] }}">
                            {{ $item['label_status'] }}
                        </span>
                        <span class="history-item-time">{{ $item['waktu'] }}</span>
                    </div>

                    <h3 class="history-item-title">{{ $item['judul'] }}</h3>
                    <p class="history-item-snippet" data-full-content="{{ $item['isi'] }}">{{ $item['isi'] }}</p>

                    @if ($item['status'] === 'terkirim')
                        <div class="history-item-stats">
                            <div class="history-stat">
                                <strong>{{ $item['terjangkau'] }}</strong>
                                <span>pembaca terjangkau</span>
                            </div>
                            <div class="history-stat">
                                <strong>{{ $item['dibaca'] }}</strong>
                                <span>dibaca</span>
                            </div>
                        </div>
                    @elseif ($item['status'] === 'terjadwal')
                        <p class="history-scheduled-info">
                            {{ $item['jadwal_kirim'] ?? 'Dikirim pada 25 Okt 2024, 09:00' }}
                        </p>
                    @else
                        <p class="history-cancelled-info">
                            Dibatalkan
                        </p>
                    @endif

                    <div class="history-item-actions">
                        <button type="button" class="btn-history-action btn-reuse" data-id="{{ $item['id'] }}">
                            Edit
                        </button>
                        @if ($item['status'] === 'terjadwal')
                            <button type="button" class="btn-history-action btn-cancel-broadcast" data-id="{{ $item['id'] }}">
                                Batalkan
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Pesan kosong jika tidak ada item pada kategori yang dipilih --}}
            <div class="history-empty-state" id="historyEmptyState" style="display: none;">
                Belum ada siaran pada kategori ini.
            </div>
        </div>

        {{-- Footer Panel: Tombol Lihat Semua Riwayat --}}
        {{-- TODO: hubungkan ke rute backend arsip riwayat siaran lengkap --}}
        <button type="button" class="btn-all-history" id="btnAllHistory">
            Lihat Semua Riwayat
        </button>

    </aside>

</div>

{{-- Kontainer Toast Notifikasi --}}
<div id="toastContainer" class="toast-container" aria-live="assertive"></div>

@endsection

{{-- Script JS khusus halaman siaran notifikasi --}}
@section('scripts')
    <script src="{{ asset('js/admin-siaran-notifikasi.js') }}?v={{ file_exists(public_path('js/admin-siaran-notifikasi.js')) ? filemtime(public_path('js/admin-siaran-notifikasi.js')) : time() }}"></script>
@endsection
