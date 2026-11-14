<?php

namespace Tests\Unit;

use App\Services\ParkingFeeCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * ParkingFeeCalculatorTest
 *
 * Unit test untuk logika perhitungan tarif parkir.
 * NFR-09: Logika tarif dipisahkan dari controller agar dapat diuji independen.
 *
 * Aturan Tarif (SRS ParkirPintar):
 * Motor : gratis 15 menit, jam-1 Rp2.000, berikutnya Rp1.000, maks harian Rp20.000
 * Mobil : gratis 15 menit, jam-1 Rp5.000, berikutnya Rp3.000, maks harian Rp50.000
 */
class ParkingFeeCalculatorTest extends TestCase
{
    private ParkingFeeCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new ParkingFeeCalculator();
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function makeCheckIn(string $baseTime = '2024-01-01 08:00:00'): Carbon
    {
        return Carbon::parse($baseTime);
    }

    private function addMinutes(Carbon $checkIn, int $minutes): Carbon
    {
        return $checkIn->copy()->addMinutes($minutes);
    }

    // ─── FR-16: Grace Period (15 menit pertama gratis) ───────────────────────

    public function test_motorcycle_free_within_grace_period(): void
    {
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 10);

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'motorcycle');

        $this->assertSame(0, $fee, 'Parkir 10 menit — masih dalam grace period — harus gratis.');
    }

    public function test_motorcycle_free_exactly_at_grace_period(): void
    {
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 15);

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'motorcycle');

        $this->assertSame(0, $fee, 'Tepat 15 menit — harus gratis.');
    }

    public function test_car_free_within_grace_period(): void
    {
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 14);

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'car');

        $this->assertSame(0, $fee, 'Mobil 14 menit — masih dalam grace period — harus gratis.');
    }

    // ─── FR-15/FR-18: Jam Pertama ─────────────────────────────────────────────

    public function test_motorcycle_first_hour_fee(): void
    {
        // 16 menit = 1 menit setelah grace period → kena tarif jam pertama
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 16);

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'motorcycle');

        $this->assertSame(2_000, $fee, 'Motor 16 menit — jam pertama — harus Rp2.000.');
    }

    public function test_motorcycle_exactly_one_hour(): void
    {
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 60);

        // Billable = 60 - 15 = 45 menit ≤ 60 menit → jam pertama saja
        $fee = $this->calculator->calculate($checkIn, $checkOut, 'motorcycle');

        $this->assertSame(2_000, $fee, 'Motor tepat 1 jam — jam pertama Rp2.000.');
    }

    public function test_car_first_hour_fee(): void
    {
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 30);

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'car');

        $this->assertSame(5_000, $fee, 'Mobil 30 menit — jam pertama — harus Rp5.000.');
    }

    // ─── FR-17: Jam Berikutnya (ceiling) ─────────────────────────────────────

    public function test_motorcycle_two_hours(): void
    {
        // Total: 75 menit = 15 (grace) + 60 (billable)
        // Billable 60 menit: jam-1 (Rp2.000) + jam-2 dibutuhkan?
        // Billable = 75 - 15 = 60 menit ≤ 60 → masih jam pertama
        // Lanjutkan: total 76 menit
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 76); // billable = 61 menit → jam-1 + 1 jam-berikutnya

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'motorcycle');

        // Rp2.000 + 1 × Rp1.000 = Rp3.000
        $this->assertSame(3_000, $fee, 'Motor 76 menit — jam-1 + 1 jam berikutnya = Rp3.000.');
    }

    public function test_motorcycle_ceiling_on_fractional_hour(): void
    {
        // Total: 15 + 60 + 1 = 76 menit → billable 61 menit → ceil(1/60) = 1 jam tambahan
        // Sudah sama dengan test di atas, tambahkan skenario pecahan lain
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 136); // billable = 121 menit → jam-1 + ceil(61/60) = jam-1 + 2 jam
        // Rp2.000 + 2 × Rp1.000 = Rp4.000
        $fee = $this->calculator->calculate($checkIn, $checkOut, 'motorcycle');

        $this->assertSame(4_000, $fee, 'Motor 136 menit — jam-1 + 2 jam berikutnya = Rp4.000.');
    }

    // ─── FR-19: Maksimal Harian ───────────────────────────────────────────────

    public function test_motorcycle_capped_at_daily_max(): void
    {
        // Parkir 23 jam → pasti melampaui max harian Rp20.000
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 23 * 60);

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'motorcycle');

        $this->assertSame(20_000, $fee, 'Motor 23 jam — harus dibatasi maks harian Rp20.000.');
    }

    public function test_car_capped_at_daily_max(): void
    {
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 23 * 60);

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'car');

        $this->assertSame(50_000, $fee, 'Mobil 23 jam — harus dibatasi maks harian Rp50.000.');
    }

    // ─── Parkir Multi-hari ────────────────────────────────────────────────────

    public function test_motorcycle_over_24_hours(): void
    {
        // 2 hari penuh → 2 × Rp20.000 = Rp40.000
        $checkIn  = $this->makeCheckIn();
        $checkOut = $this->addMinutes($checkIn, 2 * 24 * 60);

        $fee = $this->calculator->calculate($checkIn, $checkOut, 'motorcycle');

        $this->assertSame(40_000, $fee, 'Motor 48 jam — 2 × maks harian = Rp40.000.');
    }

    // ─── Utility Methods ─────────────────────────────────────────────────────

    public function test_format_duration_minutes_only(): void
    {
        $result = $this->calculator->formatDuration(45);
        $this->assertSame('45 menit', $result);
    }

    public function test_format_duration_hours_and_minutes(): void
    {
        $result = $this->calculator->formatDuration(90);
        $this->assertSame('1 jam 30 menit', $result);
    }

    public function test_format_duration_exact_hours(): void
    {
        $result = $this->calculator->formatDuration(120);
        $this->assertSame('2 jam', $result);
    }

    public function test_format_currency(): void
    {
        $result = $this->calculator->formatCurrency(20_000);
        $this->assertSame('Rp 20.000', $result);
    }
}
