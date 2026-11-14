@extends('layouts.app')

@section('title', 'Laporan Okupansi — ParkirPintar')

@section('content')
<h1 class="h3 mb-4"><i class="bi bi-pie-chart me-2"></i>Laporan Okupansi Area Parkir</h1>

<div class="row g-4">
    @forelse($areas as $area)
    @php
        $availMoto = $area->capacity_motorcycle - ($area->occupied_motorcycle ?? 0);
        $availCar  = $area->capacity_car - ($area->occupied_car ?? 0);
        $pctMoto   = $area->capacity_motorcycle > 0 ? round(($area->occupied_motorcycle / $area->capacity_motorcycle) * 100) : 0;
        $pctCar    = $area->capacity_car > 0 ? round(($area->occupied_car / $area->capacity_car) * 100) : 0;
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                {{ $area->name }}
                @if($area->is_active)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-secondary">Nonaktif</span>
                @endif
            </div>
            <div class="card-body">
                @if($area->location)
                <div class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i>{{ $area->location }}</div>
                @endif

                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span><i class="bi bi-bicycle me-1 text-primary"></i>Motor</span>
                        <span class="fw-semibold {{ $availMoto == 0 ? 'text-danger' : 'text-success' }}">
                            {{ $area->occupied_motorcycle ?? 0 }}/{{ $area->capacity_motorcycle }}
                            ({{ $availMoto }} tersedia)
                        </span>
                    </div>
                    <div class="progress" style="height:10px">
                        <div class="progress-bar {{ $pctMoto >= 90 ? 'bg-danger' : ($pctMoto >= 70 ? 'bg-warning' : 'bg-primary') }}"
                             style="width:{{ $pctMoto }}%">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span><i class="bi bi-car-front me-1 text-success"></i>Mobil</span>
                        <span class="fw-semibold {{ $availCar == 0 ? 'text-danger' : 'text-success' }}">
                            {{ $area->occupied_car ?? 0 }}/{{ $area->capacity_car }}
                            ({{ $availCar }} tersedia)
                        </span>
                    </div>
                    <div class="progress" style="height:10px">
                        <div class="progress-bar bg-success {{ $pctCar >= 90 ? 'bg-danger' : ($pctCar >= 70 ? 'bg-warning' : 'bg-success') }}"
                             style="width:{{ $pctCar }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center text-muted py-5">
            Belum ada area parkir. <a href="{{ route('parking-areas.create') }}">Tambahkan area</a>.
        </div>
    </div>
    @endforelse
</div>
@endsection
