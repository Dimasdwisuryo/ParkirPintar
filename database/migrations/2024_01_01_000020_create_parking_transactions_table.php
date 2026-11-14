<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * FR-07 s/d FR-14: Check-in dan check-out kendaraan
     * FR-15 s/d FR-19: Perhitungan tarif
     */
    public function up(): void
    {
        Schema::create('parking_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique();
            $table->string('license_plate', 15);
            $table->enum('vehicle_type', ['motorcycle', 'car']);
            $table->foreignId('parking_area_id')
                ->constrained('parking_areas')
                ->restrictOnDelete(); // FR-04: area dgn riwayat transaksi tidak boleh dihapus sembarangan
            $table->dateTime('check_in_at');
            $table->dateTime('check_out_at')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable(); // durasi dalam menit
            $table->unsignedBigInteger('fee')->nullable();           // biaya dalam rupiah
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestamps();

            // NFR-03: index untuk mencegah tiket aktif ganda per plat
            $table->index(['license_plate', 'status']);
            // Index untuk pencarian tiket
            $table->index('ticket_number');
            // Index untuk laporan berdasarkan tanggal
            $table->index('check_in_at');
            $table->index('check_out_at');
            // Index untuk laporan per area
            $table->index(['parking_area_id', 'status']);
            $table->index(['parking_area_id', 'vehicle_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_transactions');
    }
};
