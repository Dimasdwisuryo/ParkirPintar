<?php

namespace App\Services;

use App\Models\ParkingArea;
use Illuminate\Database\Eloquent\Collection;

/**
 * ParkingAllocationService
 *
 * Mengelola alokasi area parkir secara otomatis menggunakan algoritma Sequential Fill (Pengisian Berurutan Hingga Penuh).
 *
 * Aturan Alokasi:
 * 1. Mengambil seluruh area aktif yang diurutkan berdasarkan urutan area (ID ascending).
 * 2. Memprioritaskan satu area pertama yang masih memiliki sisa kapasitas untuk tipe kendaraan (occupied < capacity).
 * 3. Terus mengarahkan kendaraan berikutnya ke area tersebut sampai kapasitasnya terisi penuh (occupied == capacity).
 * 4. Setelah area tersebut penuh, kendaraan berikutnya otomatis dialokasikan ke area aktif berikutnya yang masih tersedia.
 * 5. Jika seluruh area aktif untuk tipe kendaraan tersebut sudah penuh, sistem mengembalikan null.
 */
class ParkingAllocationService
{
    /**
     * Alokasi area parkir otomatis dengan algoritma Sequential Fill.
     *
     * @param string $vehicleType 'car' | 'motorcycle'
     * @param bool $lockForUpdate Apakah perlu mengunci baris di database (dalam DB::transaction)
     * @return ParkingArea|null
     */
    public function allocate(string $vehicleType, bool $lockForUpdate = false): ?ParkingArea
    {
        $query = ParkingArea::active()->orderBy('id', 'asc');

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $areas = $query->get();

        foreach ($areas as $area) {
            if ($area->hasAvailableSlot($vehicleType)) {
                return $area;
            }
        }

        return null;
    }

    /**
     * Alokasi area untuk mobil.
     */
    public function allocateCar(bool $lockForUpdate = false): ?ParkingArea
    {
        return $this->allocate('car', $lockForUpdate);
    }

    /**
     * Alokasi area untuk motor.
     */
    public function allocateMotorcycle(bool $lockForUpdate = false): ?ParkingArea
    {
        return $this->allocate('motorcycle', $lockForUpdate);
    }
}
