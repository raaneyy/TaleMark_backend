{{-- resources/views/admin/validasi-buku.blade.php
     Halaman Validasi & Kurasi Buku Manual Admin TaleMark
     Tampilan frontend murni dengan data dummy lokal (backend dikerjakan terpisah)
--}}
@extends('layouts.admin')

@section('title', 'Validasi & Tambah Buku Manual')

{{-- CSS khusus halaman validasi buku --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-validasi-buku.css') }}?v={{ file_exists(public_path('css/admin-validasi-buku.css')) ? filemtime(public_path('css/admin-validasi-buku.css')) : time() }}">

    {{-- CSS tombol & pop-up Kurasi Judul Baru --}}
    <style>
        /* =============================================
           TOMBOL KURASI JUDUL BARU (header halaman)
           ============================================= */
        .curation-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }
        .btn-kurasi {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 44px;
            padding: 0 20px;
            border: 0;
            border-radius: 12px;
            background: #111827;
            color: #FFFFFF;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s;
        }
        .btn-kurasi:hover { background: #1F2937; }

        /* =============================================
           POP-UP TAMBAH BUKU MANUAL
           ============================================= */
        .tb-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .tb-overlay[hidden] { display: none !important; }
        .tb-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, .45);
            backdrop-filter: blur(2px);
            animation: tbFade .2s ease;
        }
        .tb-dialog {
            position: relative;
            width: min(820px, 100%);
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            overflow-x: hidden;
            background: #FFFFFF;
            border: 1px solid #E5E7EB;
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(17, 24, 39, .18);
            padding: 28px;
            animation: tbMuncul .22s ease;
        }
        .tb-close {
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
        }
        .tb-close:hover { background: #F3F4F6; }
        .tb-kicker {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9CA3AF;
        }
        .tb-title {
            margin: 4px 0 0;
            font-family: 'Newsreader', Georgia, serif;
            font-size: 26px;
            font-weight: 400;
            color: #111827;
            padding-right: 44px;
        }
        .tb-desc {
            margin: 6px 0 22px;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 13px;
            color: #6B7280;
        }
        .tb-desc span, .tb-label span { color: #B91C1C; }

        .tb-grid {
            display: grid;
            grid-template-columns: 170px minmax(0, 1fr);
            gap: 24px;
        }
        .tb-fields {
            display: flex;
            flex-direction: column;
            gap: 14px;
            min-width: 0;
        }
        .tb-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .tb-row-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .tb-field { min-width: 0; }
        .tb-label {
            display: block;
            margin-bottom: 6px;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }
        .tb-input,
        .tb-select,
        .tb-textarea {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            background: #FFFFFF;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 14px;
            color: #111827;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .tb-input,
        .tb-select { height: 42px; padding: 0 12px; }
        .tb-textarea { padding: 10px 12px; resize: vertical; min-height: 84px; }
        .tb-input:focus,
        .tb-select:focus,
        .tb-textarea:focus {
            border-color: #111827;
            box-shadow: 0 0 0 3px rgba(17, 24, 39, .08);
        }
        .tb-field.tb-invalid .tb-input,
        .tb-field.tb-invalid .tb-select { border-color: #DC2626; }
        .tb-error {
            min-height: 0;
            margin: 4px 0 0;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 12px;
            color: #DC2626;
        }
        .tb-error:empty { display: none; }
        .tb-hint {
            margin: 4px 0 0;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 11px;
            color: #9CA3AF;
        }

        /* Unggah sampul */
        .tb-cover-drop {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            aspect-ratio: 120 / 165;
            border: 1.5px dashed #D1D5DB;
            border-radius: 12px;
            background: #F9FAFB;
            color: #6B7280;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 12px;
            text-align: center;
            cursor: pointer;
            overflow: hidden;
            position: relative;
        }
        .tb-cover-drop:hover { border-color: #111827; color: #111827; }
        .tb-cover-drop small { color: #9CA3AF; font-size: 11px; }
        .tb-cover-drop img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .tb-cover-remove {
            margin-top: 8px;
            width: 100%;
            height: 32px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            background: #FFFFFF;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 12px;
            color: #374151;
            cursor: pointer;
        }
        .tb-cover-remove[hidden] { display: none; }

        /* Tombol bawah */
        .tb-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #F3F4F6;
        }
        .tb-btn {
            height: 42px;
            padding: 0 20px;
            border-radius: 10px;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .tb-btn-batal { border: 1px solid #E5E7EB; background: #FFFFFF; color: #374151; }
        .tb-btn-batal:hover { background: #F3F4F6; }
        .tb-btn-simpan { border: 0; background: #111827; color: #FFFFFF; }
        .tb-btn-simpan:hover { background: #1F2937; }

        /* Notifikasi berhasil */
        .tb-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 1100;
            max-width: calc(100vw - 48px);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #111827;
            color: #FFFFFF;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 13px;
            box-shadow: 0 12px 30px rgba(17, 24, 39, .2);
            opacity: 0;
            transform: translateY(-8px);
            pointer-events: none;
            transition: opacity .2s, transform .2s;
        }
        .tb-toast.tampil { opacity: 1; transform: none; }

        /* Kartu yang baru ditambahkan disorot sebentar */
        .book-card.tb-baru { animation: tbSorot 2.4s ease; }

        body.tb-terbuka { overflow: hidden; }

        @keyframes tbFade { from { opacity: 0; } to { opacity: 1; } }
        @keyframes tbMuncul {
            from { opacity: 0; transform: translateY(12px) scale(.98); }
            to   { opacity: 1; transform: none; }
        }
        @keyframes tbSorot {
            0%, 40% { box-shadow: 0 0 0 3px rgba(17, 24, 39, .35); }
            100%    { box-shadow: none; }
        }

        /* Tablet */
        @media (max-width: 760px) {
            .tb-grid { grid-template-columns: minmax(0, 1fr); }
            .tb-cover-col { max-width: 150px; }
        }

        /* HP: pop-up layar penuh */
        @media (max-width: 640px) {
            .btn-kurasi { width: 100%; justify-content: center; }
            .tb-overlay { padding: 0; }
            .tb-dialog {
                width: 100%;
                height: 100%;
                max-height: 100%;
                border-radius: 0;
                border: 0;
                padding: 20px 16px;
            }
            .tb-row,
            .tb-row-3 { grid-template-columns: minmax(0, 1fr); }
            .tb-actions {
                position: sticky;
                bottom: -20px;
                background: #FFFFFF;
                padding-bottom: 20px;
                margin-bottom: -20px;
            }
            .tb-btn {
                flex: 1;
                padding: 0 12px;
                white-space: nowrap;
            }
            .tb-toast {
                top: auto;
                bottom: 16px;
                left: 16px;
                right: 16px;
                max-width: none;
            }
        }
    </style>
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

    {{-- Tombol hitam: Kurasi Judul Baru (membuka pop-up tambah buku manual) --}}
    <button type="button" class="btn-kurasi" id="btnKurasiBaru">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Penambahan Buku
    </button>
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

{{-- =============================================
     POP-UP KURASI JUDUL BARU (TAMBAH BUKU MANUAL)
     Muncul saat tombol "Kurasi Judul Baru" ditekan
     ============================================= --}}
<div class="tb-overlay" id="tambahBukuModal" hidden>
    <div class="tb-backdrop" data-tutup-tambah></div>

    <div class="tb-dialog" role="dialog" aria-modal="true" aria-labelledby="tambahBukuTitle">

        {{-- Tombol tutup --}}
        <button type="button" class="tb-close" data-tutup-tambah aria-label="Tutup pop-up tambah buku">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>

        <p class="tb-kicker">Kurasi Judul Baru</p>
        <h2 class="tb-title" id="tambahBukuTitle">Tambah Buku Manual</h2>
        <p class="tb-desc">Buku yang ditambahkan admin langsung masuk ke katalog. Kolom bertanda <span>*</span> wajib diisi.</p>

        {{-- TODO: ganti action ke route backend, misalnya {{ route('admin.buku.store') }} --}}
        <form id="formTambahBuku" action="#" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="tb-grid">

                {{-- Kolom kiri: sampul --}}
                <div class="tb-cover-col">
                    <span class="tb-label">Sampul Buku</span>
                    <label class="tb-cover-drop" for="tbSampul" id="tbSampulPreview">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <span>Unggah sampul</span>
                        <small>JPG / PNG / WEBP<br>maks. 2 MB</small>
                    </label>
                    <input type="file" id="tbSampul" name="sampul" accept="image/png,image/jpeg,image/webp" hidden>
                    <button type="button" class="tb-cover-remove" id="tbSampulHapus" hidden>Hapus sampul</button>
                    <p class="tb-error" data-error="sampul"></p>
                </div>

                {{-- Kolom kanan: data buku --}}
                <div class="tb-fields">

                    <div class="tb-field" data-field="judul">
                        <label for="tbJudul" class="tb-label">Judul Buku <span>*</span></label>
                        <input type="text" id="tbJudul" name="judul" class="tb-input" maxlength="200" placeholder="mis. Bumi Manusia">
                        <p class="tb-error" data-error="judul"></p>
                    </div>

                    <div class="tb-row">
                        <div class="tb-field" data-field="penulis">
                            <label for="tbPenulis" class="tb-label">Penulis <span>*</span></label>
                            <input type="text" id="tbPenulis" name="penulis" class="tb-input" maxlength="120" placeholder="mis. Pramoedya Ananta Toer">
                            <p class="tb-error" data-error="penulis"></p>
                        </div>
                        <div class="tb-field" data-field="penerbit">
                            <label for="tbPenerbit" class="tb-label">Penerbit <span>*</span></label>
                            <input type="text" id="tbPenerbit" name="penerbit" class="tb-input" maxlength="120" placeholder="mis. Lentera Dipantara">
                            <p class="tb-error" data-error="penerbit"></p>
                        </div>
                    </div>

                    <div class="tb-row">
                        <div class="tb-field" data-field="isbn">
                            <label for="tbIsbn" class="tb-label">ISBN-13 <span>*</span></label>
                            <input type="text" id="tbIsbn" name="isbn" class="tb-input" inputmode="numeric" maxlength="17" placeholder="978-602-424-890-1">
                            <p class="tb-error" data-error="isbn"></p>
                        </div>
                        <div class="tb-field" data-field="kategori">
                            <label for="tbKategori" class="tb-label">Kategori <span>*</span></label>
                            <select id="tbKategori" name="kategori" class="tb-select">
                                <option value="" selected disabled>Pilih kategori…</option>
                                <option>Sastra Klasik &amp; Kanon</option>
                                <option>Fiksi Sastra</option>
                                <option>Fiksi Filsafat &amp; Realisme</option>
                                <option>Esai &amp; Kritik Sastra</option>
                                <option>Catatan Esai &amp; Humaniora</option>
                                <option>Antologi Puisi &amp; Sajak</option>
                            </select>
                            <p class="tb-error" data-error="kategori"></p>
                        </div>
                    </div>

                    <div class="tb-row tb-row-3">
                        <div class="tb-field" data-field="format">
                            <label for="tbFormat" class="tb-label">Format <span>*</span></label>
                            <select id="tbFormat" name="format" class="tb-select">
                                <option value="Paperback" selected>Paperback</option>
                                <option value="Hardcover">Hardcover</option>
                                <option value="E-book">E-book</option>
                            </select>
                            <p class="tb-error" data-error="format"></p>
                        </div>
                        <div class="tb-field" data-field="halaman">
                            <label for="tbHalaman" class="tb-label">Jumlah Halaman</label>
                            <input type="number" id="tbHalaman" name="halaman" class="tb-input" min="1" max="10000" placeholder="mis. 340">
                            <p class="tb-error" data-error="halaman"></p>
                        </div>
                        <div class="tb-field" data-field="tahun">
                            <label for="tbTahun" class="tb-label">Tahun Terbit</label>
                            <input type="number" id="tbTahun" name="tahun_terbit" class="tb-input" min="1000" placeholder="mis. 1980">
                            <p class="tb-error" data-error="tahun"></p>
                        </div>
                    </div>

                    <div class="tb-field" data-field="sinopsis">
                        <label for="tbSinopsis" class="tb-label">Sinopsis</label>
                        <textarea id="tbSinopsis" name="sinopsis" class="tb-textarea" rows="3" maxlength="1000" placeholder="Ringkasan singkat isi buku (opsional)"></textarea>
                        <p class="tb-hint"><span id="tbSinopsisHitung">0</span>/1000 karakter</p>
                    </div>

                </div>
            </div>

            <div class="tb-actions">
                <button type="button" class="tb-btn tb-btn-batal" data-tutup-tambah>Batal</button>
                <button type="submit" class="tb-btn tb-btn-simpan">Simpan ke Katalog</button>
            </div>
        </form>
    </div>
</div>

{{-- Notifikasi berhasil menambah buku --}}
<div class="tb-toast" id="tbToast" role="status" aria-live="polite"></div>

@endsection

{{-- Script JS khusus validasi buku --}}
@section('scripts')
    <script src="{{ asset('js/admin-validasi-buku.js') }}?v={{ file_exists(public_path('js/admin-validasi-buku.js')) ? filemtime(public_path('js/admin-validasi-buku.js')) : time() }}"></script>

    {{-- Script pop-up Kurasi Judul Baru (Tambah Buku Manual) --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal   = document.getElementById('tambahBukuModal');
        const form    = document.getElementById('formTambahBuku');
        const tombol  = document.getElementById('btnKurasiBaru');
        if (!modal || !form || !tombol) return;

        const sampulInput   = document.getElementById('tbSampul');
        const sampulPreview = document.getElementById('tbSampulPreview');
        const sampulHapus   = document.getElementById('tbSampulHapus');
        const isiPreviewAwal = sampulPreview.innerHTML;
        let sampulUrl = null;

        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        /* ---------- Buka & tutup ---------- */
        function buka() {
            modal.hidden = false;
            document.body.classList.add('tb-terbuka');
            modal.querySelector('.tb-dialog').scrollTop = 0;
            setTimeout(() => document.getElementById('tbJudul').focus(), 50);
        }
        function tutup() {
            modal.hidden = true;
            document.body.classList.remove('tb-terbuka');
            tombol.focus();
        }
        tombol.addEventListener('click', buka);
        modal.querySelectorAll('[data-tutup-tambah]').forEach(el => el.addEventListener('click', tutup));
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.hidden) tutup();
        });

        /* ---------- Sampul ---------- */
        function resetSampul() {
            if (sampulUrl) URL.revokeObjectURL(sampulUrl);
            sampulUrl = null;
            sampulInput.value = '';
            sampulPreview.innerHTML = isiPreviewAwal;
            sampulHapus.hidden = true;
        }
        sampulInput.addEventListener('change', () => {
            setError('sampul', '');
            const file = sampulInput.files[0];
            if (!file) return;
            if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
                resetSampul();
                return setError('sampul', 'Format harus JPG, PNG, atau WEBP.');
            }
            if (file.size > 2 * 1024 * 1024) {
                resetSampul();
                return setError('sampul', 'Ukuran sampul maksimal 2 MB.');
            }
            if (sampulUrl) URL.revokeObjectURL(sampulUrl);
            sampulUrl = URL.createObjectURL(file);
            sampulPreview.innerHTML = '<img src="' + sampulUrl + '" alt="Pratinjau sampul">';
            sampulHapus.hidden = false;
        });
        sampulHapus.addEventListener('click', resetSampul);

        /* ---------- Hitung karakter sinopsis ---------- */
        const sinopsis = document.getElementById('tbSinopsis');
        sinopsis.addEventListener('input', () => {
            document.getElementById('tbSinopsisHitung').textContent = sinopsis.value.length;
        });

        /* ---------- Validasi ---------- */
        function setError(nama, pesan) {
            const p = form.querySelector('[data-error="' + nama + '"]');
            if (p) p.textContent = pesan;
            const f = form.querySelector('[data-field="' + nama + '"]');
            if (f) f.classList.toggle('tb-invalid', !!pesan);
        }

        // Hapus pesan error saat kolom diperbaiki
        form.querySelectorAll('.tb-field[data-field]').forEach(f => {
            f.addEventListener('input', () => setError(f.dataset.field, ''));
            f.addEventListener('change', () => setError(f.dataset.field, ''));
        });

        // ISBN: hanya angka & tanda hubung
        const isbnInput = document.getElementById('tbIsbn');
        isbnInput.addEventListener('input', () => {
            isbnInput.value = isbnInput.value.replace(/[^0-9-]/g, '');
        });

        function validasi() {
            const v = (id) => document.getElementById(id).value.trim();
            const tahunIni = new Date().getFullYear();
            let pertamaSalah = null;
            const cek = (nama, id, pesan) => {
                setError(nama, pesan);
                if (pesan && !pertamaSalah) pertamaSalah = document.getElementById(id);
            };

            cek('judul', 'tbJudul', v('tbJudul') ? '' : 'Judul buku wajib diisi.');
            cek('penulis', 'tbPenulis', v('tbPenulis') ? '' : 'Nama penulis wajib diisi.');
            cek('penerbit', 'tbPenerbit', v('tbPenerbit') ? '' : 'Penerbit wajib diisi.');
            cek('kategori', 'tbKategori', v('tbKategori') ? '' : 'Pilih kategori buku.');

            const angkaIsbn = v('tbIsbn').replace(/-/g, '');
            const isbnSudahAda = [...document.querySelectorAll('.book-card[data-isbn]')]
                .some(k => k.dataset.isbn.replace(/-/g, '') === angkaIsbn);
            cek('isbn', 'tbIsbn',
                !angkaIsbn ? 'ISBN wajib diisi.'
                : !/^\d{13}$/.test(angkaIsbn) ? 'ISBN-13 harus terdiri dari 13 angka.'
                : isbnSudahAda ? 'ISBN ini sudah ada di daftar.'
                : '');

            const hal = v('tbHalaman');
            cek('halaman', 'tbHalaman', hal && (!/^\d+$/.test(hal) || +hal < 1) ? 'Jumlah halaman tidak valid.' : '');

            const thn = v('tbTahun');
            cek('tahun', 'tbTahun', thn && (!/^\d{4}$/.test(thn) || +thn > tahunIni + 1) ? 'Tahun terbit tidak valid.' : '');

            if (pertamaSalah) pertamaSalah.focus();
            return !pertamaSalah;
        }

        /* ---------- Simpan ---------- */
        form.addEventListener('submit', (e) => {
            // TODO: saat backend siap, hapus preventDefault & biarkan form terkirim ke server
            e.preventDefault();
            if (!validasi()) return;

            const data = {
                judul:    document.getElementById('tbJudul').value.trim(),
                penulis:  document.getElementById('tbPenulis').value.trim(),
                penerbit: document.getElementById('tbPenerbit').value.trim(),
                isbn:     document.getElementById('tbIsbn').value.trim(),
                kategori: document.getElementById('tbKategori').value,
                format:   document.getElementById('tbFormat').value,
                halaman:  document.getElementById('tbHalaman').value.trim(),
            };
            const chip = data.format === 'Paperback' && data.halaman ? data.halaman + ' H' : data.format;
            const tanggal = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            const id = 'baru-' + Date.now();

            const sampulHtml = sampulUrl
                ? '<img src="' + sampulUrl + '" alt="Sampul ' + esc(data.judul) + '" class="book-cover-img">'
                : '<div class="cover-inner"><span class="cover-mock-title">' + esc(data.judul) + '</span><span class="cover-mock-deco"></span></div>';

            // Kartu baru dengan susunan yang sama seperti kartu pengajuan
            const kartu = document.createElement('article');
            kartu.className = 'book-card tb-baru';
            kartu.dataset.id = id;
            kartu.dataset.status = 'disetujui';
            kartu.dataset.judul = data.judul;
            kartu.dataset.penulis = data.penulis;
            kartu.dataset.isbn = data.isbn;
            kartu.dataset.pengaju = 'Admin';
            kartu.dataset.sampul = sampulUrl || '';
            kartu.innerHTML =
                  '<div class="card-select-wrapper"><input type="checkbox" class="card-checkbox item-checkbox" value="' + id + '" aria-label="Pilih buku ' + esc(data.judul) + '"></div>'
                + '<div class="book-cover">' + sampulHtml + '<span class="cover-chip">' + esc(chip) + '</span></div>'
                + '<div class="book-content">'
                +   '<div class="book-meta-top">'
                +     '<span class="book-category">' + esc(data.kategori.toUpperCase()) + ' &bull; ' + esc(data.penerbit.toUpperCase()) + '</span>'
                +     '<span class="status-pill">DITAMBAHKAN ADMIN</span>'
                +   '</div>'
                +   '<h2 class="book-title">' + esc(data.judul) + '</h2>'
                +   '<p class="book-author">' + esc(data.penulis) + '</p>'
                +   '<div class="book-info-box">'
                +     '<div class="info-col"><span class="info-label">ISBN-13</span><span class="info-value info-isbn">' + esc(data.isbn) + '</span></div>'
                +     '<div class="info-col"><span class="info-label">PENGAJU</span><span class="info-value">Admin (manual)</span></div>'
                +     '<div class="info-col"><span class="info-label">TANGGAL PENGAJUAN</span><span class="info-value">' + esc(tanggal) + '</span></div>'
                +   '</div>'
                +   '<div class="book-actions"><div class="action-right">'
                +     '<span class="text-approved"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Sudah ditambahkan ke katalog</span>'
                +   '</div></div>'
                + '</div>';

            const daftar = document.getElementById('bookCardsContainer');
            daftar.insertBefore(kartu, daftar.querySelector('.book-card') || daftar.firstChild);

            // Sampul sekarang dipakai kartu, jangan dihapus dari memori
            sampulUrl = null;
            form.reset();
            resetSampul();
            document.getElementById('tbSinopsisHitung').textContent = '0';
            tutup();

            kartu.scrollIntoView({ behavior: 'smooth', block: 'center' });
            tampilkanToast('“' + data.judul + '” berhasil ditambahkan ke katalog.');
        });

        /* ---------- Notifikasi ---------- */
        let timerToast = null;
        function tampilkanToast(pesan) {
            const t = document.getElementById('tbToast');
            t.textContent = pesan;
            t.classList.add('tampil');
            clearTimeout(timerToast);
            timerToast = setTimeout(() => t.classList.remove('tampil'), 3200);
        }
    });
    </script>
@endsection