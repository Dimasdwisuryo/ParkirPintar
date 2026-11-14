<?php

namespace App\Http\Controllers;

use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ReportController
 *
 * Modul 5: Laporan Parkir
 * FR-20: Menampilkan okupansi berdasarkan area dan jenis kendaraan
 * FR-21: Menampilkan kendaraan yang sedang parkir
 * FR-22: Menampilkan riwayat transaksi
 * FR-23: Menghitung total pendapatan dari transaksi selesai
 * FR-24: Menampilkan laporan pendapatan berdasarkan tanggal
 */
class ReportController extends Controller
{
    /**
     * FR-20: Laporan okupansi area parkir (dashboard utama laporan).
     */
    public function occupancy(): View
    {
        $areas = ParkingArea::withCount([
            'activeTransactions as occupied_motorcycle' => fn ($q) => $q->where('vehicle_type', 'motorcycle'),
            'activeTransactions as occupied_car'        => fn ($q) => $q->where('vehicle_type', 'car'),
        ])->orderBy('name')->get();

        return view('reports.occupancy', compact('areas'));
    }

    /**
     * FR-21: Daftar kendaraan yang saat ini terparkir.
     */
    public function active(): View
    {
        $transactions = ParkingTransaction::with('parkingArea')
            ->where('status', 'active')
            ->orderBy('check_in_at', 'desc')
            ->paginate(25);

        return view('reports.active', compact('transactions'));
    }

    /**
     * FR-22: Riwayat seluruh transaksi (selesai).
     */
    public function history(Request $request): View
    {
        $query = ParkingTransaction::with('parkingArea')
            ->where('status', 'completed');

        // Filter berdasarkan tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('check_out_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('check_out_at', '<=', $request->input('date_to'));
        }

        // Filter berdasarkan area
        if ($request->filled('area_id')) {
            $query->where('parking_area_id', $request->input('area_id'));
        }

        // Filter berdasarkan jenis kendaraan
        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->input('vehicle_type'));
        }

        $transactions = $query->orderBy('check_out_at', 'desc')->paginate(25)->withQueryString();
        $areas        = ParkingArea::orderBy('name')->get();

        return view('reports.history', compact('transactions', 'areas'));
    }

    /**
     * FR-23/FR-24: Laporan pendapatan berdasarkan tanggal.
     * Hanya menghitung transaksi yang sudah selesai (completed).
     */
    public function revenue(Request $request): View
    {
        // Default: tampilkan data bulan ini
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to', now()->toDateString());

        $query = ParkingTransaction::where('status', 'completed')
            ->whereNotNull('fee')
            ->whereDate('check_out_at', '>=', $dateFrom)
            ->whereDate('check_out_at', '<=', $dateTo);

        // FR-23: Total pendapatan dari transaksi selesai
        $totalRevenue = $query->sum('fee');
        $totalCount   = $query->count();

        // FR-24: Pendapatan per hari
        $dailyRevenue = ParkingTransaction::where('status', 'completed')
            ->whereNotNull('fee')
            ->whereDate('check_out_at', '>=', $dateFrom)
            ->whereDate('check_out_at', '<=', $dateTo)
            ->selectRaw('DATE(check_out_at) as date, SUM(fee) as revenue, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Pendapatan per area
        $areaRevenue = ParkingTransaction::with('parkingArea')
            ->where('status', 'completed')
            ->whereNotNull('fee')
            ->whereDate('check_out_at', '>=', $dateFrom)
            ->whereDate('check_out_at', '<=', $dateTo)
            ->selectRaw('parking_area_id, vehicle_type, SUM(fee) as revenue, COUNT(*) as count')
            ->groupBy('parking_area_id', 'vehicle_type')
            ->get();

        return view('reports.revenue', compact(
            'totalRevenue',
            'totalCount',
            'dailyRevenue',
            'areaRevenue',
            'dateFrom',
            'dateTo',
        ));
    }
}
