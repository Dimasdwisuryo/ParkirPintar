@extends('layouts.app')

@section('title', 'Tiket Parkir — ParkirPintar')

@section('content')
<div class="d-flex align-items-center mb-4">
    <h1 class="h3 mb-0 text-success"><i class="bi bi-check-circle-fill me-2"></i>Check-In Berhasil!</h1>
</div>

<div class="card border-success border-2 shadow-sm" style="max-width:500px">
    <div class="card-header bg-success text-white text-center py-3">
        <div class="fs-5 fw-bold">TIKET PARKIR</div>
        <div class="fs-3 fw-bolder letter-spacing-wide">{{ $transaction->ticket_number }}</div>
    </div>
    <div class="card-body">
        <table class="table table-borderless mb-0">
            <tr>
                <td class="text-muted" style="width:40%">Nomor Plat</td>
                <td class="fw-bold">{{ $transaction->license_plate }}</td>
            </tr>
            <tr>
                <td class="text-muted">Jenis Kendaraan</td>
                <td>
                    @if($transaction->vehicle_type === 'motorcycle')
                        <i class="bi bi-bicycle me-1 text-primary"></i>Motor
                    @else
                        <i class="bi bi-car-front me-1 text-success"></i>Mobil
                    @endif
                </td>
            </tr>
            <tr>
                <td class="text-muted">Area Parkir</td>
                <td>{{ $transaction->parkingArea->name }}</td>
            </tr>
            <tr>
                <td class="text-muted">Waktu Masuk</td>
                <td class="fw-semibold">{{ $transaction->check_in_at->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
        <div class="alert alert-warning mt-3 small mb-0">
            <i class="bi bi-info-circle me-1"></i>
            Simpan nomor tiket ini. Diperlukan saat check-out.
            <strong>15 menit pertama gratis.</strong>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('check-in.create') }}" class="btn btn-primary flex-fill">
            <i class="bi bi-plus me-1"></i>Check-In Baru
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary flex-fill">
            <i class="bi bi-house me-1"></i>Dashboard
        </a>
    </div>
</div>
@endsection
