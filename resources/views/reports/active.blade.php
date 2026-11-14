@extends('layouts.app')

@section('title', 'Kendaraan Parkir — ParkirPintar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-car-front me-2"></i>Kendaraan Sedang Parkir</h1>
    <span class="badge bg-primary fs-6">{{ $transactions->total() }} kendaraan</span>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No. Tiket</th>
                    <th>Plat</th>
                    <th>Jenis</th>
                    <th>Area</th>
                    <th>Waktu Masuk</th>
                    <th>Durasi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td class="font-monospace fw-bold small">{{ $t->ticket_number }}</td>
                    <td class="fw-bold">{{ $t->license_plate }}</td>
                    <td>
                        @if($t->vehicle_type === 'motorcycle')
                            <span class="badge bg-primary"><i class="bi bi-bicycle me-1"></i>Motor</span>
                        @else
                            <span class="badge bg-success"><i class="bi bi-car-front me-1"></i>Mobil</span>
                        @endif
                    </td>
                    <td>{{ $t->parkingArea->name }}</td>
                    <td class="small">{{ $t->check_in_at->format('d/m/Y H:i') }}</td>
                    <td class="text-warning fw-semibold small">{{ $t->check_in_at->diffForHumans(null, true) }}</td>
                    <td class="text-center">
                        <a href="{{ route('check-out.search') }}?search={{ $t->ticket_number }}"
                           class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-box-arrow-left me-1"></i>Check-Out
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>
                        Tidak ada kendaraan yang sedang parkir.
                    </td>
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
