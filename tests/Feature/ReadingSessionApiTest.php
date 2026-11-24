<?php

namespace Tests\Feature;

use App\Models\ReadingSession;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingSessionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_reading_sessions(): void
    {
        ReadingSession::create([
            'book_title' => 'Filosofi Teras',
            'author' => 'Henry Manampiring',
            'duration_seconds' => 1800,
            'pages_read' => 25,
            'session_date' => Carbon::today()->toDateString(),
        ]);

        $response = $this->getJson('/api/reading-sessions');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'book_title', 'author', 'duration_seconds', 'pages_read', 'session_date'],
                ],
                'meta' => ['total_sessions', 'total_duration_seconds', 'total_minutes', 'total_pages_read'],
            ])
            ->assertJsonPath('meta.total_sessions', 1)
            ->assertJsonPath('meta.total_duration_seconds', 1800)
            ->assertJsonPath('meta.total_minutes', 30);
    }

    public function test_can_store_reading_session_from_timer(): void
    {
        $payload = [
            'book_title' => 'Atomic Habits',
            'author' => 'James Clear',
            'duration_seconds' => 1500,
            'pages_read' => 20,
            'session_date' => Carbon::today()->toDateString(),
        ];

        $response = $this->postJson('/api/reading-sessions', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Sesi membaca berhasil dicatat.',
                'data' => [
                    'book_title' => 'Atomic Habits',
                    'duration_seconds' => 1500,
                    'pages_read' => 20,
                ],
            ]);

        $this->assertDatabaseHas('reading_sessions', [
            'book_title' => 'Atomic Habits',
            'duration_seconds' => 1500,
        ]);
    }

    public function test_can_get_today_reading_statistics(): void
    {
        ReadingSession::create([
            'book_title' => 'Buku Pagi',
            'duration_seconds' => 1200,
            'pages_read' => 15,
            'session_date' => Carbon::today()->toDateString(),
        ]);

        ReadingSession::create([
            'book_title' => 'Buku Sore',
            'duration_seconds' => 1800,
            'pages_read' => 25,
            'session_date' => Carbon::today()->toDateString(),
        ]);

        // Sesi kemarin (tidak boleh terhitung di today)
        ReadingSession::create([
            'book_title' => 'Buku Kemarin',
            'duration_seconds' => 3600,
            'pages_read' => 50,
            'session_date' => Carbon::yesterday()->toDateString(),
        ]);

        $response = $this->getJson('/api/reading-sessions/today');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'date' => Carbon::today()->toDateString(),
                    'total_seconds' => 3000,
                    'total_minutes' => 50,
                    'total_pages' => 40,
                    'sessions_count' => 2,
                ],
            ]);
    }

    public function test_can_show_reading_session(): void
    {
        $session = ReadingSession::create([
            'book_title' => 'Sapiens',
            'author' => 'Yuval Noah Harari',
            'duration_seconds' => 2400,
            'pages_read' => 30,
            'session_date' => Carbon::today()->toDateString(),
        ]);

        $response = $this->getJson("/api/reading-sessions/{$session->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $session->id,
                    'book_title' => 'Sapiens',
                ],
            ]);
    }

    public function test_can_delete_reading_session(): void
    {
        $session = ReadingSession::create([
            'book_title' => 'Sesi Hapus',
            'duration_seconds' => 600,
            'pages_read' => 10,
            'session_date' => Carbon::today()->toDateString(),
        ]);

        $response = $this->deleteJson("/api/reading-sessions/{$session->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Sesi membaca berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('reading_sessions', [
            'id' => $session->id,
        ]);
    }
}
