<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model Area Parkir
 *
 * FR-01: Menambahkan area parkir
 * FR-02: Menampilkan daftar area parkir
 * FR-03: Mengubah informasi area
 * FR-04: Menghapus area (dengan ketentuan)
 * FR-05: Mengatur kapasitas motor dan mobil
 * FR-06: Menghitung slot terisi dan tersedia
 *
 * @property int $id
 * @property string $name
 * @property string|null $location
 * @property int $capacity_motorcycle
 * @property int $capacity_car
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ParkingArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'capacity_motorcycle',
        'capacity_car',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity_motorcycle' => 'integer',
            'capacity_car'        => 'integer',
            'is_active'           => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function transactions(): HasMany
    {
        return $this->hasMany(ParkingTransaction::class);
    }

    public function activeTransactions(): HasMany
    {
        return $this->hasMany(ParkingTransaction::class)
            ->where('status', 'active');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ─── Business Logic Helpers ───────────────────────────────────────────────

    /**
     * FR-06: Jumlah motor yang sedang terparkir.
     */
    public function getOccupiedMotorcycleCount(): int
    {
        return $this->activeTransactions()
            ->where('vehicle_type', 'motorcycle')
            ->count();
    }

    /**
     * FR-06: Jumlah mobil yang sedang terparkir.
     */
    public function getOccupiedCarCount(): int
    {
        return $this->activeTransactions()
            ->where('vehicle_type', 'car')
            ->count();
    }

    /**
     * FR-06: Slot motor tersedia.
     */
    public function getAvailableMotorcycleSlots(): int
    {
        return max(0, $this->capacity_motorcycle - $this->getOccupiedMotorcycleCount());
    }

    /**
     * FR-06: Slot mobil tersedia.
     */
    public function getAvailableCarSlots(): int
    {
        return max(0, $this->capacity_car - $this->getOccupiedCarCount());
    }

    /**
     * FR-07/FR-10: Cek apakah area masih menerima kendaraan sesuai tipe.
     */
    public function hasAvailableSlot(string $vehicleType): bool
    {
        return match ($vehicleType) {
            'motorcycle' => $this->getAvailableMotorcycleSlots() > 0,
            'car'        => $this->getAvailableCarSlots() > 0,
            default      => false,
        };
    }

    /**
     * FR-04: Cek apakah area memiliki riwayat transaksi.
     * Area dengan riwayat transaksi tidak boleh dihapus fisik.
     */
    public function hasTransactionHistory(): bool
    {
        return $this->transactions()->exists();
    }

    /**
     * FR-04: Cek apakah area memiliki transaksi aktif.
     */
    public function hasActiveTransactions(): bool
    {
        return $this->activeTransactions()->exists();
    }
}
