<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'year',
        'pages',
        'rating',
        'reviews',
        'cover_url',
    ];

    protected $casts = [
        'year' => 'integer',
        'pages' => 'integer',
        'rating' => 'double',
    ];
}
