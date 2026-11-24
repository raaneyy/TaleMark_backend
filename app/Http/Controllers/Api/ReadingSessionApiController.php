<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingSession;
use Illuminate\Http\Request;

class ReadingSessionApiController extends Controller
{
    /**
     * Ambil riwayat sesi membaca
     */
    public function index(Request $request)
    {
        $userId = $request->user()?->id;

        $query = ReadingSession::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $sessions = $query->latest('session_date')->latest('id')->limit(20)->get();

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    /**
     * Simpan sesi membaca baru dari timer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'duration_seconds' => 'required|integer|min:1',
            'pages_read' => 'nullable|integer|min:0',
            'session_date' => 'nullable|date',
        ]);

        $validated['user_id'] = $request->user()?->id;
        $validated['session_date'] = $validated['session_date'] ?? now()->toDateString();
        $validated['pages_read'] = $validated['pages_read'] ?? 0;

        $session = ReadingSession::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sesi membaca berhasil dicatat.',
            'data' => $session,
        ], 201);
    }
}
