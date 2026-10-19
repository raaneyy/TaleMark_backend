{{-- resources/views/admin/siaran-notifikasi.blade.php
     Halaman Kelola Notifikasi (Siaran Notifikasi) Admin TaleMark
     Tampilan frontend murni untuk pengumuman editorial (backend dikerjakan terpisah)
--}}
@extends('layouts.admin')

@section('title', 'Kelola Notifikasi')

{{-- CSS khusus halaman siaran notifikasi --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-siaran-notifikasi.css') }}">
@endsection

@section('content')

{{-- =============================================
     HEADER HALAMAN
     ============================================= --}}
<div class="notification-header">
    <h1 class="notification-title">Kelola Notifikasi</h1>
    <p class="notification-subtitle">Kirim pengumuman langsung ke pembaca TaleMark.</p>
</div>

{{-- =============================================
     KARTU FORM NOTIFIKASI
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

{{-- Kontainer Toast Notifikasi --}}
<div id="toastContainer" class="toast-container" aria-live="assertive"></div>

@endsection

{{-- Script JS khusus halaman siaran notifikasi --}}
@section('scripts')
    <script src="{{ asset('js/admin-siaran-notifikasi.js') }}"></script>
@endsection
