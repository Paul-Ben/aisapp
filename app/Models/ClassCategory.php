<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'class_category_id');
    }
}
