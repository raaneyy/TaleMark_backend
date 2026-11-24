<?php

namespace Tests\Feature;

use App\Models\ReadingSession;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyRecapApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_weekly_recap_for_current_week(): void
    {
        $now = Carbon::today();
        $startOfWeek = $now->copy()->startOfWeek();

        // Buat sesi di hari Senin dan Rabu
        ReadingSession::create([
            'book_title' => 'Bumi Manusia',
            'author' => 'Pramoedya Ananta Toer',
            'duration_seconds' => 3600, // 60 menit
            'pages_read' => 40,
            'session_date' => $startOfWeek->copy()->addDays(0)->toDateString(),
        ]);

        ReadingSession::create([
            'book_title' => 'Anak Semua Bangsa',
            'author' => 'Pramoedya Ananta Toer',
            'duration_seconds' => 1800, // 30 menit
            'pages_read' => 20,
            'session_date' => $startOfWeek->copy()->addDays(2)->toDateString(),
        ]);

        $response = $this->getJson('/api/stats/weekly-recap');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'books_read' => 2,
                    'pages_read' => 60,
                    'reading_seconds' => 5400,
                    'reading_minutes' => 90,
                ],
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'period',
                    'start_date',
                    'end_date',
                    'books_read',
                    'pages_read',
                    'reading_time',
                    'reading_seconds',
                    'reading_minutes',
                    'reading_hours',
                    'daily_activity' => [
                        '*' => ['day', 'full_day', 'date', 'minutes', 'seconds', 'pages', 'factor'],
                    ],
                    'books_detail' => [
                        '*' => ['book_title', 'author', 'duration_seconds', 'duration_minutes', 'pages_read', 'sessions_count'],
                    ],
                    'comparison' => ['previous_week_seconds', 'previous_week_minutes', 'percentage_change', 'trend'],
                    'summary_text',
                ],
            ]);

        // Verifikasi 7 hari di daily activity
        $response->assertJsonCount(7, 'data.daily_activity');
    }

    public function test_can_get_weekly_recap_with_date_and_week_offset(): void
    {
        $targetDate = '2026-11-15';
        $response = $this->getJson("/api/stats/weekly-recap?date={$targetDate}&week_offset=0");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
