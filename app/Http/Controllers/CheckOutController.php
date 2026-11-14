<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckOutRequest;
use App\Models\ParkingTransaction;
use App\Services\ParkingFeeCalculator;
use App\Services\ParkingService;
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

        if ($search) {
            $transaction = $this->parkingService->findActiveTransaction($search);

            if (! $transaction) {
                $error = "Tidak ditemukan transaksi aktif untuk: {$search}";
            }
        }

        return view('check-out.index', compact('transaction', 'search', 'error'));
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

        return view('check-out.receipt', compact('transaction', 'formattedDuration', 'formattedFee'));
    }
}
