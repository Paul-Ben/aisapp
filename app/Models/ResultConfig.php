<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultConfig extends Model
{
    protected $fillable = [
        'class_id',
        'max_ca_score',
        'max_project_score',
        'max_exam_score',
        'project_enabled',
    ];

    protected $casts = [
        'project_enabled' => 'boolean',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function gradeScales(): HasMany
    {
        return $this->hasMany(GradeScale::class, 'result_config_id');
    }

    /**
     * Get or create result config for a class
     */
    public static function getOrCreateForClass($classId): self
    {
        $config = static::where('class_id', $classId)->first();
        
        if (!$config) {
            $config = static::create([
                'class_id' => $classId,
                'max_ca_score' => 40,
                'max_project_score' => 20,
                'max_exam_score' => 100,
                'project_enabled' => true,
            ]);

            // Create default grade scales
            $defaultGrades = [
                ['grade' => 'A', 'min_percentage' => 75, 'max_percentage' => 100],
                ['grade' => 'B', 'min_percentage' => 65, 'max_percentage' => 74],
                ['grade' => 'C', 'min_percentage' => 55, 'max_percentage' => 64],
                ['grade' => 'D', 'min_percentage' => 45, 'max_percentage' => 54],
                ['grade' => 'E', 'min_percentage' => 40, 'max_percentage' => 44],
                ['grade' => 'F', 'min_percentage' => 0, 'max_percentage' => 39],
            ];

            foreach ($defaultGrades as $gradeData) {
                $config->gradeScales()->create($gradeData);
            }
        }

        return $config;
    }
}
