<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'class_category_id',
        'name',
        'arm',
        'description',
        'academic_year',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'academic_year' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClassCategory::class, 'class_category_id');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'class_staff', 'class_id', 'staff_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
                    ->withPivot('is_compulsory')
                    ->withTimestamps();
    }

    public function resultConfig(): HasOne
    {
        return $this->hasOne(ResultConfig::class, 'class_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ($this->arm ? ' (' . $this->arm . ')' : '');
    }
}
