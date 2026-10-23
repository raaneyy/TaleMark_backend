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
{{-- TODO: Variabel $data dan $labels di bawah ini adalah data fallback dummy.
     Hubungkan dengan controller backend (AdminDashboardController) saat siap:
     $data: data aktivitas pembaca & buku terindeks
     $labels: label hari atau periode --}}
@php
    $labels = $labels ?? ['Sen (18)', 'Sel (19)', 'Rab (20)', 'Kam (21)', 'Jum (22)', 'Sab (23)', 'Min (24)'];
    $data = $data ?? [
        [
            'name' => 'Jam Membaca (K-hrs)',
            'data' => [32, 48, 42, 68, 62, 85, 94],
        ],
        [
            'name' => 'Buku Terindeks',
            'data' => [15, 22, 28, 35, 44, 52, 60],
        ],
    ];

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

            {{-- Grafik Dinamis ApexCharts --}}
            <div id="statsChart" class="chart-apex-wrapper"></div>

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

@push('scripts')
{{-- Library ApexCharts via CDN jsdelivr --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rawData = @json($data);
        const rawLabels = @json($labels);

        // Standarisasi format series baik format array objek series maupun array flat numerik
        let seriesData = [];
        if (Array.isArray(rawData) && rawData.length > 0) {
            if (typeof rawData[0] === 'object' && rawData[0] !== null && 'data' in rawData[0]) {
                seriesData = rawData;
            } else {
                seriesData = [
                    {
                        name: 'Jam Membaca (K-hrs)',
                        data: rawData
                    }
                ];
            }
        }

        const options = {
            chart: {
                type: 'area',
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
                        speed: 350
                    }
                },
                zoom: {
                    enabled: false
                }
            },
            series: seriesData,
            xaxis: {
                categories: rawLabels,
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
            yaxis: {
                labels: {
                    style: {
                        colors: '#9CA3AF',
                        fontSize: '11px',
                        fontFamily: 'Inter, system-ui, sans-serif'
                    }
                }
            },
            colors: ['#111827', '#9CA3AF'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.04,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: [2.5, 2]
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
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, system-ui, sans-serif'
                }
            }
        };

        const chartElement = document.querySelector("#statsChart");
        if (chartElement) {
            const chart = new ApexCharts(chartElement, options);
            chart.render();
        }
    });
</script>
@endpush
