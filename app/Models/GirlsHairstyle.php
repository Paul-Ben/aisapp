<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GirlsHairstyle extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'term',
        'session',
    ];

    /**
     * Get the latest girls hairstyle
     */
    public static function getLatest()
    {
        return static::latest()->first();
    }
}
