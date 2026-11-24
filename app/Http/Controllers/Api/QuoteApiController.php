<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteApiController extends Controller
{
    /**
     * Tampilkan daftar kutipan dengan filter pencarian, status favorit, dan label.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('sanctum')->user() ?: $request->user();
        $userId = $user?->id;

        $query = Quote::query();

        if ($userId) {
            if ($request->boolean('user_only')) {
                $query->where('user_id', $userId);
            } else {
                $query->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                });
            }
        }

        // Filter status favorit
        if ($request->has('favorite')) {
            $query->where('is_favorite', $request->boolean('favorite'));
        }

        // Filter label kategori (misal: 'CATATAN', 'ENDING', 'PEMBUKA')
        if ($request->filled('label')) {
            $query->where('label', $request->input('label'));
        }

        // Filter judul buku tertentu
        if ($request->filled('book_title')) {
            $query->where('book_title', $request->input('book_title'));
        }

        // Pencarian teks, judul buku, atau penulis
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('text', 'like', "%{$search}%")
                    ->orWhere('book_title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%");
            });
        }

        $quotes = $query->latest('id')->get();

        // Statistik relevan terhadap cakupan user
        $statsQuery = Quote::query();
        if ($userId) {
            $statsQuery->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhereNull('user_id');
            });
        }

        $totalQuotes = (clone $statsQuery)->count();
        $favCount = (clone $statsQuery)->where('is_favorite', true)->count();
        $booksCount = (clone $statsQuery)->distinct('book_title')->count('book_title');
        $availableLabels = (clone $statsQuery)->whereNotNull('label')->distinct()->pluck('label');

        return response()->json([
            'success' => true,
            'data' => $quotes,
            'meta' => [
                'total_quotes' => $totalQuotes,
                'favorite_quotes' => $favCount,
                'total_books' => $booksCount,
                'available_labels' => $availableLabels,
            ],
        ]);
    }

    /**
     * Tampilkan detail satu kutipan berdasarkan ID.
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $quote = Quote::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $quote,
        ]);
    }

    /**
     * Simpan kutipan baru ke dalam database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'label' => 'nullable|string|max:100',
            'text' => 'required|string',
            'cover_asset' => 'nullable|string|max:255',
            'is_favorite' => 'nullable|boolean',
        ]);

        $user = auth('sanctum')->user() ?: $request->user();
        $validated['user_id'] = $user?->id;
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
     * Perbarui data kutipan yang sudah ada.
     */
    public function update(Request $request, int|string $id): JsonResponse
    {
        $quote = Quote::findOrFail($id);

        $user = auth('sanctum')->user() ?: $request->user();
        if ($quote->user_id && $user && $quote->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk mengubah kutipan ini.',
            ], 403);
        }

        $validated = $request->validate([
            'book_title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'label' => 'nullable|string|max:100',
            'text' => 'sometimes|required|string',
            'cover_asset' => 'nullable|string|max:255',
            'is_favorite' => 'nullable|boolean',
        ]);

        $quote->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kutipan berhasil diperbarui.',
            'data' => $quote,
        ]);
    }

    /**
     * Toggle atau ubah status favorit kutipan.
     */
    public function toggleFavorite(Request $request, int|string $id): JsonResponse
    {
        $quote = Quote::findOrFail($id);

        if ($request->has('is_favorite')) {
            $quote->is_favorite = $request->boolean('is_favorite');
        } else {
            $quote->is_favorite = ! $quote->is_favorite;
        }

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
     * Hapus kutipan dari database.
     */
    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $quote = Quote::findOrFail($id);

        $user = auth('sanctum')->user() ?: $request->user();
        if ($quote->user_id && $user && $quote->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk menghapus kutipan ini.',
            ], 403);
        }

        $quote->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kutipan berhasil dihapus.',
        ]);
    }
}
