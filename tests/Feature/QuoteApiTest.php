<?php

namespace Tests\Feature;

use App\Models\Quote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_quotes(): void
    {
        Quote::create([
            'book_title' => 'Bumi Manusia',
            'author' => 'Pramoedya Ananta Toer',
            'label' => 'FILOSOFI',
            'text' => 'Kalian boleh maju dalam pelajaran, tapi tanpa budi pekerti, kalian adalah liar.',
            'is_favorite' => true,
        ]);

        $response = $this->getJson('/api/quotes');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'book_title', 'author', 'label', 'text', 'is_favorite'],
                ],
                'meta' => ['total_quotes', 'favorite_quotes', 'total_books', 'available_labels'],
            ]);
    }

    public function test_can_filter_quotes_by_favorite_and_search(): void
    {
        Quote::create([
            'book_title' => 'Hujan',
            'author' => 'Tere Liye',
            'label' => 'ENDING',
            'text' => 'Bukan seberapa lama umat manusia bisa bertahan hidup.',
            'is_favorite' => true,
        ]);

        Quote::create([
            'book_title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'label' => 'CATATAN',
            'text' => 'Bermimpilah, karena Tuhan akan memeluk mimpi-mimpimu.',
            'is_favorite' => false,
        ]);

        // Filter favorite
        $favResponse = $this->getJson('/api/quotes?favorite=1');
        $favResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.book_title', 'Hujan');

        // Search q
        $searchResponse = $this->getJson('/api/quotes?q=Tuhan');
        $searchResponse->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.book_title', 'Laskar Pelangi');
    }

    public function test_can_create_new_quote(): void
    {
        $payload = [
            'book_title' => 'Filosofi Teras',
            'author' => 'Henry Manampiring',
            'label' => 'BAB 3',
            'text' => 'Beberapa hal ada di bawah kendali kita, dan beberapa lainnya tidak.',
            'is_favorite' => true,
        ];

        $response = $this->postJson('/api/quotes', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Kutipan berhasil disimpan.',
                'data' => [
                    'book_title' => 'Filosofi Teras',
                    'label' => 'BAB 3',
                    'is_favorite' => true,
                ],
            ]);

        $this->assertDatabaseHas('quotes', [
            'book_title' => 'Filosofi Teras',
            'text' => 'Beberapa hal ada di bawah kendali kita, dan beberapa lainnya tidak.',
        ]);
    }

    public function test_can_show_quote_detail(): void
    {
        $quote = Quote::create([
            'book_title' => 'Cantik Itu Luka',
            'author' => 'Eka Kurniawan',
            'label' => 'PEMBUKA',
            'text' => 'Suatu sore, Dewi Ayu bangkit dari kuburan.',
            'is_favorite' => false,
        ]);

        $response = $this->getJson("/api/quotes/{$quote->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $quote->id,
                    'book_title' => 'Cantik Itu Luka',
                ],
            ]);
    }

    public function test_can_update_quote(): void
    {
        $quote = Quote::create([
            'book_title' => 'Catatan Juang',
            'author' => 'Fiersa Besari',
            'label' => 'CATATAN',
            'text' => 'Teks lama',
            'is_favorite' => false,
        ]);

        $response = $this->putJson("/api/quotes/{$quote->id}", [
            'text' => 'Teks baru yang telah diedit.',
            'label' => 'REVISI',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Kutipan berhasil diperbarui.',
                'data' => [
                    'text' => 'Teks baru yang telah diedit.',
                    'label' => 'REVISI',
                ],
            ]);

        $this->assertDatabaseHas('quotes', [
            'id' => $quote->id,
            'text' => 'Teks baru yang telah diedit.',
        ]);
    }

    public function test_can_toggle_favorite_status(): void
    {
        $quote = Quote::create([
            'book_title' => 'Ronggeng Dukuh Paruk',
            'author' => 'Ahmad Tohari',
            'label' => 'CATATAN',
            'text' => 'Dukuh Paruk yang tenang kembali bergetar.',
            'is_favorite' => false,
        ]);

        // Toggle to true
        $res1 = $this->putJson("/api/quotes/{$quote->id}/favorite");
        $res1->assertStatus(200)->assertJsonPath('data.is_favorite', true);

        // Toggle back to false
        $res2 = $this->putJson("/api/quotes/{$quote->id}/favorite");
        $res2->assertStatus(200)->assertJsonPath('data.is_favorite', false);

        // Explicit set via body
        $res3 = $this->putJson("/api/quotes/{$quote->id}/favorite", ['is_favorite' => true]);
        $res3->assertStatus(200)->assertJsonPath('data.is_favorite', true);
    }

    public function test_can_delete_quote(): void
    {
        $quote = Quote::create([
            'book_title' => 'Buku Hapus',
            'author' => 'Penulis',
            'label' => 'CATATAN',
            'text' => 'Kutipan yang akan segera dihapus.',
            'is_favorite' => false,
        ]);

        $response = $this->deleteJson("/api/quotes/{$quote->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Kutipan berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('quotes', [
            'id' => $quote->id,
        ]);
    }
}
