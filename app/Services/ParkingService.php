<?php

namespace App\Services;

use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * ParkingService
 *
 * Bertanggung jawab atas business logic check-in dan check-out kendaraan.
 * NFR-09: Logika bisnis dipisahkan dari controller.
 */
class ParkingService
{
    public function __construct(
        private readonly ParkingFeeCalculator $feeCalculator,
        private readonly ParkingAllocationService $allocationService
    ) {}

    // ─── Check-In ─────────────────────────────────────────────────────────────

    /**
     * FR-07 s/d FR-11: Proses check-in kendaraan.
     * Mendukung alokasi spesifik atau alokasi otomatis berurutan hingga penuh (Sequential Fill).
     *
     * @param  string   $licensePlate  Nomor plat kendaraan
     * @param  string   $vehicleType   'motorcycle' | 'car'
     * @param  int|null $areaId        ID area parkir (opsional, jika null otomatis sequential fill)
     * @return ParkingTransaction      Transaksi yang baru dibuat
     *
     * @throws \RuntimeException Jika validasi gagal
     */
    public function checkIn(string $licensePlate, string $vehicleType, ?int $areaId = null): ParkingTransaction
    {
        $plate = strtoupper(trim($licensePlate));

        return DB::transaction(function () use ($plate, $vehicleType, $areaId) {
            $typeLabel = $vehicleType === 'car' ? 'mobil' : 'motor';

            // NFR-03: Cegah satu plat memiliki dua transaksi aktif
            $existingActive = ParkingTransaction::where('license_plate', $plate)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if ($existingActive) {
                throw new \RuntimeException(
                    "Nomor plat {$plate} sudah memiliki transaksi aktif (tiket: {$existingActive->ticket_number})."
                );
            }

            // Alokasi Area: Spesifik atau Otomatis Sequential Fill
            if ($areaId && $areaId > 0) {
                // NFR-02: Lock area agar tidak terjadi race condition kapasitas
                $area = ParkingArea::lockForUpdate()->find($areaId);

                if (! $area) {
                    throw new \RuntimeException('Area parkir tidak ditemukan.');
                }

                // FR-01/FR-05: Pastikan area aktif
                if (! $area->is_active) {
                    throw new \RuntimeException('Area parkir tidak aktif.');
                }

                // FR-10: Tolak check-in jika kapasitas penuh
                if (! $area->hasAvailableSlot($vehicleType)) {
                    throw new \RuntimeException("Kapasitas {$typeLabel} di area ini sudah penuh.");
                }
            } else {
                // Sequential Fill: Alokasikan ke area aktif pertama yang memiliki sisa slot
                $area = $this->allocationService->allocate($vehicleType, true);

                if (! $area) {
                    throw new \RuntimeException("Kapasitas parkir untuk jenis kendaraan {$typeLabel} sudah penuh.");
                }
            }

            // FR-11: Generate nomor tiket unik
            $ticketNumber = $this->generateUniqueTicketNumber();

            // FR-08/FR-09: Simpan transaksi dengan waktu masuk otomatis
            return ParkingTransaction::create([
                'ticket_number'   => $ticketNumber,
                'license_plate'   => $plate,
                'vehicle_type'    => $vehicleType,
                'parking_area_id' => $area->id,
                'check_in_at'     => Carbon::now(),
                'status'          => 'active',
                'checkin_by'      => Auth::id(),
            ]);
        });
    }

    // ─── Check-Out ────────────────────────────────────────────────────────────

    /**
     * FR-12 s/d FR-14 + FR-15 s/d FR-19: Proses check-out kendaraan.
     *
     * @param  string $search  Nomor tiket ATAU nomor plat
     * @return ParkingTransaction  Transaksi yang telah diselesaikan
     *
     * @throws \RuntimeException Jika transaksi tidak ditemukan
     */
    public function checkOut(string $search): ParkingTransaction
    {
        return DB::transaction(function () use ($search) {
            // FR-12: Cari transaksi aktif berdasarkan tiket atau plat
            $transaction = $this->findActiveTransaction(strtoupper(trim($search)));

            if (! $transaction) {
                throw new \RuntimeException(
                    "Tidak ditemukan transaksi aktif untuk: {$search}"
                );
            }

            $checkOut = Carbon::now();
            $checkIn  = $transaction->check_in_at;

            // FR-13: Catat waktu keluar
            // FR-14: Hitung durasi otomatis
            $durationMinutes = $this->feeCalculator->calculateDurationMinutes($checkIn, $checkOut);

            // FR-15 s/d FR-19: Hitung tarif
            $fee = $this->feeCalculator->calculate($checkIn, $checkOut, $transaction->vehicle_type);

            // Update transaksi menjadi selesai
            $transaction->update([
                'check_out_at'     => $checkOut,
                'duration_minutes' => $durationMinutes,
                'fee'              => $fee,
                'status'           => 'completed',
                'checkout_by'      => Auth::id(),
            ]);

            return $transaction->fresh(['parkingArea']);
        });
    }

    // ─── Search ───────────────────────────────────────────────────────────────

    /**
     * FR-12: Cari transaksi aktif berdasarkan nomor tiket atau nomor plat.
     */
    public function findActiveTransaction(string $search): ?ParkingTransaction
    {
        return ParkingTransaction::with('parkingArea')
            ->where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('ticket_number', $search)
                  ->orWhere('license_plate', $search);
            })
            ->first();
    }

    // ─── Ticket Generation ────────────────────────────────────────────────────

    /**
     * FR-11: Generate nomor tiket unik.
     * Format: PKR-YYYYMMDD-XXXXX (contoh: PKR-20241114-A3F9B)
     * Menggunakan loop dengan retry untuk memastikan unik.
     */
    private function generateUniqueTicketNumber(): string
    {
        $maxAttempts = 10;
        $date        = now()->format('Ymd');

        for ($i = 0; $i < $maxAttempts; $i++) {
            $random       = strtoupper(Str::random(5));
            $ticketNumber = "PKR-{$date}-{$random}";

            $exists = ParkingTransaction::where('ticket_number', $ticketNumber)->exists();
            if (! $exists) {
                return $ticketNumber;
            }
        }

        // Fallback: gunakan UUID jika semua percobaan gagal (sangat jarang)
        return 'PKR-' . strtoupper(Str::uuid()->toString());
    }
}
