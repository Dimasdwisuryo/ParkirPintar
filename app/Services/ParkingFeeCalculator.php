<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * ParkingFeeCalculator
 *
 * Bertanggung jawab HANYA menghitung tarif parkir.
 * Dipisahkan dari controller agar dapat diuji secara independen.
 *
 * NFR-09: Pisahkan logika perhitungan tarif dari controller.
 *
 * Aturan Tarif:
 * ─────────────────────────────────────────────────────────
 * MOTOR:
 *   - 15 menit pertama: GRATIS
 *   - Jam pertama (setelah grace period): Rp 2.000
 *   - Setiap jam berikutnya: Rp 1.000
 *   - Maksimal per hari (24 jam): Rp 20.000
 *
 * MOBIL:
 *   - 15 menit pertama: GRATIS
 *   - Jam pertama (setelah grace period): Rp 5.000
 *   - Setiap jam berikutnya: Rp 3.000
 *   - Maksimal per hari (24 jam): Rp 50.000
 *
 * Ketentuan:
 *   - Pecahan jam dibulatkan ke atas (ceiling).
 *   - Tarif gratis hanya berlaku sekali di awal kunjungan.
 *   - Parkir > 24 jam: kelipatan blok 24 jam, masing-masing
 *     dibatasi oleh tarif maksimal hariannya.
 */
class ParkingFeeCalculator
{
    // ─── Konstanta Tarif ─────────────────────────────────────────────────────

    private const GRACE_PERIOD_MINUTES = 15;

    private const MOTORCYCLE_FIRST_HOUR_FEE = 2_000;
    private const MOTORCYCLE_NEXT_HOUR_FEE  = 1_000;
    private const MOTORCYCLE_DAILY_MAX_FEE  = 20_000;

    private const CAR_FIRST_HOUR_FEE = 5_000;
    private const CAR_NEXT_HOUR_FEE  = 3_000;
    private const CAR_DAILY_MAX_FEE  = 50_000;

    private const MINUTES_PER_HOUR = 60;
    private const MINUTES_PER_DAY  = 1_440; // 24 * 60

    // ─── Public API ───────────────────────────────────────────────────────────

    /**
     * FR-15/FR-16/FR-17/FR-18/FR-19: Hitung biaya parkir.
     *
     * @param  Carbon $checkIn  Waktu masuk
     * @param  Carbon $checkOut Waktu keluar
     * @param  string $vehicleType  'motorcycle' | 'car'
     * @return int  Biaya dalam rupiah (bilangan bulat)
     */
    public function calculate(Carbon $checkIn, Carbon $checkOut, string $vehicleType): int
    {
        $totalMinutes = (int) $checkIn->diffInMinutes($checkOut);

        // FR-16: 15 menit pertama gratis
        if ($totalMinutes <= self::GRACE_PERIOD_MINUTES) {
            return 0;
        }

        // Menit yang dikenai tarif (setelah grace period)
        $billableMinutes = $totalMinutes - self::GRACE_PERIOD_MINUTES;

        return $this->calculateBillableMinutes($billableMinutes, $vehicleType);
    }

    /**
     * Menghitung berdasarkan menit yang dapat ditagih (setelah grace period).
     * Mendukung parkir lebih dari 24 jam (multi-hari).
     */
    private function calculateBillableMinutes(int $billableMinutes, string $vehicleType): int
    {
        $dailyMax    = $this->getDailyMax($vehicleType);
        $totalFee    = 0;
        $remaining   = $billableMinutes;

        // Hitung per blok 24 jam
        while ($remaining > 0) {
            $blockMinutes = min($remaining, self::MINUTES_PER_DAY);
            $blockFee     = $this->calculateBlock($blockMinutes, $vehicleType);

            // Batasi per blok 24 jam dengan maksimal harian
            $totalFee += min($blockFee, $dailyMax);
            $remaining -= $blockMinutes;
        }

        return $totalFee;
    }

    /**
     * Hitung tarif untuk satu blok (maksimal 24 jam billable minutes).
     * Jam pertama = first_hour_fee, jam berikutnya = next_hour_fee.
     * Pecahan jam dibulatkan ke atas (ceiling).
     */
    private function calculateBlock(int $blockMinutes, string $vehicleType): int
    {
        $firstHourFee = $this->getFirstHourFee($vehicleType);
        $nextHourFee  = $this->getNextHourFee($vehicleType);

        if ($blockMinutes <= self::MINUTES_PER_HOUR) {
            // Masih dalam jam pertama (sudah melewati grace period)
            return $firstHourFee;
        }

        // Jam pertama + jam-jam berikutnya
        $remainingAfterFirst   = $blockMinutes - self::MINUTES_PER_HOUR;
        $additionalHours       = (int) ceil($remainingAfterFirst / self::MINUTES_PER_HOUR);

        return $firstHourFee + ($additionalHours * $nextHourFee);
    }

    // ─── Helpers per vehicle type ─────────────────────────────────────────────

    private function getFirstHourFee(string $vehicleType): int
    {
        return $vehicleType === 'car'
            ? self::CAR_FIRST_HOUR_FEE
            : self::MOTORCYCLE_FIRST_HOUR_FEE;
    }

    private function getNextHourFee(string $vehicleType): int
    {
        return $vehicleType === 'car'
            ? self::CAR_NEXT_HOUR_FEE
            : self::MOTORCYCLE_NEXT_HOUR_FEE;
    }

    private function getDailyMax(string $vehicleType): int
    {
        return $vehicleType === 'car'
            ? self::CAR_DAILY_MAX_FEE
            : self::MOTORCYCLE_DAILY_MAX_FEE;
    }

    // ─── Utility untuk tampilan ───────────────────────────────────────────────

    /**
     * Hitung durasi parkir dalam menit dari check-in ke check-out.
     */
    public function calculateDurationMinutes(Carbon $checkIn, Carbon $checkOut): int
    {
        return (int) $checkIn->diffInMinutes($checkOut);
    }

    /**
     * Format durasi menit menjadi string yang mudah dibaca.
     * Contoh: "2 jam 30 menit"
     */
    public function formatDuration(int $minutes): string
    {
        if ($minutes < self::MINUTES_PER_HOUR) {
            return "{$minutes} menit";
        }

        $hours   = intdiv($minutes, self::MINUTES_PER_HOUR);
        $mins    = $minutes % self::MINUTES_PER_HOUR;

        return $mins > 0
            ? "{$hours} jam {$mins} menit"
            : "{$hours} jam";
    }

    /**
     * Format rupiah.
     */
    public function formatCurrency(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
