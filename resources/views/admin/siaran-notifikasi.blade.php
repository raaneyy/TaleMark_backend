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
            {{-- Ikon daftar dengan pensil --}}
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="6" x2="14" y2="6"></line>
                <line x1="4" y1="12" x2="10" y2="12"></line>
                <line x1="4" y1="18" x2="14" y2="18"></line>
                <path d="M15 4l4 4-7 7H8v-4l7-7z"></path>
            </svg>
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
                {{-- Ikon pesawat kertas --}}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
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
