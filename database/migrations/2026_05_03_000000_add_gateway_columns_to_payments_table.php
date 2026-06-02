<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway', 20)->nullable()->after('notes');
            $table->string('gateway_reference', 100)->nullable()->after('gateway');
            $table->string('gateway_channel', 50)->nullable()->after('gateway_reference');
            $table->json('gateway_response')->nullable()->after('gateway_channel');

            $table->index('gateway_reference');
            $table->index('gateway');

            $table->dropForeign(['recorded_by']);
            $table->foreignId('recorded_by')->nullable()->change();
            $table->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->foreignId('recorded_by')->nullable(false)->change();
            $table->foreign('recorded_by')->references('id')->on('users')->restrictOnDelete();

            $table->dropIndex(['gateway_reference']);
            $table->dropIndex(['gateway']);

            $table->dropColumn(['gateway', 'gateway_reference', 'gateway_channel', 'gateway_response']);
        });
    }
};
