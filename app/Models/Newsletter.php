<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'pdf_path',
        'term',
        'session',
    ];

    /**
     * Get the latest newsletter
     */
    public static function getLatest()
    {
        return static::latest()->first();
    }
}
