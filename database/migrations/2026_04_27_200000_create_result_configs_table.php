<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('result_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->integer('max_ca_score')->default(40); // Continuous Assessment max score
            $table->integer('max_project_score')->default(20); // Project max score (optional)
            $table->integer('max_exam_score')->default(100); // Exam Score max score
            $table->boolean('project_enabled')->default(true); // Whether project is enabled for this class
            $table->timestamps();
        });

        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_config_id')->constrained('result_configs')->onDelete('cascade');
            $table->string('grade'); // A, B, C, D, E, F
            $table->integer('min_percentage'); // Minimum percentage for this grade
            $table->integer('max_percentage'); // Maximum percentage for this grade
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
        Schema::dropIfExists('result_configs');
    }
};
