@extends('layouts.app')

@section('title', 'Dashboard — ParkirPintar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
    <span class="text-muted small">{{ now()->isoFormat('dddd, D MMMM YYYY HH:mm') }}</span>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold">{{ \App\Models\ParkingTransaction::where('status','active')->count() }}</div>
                        <div class="small">Kendaraan Parkir</div>
                    </div>
                    <i class="bi bi-car-front fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('reports.active') }}" class="text-white small">Lihat detail →</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold">{{ \App\Models\ParkingArea::where('is_active', true)->count() }}</div>
                        <div class="small">Area Aktif</div>
                    </div>
                    <i class="bi bi-geo-alt fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('parking-areas.index') }}" class="text-white small">Kelola area →</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold">Rp {{ number_format(\App\Models\ParkingTransaction::where('status','completed')->whereDate('check_out_at', today())->sum('fee'), 0, ',', '.') }}</div>
                        <div class="small">Pendapatan Hari Ini</div>
                    </div>
                    <i class="bi bi-cash-coin fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('reports.revenue') }}" class="text-white small">Lihat laporan →</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fs-2 fw-bold">{{ \App\Models\ParkingTransaction::whereDate('check_in_at', today())->count() }}</div>
                        <div class="small">Transaksi Hari Ini</div>
                    </div>
                    <i class="bi bi-receipt fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('reports.history') }}" class="text-white small">Lihat riwayat →</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Aksi Cepat
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('check-in.create') }}" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Check-In Kendaraan
                </a>
                <a href="{{ route('check-out.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-left me-2"></i>Check-Out Kendaraan
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-pie-chart me-2 text-success"></i>Okupansi Area Parkir
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Area</th>
                            <th class="text-center">Motor</th>
                            <th class="text-center">Mobil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\ParkingArea::active()->get() as $area)
                        <tr>
                            <td>{{ $area->name }}</td>
                            <td class="text-center">
                                {{ $area->getOccupiedMotorcycleCount() }}/{{ $area->capacity_motorcycle }}
                            </td>
                            <td class="text-center">
                                {{ $area->getOccupiedCarCount() }}/{{ $area->capacity_car }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
