@extends('layouts.app')

@section('title', 'Struk Parkir — ParkirPintar')

@section('content')
<div class="d-flex align-items-center mb-4">
    <h1 class="h3 mb-0 text-success"><i class="bi bi-receipt-cutoff me-2"></i>Struk Parkir</h1>
</div>

<div class="card border-0 shadow" style="max-width:450px">
    <div class="card-header text-center py-3 bg-dark text-white">
        <div class="fs-6 text-muted">===== STRUK PARKIR =====</div>
        <div class="fw-bold fs-5 mt-1">PARKIRPINTAR</div>
        <div class="small text-muted">Kelompok 5 — Sistem Manajemen Parkir</div>
    </div>
    <div class="card-body font-monospace">
        <table class="table table-borderless table-sm mb-0">
            <tr>
                <td class="text-muted">No. Tiket</td>
                <td class="fw-bold">{{ $transaction->ticket_number }}</td>
            </tr>
            <tr>
                <td class="text-muted">Plat</td>
                <td class="fw-bold">{{ $transaction->license_plate }}</td>
            </tr>
            <tr>
                <td class="text-muted">Kendaraan</td>
                <td>{{ $transaction->vehicle_type === 'motorcycle' ? 'Motor' : 'Mobil' }}</td>
            </tr>
            <tr>
                <td class="text-muted">Area</td>
                <td>{{ $transaction->parkingArea->name }}</td>
            </tr>
            <tr><td colspan="2"><hr class="my-1"></td></tr>
            <tr>
                <td class="text-muted">Masuk</td>
                <td>{{ $transaction->check_in_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="text-muted">Keluar</td>
                <td>{{ $transaction->check_out_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="text-muted">Durasi</td>
                <td class="fw-semibold">{{ $formattedDuration }}</td>
            </tr>
            <tr><td colspan="2"><hr class="my-1"></td></tr>
            <tr class="fs-5">
                <td class="text-muted fw-bold">TOTAL BIAYA</td>
                <td class="fw-bold text-success">{{ $formattedFee }}</td>
            </tr>
        </table>

        @if($transaction->fee == 0)
        <div class="alert alert-success text-center mt-3 mb-0 small">
            <i class="bi bi-gift-fill me-1"></i> Parkir GRATIS (dalam 15 menit pertama)
        </div>
        @endif
    </div>
    <div class="card-footer text-center text-muted small py-2">
        Terima kasih telah menggunakan ParkirPintar
    </div>
</div>

<div class="mt-4 d-flex gap-2" style="max-width:450px">
    <a href="{{ route('check-out.index') }}" class="btn btn-outline-secondary flex-fill">
        <i class="bi bi-box-arrow-left me-1"></i>Check-Out Lagi
    </a>
    <a href="{{ route('dashboard') }}" class="btn btn-primary flex-fill">
        <i class="bi bi-house me-1"></i>Dashboard
    </a>
</div>
@endsection
