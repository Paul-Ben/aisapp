<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'fee_item_id',
        'academic_year_id',
        'term',
        'amount_paid',
        'status',
        'reference',
        'notes',
        'paid_at',
        'recorded_by',
        'gateway',
        'gateway_reference',
        'gateway_channel',
        'gateway_response',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'date',
        'gateway_response' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeItem(): BelongsTo
    {
        return $this->belongsTo(FeeItem::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_year_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
