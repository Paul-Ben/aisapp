<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_item_class_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['fee_item_id', 'class_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_item_class_category');
    }
};
