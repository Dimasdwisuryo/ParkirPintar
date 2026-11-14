@extends('layouts.app')

@section('title', 'Check-In Kendaraan — ParkirPintar')

@section('content')
<div class="d-flex align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Check-In Kendaraan</h1>
</div>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('check-in.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold" for="license_plate">
                    Nomor Plat Kendaraan <span class="text-danger">*</span>
                </label>
                <input type="text" id="license_plate" name="license_plate"
                       class="form-control form-control-lg text-uppercase @error('license_plate') is-invalid @enderror"
                       value="{{ old('license_plate') }}"
                       placeholder="Contoh: B 1234 ABC"
                       style="text-transform:uppercase; letter-spacing:2px; font-weight:600"
                       maxlength="15" required>
                @error('license_plate')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Kendaraan <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check form-check-inline border rounded p-3 flex-fill text-center"
                         style="cursor:pointer"
                         onclick="document.getElementById('type_motorcycle').click()">
                        <input class="form-check-input d-none" type="radio"
                               id="type_motorcycle" name="vehicle_type" value="motorcycle"
                               {{ old('vehicle_type') === 'motorcycle' ? 'checked' : '' }} required>
                        <i class="bi bi-bicycle fs-3 d-block mb-1 text-primary"></i>
                        <span class="form-check-label fw-semibold">Motor</span>
                    </div>
                    <div class="form-check form-check-inline border rounded p-3 flex-fill text-center"
                         style="cursor:pointer"
                         onclick="document.getElementById('type_car').click()">
                        <input class="form-check-input d-none" type="radio"
                               id="type_car" name="vehicle_type" value="car"
                               {{ old('vehicle_type') === 'car' ? 'checked' : '' }}>
                        <i class="bi bi-car-front fs-3 d-block mb-1 text-success"></i>
                        <span class="form-check-label fw-semibold">Mobil</span>
                    </div>
                </div>
                @error('vehicle_type')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" for="parking_area_id">
                    Area Parkir <span class="text-danger">*</span>
                </label>
                <select id="parking_area_id" name="parking_area_id"
                        class="form-select @error('parking_area_id') is-invalid @enderror" required>
                    <option value="">— Pilih area parkir —</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}"
                            {{ old('parking_area_id') == $area->id ? 'selected' : '' }}>
                        {{ $area->name }}
                        (Motor: {{ $area->getAvailableMotorcycleSlots() }}/{{ $area->capacity_motorcycle }}
                        | Mobil: {{ $area->getAvailableCarSlots() }}/{{ $area->capacity_car }})
                    </option>
                    @endforeach
                </select>
                @error('parking_area_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-light border small mb-4">
                <i class="bi bi-clock me-1"></i>
                Waktu masuk akan dicatat otomatis: <strong>{{ now()->format('d/m/Y H:i:s') }}</strong>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-check2-circle me-2"></i>Proses Check-In
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Highlight pilihan kendaraan yang diselect
document.querySelectorAll('input[name="vehicle_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.form-check-inline').forEach(el => el.classList.remove('border-primary','bg-light'));
        this.closest('.form-check-inline').classList.add('border-primary','bg-light');
    });
    if(radio.checked) {
        radio.closest('.form-check-inline').classList.add('border-primary','bg-light');
    }
});
document.getElementById('license_plate').addEventListener('input', function(){
    this.value = this.value.toUpperCase();
});
</script>
@endpush
