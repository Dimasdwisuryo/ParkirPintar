@extends('layouts.app')

@section('title', 'Edit Area Parkir — ParkirPintar')

@section('content')
@php
    $occupiedMotorcycle = $parkingArea->getOccupiedMotorcycleCount();
    $occupiedCar        = $parkingArea->getOccupiedCarCount();

    $parts = explode(' ', $parkingArea->name, 2);
    $hasCode = count($parts) === 2 && preg_match('/^[A-Za-z0-9\-]+$/', $parts[0]);
    $displayCode = $hasCode ? $parts[0] : ('A-' . sprintf('%02d', $parkingArea->id));
    $displayName = $hasCode ? $parts[1] : $parkingArea->name;
@endphp

<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('parking-areas.index') }}"
           class="w-10 h-10 rounded-xl border border-[#E0E5E2] bg-white flex items-center justify-center text-[#102D21] hover:bg-[#FAFBFA] transition-colors shadow-xs">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div>
            <div class="text-xs font-semibold tracking-wider text-[#617068] uppercase">
                MASTER DATA
            </div>
            <h1 class="text-2xl font-bold text-[#102D21] tracking-tight">
                Edit area: {{ $parkingArea->name }}
            </h1>
        </div>
    </div>

    {{-- Occupied Notice Banner --}}
    @if($occupiedMotorcycle > 0 || $occupiedCar > 0)
    <div class="p-4 bg-[#F1F7E4] border border-[#B8CF54] rounded-2xl flex items-start gap-3 shadow-xs">
        <i class="bi bi-info-circle-fill text-lg text-[#536B20] shrink-0 mt-0.5"></i>
        <div class="text-xs text-[#173F2E]">
            <span class="font-bold text-sm block mb-0.5">Informasi Kendaraan Aktif:</span>
            Saat ini terparkir: <strong>{{ $occupiedCar }} Mobil</strong> dan <strong>{{ $occupiedMotorcycle }} Motor</strong>.
            Kapasitas baru <span class="underline">tidak dapat dikurangi</span> di bawah jumlah kendaraan yang sedang terparkir.
        </div>
    </div>
    @endif

    {{-- Error Banner --}}
    @if($errors->any())
    <div class="p-4 bg-[#FEE2E2] border border-[#EF4444] rounded-2xl text-[#991B1B] text-xs font-medium space-y-1 shadow-xs">
        <div class="font-bold text-sm text-[#991B1B] flex items-center gap-2">
            <i class="bi bi-x-circle-fill text-[#DC2626]"></i>
            Periksa kembali isian Anda
        </div>
        <ul class="list-disc list-inside pl-4 space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Card Form --}}
    <div class="bg-white border border-[#E0E5E2] rounded-2xl p-6 sm:p-8 shadow-xs">
        <form method="POST" action="{{ route('parking-areas.update', $parkingArea) }}" onsubmit="syncEditNameFieldStandalone()">
            @csrf
            @method('PUT')
            <input type="hidden" name="name" id="standalone_edit_name">

            <div class="space-y-5">
                {{-- Kode & Nama Area --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                            Kode Area <span class="text-[#EA6546]">*</span>
                        </label>
                        <input type="text" id="standalone_edit_code" name="code" required
                               value="{{ old('code', $displayCode) }}"
                               class="w-full px-3.5 py-3 bg-white border border-[#E0E5E2] rounded-xl text-sm font-bold uppercase text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                            Nama Area <span class="text-[#EA6546]">*</span>
                        </label>
                        <input type="text" id="standalone_edit_name_input" name="name_input" required
                               value="{{ old('name_input', $displayName) }}"
                               class="w-full px-3.5 py-3 bg-white border border-[#E0E5E2] rounded-xl text-sm font-medium text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                </div>

                {{-- Deskripsi / Lokasi --}}
                <div>
                    <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                        Lokasi / Keterangan <span class="text-[#859089] font-normal">(opsional)</span>
                    </label>
                    <input type="text" name="location"
                           value="{{ old('location', $parkingArea->location) }}"
                           class="w-full px-3.5 py-3 bg-white border border-[#E0E5E2] rounded-xl text-sm text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                </div>

                {{-- Kapasitas Mobil & Motor --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-[#FAFBFA] border border-[#E0E5E2] rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <label class="flex items-center gap-2 text-xs font-semibold text-[#102D21]">
                                <i class="bi bi-car-front text-sm text-[#173F2E]"></i>
                                <span>Kapasitas Mobil <span class="text-[#EA6546]">*</span></span>
                            </label>
                            @if($occupiedCar > 0)
                            <span class="text-[10px] font-bold text-[#EA6546]">Min: {{ $occupiedCar }}</span>
                            @endif
                        </div>
                        <input type="number" name="capacity_car" min="{{ $occupiedCar }}" required
                               value="{{ old('capacity_car', $parkingArea->capacity_car) }}"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-base font-bold text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>

                    <div class="p-4 bg-[#FAFBFA] border border-[#E0E5E2] rounded-xl">
                        <div class="flex items-center justify-between mb-2">
                            <label class="flex items-center gap-2 text-xs font-semibold text-[#102D21]">
                                <i class="bi bi-bicycle text-sm text-[#173F2E]"></i>
                                <span>Kapasitas Motor <span class="text-[#EA6546]">*</span></span>
                            </label>
                            @if($occupiedMotorcycle > 0)
                            <span class="text-[10px] font-bold text-[#EA6546]">Min: {{ $occupiedMotorcycle }}</span>
                            @endif
                        </div>
                        <input type="number" name="capacity_motorcycle" min="{{ $occupiedMotorcycle }}" required
                               value="{{ old('capacity_motorcycle', $parkingArea->capacity_motorcycle) }}"
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-base font-bold text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                </div>

                {{-- Status Toggle --}}
                <div class="pt-2 flex items-center justify-between border-t border-[#E0E5E2]">
                    <div>
                        <span class="text-xs font-semibold text-[#102D21] block">Status Area Aktif</span>
                        <span class="text-[11px] text-[#617068]">Nonaktifkan jika area sedang dalam renovasi atau ditutup.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $parkingArea->is_active) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-[#E0E5E2] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#173F2E]"></div>
                    </label>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-[#E0E5E2]">
                    <a href="{{ route('parking-areas.index') }}"
                       class="px-5 py-2.5 rounded-xl border border-[#E0E5E2] bg-white text-xs font-semibold text-[#102D21] hover:bg-[#F4F6F4] transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-[#173F2E] hover:bg-[#234D39] text-xs font-semibold text-white shadow-xs transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function syncEditNameFieldStandalone() {
        const code = (document.getElementById('standalone_edit_code').value || '').trim();
        const nameInput = (document.getElementById('standalone_edit_name_input').value || '').trim();
        const fullName = code ? (code + ' ' + nameInput) : nameInput;
        document.getElementById('standalone_edit_name').value = fullName;
    }
</script>
@endpush
@endsection
