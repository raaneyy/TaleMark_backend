<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookScanApiController extends Controller
{
    /**
     * Cari buku berdasarkan ISBN (Katalog lokal -> Open Library API -> Fallback)
     */
    public function scan(Request $request, $isbn)
    {
        $cleanCode = preg_replace('/[\s-]/', '', $isbn);

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

        // 2. Coba ambil dari Open Library API secara online
        try {
            $response = Http::timeout(5)->get("https://openlibrary.org/api/books", [
                'bibkeys' => "ISBN:{$cleanCode}",
                'format' => 'json',
                'jscmd' => 'data',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $key = "ISBN:{$cleanCode}";

                if (isset($data[$key])) {
                    $b = $data[$key];
                    $author = isset($b['authors'][0]['name']) ? $b['authors'][0]['name'] : 'Penulis Buku';
                    $publisher = isset($b['publishers'][0]['name']) ? strtoupper($b['publishers'][0]['name']) : 'PENERBIT';
                    $pages = $b['number_of_pages'] ?? 200;
                    $year = now()->year;

                    if (isset($b['publish_date']) && preg_match('/\b(19\d\d|20\d\d)\b/', $b['publish_date'], $matches)) {
                        $year = (int)$matches[0];
                    }

                    // Simpan ke database buku lokal agar pencarian berikutnya instan
                    $newBook = Book::create([
                        'isbn' => $cleanCode,
                        'title' => $b['title'] ?? "Buku ISBN {$isbn}",
                        'author' => $author,
                        'publisher' => $publisher,
                        'year' => $year,
                        'pages' => $pages,
                        'rating' => 4.5,
                        'reviews' => 'OpenLibrary Verified',
                        'cover_url' => $b['cover']['large'] ?? $b['cover']['medium'] ?? null,
                    ]);

                    return response()->json([
                        'success' => true,
                        'source' => 'open_library',
                        'data' => $newBook,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Abaikan error koneksi luar, lanjut ke fallback
        }

        // 3. Fallback jika tidak ditemukan
        $fallbackBook = [
            'isbn' => $isbn,
            'title' => "Buku ISBN {$isbn}",
            'author' => 'Penulis Buku',
            'publisher' => 'PUSTAKA UMUM',
            'year' => now()->year,
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
     * Simpan buku hasil scan ke katalog / rak bacaan
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
            'pages' => 'nullable|integer',
            'rating' => 'nullable|numeric',
            'reviews' => 'nullable|string',
        ]);

        $cleanCode = preg_replace('/[\s-]/', '', $validated['isbn']);
        $validated['isbn'] = $cleanCode;

        $book = Book::updateOrCreate(['isbn' => $cleanCode], $validated);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil disimpan ke rak bacaan.',
            'data' => $book,
        ]);
    }
}
