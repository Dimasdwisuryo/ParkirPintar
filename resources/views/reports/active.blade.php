@extends('layouts.app')

@section('title', 'Kendaraan Parkir Saat Ini — ParkirPintar')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-xs font-semibold tracking-wider text-[#617068] uppercase">
                OPERASIONAL
            </div>
            <h1 class="text-2xl font-bold text-[#102D21] tracking-tight mt-0.5">
                Kendaraan yang sedang parkir
            </h1>
            <p class="text-sm text-[#617068] mt-0.5">
                Daftar kendaraan aktif yang saat ini masih berada di area parkir.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.revenue') }}" class="px-4 py-2.5 rounded-xl border border-[#E0E5E2] bg-white text-xs font-semibold text-[#102D21] hover:bg-[#FAFBFA] transition-colors">
                <i class="bi bi-graph-up me-1.5"></i>Laporan Pendapatan
            </a>
            <a href="{{ route('check-out.index') }}" class="px-4 py-2.5 rounded-xl bg-[#173F2E] hover:bg-[#234D39] text-white text-xs font-semibold shadow-xs transition-colors">
                <i class="bi bi-box-arrow-left me-1.5"></i>Proses Check-out
            </a>
        </div>
    </div>

    {{-- Tabel Kendaraan Aktif --}}
    <div class="bg-white border border-[#E0E5E2] rounded-2xl shadow-xs overflow-hidden">
        <div class="p-5 border-b border-[#E0E5E2] flex items-center justify-between">
            <h3 class="text-base font-bold text-[#102D21]">
                Total {{ $transactions->total() }} kendaraan aktif
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#E0E5E2] bg-[#FAFBFA]/60">
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">No. Tiket</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Plat Nomor</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Jenis</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Area Parkir</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">Waktu Masuk</th>
                        <th class="py-3 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E5E2]">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-[#FAFBFA] transition-colors">
                        <td class="py-3.5 px-6 font-mono text-xs text-[#617068]">{{ $t->ticket_number }}</td>
                        <td class="py-3.5 px-6 font-bold text-sm text-[#102D21]">{{ $t->license_plate }}</td>
                        <td class="py-3.5 px-6 text-xs font-semibold">
                            @if($t->vehicle_type === 'car')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#EDF5D6] text-[#536B20]">
                                    <i class="bi bi-car-front"></i> Mobil
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#FAFBFA] border border-[#E0E5E2] text-[#102D21]">
                                    <i class="bi bi-bicycle"></i> Motor
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-xs text-[#102D21] font-medium">{{ $t->parkingArea->name ?? '—' }}</td>
                        <td class="py-3.5 px-6 text-xs text-[#617068]">{{ $t->check_in_at?->format('d/m/Y H:i') }} WIB</td>
                        <td class="py-3.5 px-6 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#EDF5D6] text-[#536B20]">
                                Aktif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-[#859089]">
                            <i class="bi bi-check-circle text-3xl mb-2 block text-[#A0AAA4]"></i>
                            <p class="text-sm font-semibold text-[#102D21]">Tidak ada kendaraan yang sedang parkir</p>
                            <p class="text-xs text-[#617068] mt-1">Seluruh area parkir sedang dalam keadaan kosong.</p>
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
