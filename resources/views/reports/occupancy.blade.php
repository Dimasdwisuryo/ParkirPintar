@extends('layouts.app')

@section('title', 'Laporan Okupansi — ParkirPintar')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-xs font-semibold tracking-wider text-[#617068] uppercase">
                ANALITIK
            </div>
            <h1 class="text-2xl font-bold text-[#102D21] tracking-tight mt-0.5">
                Laporan okupansi area
            </h1>
            <p class="text-sm text-[#617068] mt-0.5">
                Pantau ketersediaan slot dan kapasitas real-time per area parkir.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.revenue') }}" class="px-4 py-2.5 rounded-xl border border-[#E0E5E2] bg-white text-xs font-semibold text-[#102D21] hover:bg-[#FAFBFA] transition-colors">
                <i class="bi bi-graph-up me-1.5"></i>Laporan Pendapatan
            </a>
            <a href="{{ route('reports.active') }}" class="px-4 py-2.5 rounded-xl border border-[#E0E5E2] bg-white text-xs font-semibold text-[#102D21] hover:bg-[#FAFBFA] transition-colors">
                <i class="bi bi-clock-history me-1.5"></i>Kendaraan Parkir
            </a>
        </div>
    </div>

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($areas as $area)
        @php
            $availMoto = max(0, $area->capacity_motorcycle - ($area->occupied_motorcycle ?? 0));
            $availCar  = max(0, $area->capacity_car - ($area->occupied_car ?? 0));
            $pctMoto   = $area->capacity_motorcycle > 0 ? (int) round(($area->occupied_motorcycle / $area->capacity_motorcycle) * 100) : 0;
            $pctCar    = $area->capacity_car > 0 ? (int) round(($area->occupied_car / $area->capacity_car) * 100) : 0;
        @endphp
        <div class="bg-white border border-[#E0E5E2] rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-base font-bold text-[#102D21]">{{ $area->name }}</h3>
                    @if($area->is_active)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#EDF5D6] text-[#536B20]">Aktif</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#EEF2EF] text-[#617068]">Nonaktif</span>
                    @endif
                </div>

                @if($area->location)
                <p class="text-xs text-[#859089] mb-5 flex items-center gap-1.5">
                    <i class="bi bi-geo-alt"></i> {{ $area->location }}
                </p>
                @else
                <div class="mb-4"></div>
                @endif

                {{-- Motor Bar --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-semibold text-[#102D21] flex items-center gap-1.5">
                            <i class="bi bi-bicycle text-[#173F2E]"></i> Motor
                        </span>
                        <span class="text-[#617068]">
                            <strong class="text-[#102D21]">{{ $area->occupied_motorcycle ?? 0 }}</strong> / {{ $area->capacity_motorcycle }}
                            <span class="text-[#859089]">({{ $availMoto }} sisa)</span>
                        </span>
                    </div>
                    <div class="w-full h-2 bg-[#EEF2EF] rounded-full overflow-hidden">
                        <div class="h-full bg-[#173F2E] rounded-full" style="width: {{ min(100, $pctMoto) }}%"></div>
                    </div>
                </div>

                {{-- Mobil Bar --}}
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="font-semibold text-[#102D21] flex items-center gap-1.5">
                            <i class="bi bi-car-front text-[#173F2E]"></i> Mobil
                        </span>
                        <span class="text-[#617068]">
                            <strong class="text-[#102D21]">{{ $area->occupied_car ?? 0 }}</strong> / {{ $area->capacity_car }}
                            <span class="text-[#859089]">({{ $availCar }} sisa)</span>
                        </span>
                    </div>
                    <div class="w-full h-2 bg-[#EEF2EF] rounded-full overflow-hidden">
                        <div class="h-full bg-[#173F2E] rounded-full" style="width: {{ min(100, $pctCar) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-[#E0E5E2] rounded-2xl p-12 text-center text-[#859089]">
            <i class="bi bi-inbox text-3xl mb-2 block text-[#A0AAA4]"></i>
            <p class="text-sm font-semibold text-[#102D21]">Belum ada area parkir</p>
            <p class="text-xs text-[#617068] mt-1">Silakan tambahkan area terlebih dahulu pada menu Area Parkir.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
