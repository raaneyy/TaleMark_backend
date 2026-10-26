<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk resources/views/admin/dashboard.blade.php
 * Mengirim $statistik ke grafik "Total Pengguna Aktif" dan "Total Buku"
 * dengan filter Minggu / Bulan / Tahun.
 * Memakai DB::table (sama seperti AdminAuthController), tanpa model.
 */
class AdminDashboardController extends Controller
{
    // ===== SESUAIKAN DENGAN DATABASE KAMU =====
    private const TABEL_PENGGUNA = 'users';      // nama tabel pengguna/pembaca
    private const TABEL_BUKU     = 'buku';       // nama tabel buku
    private const KOLOM_TANGGAL  = 'created_at'; // kolom tanggal daftar/dibuat

    /**
     * Definisi "pengguna aktif".
     * Contoh lain:
     *   ->where('status', 'aktif')
     */
    private function queryPenggunaAktif()
    {
        return DB::table(self::TABEL_PENGGUNA)->where('is_active', 1);
    }

    private function queryBuku()
    {
        return DB::table(self::TABEL_BUKU);
    }

    public function index()
    {
        $statistik = [
            'pengguna' => $this->rekap(fn () => $this->queryPenggunaAktif()),
            'buku'     => $this->rekap(fn () => $this->queryBuku()),
        ];

        return view('admin.dashboard', compact('statistik'));
    }

    /**
     * Membuat rekap total (kumulatif) untuk minggu, bulan, dan tahun berjalan.
     * Titik yang belum terjadi (tanggal di masa depan) diisi null
     * supaya garis berhenti di hari ini.
     */
    private function rekap(callable $query): array
    {
        $sekarang = Carbon::now();

        // --- Minggu ini: Senin s/d Minggu ---
        $minggu = [];
        $hari = $sekarang->copy()->startOfWeek(Carbon::MONDAY);
        for ($i = 0; $i < 7; $i++) {
            $minggu[] = [
                'label' => $hari->locale('id')->isoFormat('ddd'),       // Sen, Sel, ...
                'mulai' => $hari->copy()->startOfDay(),
                'akhir' => $hari->copy()->endOfDay(),
            ];
            $hari->addDay();
        }

        // --- Bulan ini: per 7 hari (Minggu 1, Minggu 2, ...) ---
        $bulan = [];
        $awalBulan  = $sekarang->copy()->startOfMonth();
        $akhirBulan = $sekarang->copy()->endOfMonth();
        $ke = 1;
        for ($mulai = $awalBulan->copy(); $mulai->lte($akhirBulan); $mulai->addDays(7)) {
            $akhir = $mulai->copy()->addDays(6)->endOfDay();
            $bulan[] = [
                'label' => 'Minggu ' . $ke++,
                'mulai' => $mulai->copy(),
                'akhir' => $akhir->gt($akhirBulan) ? $akhirBulan->copy() : $akhir,
            ];
        }

        // --- Tahun ini: Januari s/d Desember ---
        $tahun = [];
        for ($m = 1; $m <= 12; $m++) {
            $awal = Carbon::create($sekarang->year, $m, 1);
            $tahun[] = [
                'label' => $awal->locale('id')->isoFormat('MMM'),       // Jan, Feb, ...
                'mulai' => $awal->copy()->startOfMonth(),
                'akhir' => $awal->copy()->endOfMonth(),
            ];
        }

        return [
            'minggu' => $this->hitung($query, $minggu, $sekarang),
            'bulan'  => $this->hitung($query, $bulan, $sekarang),
            'tahun'  => $this->hitung($query, $tahun, $sekarang),
        ];
    }

    /**
     * Hitung total data sampai akhir tiap titik.
     */
    private function hitung(callable $query, array $titik, Carbon $sekarang): array
    {
        $labels = [];
        $data   = [];

        foreach ($titik as $t) {
            $labels[] = $t['label'];

            if ($t['mulai']->gt($sekarang)) {
                $data[] = null; // belum terjadi
                continue;
            }

            $batas  = $t['akhir']->gt($sekarang) ? $sekarang : $t['akhir'];
            $data[] = $query()->where(self::KOLOM_TANGGAL, '<=', $batas)->count();
        }

        return compact('labels', 'data');
    }
}