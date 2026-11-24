<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadingSessionApiController extends Controller
{
    /**
     * Ambil riwayat sesi membaca dengan filter tanggal dan rangkuman statistik.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user() ?: $request->user();
        $userId = $user?->id;

        $query = ReadingSession::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Filter judul buku
        if ($request->filled('book_title')) {
            $query->where('book_title', 'like', '%'.$request->input('book_title').'%');
        }

        // Filter tanggal spesifik
        if ($request->filled('date')) {
            $query->whereDate('session_date', $request->input('date'));
        }

        // Filter rentang tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('session_date', [
                $request->input('start_date'),
                $request->input('end_date'),
            ]);
        }

        $limit = min(100, max(1, (int) $request->input('limit', 50)));
        $sessions = (clone $query)->latest('session_date')->latest('id')->limit($limit)->get();

        $totalSessions = (clone $query)->count();
        $totalSeconds = (clone $query)->sum('duration_seconds');
        $totalPages = (clone $query)->sum('pages_read');

        return response()->json([
            'success' => true,
            'data' => $sessions,
            'meta' => [
                'total_sessions' => $totalSessions,
                'total_duration_seconds' => (int) $totalSeconds,
                'total_minutes' => (int) round($totalSeconds / 60),
                'total_pages_read' => (int) $totalPages,
            ],
        ]);
    }

    /**
     * Ambil statistik membaca khusus hari ini untuk layar Timer.
     */
    public function today(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user() ?: $request->user();
        $userId = $user?->id;
        $today = Carbon::today()->toDateString();

        $query = ReadingSession::whereDate('session_date', $today);
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $sessions = $query->latest('id')->get();
        $totalSeconds = $sessions->sum('duration_seconds');
        $totalPages = $sessions->sum('pages_read');

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $today,
                'total_seconds' => (int) $totalSeconds,
                'total_minutes' => (int) round($totalSeconds / 60),
                'total_hours' => round($totalSeconds / 3600, 1),
                'total_pages' => (int) $totalPages,
                'sessions_count' => $sessions->count(),
                'sessions' => $sessions,
            ],
        ]);
    }

    /**
     * Simpan sesi membaca baru yang selesai dari timer.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'duration_seconds' => 'required|integer|min:1',
            'pages_read' => 'nullable|integer|min:0',
            'session_date' => 'nullable|date',
        ]);

        $user = auth('sanctum')->user() ?: $request->user();
        $validated['user_id'] = $user?->id;
        $validated['session_date'] = $validated['session_date'] ?? Carbon::today()->toDateString();
        $validated['pages_read'] = $validated['pages_read'] ?? 0;

        $session = ReadingSession::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sesi membaca berhasil dicatat.',
            'data' => $session,
        ], 201);
    }

    /**
     * Tampilkan detail sesi membaca berdasarkan ID.
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $session = ReadingSession::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $session,
        ]);
    }

    /**
     * Hapus catatan sesi membaca.
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $session = ReadingSession::findOrFail($id);

        $user = auth('sanctum')->user() ?: $request->user();
        if ($session->user_id && $user && $session->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus sesi ini.',
            ], 403);
        }

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesi membaca berhasil dihapus.',
        ]);
    }
}
