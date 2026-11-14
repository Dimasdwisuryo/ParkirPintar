@extends('layouts.app')

@section('title', 'Riwayat Transaksi — ParkirPintar')

@section('content')
<h1 class="h3 mb-4"><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi</h1>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.history') }}" class="row g-3">
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Area Parkir</label>
                <select name="area_id" class="form-select form-select-sm">
                    <option value="">Semua Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Jenis Kendaraan</label>
                <select name="vehicle_type" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="motorcycle" {{ request('vehicle_type') === 'motorcycle' ? 'selected' : '' }}>Motor</option>
                    <option value="car" {{ request('vehicle_type') === 'car' ? 'selected' : '' }}>Mobil</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('reports.history') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle small">
            <thead class="table-dark">
                <tr>
                    <th>No. Tiket</th>
                    <th>Plat</th>
                    <th>Jenis</th>
                    <th>Area</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Durasi</th>
                    <th class="text-end">Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td class="font-monospace">{{ $t->ticket_number }}</td>
                    <td class="fw-bold">{{ $t->license_plate }}</td>
                    <td>
                        {{ $t->vehicle_type === 'motorcycle' ? 'Motor' : 'Mobil' }}
                    </td>
                    <td>{{ $t->parkingArea->name }}</td>
                    <td>{{ $t->check_in_at->format('d/m H:i') }}</td>
                    <td>{{ $t->check_out_at?->format('d/m H:i') ?? '-' }}</td>
                    <td>
                        @if($t->duration_minutes !== null)
                            {{ intdiv($t->duration_minutes, 60) }}j {{ $t->duration_minutes % 60 }}m
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-end fw-semibold">
                        {{ $t->fee !== null ? 'Rp '.number_format($t->fee, 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Tidak ada riwayat transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer bg-white">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
