<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookScanApiController extends Controller
{
    /**
     * Tampilkan katalog buku yang tersedia / pernah discan.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Book::query();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $limit = min(50, max(1, (int) $request->input('limit', 20)));
        $books = $query->latest('id')->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $books->items(),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'total' => $books->total(),
            ],
        ]);
    }

    /**
     * Cari buku berdasarkan ISBN (Database lokal -> Google Books API -> Open Library API -> Fallback).
     */
    public function scan(Request $request, string $isbn): JsonResponse
    {
        $cleanCode = preg_replace('/[^0-9X]/i', '', $isbn);

        // 1. Cek di database lokal Laravel
        $book = Book::where('isbn', $cleanCode)
            ->orWhere('isbn', $isbn)
            ->first();

        if ($book) {
            return response()->json([
                'success' => true,
                'source' => 'local_database',
                'data' => $book,
            ]);
        }

        // 2. Coba cari di Google Books API
        try {
            $googleResponse = Http::timeout(4)->get('https://www.googleapis.com/books/v1/volumes', [
                'q' => "isbn:{$cleanCode}",
            ]);

            if ($googleResponse->successful()) {
                $gData = $googleResponse->json();
                if (! empty($gData['items'][0]['volumeInfo'])) {
                    $info = $gData['items'][0]['volumeInfo'];

                    $title = $info['title'] ?? "Buku ISBN {$isbn}";
                    $authors = isset($info['authors']) ? implode(', ', $info['authors']) : 'Penulis Buku';
                    $publisher = isset($info['publisher']) ? strtoupper($info['publisher']) : 'PENERBIT';
                    $pages = $info['pageCount'] ?? 200;
                    $year = now()->year;

                    if (! empty($info['publishedDate']) && preg_match('/\b(19\d\d|20\d\d)\b/', $info['publishedDate'], $matches)) {
                        $year = (int) $matches[0];
                    }

                    $rating = isset($info['averageRating']) ? (float) $info['averageRating'] : 4.5;
                    $ratingsCount = $info['ratingsCount'] ?? null;
                    $reviews = $ratingsCount ? "{$ratingsCount} ulasan" : 'Google Books Verified';

                    $coverUrl = $info['imageLinks']['thumbnail']
                        ?? $info['imageLinks']['smallThumbnail']
                        ?? null;

                    if ($coverUrl) {
                        $coverUrl = str_replace('http://', 'https://', $coverUrl);
                    }

                    $newBook = Book::updateOrCreate(
                        ['isbn' => $cleanCode],
                        [
                            'title' => $title,
                            'author' => $authors,
                            'publisher' => $publisher,
                            'year' => $year,
                            'pages' => $pages,
                            'rating' => $rating,
                            'reviews' => $reviews,
                            'cover_url' => $coverUrl,
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'source' => 'google_books',
                        'data' => $newBook,
                    ]);
                }
            }
        } catch (\Exception) {
            // Lanjut ke Open Library jika Google Books gagal
        }

        // 3. Coba cari di Open Library API
        try {
            $olResponse = Http::timeout(4)->get('https://openlibrary.org/api/books', [
                'bibkeys' => "ISBN:{$cleanCode}",
                'format' => 'json',
                'jscmd' => 'data',
            ]);

            if ($olResponse->successful()) {
                $data = $olResponse->json();
                $key = "ISBN:{$cleanCode}";

                if (isset($data[$key])) {
                    $b = $data[$key];
                    $author = isset($b['authors'][0]['name']) ? $b['authors'][0]['name'] : 'Penulis Buku';
                    $publisher = isset($b['publishers'][0]['name']) ? strtoupper($b['publishers'][0]['name']) : 'PENERBIT';
                    $pages = $b['number_of_pages'] ?? 200;
                    $year = now()->year;

                    if (isset($b['publish_date']) && preg_match('/\b(19\d\d|20\d\d)\b/', $b['publish_date'], $matches)) {
                        $year = (int) $matches[0];
                    }

                    $coverUrl = $b['cover']['large'] ?? $b['cover']['medium'] ?? null;

                    $newBook = Book::updateOrCreate(
                        ['isbn' => $cleanCode],
                        [
                            'title' => $b['title'] ?? "Buku ISBN {$isbn}",
                            'author' => $author,
                            'publisher' => $publisher,
                            'year' => $year,
                            'pages' => $pages,
                            'rating' => 4.5,
                            'reviews' => 'OpenLibrary Verified',
                            'cover_url' => $coverUrl,
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'source' => 'open_library',
                        'data' => $newBook,
                    ]);
                }
            }
        } catch (\Exception) {
            // Abaikan error jaringan
        }

        // 4. Fallback jika buku belum terdata secara online
        $fallbackBook = [
            'isbn' => $cleanCode ?: $isbn,
            'title' => "Buku ISBN {$isbn}",
            'author' => 'Penulis Buku',
            'publisher' => 'PUSTAKA UMUM',
            'year' => (int) now()->year,
            'pages' => 200,
            'rating' => 4.5,
            'reviews' => 'Barcode Terdeteksi',
            'cover_url' => null,
        ];

        return response()->json([
            'success' => true,
            'source' => 'fallback',
            'data' => $fallbackBook,
        ]);
    }

    /**
     * Tampilkan detail satu buku berdasarkan ID atau ISBN.
     */
    public function show(Request $request, int|string $id): JsonResponse
    {
        $book = Book::where('id', $id)
            ->orWhere('isbn', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $book,
        ]);
    }

    /**
     * Simpan atau perbarui buku hasil scan ke katalog.
     */
    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'isbn' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
            'pages' => 'nullable|integer',
            'rating' => 'nullable|numeric',
            'reviews' => 'nullable|string|max:255',
            'cover_url' => 'nullable|string',
        ]);

        $cleanCode = preg_replace('/[^0-9X]/i', '', $validated['isbn']) ?: $validated['isbn'];
        $validated['isbn'] = $cleanCode;

        $book = Book::updateOrCreate(['isbn' => $cleanCode], $validated);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil disimpan ke rak bacaan.',
            'data' => $book,
        ]);
    }
}
