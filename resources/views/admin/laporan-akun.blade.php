{{-- resources/views/admin/laporan-akun.blade.php
     Halaman Laporan Akun & Tinjau Komunitas Admin TaleMark
     Tampilan frontend murni dengan data dummy lokal (backend dikerjakan terpisah)
--}}
@extends('layouts.admin')

@section('title', 'Laporan Akun & Tinjau Komunitas')

{{-- CSS khusus halaman laporan akun --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-laporan-akun.css') }}?v={{ file_exists(public_path('css/admin-laporan-akun.css')) ? filemtime(public_path('css/admin-laporan-akun.css')) : time() }}">
@endsection

@section('content')

{{-- =============================================
     DATA DUMMY — array lokal laporan akun & postingan
     ============================================= --}}
@php
    $laporanAkun = [
        [
            'id'             => 'USR-9021',
            'username'       => '@bot_promobuku_99',
            'inisial'        => 'B',
            'terdaftar'      => '2 hari lalu',
            'pelapor_count'  => 14,
            'alasan'         => 'Spam tautan affiliate ilegal di kolom komentar feed sastra',
            'bukti'          => '45 komentar serupa terdeteksi dalam 1 jam secara paralel.',
            'kategori'       => 'spam',
            'status'         => 'baru',
            'tingkat_risiko' => 'TINGGI',
        ],
        [
            'id'             => 'USR-4412',
            'username'       => '@pembaca_kasar',
            'inisial'        => 'P',
            'terdaftar'      => '3 bulan lalu',
            'pelapor_count'  => 6,
            'alasan'         => 'Komentar bernada pelecehan terhadap penulis di kolom ulasan buku',
            'bukti'          => '8 komentar dengan ujaran kebencian ditandai oleh pembaca lain.',
            'kategori'       => 'sara',
            'status'         => 'baru',
            'tingkat_risiko' => 'SEDANG',
        ],
        [
            'id'             => 'USR-5120',
            'username'       => '@komentar_iseng',
            'inisial'        => 'K',
            'terdaftar'      => '1 minggu lalu',
            'pelapor_count'  => 3,
            'alasan'         => 'Komentar promosi blog pribadi di ulasan buku non-fiksi',
            'bukti'          => '4 komentar berulang di buku yang sama dalam waktu singkat.',
            'kategori'       => 'spam',
            'status'         => 'baru',
            'tingkat_risiko' => 'RENDAH',
        ],
        [
            'id'             => 'USR-7830',
            'username'       => '@kutipan_ulang',
            'inisial'        => 'K',
            'terdaftar'      => '1 bulan lalu',
            'pelapor_count'  => 2,
            'alasan'         => 'Kutipan dibagikan berulang tanpa atribusi penulis asli',
            'bukti'          => '3 kutipan duplikat diunggah dalam kurun waktu 15 menit.',
            'kategori'       => 'spam',
            'status'         => 'selesai',
            'tindakan'       => 'WARNING',
            'tanggal_ditutup'=> '21 Okt 2024, 14:05',
            'ditangani_oleh' => 'Agus',
        ],
        [
            'id'             => 'USR-3190',
            'username'       => '@debat_resensi',
            'inisial'        => 'D',
            'terdaftar'      => '2 bulan lalu',
            'pelapor_count'  => 1,
            'alasan'         => 'Perdebatan tajam terkait analisis karakter tokoh novel di kolom ulasan buku',
            'bukti'          => 'Komentar berupa adu argumen sastra biasa dan tidak memuat kata kasar atau SARA.',
            'kategori'       => 'sara',
            'status'         => 'selesai',
            'tindakan'       => 'DIABAIKAN',
            'tanggal_ditutup'=> '20 Okt 2024, 09:30',
            'ditangani_oleh' => 'Dewi',
        ],
        [
            'id'             => 'USR-6211',
            'username'       => '@link_judi_slot',
            'inisial'        => 'L',
            'terdaftar'      => '5 hari lalu',
            'pelapor_count'  => 42,
            'alasan'         => 'Penyebaran tautan promosi judi online secara terorganisir di seluruh feed kutipan',
            'bukti'          => 'Ditemukan lebih dari 60 komentar berantai dengan tautan domain terlarang.',
            'kategori'       => 'spam',
            'status'         => 'selesai',
            'tindakan'       => 'BANNED',
            'tanggal_ditutup'=> '19 Okt 2024, 16:15',
            'ditangani_oleh' => 'Rian',
        ],
        [
            'id'             => 'USR-5519',
            'username'       => '@toko_buku_bajakan',
            'inisial'        => 'T',
            'terdaftar'      => '2 minggu lalu',
            'pelapor_count'  => 23,
            'alasan'         => 'Penyebaran tautan download PDF bajakan berbayar secara masif di forum sastra',
            'bukti'          => '18 tautan unduhan ilegal disematkan berulang di berbagai ulasan buku best seller.',
            'kategori'       => 'spam',
            'status'         => 'banned',
            'banned_until'   => '15 November 2026, 23:59 WIB',
        ],
        [
            'id'             => 'USR-8204',
            'username'       => '@ujaran_kebencian_99',
            'inisial'        => 'U',
            'terdaftar'      => '4 bulan lalu',
            'pelapor_count'  => 31,
            'alasan'         => 'Serangan personal, ujaran diskriminasi SARA terhadap penulis fiksi sejarah',
            'bukti'          => '14 tangkapan layar komentar provokatif telah diverifikasi oleh tim kurator.',
            'kategori'       => 'sara',
            'status'         => 'banned',
            'banned_until'   => '31 Desember 2026, 18:00 WIB',
        ],
        [
            'id'             => 'USR-6731',
            'username'       => '@kloning_resensi',
            'inisial'        => 'K',
            'terdaftar'      => '1 bulan lalu',
            'pelapor_count'  => 11,
            'alasan'         => 'Scraping dan plagiasi ulasan pembaca lain secara otomatis untuk manipulasi rating',
            'bukti'          => '35 ulasan identik kata demi kata disalin dari kurator resmi TaleMark.',
            'kategori'       => 'spam',
            'status'         => 'banned',
            'banned_until'   => '28 Oktober 2026, 12:00 WIB',
        ],
    ];

    $laporanPostingan = [
        [
            'id'             => 'PST-1042',
            'penulis'        => '@penulis_liar',
            'inisial'        => 'P',
            'waktu'          => '3 jam lalu',
            'pelapor_count'  => 9,
            'alasan'         => 'Postingan berisi tautan promosi judi online',
            'cuplikan'       => 'Dapatkan bonus member baru 100% dan putaran gratis dengan mendaftar di situs resmi kami sekarang juga melalui link https://bit.ly/slot-gacor-promo...',
            'kategori'       => 'spam',
            'status'         => 'baru',
            'tingkat_risiko' => 'TINGGI',
        ],
        [
            'id'             => 'PST-1043',
            'penulis'        => '@kutipan_kontroversial',
            'inisial'        => 'K',
            'waktu'          => '5 jam lalu',
            'pelapor_count'  => 6,
            'alasan'         => 'Konten SARA di kolom komentar postingan kutipan',
            'cuplikan'       => 'Buku sastra sejarah ini sebenarnya hanya propaganda kaum tertentu yang berusaha mendiskreditkan kelompok etnis mayoritas di negeri kita...',
            'kategori'       => 'sara',
            'status'         => 'baru',
            'tingkat_risiko' => 'SEDANG',
        ],
        [
            'id'             => 'PST-1044',
            'penulis'        => '@kutipan_bot_01',
            'inisial'        => 'B',
            'waktu'          => '1 hari lalu',
            'pelapor_count'  => 3,
            'alasan'         => 'Postingan duplikat berulang',
            'cuplikan'       => 'Kutipan Pramoedya: “Kalian boleh maju dalam pelajaran, mungkin mencapai derajad tertinggi, tapi tanpa mencintai sastra, kalian hanya hewan yang pandai.”',
            'kategori'       => 'spam',
            'status'         => 'selesai',
            'tingkat_risiko' => 'RENDAH',
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
     DUA KARTU PEMILIH (LAPORAN AKUN & LAPORAN POSTINGAN)
     ============================================= --}}
<div class="report-stats-grid" role="tablist" aria-label="Pemilih jenis laporan">

    {{-- Kartu Pemilih 1: Laporan Akun --}}
    <button type="button" class="stat-card selector-card is-active" id="tabLaporanAkun" role="tab" aria-selected="true" aria-controls="sectionLaporanAkun" tabindex="0">
        <span class="stat-card-label">LAPORAN AKUN</span>
        <span class="stat-card-number">12</span>
        <h3 class="stat-card-title">Laporan Akun Membutuhkan Review</h3>
    </button>

    {{-- Kartu Pemilih 2: Laporan Postingan --}}
    <button type="button" class="stat-card selector-card" id="tabLaporanPostingan" role="tab" aria-selected="false" aria-controls="sectionLaporanPostingan" tabindex="0">
        <span class="stat-card-label">LAPORAN POSTINGAN</span>
        <span class="stat-card-number">5</span>
        <h3 class="stat-card-title">Laporan Postingan Membutuhkan Review</h3>
    </button>

</div>

{{-- =============================================
     BARIS FILTER (RATA KIRI, TEPAT DI BAWAH KARTU PEMILIH)
     ============================================= --}}
<div class="report-filter-bar">
    <div class="report-filter-controls">
        {{-- TODO: sambungkan pengurutan ke route backend --}}
        <div class="select-btn-wrapper">
            <svg class="select-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h13M3 18h6"/></svg>
            <select class="select-btn" id="selectUrutan" aria-label="Urutan laporan">
                <option value="prioritas">Urutan: Prioritas Risiko</option>
                <option value="terbaru">Terbaru</option>
                <option value="terbanyak">Terbanyak Dilaporkan</option>
            </select>
        </div>

        {{-- TODO: sambungkan kanal forum ke route backend --}}
        <div class="select-btn-wrapper">
            <svg class="select-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M6 12h12M10 18h4"/></svg>
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
     DAFTAR KARTU LAPORAN AKUN (TAB 1)
     ============================================= --}}
