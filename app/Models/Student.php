<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'state',
        'lga',
        'nationality',
        'religion',
        'blood_group',
        'genotype',
        'class_id',
        'previous_class_id',
        'status',
        'admission_date',
        'graduation_date',
        'graduation_session_id',
        'parent_info',
        'guardian_info',
        'medical_info',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'graduation_date' => 'date',
        'parent_info' => 'array',
        'guardian_info' => 'array',
        'medical_info' => 'array',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function previousClass()
    {
        return $this->belongsTo(SchoolClass::class, 'previous_class_id');
    }

    public function graduationSession()
    {
        return $this->belongsTo(AcademicSession::class, 'graduation_session_id');
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isGraduated()
    {
        return $this->status === 'graduated';
    }

    public function graduate($sessionId = null)
    {
        $this->update([
            'status' => 'graduated',
            'graduation_date' => now(),
            'graduation_session_id' => $sessionId ?? AcademicSession::getActive()?->id,
        ]);
    }

    public function promote($newClassId)
    {
        $this->update([
            'previous_class_id' => $this->class_id,
            'class_id' => $newClassId,
        ]);
    }

    public function demote($newClassId)
    {
        $this->update([
            'previous_class_id' => $this->class_id,
            'class_id' => $newClassId,
        ]);
    }
}
