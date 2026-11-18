@extends('layouts.app')

@section('title', 'Dashboard — ParkirPintar')

@section('content')
{{-- 1. Greeting Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <div class="text-[11px] font-bold text-[#8A958F] tracking-widest uppercase mb-1">
            {{ $todayDateFormatted }}
        </div>
        <h1 class="text-3xl font-extrabold text-[#102D21] tracking-tight mb-1">
            {{ $greeting }}, {{ auth()->user()->name ?? 'Penanggung Jawab' }}
        </h1>
        <p class="text-xs text-[#617068] font-medium m-0">
            Berikut ringkasan aktivitas parkir gedung hari ini.
        </p>
    </div>

    <div>
        <a href="{{ route('reports.occupancy') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-[#E0E5E2] hover:bg-gray-50 text-[#102D21] text-xs font-bold shadow-xs transition active:scale-98">
            <span>Lihat laporan</span>
            <i class="bi bi-arrow-right text-xs"></i>
        </a>
    </div>
</div>

{{-- 2. KPI Summary Cards (Grid 4 Kolom) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    {{-- Card 1: MOBIL TERPARKIR --}}
    <div class="bg-white rounded-2xl border border-[#E0E5E2] p-5 shadow-xs flex flex-col justify-between hover:border-[#173F2E]/30 transition">
        <div>
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-[#EEF2EF] text-[#173F2E] flex items-center justify-center text-lg">
                    <i class="bi bi-car-front-fill"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#EDF5D6] text-[#536B20]">
                    +{{ $carsToday }} hari ini
                </span>
            </div>
            <div class="text-[10px] font-bold tracking-widest text-[#8A958F] uppercase mt-4 mb-1">
                MOBIL TERPARKIR
            </div>
            <div class="flex items-baseline">
                <span class="text-3xl font-extrabold text-[#102D21] tracking-tight">{{ $occupiedCars }}</span>
                <span class="text-xs text-[#8A958F] font-semibold ml-2">dari {{ $capacityCar }} slot</span>
            </div>
        </div>
        <div class="w-full bg-[#EEF2EF] h-1.5 rounded-full overflow-hidden mt-4">
            <div class="bg-[#173F2E] h-full rounded-full transition-all duration-700" style="width: {{ $carOccupancyRate }}%"></div>
        </div>
    </div>

    {{-- Card 2: MOTOR TERPARKIR --}}
    <div class="bg-white rounded-2xl border border-[#E0E5E2] p-5 shadow-xs flex flex-col justify-between hover:border-[#173F2E]/30 transition">
        <div>
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-[#F1F7E4] text-[#718826] flex items-center justify-center text-lg">
                    <i class="bi bi-bicycle"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#EDF5D6] text-[#536B20]">
                    +{{ $motorcyclesToday }} hari ini
                </span>
            </div>
            <div class="text-[10px] font-bold tracking-widest text-[#8A958F] uppercase mt-4 mb-1">
                MOTOR TERPARKIR
            </div>
            <div class="flex items-baseline">
                <span class="text-3xl font-extrabold text-[#102D21] tracking-tight">{{ $occupiedMotorcycles }}</span>
                <span class="text-xs text-[#8A958F] font-semibold ml-2">dari {{ $capacityMotorcycle }} slot</span>
            </div>
        </div>
        <div class="w-full bg-[#EEF2EF] h-1.5 rounded-full overflow-hidden mt-4">
            <div class="bg-[#173F2E] h-full rounded-full transition-all duration-700" style="width: {{ $motorcycleOccupancyRate }}%"></div>
        </div>
    </div>

    {{-- Card 3: TRANSAKSI HARI INI --}}
    <div class="bg-white rounded-2xl border border-[#E0E5E2] p-5 shadow-xs flex flex-col justify-between hover:border-[#173F2E]/30 transition">
        <div>
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-[#FBEDE4] text-[#EA6546] flex items-center justify-center text-lg">
                    <i class="bi bi-ticket-perforated-fill"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#EDF5D6] text-[#536B20]">
                    {{ $transactionChangePercentage }}
                </span>
            </div>
            <div class="text-[10px] font-bold tracking-widest text-[#8A958F] uppercase mt-4 mb-1">
                TRANSAKSI HARI INI
            </div>
            <div class="flex items-baseline">
                <span class="text-3xl font-extrabold text-[#102D21] tracking-tight">{{ $displayedTransactions }}</span>
                <span class="text-xs text-[#8A958F] font-semibold ml-2">tiket selesai</span>
            </div>
        </div>
        <div class="w-full bg-transparent h-1.5 mt-4"></div>
    </div>

    {{-- Card 4: PENDAPATAN HARI INI --}}
    <div class="bg-white rounded-2xl border border-[#E0E5E2] p-5 shadow-xs flex flex-col justify-between hover:border-[#173F2E]/30 transition">
        <div>
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-[#E7F0F3] text-[#3F7089] flex items-center justify-center text-lg">
                    <i class="bi bi-wallet2"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#EDF5D6] text-[#536B20]">
                    +8,6%
                </span>
            </div>
            <div class="text-[10px] font-bold tracking-widest text-[#8A958F] uppercase mt-4 mb-1">
                PENDAPATAN HARI INI
            </div>
            <div class="flex items-baseline">
                <span class="text-3xl font-extrabold text-[#102D21] tracking-tight">Rp{{ $revenueDisplay }}</span>
                <span class="text-xs text-[#8A958F] font-semibold ml-2">Target Rp5,5 jt</span>
            </div>
        </div>
        <div class="w-full bg-[#EEF2EF] h-1.5 rounded-full overflow-hidden mt-4">
            <div class="bg-[#173F2E] h-full rounded-full transition-all duration-700" style="width: {{ $revenueTargetRate }}%"></div>
        </div>
    </div>
</div>

{{-- 3. Middle Section: Tren Kendaraan (8 Kolom) + Okupansi Area (4 Kolom) --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
    {{-- Left: Tren Kendaraan (8 Kolom) --}}
    <div class="lg:col-span-8 bg-white rounded-2xl border border-[#E0E5E2] p-6 shadow-xs flex flex-col justify-between">
        <div>
            {{-- Header & Dropdown --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-[#102D21] m-0">Tren kendaraan</h2>
                    <p class="text-xs text-[#8A958F] font-medium m-0 mt-0.5">Jumlah kendaraan masuk dan keluar hari ini</p>
                </div>
                <div class="relative">
                    <button type="button" class="border border-[#E0E5E2] rounded-xl px-3 py-1.5 text-xs font-semibold text-[#102D21] bg-white flex items-center gap-1.5 hover:bg-gray-50 transition shadow-2xs">
                        <span>Hari ini</span>
                        <i class="bi bi-chevron-down text-[10px] text-[#8A958F]"></i>
                    </button>
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-6 mt-4 mb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#173F2E]"></span>
                    <span class="text-xs font-bold text-[#102D21]">Kendaraan masuk</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#B8CF54]"></span>
                    <span class="text-xs font-bold text-[#102D21]">Kendaraan keluar</span>
                </div>
            </div>
        </div>

        {{-- Line Chart Container --}}
        <div class="relative w-full h-64 mt-3">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- Right: Okupansi Area (4 Kolom) --}}
    <div class="lg:col-span-4 bg-white rounded-2xl border border-[#E0E5E2] p-6 shadow-xs flex flex-col justify-between">
        <div>
            {{-- Header --}}
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-[#102D21] m-0">Okupansi area</h2>
                    <p class="text-xs text-[#8A958F] font-medium m-0 mt-0.5">Ketersediaan per zona</p>
                </div>
                <a href="{{ route('parking-areas.index') }}" class="text-xs font-bold text-[#173F2E] hover:underline">
                    Kelola
                </a>
            </div>

            {{-- List Progress Bar Tiap Area --}}
            <div class="flex flex-col gap-4">
                @forelse($areaOccupancies as $area)
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1.5">
                            <span class="text-[#102D21] truncate max-w-[180px]">
                                {{ $area['name'] }}{{ $area['location'] ? ' — ' . $area['location'] : '' }}
                            </span>
                            <span class="text-[#8A958F] font-bold">
                                {{ $area['occupied'] }} / {{ $area['total'] }}
                            </span>
                        </div>
                        <div class="w-full bg-[#EEF2EF] h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700"
                                 style="width: {{ $area['rate'] }}%; background-color: {{ $area['color'] }};">
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-[#8A958F] text-center py-4">
                        Belum ada area parkir yang terdaftar.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Footer Total Ketersediaan --}}
        <div class="bg-[#F4F6F4] rounded-xl p-3.5 flex items-center justify-between mt-5">
            <span class="text-xs text-[#617068] font-medium">Total ketersediaan</span>
            <span class="text-xs font-extrabold text-[#102D21]">
                {{ $totalAvailableSlots }} slot tersedia
            </span>
        </div>
    </div>
</div>

{{-- 4. Bottom Section: Tabel Aktivitas Terbaru --}}
<div class="bg-white rounded-2xl border border-[#E0E5E2] p-6 shadow-xs">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-base font-bold text-[#102D21] m-0">Aktivitas terbaru</h2>
            <p class="text-xs text-[#8A958F] font-medium m-0 mt-0.5">Transaksi masuk dan keluar terakhir</p>
        </div>
        <a href="{{ route('reports.history') }}" class="text-xs font-bold text-[#102D21] hover:underline inline-flex items-center gap-1">
            <span>Lihat semua</span>
            <i class="bi bi-chevron-right text-[10px]"></i>
        </a>
    </div>

    {{-- Tabel Bersih --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-[#E0E5E2]">
                    <th class="py-3 px-3 text-[11px] font-bold tracking-wider text-[#8A958F] uppercase">KENDARAAN</th>
                    <th class="py-3 px-3 text-[11px] font-bold tracking-wider text-[#8A958F] uppercase">NOMOR TIKET</th>
                    <th class="py-3 px-3 text-[11px] font-bold tracking-wider text-[#8A958F] uppercase">AREA</th>
                    <th class="py-3 px-3 text-[11px] font-bold tracking-wider text-[#8A958F] uppercase">WAKTU</th>
                    <th class="py-3 px-3 text-[11px] font-bold tracking-wider text-[#8A958F] uppercase">STATUS</th>
                    <th class="py-3 px-3 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F4F6F4]">
                @forelse($recentTransactions as $trx)
                    <tr class="hover:bg-[#FAFBFA] transition group">
                        {{-- Kendaraan --}}
                        <td class="py-3.5 px-3">
                            <div class="flex items-center gap-3">
                                @if($trx->vehicle_type === 'car')
                                    <div class="w-9 h-9 rounded-xl bg-[#EEF2EF] text-[#173F2E] flex items-center justify-center flex-shrink-0 text-base">
                                        <i class="bi bi-car-front-fill"></i>
                                    </div>
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-[#F1F7E4] text-[#718826] flex items-center justify-center flex-shrink-0 text-base">
                                        <i class="bi bi-bicycle"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-xs font-bold text-[#102D21] tracking-wide">
                                        {{ $trx->license_plate }}
                                    </div>
                                    <div class="text-[11px] text-[#8A958F] font-medium">
                                        {{ $trx->vehicle_type === 'car' ? 'Mobil' : 'Motor' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Nomor Tiket --}}
                        <td class="py-3.5 px-3">
                            <span class="font-mono text-xs font-semibold text-[#102D21]">
                                {{ $trx->ticket_number }}
                            </span>
                        </td>

                        {{-- Area --}}
                        <td class="py-3.5 px-3">
                            <span class="text-xs font-bold text-[#102D21]">
                                {{ $trx->parkingArea->name ?? 'A-01' }}
                            </span>
                        </td>

                        {{-- Waktu --}}
                        <td class="py-3.5 px-3">
                            <span class="text-xs text-[#617068] font-medium">
                                {{ $trx->check_in_at ? $trx->check_in_at->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB' : '—' }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="py-3.5 px-3">
                            @if($trx->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#EDF5D6] text-[#536B20]">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#EEF2EF] text-[#617068]">
                                    Selesai
                                </span>
                            @endif
                        </td>

                        {{-- Aksi Dropdown --}}
                        <td class="py-3.5 px-3 text-right">
                            <div class="dropdown inline-block">
                                <button type="button" class="text-[#8A958F] hover:text-[#102D21] p-1.5 rounded-lg hover:bg-gray-100 font-black text-xs transition" data-bs-toggle="dropdown" aria-expanded="false">
                                    •••
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-md border border-[#E0E5E2] rounded-xl text-xs py-1.5">
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 font-semibold text-[#102D21] hover:bg-[#F4F6F4]" href="{{ route('check-in.success', $trx) }}">
                                            <i class="bi bi-ticket-detailed me-2 text-[#8A958F]"></i>Lihat Karcis
                                        </a>
                                    </li>
                                    @if($trx->status === 'active')
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3 font-semibold text-[#173F2E] hover:bg-[#EDF5D6]" href="{{ route('check-out.index', ['search' => $trx->ticket_number]) }}">
                                                <i class="bi bi-box-arrow-left me-2 text-[#173F2E]"></i>Proses Check-Out
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-xs text-[#8A958F] font-medium">
                            Belum ada aktivitas transaksi kendaraan hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('trendChart');
        if (!ctx) return;

        const chartHours = @json($chartHours);
        const checkIns = @json($checkInsHourly);
        const checkOuts = @json($checkOutsHourly);

        // Gradient untuk Kendaraan Masuk (#173F2E)
        const gradientIn = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
        gradientIn.addColorStop(0, 'rgba(23, 63, 46, 0.15)');
        gradientIn.addColorStop(1, 'rgba(23, 63, 46, 0.00)');

        // Gradient untuk Kendaraan Keluar (#B8CF54)
        const gradientOut = ctx.getContext('2d').createLinearGradient(0, 0, 0, 240);
        gradientOut.addColorStop(0, 'rgba(184, 207, 84, 0.15)');
        gradientOut.addColorStop(1, 'rgba(184, 207, 84, 0.00)');

        // Point highlight untuk index ke-3 (sekitar jam 15:00 persis seperti di Figma)
        const pointRadii = [0, 0, 0, 5, 0, 0];
        const pointBg = ['#173F2E', '#173F2E', '#173F2E', '#FFFFFF', '#173F2E', '#173F2E'];
        const pointBorder = ['#173F2E', '#173F2E', '#173F2E', '#173F2E', '#173F2E', '#173F2E'];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartHours,
                datasets: [
                    {
                        label: 'Kendaraan masuk',
                        data: checkIns,
                        borderColor: '#173F2E',
                        backgroundColor: gradientIn,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: pointRadii,
                        pointHoverRadius: 6,
                        pointBackgroundColor: pointBg,
                        pointBorderColor: pointBorder,
                        pointBorderWidth: 2.5,
                    },
                    {
                        label: 'Kendaraan keluar',
                        data: checkOuts,
                        borderColor: '#B8CF54',
                        backgroundColor: gradientOut,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#B8CF54',
                        pointBorderWidth: 2.5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: '#102D21',
                        titleFont: { family: "'Plus Jakarta Sans'", size: 12, weight: '700' },
                        bodyFont: { family: "'Plus Jakarta Sans'", size: 11, weight: '500' },
                        padding: 10,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans'", size: 11, weight: '600' },
                            color: '#8A958F',
                            padding: 10,
                        }
                    },
                    y: {
                        display: false,
                        grid: {
                            display: false,
                        },
                        min: 0,
                    }
                }
            }
        });
    });
</script>
@endpush
