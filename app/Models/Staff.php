<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Staff extends Model
{
    protected $fillable = [
        'user_id',
        'staff_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'employment_date',
        'address',
        'is_active',
    ];

    protected $casts = [
        'employment_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_staff')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
