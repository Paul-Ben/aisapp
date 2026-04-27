<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeScale extends Model
{
    protected $fillable = [
        'result_config_id',
        'grade',
        'min_percentage',
        'max_percentage',
    ];

    public function resultConfig(): BelongsTo
    {
        return $this->belongsTo(ResultConfig::class, 'result_config_id');
    }

    /**
     * Get grade for a percentage score
     */
    public static function getGradeForPercentage($resultConfigId, $percentage): ?string
    {
        $gradeScale = static::where('result_config_id', $resultConfigId)
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        return $gradeScale ? $gradeScale->grade : null;
    }
}
