<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Quote;
use App\Models\ReadingSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Priority3Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Katalog Buku untuk Scanner Barcode/ISBN (F006)
        $books = [
            [
                'isbn' => '9786020324784',
                'publisher' => 'GRAMEDIA PUSTAKA UTAMA',
                'title' => 'Hujan',
                'author' => 'Tere Liye',
                'year' => 2016,
                'pages' => 320,
                'rating' => 4.6,
                'reviews' => '52k ulasan',
                'cover_url' => 'assets/images/hujan.jpeg',
            ],
            [
                'isbn' => '9789792219463',
                'publisher' => 'BENTANG PUSTAKA',
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'year' => 2005,
                'pages' => 529,
                'rating' => 4.7,
                'reviews' => '89k ulasan',
                'cover_url' => null,
            ],
            [
                'isbn' => '9789799731234',
                'publisher' => 'HASTA MITRA',
                'title' => 'Bumi Manusia',
                'author' => 'Pramoedya Ananta Toer',
                'year' => 1980,
                'pages' => 535,
                'rating' => 4.8,
                'reviews' => '115k ulasan',
                'cover_url' => null,
            ],
            [
                'isbn' => '9780143105244',
                'publisher' => 'PENGUIN CLASSICS',
                'title' => 'The Metamorphosis',
                'author' => 'Franz Kafka',
                'year' => 1915,
                'pages' => 224,
                'rating' => 4.3,
                'reviews' => '412k ulasan',
                'cover_url' => null,
            ],
            [
                'isbn' => '9780679720201',
                'publisher' => 'VINTAGE INTERNATIONAL',
                'title' => 'The Stranger',
                'author' => 'Albert Camus',
                'year' => 1942,
                'pages' => 123,
                'rating' => 4.5,
                'reviews' => '380k ulasan',
                'cover_url' => null,
            ],
            [
                'isbn' => '9780156027601',
                'publisher' => 'HARCOURT BRACE',
                'title' => 'Blindness',
                'author' => 'José Saramago',
                'year' => 1995,
                'pages' => 352,
                'rating' => 4.2,
                'reviews' => '210k ulasan',
                'cover_url' => null,
            ],
        ];

        foreach ($books as $b) {
            Book::updateOrCreate(['isbn' => $b['isbn']], $b);
        }

        // 2. Seed Kutipan Favorit (F016)
        $quotes = [
            [
                'book_title' => 'Hujan',
                'author' => 'Tere Liye',
                'label' => 'ENDING',
                'text' => 'Bukan seberapa lama umat manusia bisa bertahan hidup sebagai ukuran kebahagiaan, tapi seberapa besar kemampuan mereka memeluk erat-erat semua hal menyakitkan yang mereka alami.',
                'cover_asset' => 'assets/images/hujan.jpeg',
                'is_favorite' => true,
            ],
            [
                'book_title' => 'Hujan',
                'author' => 'Tere Liye',
                'label' => 'HALAMAN 77',
                'text' => 'Jangan pernah menyesal mengenal seseorang dalam hidupmu. Karena dalam setiap pengalaman buruk, ada pelajaran yang bisa diambil.',
                'cover_asset' => 'assets/images/hujan.jpeg',
                'is_favorite' => true,
            ],
            [
                'book_title' => 'Aldebaran',
                'author' => 'Tere Liye',
                'label' => 'PEMBUKA',
                'text' => 'Ketika seseorang memutuskan untuk pergi, biarkan dia pergi. Takdirmu tidak pernah bergantung pada siapapun yang meninggalkanmu.',
                'cover_asset' => 'assets/images/aldebaran.jpeg',
                'is_favorite' => true,
            ],
            [
                'book_title' => 'The Stranger',
                'author' => 'Albert Camus',
                'label' => 'HALAMAN 112',
                'text' => 'In the depth of winter, I finally learned that within me there lay an invincible summer.',
                'cover_asset' => null,
                'is_favorite' => false,
            ],
            [
                'book_title' => 'Invisible Cities',
                'author' => 'Italo Calvino',
                'label' => 'HALAMAN 55',
                'text' => 'Kota-kota, seperti mimpi, dibangun dari keinginan dan ketakutan.',
                'cover_asset' => null,
                'is_favorite' => true,
            ],
            [
                'book_title' => 'The Myth of Sisyphus',
                'author' => 'Albert Camus',
                'label' => 'PENUTUP',
                'text' => 'One must imagine Sisyphus happy.',
                'cover_asset' => null,
                'is_favorite' => false,
            ],
        ];

        foreach ($quotes as $q) {
            Quote::updateOrCreate(
                ['book_title' => $q['book_title'], 'text' => $q['text']],
                $q
            );
        }

        // 3. Seed Sesi Membaca Minggu Ini (F013 & F014)
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();

        $sessions = [
            ['day_offset' => 0, 'book' => 'Hujan', 'minutes' => 35, 'pages' => 30],
            ['day_offset' => 1, 'book' => 'Hujan', 'minutes' => 50, 'pages' => 45],
            ['day_offset' => 2, 'book' => 'The Stranger', 'minutes' => 20, 'pages' => 18],
            ['day_offset' => 3, 'book' => 'The Stranger', 'minutes' => 40, 'pages' => 35],
            ['day_offset' => 4, 'book' => 'Aldebaran', 'minutes' => 60, 'pages' => 55],
            ['day_offset' => 5, 'book' => 'Aldebaran', 'minutes' => 30, 'pages' => 28],
            ['day_offset' => 6, 'book' => 'Invisible Cities', 'minutes' => 45, 'pages' => 36],
        ];

        foreach ($sessions as $s) {
            ReadingSession::updateOrCreate(
                [
                    'book_title' => $s['book'],
                    'session_date' => $startOfWeek->copy()->addDays($s['day_offset'])->toDateString(),
                ],
                [
                    'author' => 'Penulis',
                    'duration_seconds' => $s['minutes'] * 60,
                    'pages_read' => $s['pages'],
                ]
            );
        }
    }
}
