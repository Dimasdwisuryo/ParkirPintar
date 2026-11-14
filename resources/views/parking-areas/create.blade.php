@extends('layouts.app')

@section('title', 'Tambah Area Parkir — ParkirPintar')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('parking-areas.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Tambah Area Parkir</h1>
</div>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('parking-areas.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold" for="name">Nama Area <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Contoh: Area A — Lantai 1" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="location">Lokasi / Keterangan</label>
                <input type="text" id="location" name="location"
                       class="form-control @error('location') is-invalid @enderror"
                       value="{{ old('location') }}" placeholder="Opsional">
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-semibold" for="capacity_motorcycle">
                        <i class="bi bi-bicycle me-1"></i>Kapasitas Motor <span class="text-danger">*</span>
                    </label>
                    <input type="number" id="capacity_motorcycle" name="capacity_motorcycle"
                           class="form-control @error('capacity_motorcycle') is-invalid @enderror"
                           value="{{ old('capacity_motorcycle', 0) }}" min="0" required>
                    @error('capacity_motorcycle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold" for="capacity_car">
                        <i class="bi bi-car-front me-1"></i>Kapasitas Mobil <span class="text-danger">*</span>
                    </label>
                    <input type="number" id="capacity_car" name="capacity_car"
                           class="form-control @error('capacity_car') is-invalid @enderror"
                           value="{{ old('capacity_car', 0) }}" min="0" required>
                    @error('capacity_car')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Area
                </button>
                <a href="{{ route('parking-areas.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
