<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeeItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'amount',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'fee_item_class', 'fee_item_id', 'class_id')
            ->withTimestamps();
    }

    public function classCategories(): BelongsToMany
    {
        return $this->belongsToMany(ClassCategory::class, 'fee_item_class_category', 'fee_item_id', 'class_category_id')
            ->withTimestamps();
    }
}