<div class="report-cards-list" id="sectionLaporanAkun">

    @foreach ($laporanAkun as $item)
        @php
            $isBaru = ($item['status'] === 'baru');
            $risiko = strtoupper($item['tingkat_risiko'] ?? '');
            $isHighRiskNew = ($isBaru && $risiko === 'TINGGI');
        @endphp
        <div class="report-card {{ $item['status'] === 'banned' ? 'report-card--banned' : '' }}"
             data-id="{{ $item['id'] }}"
             data-kategori="{{ $item['kategori'] }}"
             data-status="{{ $item['status'] }}"
             data-username="{{ $item['username'] }}">

            {{-- Baris atas kartu: avatar, info akun, badge risiko, & pill jumlah pelapor --}}
            <div class="report-card-top">
                <div class="report-user-info">
                    {{-- Avatar inisial huruf: Latar #FBD9D9 huruf #9B1C1C hanya untuk baru berisiko TINGGI --}}
                    <div class="report-avatar {{ $isHighRiskNew ? 'avatar-danger' : 'avatar-neutral' }}">
                        {{ $item['inisial'] }}
                    </div>

                    <div class="report-user-meta">
                        <div class="report-user-row">
                            <span class="report-username">{{ $item['username'] }}</span>

                            {{-- Pill tingkat risiko: HANYA untuk kartu berstatus "baru" --}}
                            @if ($isBaru && !empty($risiko))
                                @if ($risiko === 'TINGGI')
                                    <span class="risk-badge risk-tinggi">TINGKAT RISIKO: TINGGI</span>
                                @elseif ($risiko === 'SEDANG')
                                    <span class="risk-badge risk-sedang">TINGKAT RISIKO: SEDANG</span>
                                @else
                                    <span class="risk-badge risk-rendah">TINGKAT RISIKO: RENDAH</span>
                                @endif
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
                    @php
                        $tindakan = strtoupper($item['tindakan'] ?? 'WARNING');
                        $tglDitutup = $item['tanggal_ditutup'] ?? '21 Okt 2024, 14:05';
                        $petugas = $item['ditangani_oleh'] ?? 'Agus';
                    @endphp
                    <div class="report-completed-row">
                        <span class="report-completed-badge report-badge--selesai">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            SELESAI
                        </span>
                        <span class="report-completed-meta">
                            @if ($tindakan === 'DIABAIKAN')
                                Laporan diabaikan pada <b>{{ $tglDitutup }}</b> oleh <b>{{ $petugas }}</b>
                            @elseif ($tindakan === 'BANNED')
                                Akun di-banned pada <b>{{ $tglDitutup }}</b> oleh <b>{{ $petugas }}</b>
                            @else
                                Akun sudah diberi warning pada <b>{{ $tglDitutup }}</b> oleh <b>{{ $petugas }}</b>
                            @endif
                        </span>
                    </div>
                @elseif ($item['status'] === 'banned')
                    {{-- Keterangan jika akun sedang dalam penangguhan / banned --}}
                    <div class="report-banned-container">
                        <div class="report-banned-text">
                            <span class="report-banned-badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                BANNED SEMENTARA
                            </span>
                            <span>Akses dibekukan hingga: <strong>{{ $item['banned_until'] ?? '30 November 2026, 23:59 WIB' }}</strong></span>
                        </div>
                        <button type="button" class="btn-action btn-ignore btn-unban" data-id="{{ $item['id'] }}" data-username="{{ $item['username'] }}" title="Buka penangguhan akun ini">
                            Buka Blokir
                        </button>
                    </div>
                @else
                    {{-- Tiga tombol aksi jika masih baru --}}
                    <div class="report-btn-group">
                        {{-- TODO: sambungkan ke route backend untuk abaikan laporan --}}
                        <button type="button" class="btn-action btn-ignore" data-id="{{ $item['id'] }}">
                            Abaikan Laporan
                        </button>

                        {{-- Tombol buka modal dialog Kirim Peringatan (Kasih warning) --}}
                        <button type="button" class="btn-action btn-warn" data-id="{{ $item['id'] }}" data-username="{{ $item['username'] }}">
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

</div>

<!-- Desain Laporan Postingan belum ada di Figma, ganti setelah desain final -->
<div class="report-cards-list" id="sectionLaporanPostingan" style="display: none;">

    @foreach ($laporanPostingan as $post)
        @php
            $isBaru = ($post['status'] === 'baru');
            $risiko = strtoupper($post['tingkat_risiko'] ?? '');
            $isHighRiskNew = ($isBaru && $risiko === 'TINGGI');
        @endphp
        <div class="report-card"
             data-id="{{ $post['id'] }}"
             data-kategori="{{ $post['kategori'] }}"
             data-status="{{ $post['status'] }}"
             data-username="{{ $post['penulis'] }}">

            {{-- Baris atas kartu postingan --}}
            <div class="report-card-top">
                <div class="report-user-info">
                    <div class="report-avatar {{ $isHighRiskNew ? 'avatar-danger' : 'avatar-neutral' }}">
                        {{ $post['inisial'] }}
                    </div>

                    <div class="report-user-meta">
                        <div class="report-user-row">
                            <span class="report-username">{{ $post['penulis'] }}</span>

                            @if ($isBaru && !empty($risiko))
                                @if ($risiko === 'TINGGI')
                                    <span class="risk-badge risk-tinggi">TINGKAT RISIKO: TINGGI</span>
                                @elseif ($risiko === 'SEDANG')
                                    <span class="risk-badge risk-sedang">TINGKAT RISIKO: SEDANG</span>
                                @else
                                    <span class="risk-badge risk-rendah">TINGKAT RISIKO: RENDAH</span>
                                @endif
                            @endif
                        </div>

                        <p class="report-user-sub">
                            Postingan &bull; {{ $post['waktu'] }} &bull; ID: #{{ $post['id'] }}
                        </p>
                    </div>
                </div>

                {{-- Pill jumlah pembaca yang melaporkan --}}
                <div class="report-reporter-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                        <line x1="4" y1="22" x2="4" y2="15"></line>
                    </svg>
                    <span>{{ $post['pelapor_count'] }} Pembaca Melaporkan</span>
                </div>
            </div>

            {{-- Kotak abu: Alasan pokok & cuplikan postingan --}}
            <div class="report-reason-box">
                <p class="report-reason-label">ALASAN PELAPORAN POKOK</p>
                <blockquote class="report-reason-quote">
                    &ldquo;{{ $post['alasan'] }}&rdquo;
                </blockquote>
                <div class="report-post-snippet-row">
                    <strong>Cuplikan Postingan:</strong>
                    <span class="report-post-snippet-text">&ldquo;<em>{{ $post['cuplikan'] }}</em>&rdquo;</span>
                </div>
            </div>

            {{-- Baris aksi tombol postingan --}}
            <div class="report-actions-row">
                @if ($post['status'] === 'selesai')
                    <p class="report-completed-text">
                        Laporan selesai: postingan sudah disembunyikan.
                    </p>
                @else
                    <div class="report-btn-group">
                        {{-- TODO: sambungkan ke route backend (id postingan) --}}
                        <button type="button" class="btn-action btn-ignore btn-ignore-post" data-post-id="{{ $post['id'] }}">
                            Abaikan Laporan
                        </button>

                        {{-- TODO: sambungkan ke route backend (id postingan) --}}
                        <button type="button" class="btn-action btn-warn btn-hide-post" data-post-id="{{ $post['id'] }}" data-author="{{ $post['penulis'] }}">
                            Sembunyikan Postingan
                        </button>

                        {{-- TODO: sambungkan ke route backend (id postingan) --}}
                        <button type="button" class="btn-action btn-delete-post" data-post-id="{{ $post['id'] }}" data-author="{{ $post['penulis'] }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Hapus Postingan
                        </button>
                    </div>
                @endif
            </div>

        </div>
    @endforeach

</div>

{{-- Pesan kosong jika tidak ada kartu dalam kategori/status yang dipilih --}}
<div class="report-empty-state" id="reportEmptyState" style="display: none;">
    <p>Tidak ada laporan pada kategori ini.</p>
</div>

{{-- =============================================
     FOOTER DAFTAR & PAGINASI (HANYA PAGINASI DI KANAN)
     ============================================= --}}
<div class="report-footer">
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

@endsection

{{-- =============================================
     POP-UP & MODAL DIALOGS
     Ditempatkan di section('modals') di luar admin-wrapper
     agar selalu muncul 100% tepat di tengah layar penuh
     ============================================= --}}
@section('modals')

{{-- =============================================
     POP-UP BANNED DURATION (MODAL)
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
            <div class="ban-fields-grid" id="banFieldsGrid">
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

            {{-- Checkbox Banned Permanen --}}
            <div class="ban-permanent-wrapper">
                <label class="ban-checkbox-label" for="banPermanent">
                    <input type="checkbox" id="banPermanent" name="ban_permanent" class="ban-checkbox">
                    <span class="ban-checkbox-text">Banned akun secara permanen</span>
                </label>
            </div>

            {{-- Tombol Simpan di tengah --}}
            <button type="submit" class="btn-save-ban" id="btnSaveBan">
                Simpan
            </button>
        </form>
    </div>
</div>

{{-- =============================================
     POP-UP KONFIRMASI BUKA BLOKIR (MODAL)
     ============================================= --}}
<div class="unban-overlay" id="unbanOverlay" role="dialog" aria-modal="true" aria-labelledby="unbanModalTitle" aria-hidden="true">
    <div class="unban-modal" id="unbanModal" data-id="" data-username="">
        {{-- Tombol panah kembali di pojok kiri atas --}}
        <button type="button" class="unban-modal-back ban-modal-back" id="btnUnbanBack" aria-label="Tutup pop-up buka blokir">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        <h2 class="unban-modal-title" id="unbanModalTitle">Buka Blokir Akun</h2>
        <p class="unban-modal-desc" id="unbanModalDesc">
            Akun <strong id="unbanTargetUsername">@username</strong> akan bisa masuk kembali ke TaleMark.
        </p>
        <div class="unban-modal-actions">
            <button type="button" class="btn-unban-cancel" id="btnCancelUnban">
                Batal
            </button>
            <button type="button" class="btn-unban-confirm" id="btnConfirmUnban">
                Ya, Buka Blokir
            </button>
        </div>
    </div>
</div>

{{-- =============================================
     POP-UP KIRIM PERINGATAN (KASIH WARNING)
     ============================================= --}}
<div class="warn-overlay" id="warnOverlay" role="dialog" aria-modal="true" aria-labelledby="warnModalTitle" aria-hidden="true">
    <div class="warn-modal" id="warnModal" data-id="" data-username="">
        {{-- Tombol panah kembali di pojok kiri atas --}}
        <button type="button" class="warn-modal-back" id="btnWarnBack" aria-label="Tutup pop-up peringatan">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        {{-- Judul serif di tengah atas --}}
        <h2 class="warn-modal-title" id="warnModalTitle">Kirim Peringatan</h2>
        <p class="warn-modal-desc" id="warnModalDesc">
            Peringatan akan dicatat di akun <strong id="warnTargetUsername">@username</strong> dan dikirim sebagai notifikasi.
        </p>

        {{-- Form input pesan peringatan --}}
        <form class="warn-form" id="warnForm" novalidate>
            <div class="warn-field">
                <label for="warnMessage" class="warn-field-label">Pesan Peringatan</label>
                <textarea class="warn-field-textarea" id="warnMessage" name="warn_message" rows="4" placeholder="Jelaskan pelanggaran yang dilakukan..."></textarea>
                <p class="field-error" id="warnMessageError" role="alert"></p>
            </div>

            <div class="warn-modal-actions">
                <button type="button" class="btn-warn-cancel" id="btnCancelWarn">
                    Batal
                </button>
                <button type="submit" class="btn-warn-confirm" id="btnConfirmWarn">
                    Kirim Peringatan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- =============================================
     POP-UP KONFIRMASI SEMBUNYIKAN POSTINGAN (MODAL)
     ============================================= --}}
<div class="unban-overlay" id="hidePostOverlay" role="dialog" aria-modal="true" aria-labelledby="hidePostModalTitle" aria-hidden="true">
    <div class="unban-modal" id="hidePostModal" data-post-id="" data-author="">
        {{-- Tombol panah kembali di pojok kiri atas --}}
        <button type="button" class="unban-modal-back ban-modal-back" id="btnHidePostBack" aria-label="Tutup pop-up sembunyikan postingan">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        <h2 class="unban-modal-title" id="hidePostModalTitle">Sembunyikan Postingan</h2>
        <p class="unban-modal-desc" id="hidePostModalDesc">
            Postingan <strong id="hidePostTargetId">#PST-1042</strong> oleh <strong id="hidePostTargetAuthor">@penulis</strong> tidak akan ditampilkan lagi kepada pembaca di feed publik.
        </p>
        <div class="unban-modal-actions">
            <button type="button" class="btn-unban-cancel" id="btnCancelHidePost">
                Batal
            </button>
            <button type="button" class="btn-warning-confirm" id="btnConfirmHidePost">
                Ya, Sembunyikan
            </button>
        </div>
    </div>
</div>

{{-- =============================================
     POP-UP KONFIRMASI HAPUS POSTINGAN (MODAL)
     ============================================= --}}
<div class="unban-overlay" id="deletePostOverlay" role="dialog" aria-modal="true" aria-labelledby="deletePostModalTitle" aria-hidden="true">
    <div class="unban-modal" id="deletePostModal" data-post-id="" data-author="">
        {{-- Tombol panah kembali di pojok kiri atas --}}
        <button type="button" class="unban-modal-back ban-modal-back" id="btnDeletePostBack" aria-label="Tutup pop-up hapus postingan">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        <h2 class="unban-modal-title" id="deletePostModalTitle">Hapus Postingan</h2>
        <p class="unban-modal-desc" id="deletePostModalDesc">
            Postingan <strong id="deletePostTargetId">#PST-1042</strong> oleh <strong id="deletePostTargetAuthor">@penulis</strong> akan dihapus secara permanen dari sistem. Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="unban-modal-actions">
            <button type="button" class="btn-unban-cancel" id="btnCancelDeletePost">
                Batal
            </button>
            <button type="button" class="btn-danger-confirm" id="btnConfirmDeletePost">
                Ya, Hapus Postingan
            </button>
        </div>
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

{{-- Script JS khusus halaman laporan akun --}}
@section('scripts')
    <script src="{{ asset('js/admin-laporan-akun.js') }}?v={{ file_exists(public_path('js/admin-laporan-akun.js')) ? filemtime(public_path('js/admin-laporan-akun.js')) : time() }}"></script>
@endsection
