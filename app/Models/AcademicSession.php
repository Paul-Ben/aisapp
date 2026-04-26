<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session',
        'term',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the currently active academic session
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Set this session as active (and deactivate others)
     */
    public function setActive()
    {
        // Deactivate all other sessions
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this session
        $this->update(['is_active' => true]);
    }
}
