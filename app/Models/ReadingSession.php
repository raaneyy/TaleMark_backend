<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_title',
        'author',
        'duration_seconds',
        'pages_read',
        'session_date',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'pages_read' => 'integer',
        'session_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
