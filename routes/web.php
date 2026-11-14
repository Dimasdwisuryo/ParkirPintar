<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ParkingAreaController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — ParkirPintar
|--------------------------------------------------------------------------
|
| FR-01 s/d FR-27: Seluruh route aplikasi ParkirPintar.
|
*/

// ─── Dashboard ───────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// ─── Modul 1: Manajemen Area Parkir (FR-01 s/d FR-06) ────────────────────────
Route::resource('parking-areas', ParkingAreaController::class)
    ->except(['show']);

// ─── Modul 2: Check-In Kendaraan (FR-07 s/d FR-11) ───────────────────────────
Route::prefix('check-in')->name('check-in.')->group(function () {
    Route::get('/', [CheckInController::class, 'create'])->name('create');
    Route::post('/', [CheckInController::class, 'store'])->name('store');
    Route::get('/success/{transaction}', [CheckInController::class, 'success'])->name('success');
});

// ─── Modul 3 & 4: Check-Out dan Perhitungan Tarif (FR-12 s/d FR-19) ──────────
Route::prefix('check-out')->name('check-out.')->group(function () {
    Route::get('/', [CheckOutController::class, 'index'])->name('index');
    Route::get('/search', [CheckOutController::class, 'search'])->name('search');
    Route::post('/process', [CheckOutController::class, 'process'])->name('process');
    Route::get('/receipt/{transaction}', [CheckOutController::class, 'receipt'])->name('receipt');
});

// ─── Modul 5: Laporan Parkir (FR-20 s/d FR-24) ───────────────────────────────
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');   // FR-20
    Route::get('/active', [ReportController::class, 'active'])->name('active');             // FR-21
    Route::get('/history', [ReportController::class, 'history'])->name('history');          // FR-22
    Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');          // FR-23 & FR-24
});

// ─── Modul 6: Healthcheck (FR-25 s/d FR-27) ──────────────────────────────────
// Endpoint untuk Jenkins Smoke Test (Andrew - DevOps)
Route::get('/health', HealthController::class)->name('health');
