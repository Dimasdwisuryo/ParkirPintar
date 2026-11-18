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
        Schema::table('parking_transactions', function (Blueprint $table) {
            $table->foreignId('checkin_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('checkout_by')->nullable()->after('checkin_by')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_transactions', function (Blueprint $table) {
            $table->dropForeign(['checkin_by']);
            $table->dropForeign(['checkout_by']);
            $table->dropColumn(['checkin_by', 'checkout_by']);
        });
    }
};
