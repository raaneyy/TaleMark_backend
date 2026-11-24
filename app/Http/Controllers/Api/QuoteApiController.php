<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;

class QuoteApiController extends Controller
{
    /**
     * Tampilkan daftar kutipan (bisa filter favorite & search)
     */
    public function index(Request $request)
    {
        $userId = $request->user()?->id;

        $query = Quote::query();

        if ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        }

        // Filter hanya favorit
        if ($request->boolean('favorite')) {
            $query->where('is_favorite', true);
        }

        // Pencarian teks, judul buku, atau penulis
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('text', 'like', "%{$search}%")
                  ->orWhere('book_title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $quotes = $query->latest()->get();

        $totalCount = Quote::count();
        $favCount = Quote::where('is_favorite', true)->count();
        $booksCount = Quote::distinct('book_title')->count('book_title');

        return response()->json([
            'success' => true,
            'data' => $quotes,
            'meta' => [
                'total_quotes' => $totalCount,
                'favorite_quotes' => $favCount,
                'total_books' => $booksCount,
            ]
        ]);
    }

    /**
     * Simpan kutipan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'label' => 'nullable|string|max:100',
            'text' => 'required|string',
            'cover_asset' => 'nullable|string',
            'is_favorite' => 'nullable|boolean',
        ]);

        $validated['user_id'] = $request->user()?->id;
        $validated['label'] = $validated['label'] ?? 'CATATAN';
        $validated['is_favorite'] = $validated['is_favorite'] ?? false;

        $quote = Quote::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kutipan berhasil disimpan.',
            'data' => $quote,
        ], 201);
    }

    /**
     * Toggle status favorit kutipan
     */
    public function toggleFavorite(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);
        $quote->is_favorite = !$quote->is_favorite;
        $quote->save();

        return response()->json([
            'success' => true,
            'message' => $quote->is_favorite
                ? 'Kutipan ditandai sebagai favorit.'
                : 'Kutipan dihapus dari favorit.',
            'data' => $quote,
        ]);
    }

    /**
     * Hapus kutipan
     */
    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kutipan berhasil dihapus.',
        ]);
    }
}
