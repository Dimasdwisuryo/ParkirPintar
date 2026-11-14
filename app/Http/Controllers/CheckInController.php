<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckInRequest;
use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use App\Services\ParkingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CheckInController
 *
 * Modul 2: Check-In Kendaraan
 * FR-07: Input nomor plat
 * FR-08: Memilih jenis kendaraan
 * FR-09: Memilih area parkir
 * FR-10: Mencatat waktu masuk otomatis (dilakukan di service)
 * FR-11: Menghasilkan nomor tiket unik (dilakukan di service)
 * FR-10: Menolak check-in ketika kapasitas penuh
 * NFR-03: Mencegah satu plat memiliki dua transaksi aktif
 */
class CheckInController extends Controller
{
    public function __construct(
        private readonly ParkingService $parkingService
    ) {}

    /**
     * Tampilkan form check-in.
     */
    public function create(): View
    {
        $areas = ParkingArea::active()->orderBy('name')->get();

        return view('check-in.create', compact('areas'));
    }

    /**
     * Proses check-in kendaraan.
     */
    public function store(StoreCheckInRequest $request): RedirectResponse
    {
        try {
            $transaction = $this->parkingService->checkIn(
                licensePlate: $request->input('license_plate'),
                vehicleType:  $request->input('vehicle_type'),
                areaId:       (int) $request->input('parking_area_id'),
            );

            return redirect()
                ->route('check-in.success', $transaction->id)
                ->with('success', "Check-in berhasil. Nomor tiket: {$transaction->ticket_number}");

        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman konfirmasi tiket setelah check-in berhasil.
     */
    public function success(ParkingTransaction $transaction): View
    {
        $transaction->load('parkingArea');

        return view('check-in.success', compact('transaction'));
    }
}
