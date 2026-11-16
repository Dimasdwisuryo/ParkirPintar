@extends('layouts.app')

@section('title', 'Area Parkir — ParkirPintar')

@section('content')
<div class="space-y-6">

    {{-- Flash Notifications --}}
    @if(session('success'))
    <div class="flex items-center justify-between p-4 bg-[#EDF5D6] border border-[#B8CF54] rounded-2xl text-[#173F2E] text-sm font-semibold shadow-xs">
        <div class="flex items-center gap-2.5">
            <i class="bi bi-check-circle-fill text-[#536B20] text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-[#536B20] hover:text-[#173F2E] p-1">
            <i class="bi bi-x-lg text-xs"></i>
        </button>
    </div>
    @endif

    @if(session('warning'))
    <div class="flex items-center justify-between p-4 bg-[#FEF3C7] border border-[#F59E0B] rounded-2xl text-[#92400E] text-sm font-semibold shadow-xs">
        <div class="flex items-center gap-2.5">
            <i class="bi bi-exclamation-triangle-fill text-[#B45309] text-base"></i>
            <span>{{ session('warning') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-[#92400E] hover:text-[#78350F] p-1">
            <i class="bi bi-x-lg text-xs"></i>
        </button>
    </div>
    @endif

    @if(session('error') || $errors->any())
    <div class="p-4 bg-[#FEE2E2] border border-[#EF4444] rounded-2xl text-[#991B1B] text-sm font-semibold shadow-xs space-y-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-x-circle-fill text-[#DC2626] text-base"></i>
                <span>{{ session('error') ?? 'Terjadi kesalahan saat memproses data area parkir.' }}</span>
            </div>
            <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-[#991B1B] hover:text-[#7F1D1D] p-1">
                <i class="bi bi-x-lg text-xs"></i>
            </button>
        </div>
        @if($errors->any())
        <ul class="list-disc list-inside text-xs font-normal pl-6 mt-1 space-y-0.5 text-[#B91C1C]">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        @endif
    </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-xs font-semibold tracking-wider text-[#617068] uppercase">
                MASTER DATA
            </div>
            <h1 class="text-2xl font-bold text-[#102D21] tracking-tight mt-0.5">
                Area parkir
            </h1>
            <p class="text-sm text-[#617068] mt-0.5">
                Kelola area dan kapasitas berdasarkan jenis kendaraan.
            </p>
        </div>

        <div>
            <button type="button" onclick="openCreateModal()"
                    class="inline-flex items-center gap-2 bg-[#173F2E] hover:bg-[#234D39] text-white text-sm font-semibold rounded-xl px-4 py-2.5 shadow-xs transition-colors">
                <i class="bi bi-plus-lg font-bold"></i>
                <span>Tambah area</span>
            </button>
        </div>
    </div>

    {{-- 3 KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Card 1: Total Area Aktif --}}
        <div class="bg-white border border-[#E0E5E2] rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <span class="text-xs font-medium text-[#617068]">Total area aktif</span>
            <div class="my-2">
                <span class="text-2xl font-bold text-[#102D21] tracking-tight">{{ $activeAreasCount }} area</span>
            </div>
            <span class="text-xs text-[#859089]">
                {{ $activeAreasCount === $totalAreasCount ? 'Seluruh area beroperasi' : ($activeAreasCount . ' dari ' . $totalAreasCount . ' area beroperasi') }}
            </span>
        </div>

        {{-- Card 2: Kapasitas Mobil --}}
        <div class="bg-white border border-[#E0E5E2] rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <span class="text-xs font-medium text-[#617068]">Kapasitas mobil</span>
            <div class="my-2">
                <span class="text-2xl font-bold text-[#102D21] tracking-tight">{{ $totalCarCapacity }} slot</span>
            </div>
            <span class="text-xs text-[#859089]">
                {{ $totalCarOccupied }} terisi · {{ $totalCarAvailable }} tersedia
            </span>
        </div>

        {{-- Card 3: Kapasitas Motor --}}
        <div class="bg-white border border-[#E0E5E2] rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <span class="text-xs font-medium text-[#617068]">Kapasitas motor</span>
            <div class="my-2">
                <span class="text-2xl font-bold text-[#102D21] tracking-tight">{{ $totalMotorcycleCapacity }} slot</span>
            </div>
            <span class="text-xs text-[#859089]">
                {{ $totalMotorcycleOccupied }} terisi · {{ $totalMotorcycleAvailable }} tersedia
            </span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-[#E0E5E2] rounded-2xl shadow-xs overflow-hidden">
        {{-- Table Toolbar --}}
        <div class="p-5 border-b border-[#E0E5E2] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            {{-- Search Bar --}}
            <div class="relative w-full sm:w-80">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-[#859089] text-sm pointer-events-none"></i>
                <input type="text" id="areaSearchInput" onkeyup="filterAreasTable()"
                       placeholder="Cari area..."
                       class="w-full pl-10 pr-4 py-2 bg-white border border-[#E0E5E2] rounded-xl text-sm text-[#102D21] placeholder-[#859089] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
            </div>

            {{-- Filter Status Dropdown --}}
            <div class="relative inline-block text-left">
                <button type="button" id="filterBtn" onclick="toggleFilterMenu()"
                        class="inline-flex items-center gap-2 border border-[#E0E5E2] rounded-xl px-4 py-2 text-sm font-semibold text-[#102D21] hover:bg-[#FAFBFA] transition-colors">
                    <i class="bi bi-sliders text-sm text-[#617068]"></i>
                    <span id="activeFilterLabel">Filter</span>
                    <i class="bi bi-chevron-down text-xs text-[#859089]"></i>
                </button>

                <div id="filterMenu" class="hidden absolute right-0 mt-2 w-44 bg-white border border-[#E0E5E2] rounded-xl shadow-lg py-1.5 z-30">
                    <button type="button" onclick="setAreaStatusFilter('all', 'Semua')"
                            class="w-full text-left px-4 py-2 text-xs font-semibold text-[#102D21] hover:bg-[#F4F6F4] flex items-center justify-between">
                        <span>Semua Status</span>
                        <i id="check_all" class="bi bi-check-lg text-[#173F2E]"></i>
                    </button>
                    <button type="button" onclick="setAreaStatusFilter('active', 'Aktif')"
                            class="w-full text-left px-4 py-2 text-xs font-semibold text-[#102D21] hover:bg-[#F4F6F4] flex items-center justify-between">
                        <span>Aktif</span>
                        <i id="check_active" class="bi bi-check-lg text-[#173F2E] hidden"></i>
                    </button>
                    <button type="button" onclick="setAreaStatusFilter('inactive', 'Nonaktif')"
                            class="w-full text-left px-4 py-2 text-xs font-semibold text-[#102D21] hover:bg-[#F4F6F4] flex items-center justify-between">
                        <span>Nonaktif</span>
                        <i id="check_inactive" class="bi bi-check-lg text-[#173F2E] hidden"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="areasTable">
                <thead>
                    <tr class="border-b border-[#E0E5E2] bg-[#FAFBFA]/60">
                        <th class="py-3.5 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">
                            KODE & AREA
                        </th>
                        <th class="py-3.5 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">
                            MOBIL
                        </th>
                        <th class="py-3.5 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">
                            MOTOR
                        </th>
                        <th class="py-3.5 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">
                            OKUPANSI
                        </th>
                        <th class="py-3.5 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase">
                            STATUS
                        </th>
                        <th class="py-3.5 px-6 text-[11px] font-bold tracking-wider text-[#859089] uppercase text-right">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E5E2]">
                    @forelse($areas as $area)
                    @php
                        // Split code and name if already formatted (e.g. "A-01 Basement 1")
                        $parts = explode(' ', $area->name, 2);
                        $hasCode = count($parts) === 2 && preg_match('/^[A-Za-z0-9\-]+$/', $parts[0]);
                        $displayCode = $hasCode ? $parts[0] : ('A-' . sprintf('%02d', $area->id));
                        $displayName = $hasCode ? $parts[1] : $area->name;

                        $occCar = (int) ($area->occupied_car ?? 0);
                        $capCar = (int) $area->capacity_car;
                        $occMotor = (int) ($area->occupied_motorcycle ?? 0);
                        $capMotor = (int) $area->capacity_motorcycle;

                        $totalCap = $capCar + $capMotor;
                        $totalOcc = $occCar + $occMotor;
                        $percent = $totalCap > 0 ? (int) round(($totalOcc / $totalCap) * 100) : 0;
                        if ($percent > 100) $percent = 100;
                    @endphp
                    <tr class="hover:bg-[#FAFBFA] transition-colors area-row"
                        data-name="{{ strtolower($area->name . ' ' . $displayCode . ' ' . ($area->location ?? '')) }}"
                        data-status="{{ $area->is_active ? 'active' : 'inactive' }}">

                        {{-- 1. Kode & Area --}}
                        <td class="py-4 px-6 align-middle">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-[#102D21]">{{ $displayCode }}</span>
                                <span class="text-sm text-[#617068]">{{ $displayName }}</span>
                            </div>
                            @if($area->location)
                            <div class="text-xs text-[#859089] mt-0.5">
                                {{ $area->location }}
                            </div>
                            @endif
                        </td>

                        {{-- 2. Mobil (terisi / kapasitas) --}}
                        <td class="py-4 px-6 align-middle text-sm">
                            <span class="font-bold text-[#102D21]">{{ $occCar }}</span>
                            <span class="text-[#617068]"> / {{ $capCar }}</span>
                        </td>

                        {{-- 3. Motor (terisi / kapasitas) --}}
                        <td class="py-4 px-6 align-middle text-sm">
                            <span class="font-bold text-[#102D21]">{{ $occMotor }}</span>
                            <span class="text-[#617068]"> / {{ $capMotor }}</span>
                        </td>

                        {{-- 4. Okupansi Progress Bar & Percent --}}
                        <td class="py-4 px-6 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-24 h-1.5 bg-[#EEF2EF] rounded-full overflow-hidden">
                                    <div class="h-full bg-[#173F2E] rounded-full transition-all duration-500"
                                         style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-[#102D21] min-w-[36px]">{{ $percent }}%</span>
                            </div>
                        </td>

                        {{-- 5. Status Badge --}}
                        <td class="py-4 px-6 align-middle">
                            @if($area->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#EDF5D6] text-[#536B20]">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#EEF2EF] text-[#617068]">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        {{-- 6. Aksi (Three Dots Dropdown) --}}
                        <td class="py-4 px-6 align-middle text-right relative">
                            <div class="inline-block text-left dropdown-container">
                                <button type="button" onclick="toggleActionMenu(event, 'menu_{{ $area->id }}')"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-[#859089] hover:text-[#102D21] hover:bg-[#EEF2EF] transition-colors font-bold text-base tracking-tighter">
                                    •••
                                </button>

                                <div id="menu_{{ $area->id }}" class="hidden absolute right-6 top-10 w-52 bg-white border border-[#E0E5E2] rounded-xl shadow-lg py-1.5 z-30 text-left">
                                    {{-- Edit Option --}}
                                    <button type="button"
                                            onclick="openEditModal({
                                                id: {{ $area->id }},
                                                code: '{{ addslashes($displayCode) }}',
                                                name: '{{ addslashes($displayName) }}',
                                                location: '{{ addslashes($area->location ?? '') }}',
                                                cap_car: {{ $capCar }},
                                                cap_motor: {{ $capMotor }},
                                                occ_car: {{ $occCar }},
                                                occ_motor: {{ $occMotor }},
                                                is_active: {{ $area->is_active ? 1 : 0 }},
                                                update_url: '{{ route('parking-areas.update', $area) }}'
                                            })"
                                            class="w-full px-4 py-2.5 text-xs font-semibold text-[#102D21] hover:bg-[#F4F6F4] flex items-center gap-2.5 transition-colors">
                                        <i class="bi bi-pencil text-sm text-[#617068]"></i>
                                        <span>Edit Area & Kapasitas</span>
                                    </button>

                                    {{-- Toggle Active / Inactive Option --}}
                                    <form method="POST" action="{{ route('parking-areas.update', $area) }}" class="m-0">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" value="{{ $area->name }}">
                                        <input type="hidden" name="location" value="{{ $area->location }}">
                                        <input type="hidden" name="capacity_car" value="{{ $area->capacity_car }}">
                                        <input type="hidden" name="capacity_motorcycle" value="{{ $area->capacity_motorcycle }}">
                                        <input type="hidden" name="is_active" value="{{ $area->is_active ? 0 : 1 }}">
                                        <button type="submit"
                                                class="w-full text-left px-4 py-2.5 text-xs font-semibold text-[#102D21] hover:bg-[#F4F6F4] flex items-center gap-2.5 transition-colors">
                                            <i class="bi {{ $area->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} text-sm text-[#617068]"></i>
                                            <span>{{ $area->is_active ? 'Nonaktifkan Area' : 'Aktifkan Area' }}</span>
                                        </button>
                                    </form>

                                    <div class="h-px bg-[#E0E5E2] my-1"></div>

                                    {{-- Delete Option --}}
                                    <form method="POST" action="{{ route('parking-areas.destroy', $area) }}"
                                          onsubmit="return confirm('Hapus area parkir ini? Jika area memiliki riwayat transaksi, area akan dinonaktifkan secara aman.');"
                                          class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full text-left px-4 py-2.5 text-xs font-semibold text-[#DC2626] hover:bg-[#FEF2F2] flex items-center gap-2.5 transition-colors">
                                            <i class="bi bi-trash3 text-sm text-[#DC2626]"></i>
                                            <span>Hapus Area</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyTableRow">
                        <td colspan="6" class="py-12 text-center text-[#859089]">
                            <i class="bi bi-inbox text-3xl mb-2 block text-[#A0AAA4]"></i>
                            <p class="text-sm font-semibold text-[#102D21]">Belum ada data area parkir</p>
                            <p class="text-xs text-[#617068] mt-1">Silakan klik tombol "Tambah area" untuk menambahkan area baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH AREA --}}
<div id="createAreaModal" class="hidden fixed inset-0 bg-[#102D21]/50 backdrop-blur-xs flex items-center justify-center z-50 p-4 transition-opacity">
    <div class="bg-white border border-[#E0E5E2] rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-[#E0E5E2] flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-[#102D21]">Tambah area parkir</h3>
                <p class="text-xs text-[#617068] mt-0.5">Masukkan data area dan kapasitas kendaraan.</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-[#859089] hover:text-[#102D21] hover:bg-[#F4F6F4] transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- Form Tambah --}}
        <form method="POST" action="{{ route('parking-areas.store') }}" id="createAreaForm" onsubmit="syncCreateNameField()">
            @csrf
            <input type="hidden" name="name" id="create_final_name">

            <div class="p-6 space-y-4">
                {{-- Kode & Nama Area --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                            Kode Area <span class="text-[#EA6546]">*</span>
                        </label>
                        <input type="text" id="create_code" name="code" placeholder="E-01" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm font-bold uppercase text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                            Nama Area <span class="text-[#EA6546]">*</span>
                        </label>
                        <input type="text" id="create_name_input" name="name_input" placeholder="Gedung Barat" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm font-medium text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                </div>

                {{-- Deskripsi / Lokasi --}}
                <div>
                    <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                        Lokasi / Deskripsi <span class="text-[#859089] font-normal">(opsional)</span>
                    </label>
                    <input type="text" name="location" placeholder="Contoh: Sisi Barat Lantai Basement"
                           class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                </div>

                {{-- Kapasitas Mobil & Motor --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                            Kapasitas Mobil <span class="text-[#EA6546]">*</span>
                        </label>
                        <input type="number" name="capacity_car" min="0" value="0" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm font-bold text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                            Kapasitas Motor <span class="text-[#EA6546]">*</span>
                        </label>
                        <input type="number" name="capacity_motorcycle" min="0" value="0" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm font-bold text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                </div>

                {{-- Status Aktif Toggle --}}
                <div class="pt-2 flex items-center justify-between border-t border-[#E0E5E2]">
                    <div>
                        <span class="text-xs font-semibold text-[#102D21] block">Status Area Langsung Aktif</span>
                        <span class="text-[11px] text-[#617068]">Area aktif dapat langsung menerima alokasi kendaraan.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-[#E0E5E2] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#173F2E]"></div>
                    </label>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-[#FAFBFA] border-t border-[#E0E5E2] flex items-center justify-end gap-3">
                <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2.5 rounded-xl border border-[#E0E5E2] bg-white text-xs font-semibold text-[#102D21] hover:bg-[#F4F6F4] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#173F2E] hover:bg-[#234D39] text-xs font-semibold text-white shadow-xs transition-colors">
                    Simpan Area
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT AREA --}}
<div id="editAreaModal" class="hidden fixed inset-0 bg-[#102D21]/50 backdrop-blur-xs flex items-center justify-center z-50 p-4 transition-opacity">
    <div class="bg-white border border-[#E0E5E2] rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-[#E0E5E2] flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-[#102D21]">Edit area & kapasitas</h3>
                <p class="text-xs text-[#617068] mt-0.5">Perbarui nama atau sesuaikan batas kapasitas.</p>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-[#859089] hover:text-[#102D21] hover:bg-[#F4F6F4] transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        {{-- Form Edit --}}
        <form method="POST" id="editAreaForm" onsubmit="syncEditNameField()">
            @csrf
            @method('PUT')
            <input type="hidden" name="name" id="edit_final_name">

            <div class="p-6 space-y-4">
                {{-- Occupied Notice Banner --}}
                <div class="p-3.5 bg-[#F1F7E4] border border-[#B8CF54] rounded-xl flex items-start gap-2.5 text-xs text-[#173F2E]">
                    <i class="bi bi-info-circle-fill text-sm text-[#536B20] shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold">Informasi Kendaraan Aktif:</span><br>
                        Saat ini terisi: <strong id="editOccupiedCarText">0</strong> Mobil dan <strong id="editOccupiedMotorText">0</strong> Motor.
                        Kapasitas baru <span class="underline">tidak boleh lebih kecil</span> dari jumlah kendaraan yang sedang terparkir.
                    </div>
                </div>

                {{-- Kode & Nama Area --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                            Kode Area <span class="text-[#EA6546]">*</span>
                        </label>
                        <input type="text" id="edit_code" name="code" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm font-bold uppercase text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                            Nama Area <span class="text-[#EA6546]">*</span>
                        </label>
                        <input type="text" id="edit_name_input" name="name_input" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm font-medium text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                </div>

                {{-- Deskripsi / Lokasi --}}
                <div>
                    <label class="block text-xs font-semibold text-[#102D21] mb-1.5">
                        Lokasi / Deskripsi <span class="text-[#859089] font-normal">(opsional)</span>
                    </label>
                    <input type="text" id="edit_location" name="location"
                           class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                </div>

                {{-- Kapasitas Mobil & Motor --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-[#102D21]">
                                Kapasitas Mobil <span class="text-[#EA6546]">*</span>
                            </label>
                            <span id="editMinCarLabel" class="text-[10px] text-[#859089]">Min: 0</span>
                        </div>
                        <input type="number" id="edit_cap_car" name="capacity_car" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm font-bold text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-semibold text-[#102D21]">
                                Kapasitas Motor <span class="text-[#EA6546]">*</span>
                            </label>
                            <span id="editMinMotorLabel" class="text-[10px] text-[#859089]">Min: 0</span>
                        </div>
                        <input type="number" id="edit_cap_motor" name="capacity_motorcycle" required
                               class="w-full px-3.5 py-2.5 bg-white border border-[#E0E5E2] rounded-xl text-sm font-bold text-[#102D21] focus:outline-none focus:ring-1 focus:ring-[#173F2E]">
                    </div>
                </div>

                {{-- Status Aktif Toggle --}}
                <div class="pt-2 flex items-center justify-between border-t border-[#E0E5E2]">
                    <div>
                        <span class="text-xs font-semibold text-[#102D21] block">Status Area Aktif</span>
                        <span class="text-[11px] text-[#617068]">Nonaktifkan jika area sedang dalam renovasi atau ditutup.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="sr-only peer">
                        <div class="w-11 h-6 bg-[#E0E5E2] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#173F2E]"></div>
                    </label>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-[#FAFBFA] border-t border-[#E0E5E2] flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2.5 rounded-xl border border-[#E0E5E2] bg-white text-xs font-semibold text-[#102D21] hover:bg-[#F4F6F4] transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#173F2E] hover:bg-[#234D39] text-xs font-semibold text-white shadow-xs transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // ─── Filter Status State ───────────────────────────────────────────────────
    let currentStatusFilter = 'all';

    function toggleFilterMenu() {
        const menu = document.getElementById('filterMenu');
        menu.classList.toggle('hidden');
    }

    function setAreaStatusFilter(status, label) {
        currentStatusFilter = status;
        document.getElementById('activeFilterLabel').innerText = label;

        // Toggle checkmarks
        document.getElementById('check_all').classList.toggle('hidden', status !== 'all');
        document.getElementById('check_active').classList.toggle('hidden', status !== 'active');
        document.getElementById('check_inactive').classList.toggle('hidden', status !== 'inactive');

        document.getElementById('filterMenu').classList.add('hidden');
        filterAreasTable();
    }

    // ─── Table Filter & Search ────────────────────────────────────────────────
    function filterAreasTable() {
        const query = (document.getElementById('areaSearchInput').value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.area-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowText = row.getAttribute('data-name') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchesQuery = query === '' || rowText.includes(query);
            const matchesStatus = currentStatusFilter === 'all' || rowStatus === currentStatusFilter;

            if (matchesQuery && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Manage empty table state
        let emptyRow = document.getElementById('emptyTableRowDynamic');
        if (visibleCount === 0 && rows.length > 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.id = 'emptyTableRowDynamic';
                emptyRow.innerHTML = `
                    <td colspan="6" class="py-10 text-center text-[#859089]">
                        <i class="bi bi-search text-2xl mb-1 block text-[#A0AAA4]"></i>
                        <p class="text-sm font-semibold text-[#102D21]">Tidak ada area parkir yang cocok</p>
                        <p class="text-xs text-[#617068] mt-0.5">Coba ubah kata kunci pencarian atau filter status.</p>
                    </td>
                `;
                document.querySelector('#areasTable tbody').appendChild(emptyRow);
            }
            emptyRow.style.display = '';
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }

    // ─── Action Dropdown Menu ─────────────────────────────────────────────────
    function toggleActionMenu(event, menuId) {
        event.stopPropagation();
        const targetMenu = document.getElementById(menuId);
        const allMenus = document.querySelectorAll('.dropdown-container > div');

        allMenus.forEach(m => {
            if (m.id !== menuId) m.classList.add('hidden');
        });

        if (targetMenu) {
            targetMenu.classList.toggle('hidden');
        }
    }

    // Close open menus on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-container > div').forEach(m => m.classList.add('hidden'));
        }
        if (!e.target.closest('#filterBtn') && !e.target.closest('#filterMenu')) {
            const filterMenu = document.getElementById('filterMenu');
            if (filterMenu) filterMenu.classList.add('hidden');
        }
    });

    // ─── Modal Create ────────────────────────────────────────────────────────
    function openCreateModal() {
        document.getElementById('createAreaModal').classList.remove('hidden');
        document.getElementById('create_code').focus();
    }

    function closeCreateModal() {
        document.getElementById('createAreaModal').classList.add('hidden');
    }

    function syncCreateNameField() {
        const code = (document.getElementById('create_code').value || '').trim();
        const nameInput = (document.getElementById('create_name_input').value || '').trim();
        const fullName = code ? (code + ' ' + nameInput) : nameInput;
        document.getElementById('create_final_name').value = fullName;
    }

    // ─── Modal Edit ──────────────────────────────────────────────────────────
    function openEditModal(data) {
        // Close all dropdowns
        document.querySelectorAll('.dropdown-container > div').forEach(m => m.classList.add('hidden'));

        const form = document.getElementById('editAreaForm');
        form.action = data.update_url;

        document.getElementById('edit_code').value = data.code;
        document.getElementById('edit_name_input').value = data.name;
        document.getElementById('edit_location').value = data.location;
        document.getElementById('edit_cap_car').value = data.cap_car;
        document.getElementById('edit_cap_motor').value = data.cap_motor;

        // Set min capacity from occupied
        document.getElementById('edit_cap_car').min = data.occ_car;
        document.getElementById('edit_cap_motor').min = data.occ_motor;

        document.getElementById('editMinCarLabel').innerText = `Min: ${data.occ_car} (terparkir)`;
        document.getElementById('editMinMotorLabel').innerText = `Min: ${data.occ_motor} (terparkir)`;

        document.getElementById('editOccupiedCarText').innerText = data.occ_car;
        document.getElementById('editOccupiedMotorText').innerText = data.occ_motor;

        document.getElementById('edit_is_active').checked = (data.is_active == 1);

        document.getElementById('editAreaModal').classList.remove('hidden');
        document.getElementById('edit_name_input').focus();
    }

    function closeEditModal() {
        document.getElementById('editAreaModal').classList.add('hidden');
    }

    function syncEditNameField() {
        const code = (document.getElementById('edit_code').value || '').trim();
        const nameInput = (document.getElementById('edit_name_input').value || '').trim();
        const fullName = code ? (code + ' ' + nameInput) : nameInput;
        document.getElementById('edit_final_name').value = fullName;
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush
@endsection
