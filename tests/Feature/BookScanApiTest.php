<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookScanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_scan_existing_book_from_local_database(): void
    {
        Book::create([
            'isbn' => '9786020324784',
            'title' => 'Hujan',
            'author' => 'Tere Liye',
            'publisher' => 'GRAMEDIA PUSTAKA UTAMA',
            'year' => 2016,
            'pages' => 320,
            'rating' => 4.6,
            'reviews' => '52k ulasan',
        ]);

        $response = $this->getJson('/api/books/scan/9786020324784');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'source' => 'local_database',
                'data' => [
                    'isbn' => '9786020324784',
                    'title' => 'Hujan',
                    'author' => 'Tere Liye',
                ],
            ]);
    }

    public function test_can_scan_book_with_fallback_when_external_api_has_no_match(): void
    {
        // ISBN acak yang tidak ada di DB lokal
        $response = $this->getJson('/api/books/scan/9999999999999');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'isbn' => '9999999999999',
                ],
            ]);
    }

    public function test_can_save_scanned_book_to_catalog(): void
    {
        $payload = [
            'isbn' => '9789792219463',
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'publisher' => 'BENTANG PUSTAKA',
            'year' => 2005,
            'pages' => 529,
            'rating' => 4.7,
            'reviews' => '89k ulasan',
        ];

        $response = $this->postJson('/api/books/scan/save', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Buku berhasil disimpan ke rak bacaan.',
                'data' => [
                    'isbn' => '9789792219463',
                    'title' => 'Laskar Pelangi',
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'isbn' => '9789792219463',
            'title' => 'Laskar Pelangi',
        ]);
    }

    public function test_can_list_books_catalog_with_search(): void
    {
        Book::create([
            'isbn' => '9789799731234',
            'title' => 'Bumi Manusia',
            'author' => 'Pramoedya Ananta Toer',
        ]);

        Book::create([
            'isbn' => '9780143105244',
            'title' => 'The Metamorphosis',
            'author' => 'Franz Kafka',
        ]);

        $response = $this->getJson('/api/books?q=Pramoedya');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Bumi Manusia');
    }

    public function test_can_show_book_detail(): void
    {
        $book = Book::create([
            'isbn' => '9780679720201',
            'title' => 'The Stranger',
            'author' => 'Albert Camus',
        ]);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $book->id,
                    'title' => 'The Stranger',
                ],
            ]);
    }
}
