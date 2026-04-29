<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->string('term')->default('First');
            $table->decimal('ca_score', 5, 2)->nullable();
            $table->decimal('project_score', 5, 2)->nullable();
            $table->decimal('exam_score', 5, 2)->nullable();
            $table->decimal('total_score', 5, 2);
            $table->string('grade')->nullable();
            $table->string('remark')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('staff')->onDelete('set null');
            $table->timestamps();

            $table->unique(['student_id', 'class_id', 'subject_id', 'academic_year_id', 'term'], 'results_main_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
