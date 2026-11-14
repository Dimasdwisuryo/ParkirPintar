@extends('layouts.app')

@section('title', 'Check-Out Kendaraan — ParkirPintar')

@section('content')
<div class="d-flex align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-box-arrow-left me-2 text-secondary"></i>Check-Out Kendaraan</h1>
</div>

{{-- Form pencarian --}}
<div class="card border-0 shadow-sm mb-4" style="max-width:600px">
    <div class="card-body">
        <form method="GET" action="{{ route('check-out.search') }}">
            <label class="form-label fw-semibold">Cari berdasarkan Nomor Tiket atau Nomor Plat</label>
            <div class="input-group">
                <input type="text" name="search"
                       class="form-control form-control-lg text-uppercase"
                       placeholder="Contoh: PKR-20241114-A3F9B atau B 1234 ABC"
                       value="{{ $search ?? '' }}"
                       style="text-transform:uppercase">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Hasil pencarian --}}
@if(isset($error) && $error)
    <div class="alert alert-warning d-flex align-items-center" style="max-width:600px">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}
    </div>
@endif

@if(isset($transaction) && $transaction)
<div class="card border-primary border-2 shadow-sm" style="max-width:500px">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="bi bi-receipt me-2"></i>Transaksi Ditemukan
    </div>
    <div class="card-body">
        <table class="table table-borderless mb-0">
            <tr>
                <td class="text-muted" style="width:40%">No. Tiket</td>
                <td class="fw-bold">{{ $transaction->ticket_number }}</td>
            </tr>
            <tr>
                <td class="text-muted">Nomor Plat</td>
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
                <td>{{ $transaction->check_in_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="text-muted">Durasi (saat ini)</td>
                <td class="fw-semibold text-warning">
                    {{ $transaction->check_in_at->diffForHumans(null, true) }}
                </td>
            </tr>
        </table>
    </div>
    <div class="card-footer bg-white">
        <form method="POST" action="{{ route('check-out.process') }}"
              onsubmit="return confirm('Konfirmasi check-out kendaraan {{ $transaction->license_plate }}?')">
            @csrf
            <input type="hidden" name="search" value="{{ $transaction->ticket_number }}">
            <button type="submit" class="btn btn-danger w-100 btn-lg">
                <i class="bi bi-box-arrow-left me-2"></i>Proses Check-Out & Hitung Tarif
            </button>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.querySelector('input[name="search"]').addEventListener('input', function(){
    this.value = this.value.toUpperCase();
});
</script>
@endpush
