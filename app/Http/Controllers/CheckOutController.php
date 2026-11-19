<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckOutRequest;
use App\Models\ParkingTransaction;
use App\Services\ParkingFeeCalculator;
use App\Services\ParkingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CheckOutController
 *
 * Modul 3: Check-Out dan Transaksi
 * FR-12: Mencari transaksi aktif berdasarkan nomor tiket atau plat
 * FR-13: Mencatat waktu keluar
 * FR-14: Menghitung durasi parkir otomatis
 *
 * Modul 4 (dipanggil via service):
 * FR-15 s/d FR-19: Perhitungan tarif
 */
class CheckOutController extends Controller
{
    public function __construct(
        private readonly ParkingService $parkingService,
        private readonly ParkingFeeCalculator $feeCalculator
    ) {}

    /**
     * Tampilkan form pencarian untuk check-out.
     */
    public function index(): View
    {
        return view('check-out.index');
    }

    /**
     * FR-12: Cari transaksi aktif berdasarkan tiket/plat (preview sebelum checkout).
     */
    public function search(Request $request): View|RedirectResponse
    {
        $search      = strtoupper(trim($request->input('search', '')));
        $transaction = null;
        $error       = null;
        $previewData = null;

        if ($search) {
            $transaction = $this->parkingService->findActiveTransaction($search);

            if (! $transaction) {
                $error = "Tidak ditemukan transaksi aktif untuk: {$search}";
            } else {
                $transaction->load('parkingArea');

                $now = Carbon::now('Asia/Jakarta');
                $checkIn = $transaction->check_in_at->copy()->setTimezone('Asia/Jakarta');
                $totalMinutes = max(0, (int) $checkIn->diffInMinutes($now));

                $fee = $this->feeCalculator->calculate($transaction->check_in_at, Carbon::now(), $transaction->vehicle_type);
                $formattedDuration = $this->feeCalculator->formatDuration($totalMinutes);

                // Perhitungan jam tertagih (setelah 15 menit grace period)
                $billableMinutes = max(0, $totalMinutes - 15);
                $billableHours = (int) ceil($billableMinutes / 60);

                // Rincian tarif per jenis kendaraan
                $isCar = $transaction->vehicle_type === 'car';
                $firstHourRate = $isCar ? 5000 : 2000;
                $nextHourRate  = $isCar ? 3000 : 1000;

                $previewData = [
                    'now'               => $now,
                    'checkIn'           => $checkIn,
                    'totalMinutes'      => $totalMinutes,
                    'formattedDuration' => $formattedDuration,
                    'billableHours'     => $billableHours,
                    'fee'               => $fee,
                    'firstHourRate'     => $firstHourRate,
                    'nextHourRate'      => $nextHourRate,
                ];
            }
        }

        return view('check-out.index', compact('transaction', 'search', 'error', 'previewData'));
    }

    /**
     * Proses check-out kendaraan.
     */
    public function process(StoreCheckOutRequest $request): RedirectResponse
    {
        try {
            $transaction = $this->parkingService->checkOut(
                search: $request->input('search')
            );

            return redirect()
                ->route('check-out.receipt', $transaction->id)
                ->with('payment_method', $request->input('payment_method', 'Tunai'))
                ->with('success', 'Check-out berhasil.');

        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Tampilkan struk/receipt setelah check-out berhasil.
     */
    public function receipt(ParkingTransaction $transaction): View
    {
        // Pastikan hanya transaksi selesai yang bisa dilihat
        abort_if($transaction->isActive(), 404);

        $transaction->load('parkingArea');

        $formattedDuration = $this->feeCalculator->formatDuration($transaction->duration_minutes ?? 0);
        $formattedFee      = $this->feeCalculator->formatCurrency($transaction->fee ?? 0);

        // Jam tertagih
        $billableMinutes = max(0, ($transaction->duration_minutes ?? 0) - 15);
        $billableHours = (int) ceil($billableMinutes / 60);

        $isCar = $transaction->vehicle_type === 'car';
        $firstHourRate = $isCar ? 5000 : 2000;
        $nextHourRate  = $isCar ? 3000 : 1000;

        return view('check-out.receipt', compact(
            'transaction',
            'formattedDuration',
            'formattedFee',
            'billableHours',
            'firstHourRate',
            'nextHourRate'
        ));
    }
}
