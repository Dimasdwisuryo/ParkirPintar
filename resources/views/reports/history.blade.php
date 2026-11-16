@extends('layouts.app')

@section('title', 'Riwayat Transaksi — ParkirPintar')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-xs font-semibold tracking-wider text-[#617068] uppercase">
                ANALITIK
            </div>
            <h1 class="text-2xl font-bold text-[#102D21] tracking-tight mt-0.5">
                Riwayat transaksi
            </h1>
            <p class="text-sm text-[#617068] mt-0.5">
                Arsip lengkap seluruh transaksi kendaraan yang telah selesai.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.revenue') }}" class="px-4 py-2.5 rounded-xl border border-[#E0E5E2] bg-white text-xs font-semibold text-[#102D21] hover:bg-[#FAFBFA] transition-colors">
                <i class="bi bi-graph-up me-1.5"></i>Laporan Pendapatan
            </a>
            <a href="{{ route('reports.export', request()->query()) }}" class="px-4 py-2.5 rounded-xl bg-[#173F2E] hover:bg-[#234D39] text-white text-xs font-semibold shadow-xs transition-colors">
                <i class="bi bi-download me-1.5"></i>Ekspor CSV
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white border border-[#E0E5E2] rounded-2xl p-5 shadow-xs">
        <form method="GET" action="{{ route('reports.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-[#102D21] mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 bg-white border border-[#E0E5E2] rounded-xl text-xs text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#102D21] mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 bg-white border border-[#E0E5E2] rounded-xl text-xs text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#102D21] mb-1">Area Parkir</label>
                <select name="area_id" class="w-full px-3 py-2 bg-white border border-[#E0E5E2] rounded-xl text-xs text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    <option value="">Semua Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#102D21] mb-1">Jenis</label>
                <select name="vehicle_type" class="w-full px-3 py-2 bg-white border border-[#E0E5E2] rounded-xl text-xs text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    <option value="">Semua</option>
                    <option value="car" {{ request('vehicle_type') === 'car' ? 'selected' : '' }}>Mobil</option>
                    <option value="motorcycle" {{ request('vehicle_type') === 'motorcycle' ? 'selected' : '' }}>Motor</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#173F2E] hover:bg-[#234D39] text-white text-xs font-semibold rounded-xl transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.history') }}" class="px-3 py-2 border border-[#E0E5E2] rounded-xl text-xs font-semibold text-[#617068] hover:bg-[#FAFBFA] transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="bg-white border border-[#E0E5E2] rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#E0E5E2] bg-[#FAFBFA]/60">
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">No. Tiket</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Plat</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Jenis</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Area</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Masuk</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Keluar</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Durasi</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase text-right">Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E5E2]">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-[#FAFBFA] transition-colors">
                        <td class="py-3.5 px-6 font-mono text-xs text-[#617068]">{{ $t->ticket_number }}</td>
                        <td class="py-3.5 px-6 font-bold text-sm text-[#102D21]">{{ $t->license_plate }}</td>
                        <td class="py-3.5 px-6 text-xs font-semibold">
                            @if($t->vehicle_type === 'car')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-[#EDF5D6] text-[#536B20]">
                                    <i class="bi bi-car-front"></i> Mobil
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-[#FAFBFA] border border-[#E0E5E2] text-[#102D21]">
                                    <i class="bi bi-bicycle"></i> Motor
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-xs text-[#102D21] font-medium">{{ $t->parkingArea->name ?? '—' }}</td>
                        <td class="py-3.5 px-6 text-xs text-[#617068]">{{ $t->check_in_at?->format('d/m/Y H:i') }}</td>
                        <td class="py-3.5 px-6 text-xs text-[#617068]">{{ $t->check_out_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="py-3.5 px-6 text-xs text-[#102D21]">
                            @if($t->duration_minutes !== null)
                                {{ intdiv($t->duration_minutes, 60) }}j {{ $t->duration_minutes % 60 }}m
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-sm font-bold text-[#102D21] text-right">
                            {{ $t->fee !== null ? 'Rp ' . number_format($t->fee, 0, ',', '.') : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-[#859089]">
                            <i class="bi bi-inbox text-3xl mb-2 block text-[#A0AAA4]"></i>
                            <p class="text-sm font-semibold text-[#102D21]">Tidak ada riwayat transaksi</p>
                            <p class="text-xs text-[#617068] mt-1">Coba sesuaikan filter pencarian Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-[#E0E5E2] bg-[#FAFBFA]/40">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
