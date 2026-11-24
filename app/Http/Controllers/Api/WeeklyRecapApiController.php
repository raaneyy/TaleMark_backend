<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeeklyRecapApiController extends Controller
{
    /**
     * Ambil data rekap mingguan aktivitas membaca beserta grafik harian dan perbandingan minggu lalu.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user() ?: $request->user();
        $userId = $user?->id;

        // Tentukan tanggal acuan
        $referenceDate = Carbon::today();
        if ($request->filled('date')) {
            try {
                $referenceDate = Carbon::parse($request->input('date'));
            } catch (\Exception) {
                $referenceDate = Carbon::today();
            }
        }

        if ($request->filled('week_offset')) {
            $offset = (int) $request->input('week_offset');
            $referenceDate = $referenceDate->copy()->addWeeks($offset);
        }

        $startOfWeek = $referenceDate->copy()->startOfWeek(); // Senin
        $endOfWeek = $referenceDate->copy()->endOfWeek();     // Minggu

        // Query sesi membaca minggu ini
        $currentQuery = ReadingSession::whereBetween('session_date', [
            $startOfWeek->toDateString(),
            $endOfWeek->toDateString(),
        ]);

        if ($userId) {
            $currentQuery->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        }

        $sessions = $currentQuery->get();

        // Hitung total minggu ini
        $totalSeconds = (int) $sessions->sum('duration_seconds');
        $totalHours = round($totalSeconds / 3600, 1);
        $totalMinutes = (int) round($totalSeconds / 60);
        $totalPages = (int) $sessions->sum('pages_read');
        $totalBooks = $sessions->pluck('book_title')->unique()->filter()->count();

        // Rincian buku yang dibaca
        $booksDetail = $sessions->groupBy('book_title')->map(function ($group, $title) {
            $seconds = (int) $group->sum('duration_seconds');

            return [
                'book_title' => $title,
                'author' => $group->first()->author ?? 'Penulis',
                'duration_seconds' => $seconds,
                'duration_minutes' => (int) round($seconds / 60),
                'pages_read' => (int) $group->sum('pages_read'),
                'sessions_count' => $group->count(),
            ];
        })->values();

        // Daily activity (Senin - Minggu)
        $dayShortNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $dayFullNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $dailyActivity = [];

        $maxDayMinutes = 1;
        for ($i = 0; $i < 7; $i++) {
            $currentDate = $startOfWeek->copy()->addDays($i)->toDateString();
            $daySessions = $sessions->where('session_date', $currentDate);
            $daySeconds = (int) $daySessions->sum('duration_seconds');
            $dayMinutes = (int) round($daySeconds / 60);
            $dayPages = (int) $daySessions->sum('pages_read');

            if ($dayMinutes > $maxDayMinutes) {
                $maxDayMinutes = $dayMinutes;
            }

            $dailyActivity[] = [
                'day' => $dayShortNames[$i],
                'full_day' => $dayFullNames[$i],
                'date' => $currentDate,
                'minutes' => $dayMinutes,
                'seconds' => $daySeconds,
                'pages' => $dayPages,
                'factor' => 0.0,
            ];
        }

        // Normalisasi faktor grafik (0.05 s/d 1.0)
        foreach ($dailyActivity as &$activity) {
            if ($activity['minutes'] > 0) {
                $activity['factor'] = round(min(1.0, max(0.1, $activity['minutes'] / $maxDayMinutes)), 2);
            } else {
                $activity['factor'] = 0.0;
            }
        }
        unset($activity);

        // Perbandingan dengan minggu sebelumnya
        $prevStartOfWeek = $startOfWeek->copy()->subWeek()->startOfWeek();
        $prevEndOfWeek = $startOfWeek->copy()->subWeek()->endOfWeek();

        $prevQuery = ReadingSession::whereBetween('session_date', [
            $prevStartOfWeek->toDateString(),
            $prevEndOfWeek->toDateString(),
        ]);

        if ($userId) {
            $prevQuery->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        }

        $prevSessions = $prevQuery->get();
        $prevSeconds = (int) $prevSessions->sum('duration_seconds');
        $prevMinutes = (int) round($prevSeconds / 60);

        if ($prevSeconds > 0) {
            $percentChange = round((($totalSeconds - $prevSeconds) / $prevSeconds) * 100);
            $trend = $percentChange >= 0 ? 'up' : 'down';
        } else {
            $percentChange = $totalSeconds > 0 ? 100 : 0;
            $trend = $totalSeconds > 0 ? 'up' : 'equal';
        }

        // Teks ringkasan motivasi
        if ($totalSeconds > $prevSeconds && $prevSeconds > 0) {
            $summaryText = "Kamu membaca {$percentChange}% lebih banyak dari minggu lalu. Terus semangat! 🎉";
        } elseif ($totalSeconds > 0 && $prevSeconds === 0) {
            $summaryText = 'Pencapaian bagus! Kamu berhasil memulai kebiasaan membaca minggu ini. 🌟';
        } elseif ($totalSeconds === $prevSeconds && $totalSeconds > 0) {
            $summaryText = 'Konsistensi luar biasa! Kamu menjaga durasi membacamu stabil seperti minggu lalu. 📚';
        } elseif ($totalSeconds > 0) {
            $summaryText = 'Sedikit membaca setiap hari tetap langkah besar untuk berkembang. Tetap semangat! ✨';
        } else {
            $summaryText = 'Belum ada waktu membaca minggu ini. Ayo mulai membaca hari ini! ⏱️';
        }

        $formattedPeriod = $startOfWeek->format('d M').' – '.$endOfWeek->format('d M Y');

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $formattedPeriod,
                'start_date' => $startOfWeek->toDateString(),
                'end_date' => $endOfWeek->toDateString(),
                'books_read' => $totalBooks,
                'pages_read' => $totalPages,
                'reading_time' => $totalHours > 0 ? "{$totalHours} jam" : "{$totalMinutes} menit",
                'reading_seconds' => $totalSeconds,
                'reading_minutes' => $totalMinutes,
                'reading_hours' => $totalHours,
                'daily_activity' => $dailyActivity,
                'books_detail' => $booksDetail,
                'comparison' => [
                    'previous_week_seconds' => $prevSeconds,
                    'previous_week_minutes' => $prevMinutes,
                    'percentage_change' => (int) $percentChange,
                    'trend' => $trend,
                ],
                'summary_text' => $summaryText,
            ],
        ]);
    }
}
