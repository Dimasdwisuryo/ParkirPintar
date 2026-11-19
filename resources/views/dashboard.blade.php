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

                        {{-- Aksi --}}
                        <td class="py-3.5 px-3 text-right whitespace-nowrap">
                            @if($trx->status === 'active')
                                <button type="button"
                                        data-ticket="{{ $trx->ticket_number }}"
                                        data-plate="{{ $trx->license_plate }}"
                                        data-vehicle="{{ $trx->vehicle_type === 'car' ? 'Mobil' : 'Motor' }}"
                                        data-area="{{ $trx->parkingArea->name ?? '—' }}"
                                        data-checkin="{{ $trx->check_in_at ? $trx->check_in_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB' : '—' }}"
                                        data-officer="{{ $trx->checkinUser->name ?? 'Penanggung Jawab' }}"
                                        onclick="openTicketFromBtn(this)"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-[#F1F7E4] hover:bg-[#D5F067] text-[#102D21] text-xs font-bold transition active:scale-95 shadow-2xs mr-1 cursor-pointer"
                                        title="Lihat Karcis &amp; Barcode">
                                    <i class="bi bi-ticket-perforated-fill text-xs text-[#173F2E]"></i>
                                    <span>Lihat Tiket</span>
                                </button>
                            @endif

                            <div class="dropdown inline-block">
                                <button type="button" class="text-[#8A958F] hover:text-[#102D21] p-1.5 rounded-lg hover:bg-gray-100 font-black text-xs transition cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
                                    •••
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-md border border-[#E0E5E2] rounded-xl text-xs py-1.5">
                                    @if($trx->status === 'active')
                                        <li>
                                            <button type="button"
                                                    data-ticket="{{ $trx->ticket_number }}"
                                                    data-plate="{{ $trx->license_plate }}"
                                                    data-vehicle="{{ $trx->vehicle_type === 'car' ? 'Mobil' : 'Motor' }}"
                                                    data-area="{{ $trx->parkingArea->name ?? '—' }}"
                                                    data-checkin="{{ $trx->check_in_at ? $trx->check_in_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB' : '—' }}"
                                                    data-officer="{{ $trx->checkinUser->name ?? 'Penanggung Jawab' }}"
                                                    class="dropdown-item py-1.5 px-3 font-semibold text-[#102D21] hover:bg-[#F4F6F4] flex items-center gap-2 cursor-pointer"
                                                    onclick="openTicketFromBtn(this)">
                                                <i class="bi bi-ticket-detailed text-[#173F2E]"></i>Lihat Tiket &amp; Barcode
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3 font-semibold text-[#173F2E] hover:bg-[#EDF5D6] flex items-center gap-2" href="{{ route('check-out.index', ['search' => $trx->ticket_number]) }}">
                                                <i class="bi bi-box-arrow-left text-[#173F2E]"></i>Proses Check-Out
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item py-1.5 px-3 font-semibold text-[#617068] hover:bg-[#F4F6F4] flex items-center gap-2" href="{{ route('check-in.success', $trx) }}">
                                            <i class="bi bi-box-arrow-up-right text-[#8A958F]"></i>Buka Halaman Karcis
                                        </a>
                                    </li>
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

{{-- Modal Karcis Masuk Digital --}}
<div id="ticketModal" class="fixed inset-0 bg-[#102D21]/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-[#E0E5E2] max-w-sm w-full p-6 shadow-2xl relative max-h-[92vh] overflow-y-auto">
        {{-- Tombol Close X --}}
        <button type="button" onclick="closeTicketModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-[#F4F6F4] hover:bg-[#E0E5E2] text-[#617068] hover:text-[#102D21] flex items-center justify-center text-sm font-bold transition cursor-pointer">
            <i class="bi bi-x-lg"></i>
        </button>

        {{-- Printable Card Content --}}
        <div id="ticketReceiptCard" class="bg-white">
            {{-- Header Struk --}}
            <div class="text-center pb-3 border-b border-dashed border-[#E0E5E2]">
                <div class="flex items-center justify-center gap-2 mb-1.5">
                    <div class="w-7 h-7 rounded-lg bg-[#D5F067] text-[#102D21] font-black text-xs flex items-center justify-center shadow-xs">
                        P
                    </div>
                    <span class="text-base font-extrabold text-[#102D21] tracking-tight">ParkirPintar</span>
                </div>
                <div class="text-xs font-bold text-[#102D21]">TIKET PARKIR MASUK</div>
                <div class="text-[10px] text-[#8A958F]">Sistem Manajemen Parkir Terpadu</div>
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EDF5D6] text-[#536B20]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#536B20] animate-pulse"></span>
                        STATUS: AKTIF
                    </span>
                </div>
            </div>

            {{-- Detail Informasi Tiket --}}
            <table class="w-full text-left my-3 text-xs">
                <tbody>
                    <tr class="border-b border-[#F4F6F4]">
                        <td class="py-1 text-[#8A958F] font-semibold text-[11px]">Nomor Tiket</td>
                        <td id="modalTicketNumber" class="py-1 text-right font-mono font-bold text-[#102D21] text-xs"></td>
                    </tr>
                    <tr class="border-b border-[#F4F6F4]">
                        <td class="py-1 text-[#8A958F] font-semibold text-[11px]">Plat Nomor</td>
                        <td id="modalLicensePlate" class="py-1 text-right font-extrabold text-[#102D21] text-sm tracking-wide"></td>
                    </tr>
                    <tr class="border-b border-[#F4F6F4]">
                        <td class="py-1 text-[#8A958F] font-semibold text-[11px]">Jenis Kendaraan</td>
                        <td id="modalVehicleType" class="py-1 text-right font-bold text-[#102D21] text-xs"></td>
                    </tr>
                    <tr class="border-b border-[#F4F6F4]">
                        <td class="py-1 text-[#8A958F] font-semibold text-[11px]">Area Parkir</td>
                        <td id="modalAreaName" class="py-1 text-right font-bold text-[#102D21] text-xs"></td>
                    </tr>
                    <tr class="border-b border-[#F4F6F4]">
                        <td class="py-1 text-[#8A958F] font-semibold text-[11px]">Waktu Masuk</td>
                        <td id="modalCheckInAt" class="py-1 text-right font-semibold text-[#102D21] text-[11px]"></td>
                    </tr>
                    <tr>
                        <td class="py-1 text-[#8A958F] font-semibold text-[11px]">Petugas</td>
                        <td id="modalOfficer" class="py-1 text-right font-semibold text-[#102D21] text-[11px]"></td>
                    </tr>
                </tbody>
            </table>

            {{-- Barcode 1D & QR Code 2D Nyata --}}
            <div class="bg-[#FAFBFA] border border-[#E0E5E2] rounded-2xl p-3 text-center my-2">
                {{-- QR Code 2D --}}
                <div class="flex justify-center mb-1.5">
                    <div id="modalQrCode" class="p-2 bg-white rounded-xl shadow-2xs border border-[#E0E5E2]"></div>
                </div>

                {{-- Barcode 1D Code128 --}}
                <div class="flex justify-center overflow-hidden">
                    <svg id="modalBarcode128" class="w-full max-w-[240px] h-11"></svg>
                </div>
                <div class="text-[10px] text-[#8A958F] font-medium mt-1">
                    Arahkan barcode atau QR ke scanner saat check-out
                </div>
            </div>

            <div class="text-[9px] text-[#8A958F] text-center leading-relaxed mb-3">
                Simpan karcis ini dengan baik sebagai bukti parkir yang sah.<br>
                Kehilangan karcis dikenakan denda sesuai regulasi gedung.
            </div>
        </div>

        {{-- 3 Tombol Aksi Modal (Compact: Print, Download PDF, Tutup) --}}
        <div class="flex items-center justify-center gap-2 pt-2 border-t border-[#E0E5E2] no-print">
            {{-- Print --}}
            <button type="button"
                    onclick="printModalTicket()"
                    class="h-9 px-3 text-xs font-semibold rounded-lg inline-flex items-center justify-center gap-1.5 whitespace-nowrap border border-[#E0E5E2] bg-white hover:bg-gray-50 text-[#102D21] shadow-2xs transition active:scale-98 cursor-pointer flex-1">
                <i class="bi bi-printer text-xs"></i>
                <span>Print</span>
            </button>

            {{-- Download PDF --}}
            <button type="button"
                    onclick="downloadModalTicketPdf()"
                    class="h-9 px-3 text-xs font-semibold rounded-lg inline-flex items-center justify-center gap-1.5 whitespace-nowrap border border-[#E0E5E2] bg-white hover:bg-gray-50 text-[#102D21] shadow-2xs transition active:scale-98 cursor-pointer flex-1">
                <i class="bi bi-download text-xs"></i>
                <span>Download PDF</span>
            </button>

            {{-- Tutup --}}
            <button type="button"
                    onclick="closeTicketModal()"
                    class="h-9 px-3 text-xs font-semibold rounded-lg inline-flex items-center justify-center gap-1.5 whitespace-nowrap bg-[#173F2E] hover:bg-[#234D39] text-white shadow-xs transition active:scale-98 cursor-pointer flex-1">
                <span>Tutup</span>
            </button>
        </div>
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

{{-- qrcodejs CDN untuk 2D QR Code Karcis Modal --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
{{-- JsBarcode CDN untuk 1D Code128 Barcode Modal --}}
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
{{-- html2pdf CDN untuk Download PDF Modal --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    let currentModalTicketNumber = '';

    function openTicketFromBtn(btn) {
        if (!btn) return;
        openTicketModal({
            ticket_number: btn.getAttribute('data-ticket'),
            license_plate: btn.getAttribute('data-plate'),
            vehicle_label: btn.getAttribute('data-vehicle'),
            area_name:     btn.getAttribute('data-area'),
            check_in_at:   btn.getAttribute('data-checkin'),
            officer_name:  btn.getAttribute('data-officer')
        });
    }

    function openTicketModal(data) {
        currentModalTicketNumber = data.ticket_number;
        document.getElementById('modalTicketNumber').textContent = data.ticket_number;
        document.getElementById('modalLicensePlate').textContent = data.license_plate;
        document.getElementById('modalVehicleType').textContent = data.vehicle_label;
        document.getElementById('modalAreaName').textContent = data.area_name;
        document.getElementById('modalCheckInAt').textContent = data.check_in_at;
        document.getElementById('modalOfficer').textContent = data.officer_name || 'Penanggung Jawab';

        // Render QR Code 2D Nyata
        const qrContainer = document.getElementById('modalQrCode');
        if (qrContainer) {
            qrContainer.innerHTML = '';
            new QRCode(qrContainer, {
                text: data.ticket_number,
                width: 100,
                height: 100,
                colorDark: "#102D21",
                colorLight: "#FFFFFF",
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        // Render Barcode 1D Code128 Nyata
        const barcodeElement = document.getElementById('modalBarcode128');
        if (barcodeElement && typeof JsBarcode !== 'undefined') {
            JsBarcode(barcodeElement, data.ticket_number, {
                format: "CODE128",
                width: 1.8,
                height: 38,
                displayValue: true,
                fontSize: 11,
                font: "monospace",
                textMargin: 3,
                lineColor: "#102D21"
            });
        }

        document.getElementById('ticketModal').classList.remove('hidden');
    }

    function closeTicketModal() {
        const modal = document.getElementById('ticketModal');
        if (modal) modal.classList.add('hidden');
    }

    function printModalTicket() {
        const card = document.getElementById('ticketReceiptCard');
        if (!card) return;
        const printWindow = window.open('', '_blank', 'width=450,height=600');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Tiket Parkir - ${currentModalTicketNumber}</title>
                <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
                <style>
                    body { font-family: 'Plus Jakarta Sans', sans-serif; margin: 15px; color: #102D21; background: #fff; }
                    table { width: 100%; border-collapse: collapse; }
                    .text-center { text-align: center; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                ${card.innerHTML}
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    function downloadModalTicketPdf() {
        const element = document.getElementById('ticketReceiptCard');
        if (!element) return;
        const opt = {
            margin: [5, 5, 5, 5],
            filename: `${currentModalTicketNumber || 'Tiket-Parkir'}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2.5, useCORS: true, logging: false },
            jsPDF: { unit: 'mm', format: [95, 175], orientation: 'portrait' },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        };
        html2pdf().set(opt).from(element).save();
    }

    // Tutup modal jika klik di luar card
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('ticketModal');
        if (modal && e.target === modal) {
            closeTicketModal();
        }
    });
</script>
@endpush
