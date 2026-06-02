<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('fee_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->string('term')->default('first');
            $table->decimal('amount_paid', 10, 2);
            $table->enum('status', ['paid', 'part'])->default('part');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->date('paid_at');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(
                ['student_id', 'fee_item_id', 'academic_year_id', 'term'],
                'payments_term_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
