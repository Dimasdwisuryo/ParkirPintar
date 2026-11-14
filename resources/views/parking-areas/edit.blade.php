@extends('layouts.app')

@section('title', 'Edit Area Parkir — ParkirPintar')

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('parking-areas.index') }}" class="btn btn-sm btn-outline-secondary me-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Edit Area Parkir: {{ $parkingArea->name }}</h1>
</div>

@php
    $occupiedMotorcycle = $parkingArea->getOccupiedMotorcycleCount();
    $occupiedCar        = $parkingArea->getOccupiedCarCount();
@endphp

@if($occupiedMotorcycle > 0 || $occupiedCar > 0)
<div class="alert alert-info d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-info-circle-fill me-2"></i>
    <div>
        Saat ini terparkir: <strong>{{ $occupiedMotorcycle }} motor</strong> dan
        <strong>{{ $occupiedCar }} mobil</strong>.
        Kapasitas tidak dapat dikurangi di bawah jumlah kendaraan yang sedang terparkir.
    </div>
</div>
@endif

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('parking-areas.update', $parkingArea) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold" for="name">Nama Area <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $parkingArea->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold" for="location">Lokasi / Keterangan</label>
                <input type="text" id="location" name="location"
                       class="form-control"
                       value="{{ old('location', $parkingArea->location) }}">
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-semibold" for="capacity_motorcycle">
                        <i class="bi bi-bicycle me-1"></i>Kapasitas Motor
                    </label>
                    <input type="number" id="capacity_motorcycle" name="capacity_motorcycle"
                           class="form-control @error('capacity_motorcycle') is-invalid @enderror"
                           value="{{ old('capacity_motorcycle', $parkingArea->capacity_motorcycle) }}"
                           min="{{ $occupiedMotorcycle }}" required>
                    @error('capacity_motorcycle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($occupiedMotorcycle > 0)
                        <div class="form-text">Minimum: {{ $occupiedMotorcycle }} (terparkir)</div>
                    @endif
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold" for="capacity_car">
                        <i class="bi bi-car-front me-1"></i>Kapasitas Mobil
                    </label>
                    <input type="number" id="capacity_car" name="capacity_car"
                           class="form-control @error('capacity_car') is-invalid @enderror"
                           value="{{ old('capacity_car', $parkingArea->capacity_car) }}"
                           min="{{ $occupiedCar }}" required>
                    @error('capacity_car')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($occupiedCar > 0)
                        <div class="form-text">Minimum: {{ $occupiedCar }} (terparkir)</div>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                </button>
                <a href="{{ route('parking-areas.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
