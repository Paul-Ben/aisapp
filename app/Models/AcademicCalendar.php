<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path',
        'term',
        'session',
    ];

    /**
     * Get the latest academic calendar
     */
    public static function getLatest()
    {
        return static::latest()->first();
    }
}
