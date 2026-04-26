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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->string('nationality')->default('Nigerian');
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->foreignId('class_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('previous_class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->enum('status', ['active', 'inactive', 'graduated', 'withdrawn'])->default('active');
            $table->date('admission_date');
            $table->date('graduation_date')->nullable();
            $table->text('parent_info')->nullable();
            $table->text('guardian_info')->nullable();
            $table->text('medical_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
