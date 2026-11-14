@extends('layouts.app')

@section('title', 'Laporan Pendapatan — ParkirPintar')

@section('content')
<h1 class="h3 mb-4"><i class="bi bi-currency-dollar me-2"></i>Laporan Pendapatan</h1>

{{-- Filter Tanggal --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.revenue') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ $dateFrom }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ $dateTo }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm text-bg-success">
            <div class="card-body text-center">
                <div class="fs-5 opacity-75">Total Pendapatan</div>
                <div class="display-5 fw-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="small opacity-75">{{ $dateFrom }} s/d {{ $dateTo }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm text-bg-info">
            <div class="card-body text-center">
                <div class="fs-5 opacity-75">Jumlah Transaksi Selesai</div>
                <div class="display-5 fw-bold">{{ $totalCount }}</div>
                <div class="small opacity-75">{{ $dateFrom }} s/d {{ $dateTo }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Daily Revenue --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Pendapatan per Hari</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th class="text-center">Jumlah Transaksi</th>
                    <th class="text-end">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailyRevenue as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->isoFormat('dddd, D MMMM YYYY') }}</td>
                    <td class="text-center">{{ $day->count }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($day->revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-3">Tidak ada data pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Revenue by Area --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">Pendapatan per Area & Jenis Kendaraan</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Area Parkir</th>
                    <th>Jenis</th>
                    <th class="text-center">Transaksi</th>
                    <th class="text-end">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($areaRevenue as $row)
                <tr>
                    <td>{{ $row->parkingArea->name ?? '—' }}</td>
                    <td>{{ $row->vehicle_type === 'motorcycle' ? 'Motor' : 'Mobil' }}</td>
                    <td class="text-center">{{ $row->count }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 alert alert-info small">
    <i class="bi bi-info-circle me-1"></i>
    <strong>Catatan:</strong> Laporan pendapatan hanya menghitung transaksi yang sudah selesai (check-out).
    Transaksi aktif (kendaraan masih terparkir) tidak dihitung sebagai pendapatan final.
</div>
@endsection
