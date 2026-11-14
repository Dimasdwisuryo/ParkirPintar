<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParkingAreaRequest;
use App\Http\Requests\UpdateParkingAreaRequest;
use App\Models\ParkingArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ParkingAreaController
 *
 * Modul 1: Manajemen Area Parkir
 * FR-01: Menambahkan area parkir
 * FR-02: Menampilkan daftar area parkir
 * FR-03: Mengubah informasi area
 * FR-04: Menghapus area (hanya jika tidak ada transaksi)
 * FR-05: Mengatur kapasitas motor dan mobil
 * FR-06: Menghitung slot terisi dan tersedia
 */
class ParkingAreaController extends Controller
{
    /**
     * FR-02: Menampilkan daftar seluruh area parkir beserta slot tersedia.
     */
    public function index(): View
    {
        $areas = ParkingArea::withCount([
            'activeTransactions as occupied_motorcycle' => fn ($q) => $q->where('vehicle_type', 'motorcycle'),
            'activeTransactions as occupied_car'        => fn ($q) => $q->where('vehicle_type', 'car'),
        ])->orderBy('name')->get();

        return view('parking-areas.index', compact('areas'));
    }

    /**
     * FR-01: Form tambah area parkir.
     */
    public function create(): View
    {
        return view('parking-areas.create');
    }

    /**
     * FR-01/FR-05: Simpan area parkir baru.
     */
    public function store(StoreParkingAreaRequest $request): RedirectResponse
    {
        ParkingArea::create($request->validated());

        return redirect()
            ->route('parking-areas.index')
            ->with('success', 'Area parkir berhasil ditambahkan.');
    }

    /**
     * FR-03: Form edit area parkir.
     */
    public function edit(ParkingArea $parkingArea): View
    {
        return view('parking-areas.edit', compact('parkingArea'));
    }

    /**
     * FR-03/FR-05: Simpan perubahan area parkir.
     * Validasi: kapasitas tidak boleh dikurangi di bawah jumlah kendaraan terparkir.
     */
    public function update(UpdateParkingAreaRequest $request, ParkingArea $parkingArea): RedirectResponse
    {
        $validated = $request->validated();

        // FR-05: Kapasitas tidak boleh dikurangi di bawah jumlah kendaraan yang masih terparkir
        $occupiedMotorcycle = $parkingArea->getOccupiedMotorcycleCount();
        $occupiedCar        = $parkingArea->getOccupiedCarCount();

        if ($validated['capacity_motorcycle'] < $occupiedMotorcycle) {
            return back()->withErrors([
                'capacity_motorcycle' => "Kapasitas motor tidak dapat dikurangi. Saat ini ada {$occupiedMotorcycle} motor yang sedang terparkir.",
            ])->withInput();
        }

        if ($validated['capacity_car'] < $occupiedCar) {
            return back()->withErrors([
                'capacity_car' => "Kapasitas mobil tidak dapat dikurangi. Saat ini ada {$occupiedCar} mobil yang sedang terparkir.",
            ])->withInput();
        }

        $parkingArea->update($validated);

        return redirect()
            ->route('parking-areas.index')
            ->with('success', 'Area parkir berhasil diperbarui.');
    }

    /**
     * FR-04: Hapus area parkir.
     * Area dengan riwayat transaksi dinonaktifkan, bukan dihapus fisik.
     */
    public function destroy(ParkingArea $parkingArea): RedirectResponse
    {
        // Tidak boleh menghapus jika ada transaksi aktif
        if ($parkingArea->hasActiveTransactions()) {
            return back()->with('error', 'Area parkir tidak dapat dihapus karena masih ada kendaraan yang terparkir.');
        }

        // Jika ada riwayat transaksi, nonaktifkan saja (soft delete logic)
        if ($parkingArea->hasTransactionHistory()) {
            $parkingArea->update(['is_active' => false]);

            return redirect()
                ->route('parking-areas.index')
                ->with('warning', 'Area parkir tidak dihapus karena memiliki riwayat transaksi. Area dinonaktifkan.');
        }

        // Hapus fisik hanya jika benar-benar tidak ada riwayat apapun
        $parkingArea->delete();

        return redirect()
            ->route('parking-areas.index')
            ->with('success', 'Area parkir berhasil dihapus.');
    }
}
