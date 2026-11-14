<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model Transaksi Parkir
 *
 * FR-07 s/d FR-14: Check-in dan check-out kendaraan
 * FR-15 s/d FR-19: Perhitungan tarif
 * FR-20 s/d FR-24: Laporan parkir
 *
 * @property int $id
 * @property string $ticket_number
 * @property string $license_plate
 * @property string $vehicle_type  'motorcycle'|'car'
 * @property int $parking_area_id
 * @property \Carbon\Carbon $check_in_at
 * @property \Carbon\Carbon|null $check_out_at
 * @property int|null $duration_minutes
 * @property int|null $fee
 * @property string $status  'active'|'completed'
 */
class ParkingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'license_plate',
        'vehicle_type',
        'parking_area_id',
        'check_in_at',
        'check_out_at',
        'duration_minutes',
        'fee',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at'      => 'datetime',
            'check_out_at'     => 'datetime',
            'duration_minutes' => 'integer',
            'fee'              => 'integer',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function parkingArea(): BelongsTo
    {
        return $this->belongsTo(ParkingArea::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeForLicensePlate(Builder $query, string $plate): Builder
    {
        return $query->where('license_plate', strtoupper(trim($plate)));
    }

    public function scopeForTicket(Builder $query, string $ticketNumber): Builder
    {
        return $query->where('ticket_number', strtoupper(trim($ticketNumber)));
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
