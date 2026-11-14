@extends('layouts.app')

@section('title', 'Area Parkir — ParkirPintar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-geo-alt me-2"></i>Manajemen Area Parkir</h1>
    <a href="{{ route('parking-areas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Tambah Area
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nama Area</th>
                    <th>Lokasi</th>
                    <th class="text-center">Motor (Tersedia/Total)</th>
                    <th class="text-center">Mobil (Tersedia/Total)</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($areas as $area)
                @php
                    $availMotorcycle = $area->capacity_motorcycle - ($area->occupied_motorcycle ?? 0);
                    $availCar        = $area->capacity_car        - ($area->occupied_car ?? 0);
                    $motorFull       = $availMotorcycle <= 0 && $area->capacity_motorcycle > 0;
                    $carFull         = $availCar <= 0 && $area->capacity_car > 0;
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $area->name }}</td>
                    <td class="text-muted small">{{ $area->location ?? '—' }}</td>
                    <td class="text-center">
                        <span class="badge slot-badge {{ $motorFull ? 'bg-danger' : 'bg-success' }}">
                            {{ $availMotorcycle }}/{{ $area->capacity_motorcycle }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge slot-badge {{ $carFull ? 'bg-danger' : 'bg-success' }}">
                            {{ $availCar }}/{{ $area->capacity_car }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($area->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('parking-areas.edit', $area) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST"
                              action="{{ route('parking-areas.destroy', $area) }}"
                              class="d-inline"
                              onsubmit="return confirm('Hapus area parkir ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Belum ada area parkir. <a href="{{ route('parking-areas.create') }}">Tambahkan sekarang</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
