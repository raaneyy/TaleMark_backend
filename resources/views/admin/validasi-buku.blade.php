{{-- resources/views/admin/validasi-buku.blade.php
     Halaman Validasi & Kurasi Buku Manual Admin TaleMark
     Tampilan frontend murni dengan data dummy lokal (backend dikerjakan terpisah)
--}}
@extends('layouts.admin')

@section('title', 'Validasi & Tambah Buku Manual')

{{-- CSS khusus halaman validasi buku --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-validasi-buku.css') }}?v={{ file_exists(public_path('css/admin-validasi-buku.css')) ? filemtime(public_path('css/admin-validasi-buku.css')) : time() }}">
@endsection

{{-- Kolom pencarian di Topbar (sebelah kiri badge pengguna AGUS) --}}
@section('topbar_search')
    <div class="topbar-search">
        {{-- Ikon kaca pembesar --}}
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="7.5"></circle>
            <line x1="16.5" y1="16.5" x2="21.5" y2="21.5"></line>
        </svg>
        {{-- TODO: sambungkan ke fitur pencarian global backend --}}
        <input type="text" placeholder="Cari ISBN, judul, kurator..." class="topbar-search-input" aria-label="Cari ISBN, judul, kurator">
    </div>
@endsection

@section('content')

{{-- =============================================
     DATA DUMMY — array lokal pengajuan buku
     ============================================= --}}
@php
    $bukuPengajuan = [
        [
            'id'          => 1,
            'judul'       => 'Catatan Pinggir Jilid 14',
            'penulis'     => 'Goenawan Mohamad',
            'kategori'    => 'Esai & Kritik Sastra',
            'penerbit'    => 'KPG',
            'format'      => '340 H',
            'sampul'      => 'images/books/catatan-pinggir-14.png',
            'isbn'        => '978-602-424-890-1',
            'pengaju'     => 'Julian Mahesa (@julianm)',
            'tanggal'     => '12 Nov 2024',
            'status'      => 'menunggu',
            'badge'       => 'MENUNGGU VERIFIKASI',
            'is_approved' => false,
        ],
        [
            'id'          => 2,
            'judul'       => 'Lelaki Harimau (Edisi Khusus Hardcover 2024)',
            'penulis'     => 'Eka Kurniawan',
            'kategori'    => 'Fiksi Sastra',
            'penerbit'    => 'Gramedia Pustaka Utama',
            'format'      => 'Hardcover',
            'sampul'      => 'images/books/lelaki-harimau.png',
            'isbn'        => '978-979-22-1200-5',
            'pengaju'     => 'Elena Vance (@elenareads)',
            'tanggal'     => '14 Nov 2024',
            'status'      => 'menunggu',
            'badge'       => 'EDISI BARU',
            'is_approved' => false,
        ],
        [
            'id'          => 3,
            'judul'       => 'Cantik Itu Luka',
            'penulis'     => 'Eka Kurniawan',
            'kategori'    => 'Fiksi Sastra',
            'penerbit'    => 'Gramedia Pustaka Utama',
            'format'      => '536 H',
            'sampul'      => null,
            'isbn'        => '978-602-03-1258-2',
            'pengaju'     => 'Aris Munandar (@arism)',
            'tanggal'     => '10 Nov 2024',
            'status'      => 'disetujui',
            'badge'       => 'DISETUJUI',
            'is_approved' => true,
        ],
    ];
@endphp

{{-- =============================================
     HEADER HALAMAN
     ============================================= --}}
<div class="curation-header">
    {{-- Judul besar serif --}}
    <h1 class="curation-title">Validasi &amp; Tambah Buku Manual</h1>
</div>

{{-- =============================================
     BAR TAB DAN PENCARIAN
     ============================================= --}}
<div class="filter-bar">
    {{-- Grup tombol filter tab --}}
    <div class="tab-group" role="tablist" aria-label="Filter status pengajuan">
        <button type="button" class="tab-btn active" data-status="all" role="tab" aria-selected="true">
            <span>Semua</span>
            <span class="tab-badge">48</span>
        </button>

        <button type="button" class="tab-btn" data-status="menunggu" role="tab" aria-selected="false">
            <span>Menunggu Review</span>
            <span class="tab-badge">18</span>
        </button>

        <button type="button" class="tab-btn" data-status="disetujui" role="tab" aria-selected="false">
            <span>Disetujui</span>
            <span class="tab-badge">24</span>
        </button>
    </div>

    {{-- Kolom pencarian judul --}}
    <div class="filter-search-box">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="7.5"></circle>
            <line x1="16.5" y1="16.5" x2="21.5" y2="21.5"></line>
        </svg>
        <input type="text" id="searchBookTitle" class="filter-search-input" placeholder="Cari berdasarkan judul" aria-label="Cari berdasarkan judul">
    </div>
</div>

{{-- =============================================
     BARIS PILIH SEMUA
     ============================================= --}}
<div class="select-all-bar">
    <label class="select-all-left" for="selectAllCheckbox">
        <input type="checkbox" id="selectAllCheckbox" class="card-checkbox" aria-label="Pilih semua entri dalam antrean">
        <span class="select-all-text">PILIH SEMUA ENTRI DALAM ANTREAN</span>
    </label>
</div>

{{-- =============================================
     DAFTAR KARTU PENGAJUAN BUKU
     ============================================= --}}
<div class="book-cards-list" id="bookCardsContainer">

    @foreach ($bukuPengajuan as $buku)
        <article class="book-card"
                 data-id="{{ $buku['id'] }}"
                 data-status="{{ $buku['status'] }}"
                 data-judul="{{ $buku['judul'] }}"
                 data-penulis="{{ $buku['penulis'] }}"
                 data-isbn="{{ $buku['isbn'] }}"
                 data-pengaju="{{ $buku['pengaju'] }}"
                 data-sampul="{{ !empty($buku['sampul']) ? asset($buku['sampul']) : '' }}">

            {{-- Checkbox seleksi kartu di kiri atas --}}
            <div class="card-select-wrapper">
                <input type="checkbox" class="card-checkbox item-checkbox" value="{{ $buku['id'] }}" aria-label="Pilih buku {{ $buku['judul'] }}">
            </div>

            {{-- Kotak sampul buku 120x165px --}}
            <div class="book-cover book-cover-{{ $buku['id'] }}">
                @if (!empty($buku['sampul']))
                    {{-- Gambar sampul buku asli --}}
                    <img src="{{ asset($buku['sampul']) }}" alt="Sampul {{ $buku['judul'] }}" class="book-cover-img">
                @else
                    {{-- Placeholder teks jika belum memiliki gambar --}}
                    <div class="cover-inner">
                        <span class="cover-mock-title">{{ $buku['judul'] }}</span>
                        <span class="cover-mock-deco"></span>
                    </div>
                @endif
                {{-- Chip format di dalam sampul pojok kiri bawah --}}
                <span class="cover-chip">{{ $buku['format'] }}</span>
            </div>

            {{-- Kolom konten kanan --}}
            <div class="book-content">

                {{-- Baris atas: Kategori • Penerbit & Badge Status --}}
                <div class="book-meta-top">
                    <span class="book-category">{{ strtoupper($buku['kategori']) }} &bull; {{ strtoupper($buku['penerbit']) }}</span>
                    <span class="status-pill">{{ $buku['badge'] }}</span>
                </div>

                {{-- Judul buku & Penulis --}}
                <h2 class="book-title">{{ $buku['judul'] }}</h2>
                <p class="book-author">{{ $buku['penulis'] }}</p>

                {{-- Kotak info abu muda 3 kolom --}}
                <div class="book-info-box">
                    <div class="info-col">
                        <span class="info-label">ISBN-13</span>
                        <span class="info-value info-isbn">{{ $buku['isbn'] }}</span>
                    </div>
                    <div class="info-col">
                        <span class="info-label">PENGAJU</span>
                        <span class="info-value">{{ $buku['pengaju'] }}</span>
                    </div>
                    <div class="info-col">
                        <span class="info-label">TANGGAL PENGAJUAN</span>
                        <span class="info-value">{{ $buku['tanggal'] }}</span>
                    </div>
                </div>

                {{-- Baris Aksi --}}
                <div class="book-actions">
                    {{-- Tautan Bandingkan Duplikat di kiri --}}
                    <div class="action-left">
                        {{-- TODO: sambungkan ke route backend untuk bandingkan duplikat --}}
                        <button type="button" class="btn-compare">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Bandingkan Duplikat</span>
                        </button>
                    </div>

                    {{-- Tombol aksi di kanan --}}
                    <div class="action-right">
                        @if ($buku['is_approved'])
                            {{-- Status buku yang telah disetujui --}}
                            <span class="text-approved">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Sudah ditambahkan ke katalog
                            </span>
                        @else
                            {{-- TODO: sambungkan ke route backend untuk tolak pengajuan --}}
                            <button type="button" class="btn-reject">
                                Tolak
                            </button>

                            {{-- TODO: sambungkan ke route backend untuk setujui & tambah ke katalog --}}
                            <button type="button" class="btn-approve">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 7H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2"></path>
                                    <rect x="8" y="2" width="13" height="15" rx="2"></rect>
                                    <line x1="14.5" y1="6.5" x2="14.5" y2="12.5"></line>
                                    <line x1="11.5" y1="9.5" x2="17.5" y2="9.5"></line>
                                </svg>
                                <span class="btn-approve-text">Setujui &amp; Tambah ke Katalog</span>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
            {{-- /book-content --}}

        </article>
    @endforeach

    {{-- Pesan saat tidak ada buku yang cocok dengan filter --}}
    <div id="emptyState" class="empty-state">
        <p class="empty-state-title">Tidak ada antrean buku</p>
        <p>Tidak ada entri pengajuan buku yang cocok dengan kriteria filter saat ini.</p>
    </div>

</div>
{{-- /book-cards-list --}}

{{-- =============================================
     PAGINASI
     ============================================= --}}
<div class="curation-pagination">
    <p class="pagination-text">Menampilkan 1-3 dari 18 pengajuan manual</p>

    <div class="pagination-buttons">
        {{-- Tombol panah kiri --}}
        <button type="button" class="page-btn" aria-label="Halaman sebelumnya" disabled>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        {{-- Tombol angka 1 aktif --}}
        <button type="button" class="page-btn active" aria-label="Halaman 1" aria-current="page">1</button>

        {{-- Tombol panah kanan --}}
        <button type="button" class="page-btn" aria-label="Halaman berikutnya">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
</div>

@endsection

{{-- =============================================
     POP-UP & MODAL DIALOGS
     Ditempatkan di section('modals') di luar admin-wrapper
     agar selalu muncul 100% tepat di tengah layar penuh
     ============================================= --}}
@section('modals')

{{-- =============================================
     POP-UP SETUJUI & TAMBAH KE KATALOG
     ============================================= --}}
<div class="book-modal-overlay" id="approveModalOverlay" role="dialog" aria-modal="true" aria-labelledby="approveModalTitle" aria-hidden="true">
    <div class="book-modal" id="approveModal" data-id="">
        {{-- Tombol panah kembali di pojok kiri atas --}}
        <button type="button" class="book-modal-back" id="btnApproveBack" aria-label="Tutup pop-up persetujuan">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        {{-- Judul serif di tengah atas --}}
        <h2 class="book-modal-title" id="approveModalTitle">Tambah ke Katalog</h2>

        {{-- Ringkasan buku (sampul abu kecil, judul, penulis, ISBN, pengaju) --}}
        <div class="book-summary-box">
            <div class="book-summary-cover" id="approveSummaryCover">
                {{-- Diisi secara dinamis oleh JS --}}
            </div>
            <div class="book-summary-details">
                <h3 class="book-summary-title" id="approveSummaryTitle">-</h3>
                <p class="book-summary-author" id="approveSummaryAuthor">-</p>
                <div class="book-summary-meta">
                    <span>ISBN: <strong id="approveSummaryIsbn">-</strong></span>
                    <span>&bull;</span>
                    <span>Pengaju: <strong id="approveSummaryPengaju">-</strong></span>
                </div>
            </div>
        </div>

        <p class="book-modal-desc">
            Buku akan masuk ke katalog resmi dan bisa dicari oleh semua pembaca.
        </p>

        <div class="book-modal-actions">
            <button type="button" class="btn-modal-cancel" id="btnApproveCancel">
                Batal
            </button>
            <button type="button" class="btn-modal-confirm" id="btnApproveConfirm">
                Ya, Tambahkan
            </button>
        </div>
    </div>
</div>

{{-- =============================================
     POP-UP TOLAK PENGAJUAN
     ============================================= --}}
<div class="book-modal-overlay" id="rejectModalOverlay" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle" aria-hidden="true">
    <div class="book-modal" id="rejectModal" data-id="">
        {{-- Tombol panah kembali di pojok kiri atas --}}
        <button type="button" class="book-modal-back" id="btnRejectBack" aria-label="Tutup pop-up penolakan">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        {{-- Judul serif di tengah atas --}}
        <h2 class="book-modal-title" id="rejectModalTitle">Tolak Pengajuan</h2>

        {{-- Ringkasan singkat buku (judul dan pengaju) --}}
        <div class="book-summary-box book-summary-box--compact">
            <div class="book-summary-details">
                <h3 class="book-summary-title" id="rejectSummaryTitle">-</h3>
                <p class="book-summary-meta">
                    Pengaju: <strong id="rejectSummaryPengaju">-</strong>
                </p>
            </div>
        </div>

        <form class="book-modal-form" id="rejectForm" novalidate>
            <div class="book-modal-field">
                <label for="rejectReason" class="book-modal-label">Alasan Penolakan</label>
                <select id="rejectReason" class="book-modal-select" required>
                    <option value="" disabled selected>Pilih alasan...</option>
                    <option value="Buku sudah ada di katalog (duplikat)">Buku sudah ada di katalog (duplikat)</option>
                    <option value="Data buku tidak lengkap">Data buku tidak lengkap</option>
                    <option value="ISBN tidak valid">ISBN tidak valid</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <p class="field-error" id="rejectReasonError" role="alert"></p>
            </div>

            <div class="book-modal-field">
                <label for="rejectNotes" class="book-modal-label">Catatan untuk Pengaju (opsional)</label>
                <textarea id="rejectNotes" class="book-modal-textarea" rows="3" placeholder="Tambahkan penjelasan singkat..."></textarea>
            </div>

            <div class="book-modal-actions">
                <button type="button" class="btn-modal-cancel" id="btnRejectCancel">
                    Batal
                </button>
                <button type="submit" class="btn-modal-danger" id="btnRejectConfirm">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Notifikasi Pojok Kanan Atas --}}
<div id="toastNotification" class="toast-notification" role="status" aria-live="polite">
    <span class="toast-icon">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </span>
    <span class="toast-text" id="toastMessage"></span>
</div>

@endsection

{{-- Script JS khusus validasi buku --}}
@section('scripts')
    <script src="{{ asset('js/admin-validasi-buku.js') }}?v={{ file_exists(public_path('js/admin-validasi-buku.js')) ? filemtime(public_path('js/admin-validasi-buku.js')) : time() }}"></script>
@endsection
