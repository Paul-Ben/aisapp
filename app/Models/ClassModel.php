<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'arm',
        'academic_year',
        'status',
    ];

    protected $casts = [
        'academic_year' => 'integer',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function previousStudents()
    {
        return $this->hasMany(Student::class, 'previous_class_id');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
