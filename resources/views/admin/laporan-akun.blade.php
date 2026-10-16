{{-- resources/views/admin/laporan-akun.blade.php
     Halaman Laporan Akun & Tinjau Komunitas Admin TaleMark
     Tampilan frontend murni dengan data dummy lokal (backend dikerjakan terpisah)
--}}
@extends('layouts.admin')

@section('title', 'Laporan Akun & Tinjau Komunitas')

{{-- CSS khusus halaman laporan akun --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-laporan-akun.css') }}">
@endsection

@section('content')

{{-- =============================================
     DATA DUMMY — array lokal laporan akun
     ============================================= --}}
@php
    $laporanAkun = [
        [
            'id'             => 'USR-9021',
            'username'       => '@bot_promobuku_99',
            'inisial'        => 'B',
            'terdaftar'      => '2 hari lalu',
            'tingkat_risiko' => 'TINGGI',
            'pelapor_count'  => 14,
            'alasan'         => 'Spam tautan affiliate ilegal di kolom komentar feed sastra',
            'bukti'          => '45 komentar serupa terdeteksi dalam 1 jam secara paralel.',
            'kategori'       => 'spam',
            'status'         => 'baru',
        ],
        [
            'id'             => 'USR-4412',
            'username'       => '@pembaca_kasar',
            'inisial'        => 'P',
            'terdaftar'      => '3 bulan lalu',
            'tingkat_risiko' => 'SEDANG',
            'pelapor_count'  => 6,
            'alasan'         => 'Komentar bernada pelecehan terhadap penulis di kolom ulasan buku',
            'bukti'          => '8 komentar dengan ujaran kebencian ditandai oleh pembaca lain.',
            'kategori'       => 'sara',
            'status'         => 'baru',
        ],
        [
            'id'             => 'USR-7830',
            'username'       => '@kutipan_ulang',
            'inisial'        => 'K',
            'terdaftar'      => '1 bulan lalu',
            'tingkat_risiko' => 'RENDAH',
            'pelapor_count'  => 2,
            'alasan'         => 'Kutipan dibagikan berulang tanpa atribusi penulis asli',
            'bukti'          => '3 kutipan duplikat diunggah dalam kurun waktu 15 menit.',
            'kategori'       => 'spam',
            'status'         => 'selesai',
        ],
    ];
@endphp

{{-- =============================================
     HEADER HALAMAN
     ============================================= --}}
<div class="report-header">
    <h1 class="report-title">Laporan Akun &amp; Tinjau Komunitas</h1>
    <p class="report-subtitle">
        Daftar peninjauan akun yang dilaporkan oleh pembaca karena pelanggaran etika, spam kutipan, atau konten tidak pantas.
    </p>
</div>

{{-- =============================================
     TIGA KARTU STATISTIK (SAMA TINGGI)
     ============================================= --}}
<div class="report-stats-grid">

    {{-- Kartu 1: Perlu Ditinjau Segera --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-card-label">PERLU DITINJAU SEGERA</span>
            <div class="stat-card-icon icon-danger">
                {{-- Ikon lonceng (peringatan) --}}
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    <path d="M12 7v4"/>
                    <path d="M12 14h.01"/>
                </svg>
            </div>
        </div>
        <div class="stat-card-number-row">
            <span class="stat-card-number">12</span>
            <span class="stat-card-badge text-danger">+4 hari ini</span>
        </div>
        <h3 class="stat-card-title">Laporan Baru Membutuhkan Review</h3>
    </div>

    {{-- Kartu 2: Akun Banned --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-card-label">AKUN BANNED</span>
            <div class="stat-card-icon icon-neutral">
                {{-- Ikon gembok dengan jam --}}
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7.5 10V7a3.5 3.5 0 0 1 7 0v3"/>
                    <path d="M15 21H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1"/>
                    <circle cx="18" cy="18" r="4"/>
                    <path d="M18 16.5V18l1 1"/>
                </svg>
            </div>
        </div>
        <div class="stat-card-number-row">
            <span class="stat-card-number">5</span>
            <span class="stat-card-badge text-muted">Akses Terkunci</span>
        </div>
        <h3 class="stat-card-title">Akun Dalam Penangguhan Sementara</h3>
    </div>

    {{-- Kartu 3: Data --}}
    <div class="stat-card">
        <div class="stat-card-top">
            <span class="stat-card-label">DATA</span>
            <div class="stat-card-icon icon-dark">
                {{-- Ikon lencana rosette 12 gerigi dengan centang (sesuai desain referensi) --}}
                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12,4.5 14.46,2.82 15.75,5.5 18.72,5.28 18.5,8.25 21.18,9.54 19.5,12 21.18,14.46 18.5,15.75 18.72,18.72 15.75,18.5 14.46,21.18 12,19.5 9.54,21.18 8.25,18.5 5.28,18.72 5.5,15.75 2.82,14.46 4.5,12 2.82,9.54 5.5,8.25 5.28,5.28 8.25,5.5 9.54,2.82" />
                    <polyline points="9.5 12 11.5 14 15 10" stroke-width="2.4" />
                </svg>
            </div>
        </div>
        <div class="stat-card-number-row">
            <span class="stat-card-number">142</span>
            <span class="stat-card-badge text-muted">98.4% akurat</span>
        </div>
        <h3 class="stat-card-title">Kasus Diselesaikan Bulan Ini</h3>
    </div>

</div>

{{-- =============================================
     BAR FILTER
     ============================================= --}}
<div class="report-filter-bar">

    {{-- Tab pill di sisi kiri --}}
    <div class="report-tab-group" role="tablist" aria-label="Kategori laporan">
        <button type="button" class="report-tab-btn active" data-filter="semua">
            Semua Laporan (12)
        </button>
        <button type="button" class="report-tab-btn" data-filter="spam">
            Spam &amp; Bot (6)
        </button>
        <button type="button" class="report-tab-btn" data-filter="sara">
            Kutipan SARA / Pelecehan (4)
        </button>
        <button type="button" class="report-tab-btn" data-filter="selesai">
            Selesai
        </button>
    </div>

    {{-- Kontrol select filter di sisi kanan --}}
    <div class="report-filter-controls">
        {{-- TODO: sambungkan pengurutan ke route backend --}}
        <div class="select-btn-wrapper">
            <svg class="select-btn-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="21" y1="6" x2="3" y2="6"></line>
                <line x1="17" y1="12" x2="7" y2="12"></line>
                <line x1="13" y1="18" x2="11" y2="18"></line>
            </svg>
            <select class="select-btn" id="selectUrutan" aria-label="Urutan laporan">
                <option value="prioritas">Urutan: Prioritas Risiko</option>
                <option value="terbaru">Terbaru</option>
                <option value="terbanyak">Terbanyak Dilaporkan</option>
            </select>
        </div>

        {{-- TODO: sambungkan kanal forum ke route backend --}}
        <div class="select-btn-wrapper">
            <svg class="select-btn-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            <select class="select-btn" id="selectKanal" aria-label="Semua Kanal Forum">
                <option value="semua">Semua Kanal Forum</option>
                <option value="feed">Feed Sastra</option>
                <option value="ulasan">Ulasan Buku</option>
                <option value="kutipan">Kutipan</option>
            </select>
        </div>
    </div>

</div>

{{-- =============================================
     DAFTAR KARTU LAPORAN
     ============================================= --}}
<div class="report-cards-list" id="reportCardsList">

    @foreach ($laporanAkun as $item)
        <div class="report-card"
             data-id="{{ $item['id'] }}"
             data-kategori="{{ $item['kategori'] }}"
             data-status="{{ $item['status'] }}"
             data-username="{{ $item['username'] }}">

            {{-- Baris atas kartu: avatar, info akun, badge risiko, & pill jumlah pelapor --}}
            <div class="report-card-top">
                <div class="report-user-info">
                    {{-- Avatar inisial huruf --}}
                    <div class="report-avatar {{ $item['tingkat_risiko'] === 'TINGGI' ? 'avatar-danger' : 'avatar-neutral' }}">
                        {{ $item['inisial'] }}
                    </div>

                    <div class="report-user-meta">
                        <div class="report-user-row">
                            <span class="report-username">{{ $item['username'] }}</span>

                            {{-- Badge tingkat risiko --}}
                            @if ($item['tingkat_risiko'] === 'TINGGI')
                                <span class="risk-badge risk-high">TINGKAT RISIKO: TINGGI</span>
                            @elseif ($item['tingkat_risiko'] === 'SEDANG')
                                <span class="risk-badge risk-medium">TINGKAT RISIKO: SEDANG</span>
                            @else
                                <span class="risk-badge risk-low">TINGKAT RISIKO: RENDAH</span>
                            @endif
                        </div>

                        <p class="report-user-sub">
                            Terdaftar: {{ $item['terdaftar'] }} &bull; ID: #{{ $item['id'] }}
                        </p>
                    </div>
                </div>

                {{-- Pill jumlah pembaca yang melaporkan --}}
                <div class="report-reporter-pill">
                    {{-- Ikon bendera merah --}}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                        <line x1="4" y1="22" x2="4" y2="15"></line>
                    </svg>
                    <span>{{ $item['pelapor_count'] }} Pembaca Melaporkan</span>
                </div>
            </div>

            {{-- Kotak abu muda: Alasan pokok & bukti snapshot --}}
            <div class="report-reason-box">
                <p class="report-reason-label">ALASAN PELAPORAN POKOK</p>
                <blockquote class="report-reason-quote">
                    &ldquo;{{ $item['alasan'] }}&rdquo;
                </blockquote>
                <div class="report-evidence-row">
                    {{-- Ikon snapshot / bukti --}}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="10" r="1"></circle>
                    </svg>
                    <span class="report-evidence-text">
                        <strong>Bukti / Snapshot:</strong> {{ $item['bukti'] }}
                    </span>
                </div>
            </div>

            {{-- Baris aksi tombol --}}
            <div class="report-actions-row">
                @if ($item['status'] === 'selesai')
                    {{-- Keterangan jika laporan sudah selesai ditangani --}}
                    <p class="report-completed-text">
                        Laporan selesai: akun sudah diberi warning.
                    </p>
                @else
                    {{-- Tiga tombol aksi jika masih baru --}}
                    <div class="report-btn-group">
                        {{-- TODO: sambungkan ke route backend untuk abaikan laporan --}}
                        <button type="button" class="btn-action btn-ignore" data-id="{{ $item['id'] }}">
                            Abaikan Laporan
                        </button>

                        {{-- TODO: sambungkan ke route backend untuk berikan peringatan --}}
                        <button type="button" class="btn-action btn-warn" data-id="{{ $item['id'] }}">
                            Kasih warning
                        </button>

                        {{-- Tombol buka modal dialog Banned --}}
                        <button type="button" class="btn-action btn-ban" data-id="{{ $item['id'] }}" data-username="{{ $item['username'] }}">
                            {{-- Ikon lingkaran dicoret --}}
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                            </svg>
                            Banned (durasi)
                        </button>
                    </div>
                @endif
            </div>

        </div>
    @endforeach

    {{-- Pesan kosong jika tidak ada kartu dalam kategori yang dipilih --}}
    <div class="report-empty-state" id="reportEmptyState" style="display: none;">
        <p>Tidak ada laporan pada kategori ini.</p>
    </div>

</div>

{{-- =============================================
     FOOTER DAFTAR & PAGINASI
     ============================================= --}}
<div class="report-footer">
    <p class="report-footer-info">
        Menampilkan 3 dari 12 berkas aktif &bull; Pembaruan otomatis tiap 60 detik
    </p>

    <div class="report-pagination">
        <button type="button" class="page-btn page-btn-nav" disabled>
            Sebelumnya
        </button>
        <button type="button" class="page-btn page-btn-num active">
            1
        </button>
        <button type="button" class="page-btn page-btn-num">
            2
        </button>
        <button type="button" class="page-btn page-btn-num">
            3
        </button>
        <button type="button" class="page-btn page-btn-nav">
            Selanjutnya
        </button>
    </div>
</div>

{{-- =============================================
     POP-UP BANNED DURATION (MODAL BARU)
     ============================================= --}}
<div class="ban-overlay" id="banOverlay" role="dialog" aria-modal="true" aria-labelledby="banModalTitle" aria-hidden="true">
    <div class="ban-modal" id="banModal" data-id="" data-username="">
        {{-- Tombol panah kiri untuk kembali / tutup --}}
        <button type="button" class="ban-modal-back" id="btnBanBack" aria-label="Tutup pop-up penangguhan">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        {{-- Judul di tengah atas --}}
        <h2 class="ban-modal-title" id="banModalTitle">Banned Duration</h2>

        {{-- Form input tanggal dan jam --}}
        <form class="ban-form" id="banForm" novalidate>
            <div class="ban-fields-grid">
                {{-- Kolom Date --}}
                <div class="ban-field">
                    <label for="banDate" class="ban-field-label">Date</label>
                    <input type="date" class="ban-field-input" id="banDate" name="ban_date">
                    <p class="field-error" id="dateError" role="alert"></p>
                </div>

                {{-- Kolom Time --}}
                <div class="ban-field">
                    <label for="banTime" class="ban-field-label">Time</label>
                    <input type="time" class="ban-field-input" id="banTime" name="ban_time">
                    <p class="field-error" id="timeError" role="alert"></p>
                </div>
            </div>

            {{-- Tombol Simpan di tengah --}}
            <button type="submit" class="btn-save-ban" id="btnSaveBan">
                Simpan
            </button>
        </form>
    </div>
</div>

@endsection

{{-- Script JS khusus halaman laporan akun --}}
@section('scripts')
    <script src="{{ asset('js/admin-laporan-akun.js') }}"></script>
@endsection
