<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * FR-01: Menambahkan area parkir
     * FR-02: Menampilkan daftar area parkir
     * FR-05: Mengatur kapasitas motor dan mobil
     */
    public function up(): void
    {
        Schema::create('parking_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('location', 255)->nullable();
            $table->unsignedInteger('capacity_motorcycle')->default(0);
            $table->unsignedInteger('capacity_car')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk pencarian dan filter
            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_areas');
    }
};
