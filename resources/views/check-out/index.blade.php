@extends('layouts.app')

@section('title', 'Check-Out Kendaraan — ParkirPintar')

@push('styles')
<style>
    /* Styling khusus scanner camera */
    #cameraReader video {
        border-radius: 0.75rem;
        object-fit: cover;
        width: 100% !important;
    }
    #cameraReader {
        width: 100% !important;
        border: none !important;
    }
    #cameraReader__scan_region {
        background: transparent !important;
    }
    #cameraReader__scan_region img {
        display: none !important;
    }
</style>
@endpush

@section('content')
{{-- Header Halaman --}}
<div class="mb-6">
    <div class="text-xs font-bold tracking-widest text-[#617068] uppercase mb-1">
        OPERASIONAL
    </div>
    <h1 class="text-2xl font-extrabold text-[#102D21] tracking-tight mb-1">
        Check-out kendaraan
    </h1>
    <p class="text-xs text-[#617068] font-medium m-0">
        Pindai barcode karcis atau cari tiket secara manual.
    </p>
</div>

{{-- Grid Utama 2 Kolom --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    {{-- Kolom Kiri: Pindai Barcode & Cari Manual (5 Kolom) --}}
    <div class="lg:col-span-5 flex flex-col gap-5">
        <div class="bg-white border border-[#E0E5E2] rounded-2xl p-6 shadow-xs">
            {{-- Kotak Scanner Putus-putus --}}
            <div class="border-2 border-dashed border-[#D9DFDC] rounded-2xl p-6 text-center bg-[#FAFBFA]">
                <div class="w-12 h-12 rounded-xl bg-[#173F2E] text-[#D5F067] flex items-center justify-center text-xl mx-auto mb-3 shadow-2xs">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
                <h3 class="text-sm font-bold text-[#102D21] mb-1">Pindai barcode karcis</h3>
                <p class="text-xs text-[#8A958F] font-medium max-w-xs mx-auto mb-4 leading-relaxed">
                    Arahkan barcode ke scanner atau gunakan kamera perangkat.
                </p>

                {{-- Area Kamera WebCam --}}
                <div id="cameraContainer" class="hidden mb-3">
                    {{-- Status scan success banner --}}
                    <div id="scanSuccessBanner" class="hidden p-3 bg-[#EDF5D6] border border-[#B8CF54] rounded-xl text-center mb-3">
                        <div class="text-xs font-bold text-[#173F2E] flex items-center justify-center gap-1.5">
                            <i class="bi bi-check-circle-fill text-[#536B20]"></i>
                            <span>Tiket terdeteksi: <strong id="detectedCodeText"></strong></span>
                        </div>
                        <p class="text-[10px] text-[#536B20] mt-0.5">Memuat detail tagihan & tarif...</p>
                    </div>

                    {{-- Loading Indicator saat kamera menginisialisasi --}}
                    <div id="cameraLoading" class="p-5 text-center bg-[#FAFBFA] rounded-xl border border-[#E0E5E2] mb-3">
                        <div class="w-7 h-7 border-3 border-[#173F2E] border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                        <p class="text-xs font-bold text-[#102D21]">Mengakses kamera...</p>
                        <p class="text-[11px] text-[#8A958F] mt-0.5">Izinkan akses kamera di peramban jika muncul konfirmasi.</p>
                    </div>

                    {{-- Error Notice jika izin diblokir atau kamera tidak ada --}}
                    <div id="cameraError" class="hidden p-4 bg-[#FBEDE4] border border-[#EA6546] rounded-xl text-left mb-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#EA6546] mb-1">
                            <i class="bi bi-exclamation-triangle-fill"></i> Kamera Tidak Dapat Diakses
                        </div>
                        <p id="cameraErrorMsg" class="text-[11px] text-[#102D21] leading-relaxed"></p>
                    </div>

                    {{-- Viewfinder Container html5-qrcode --}}
                    <div class="relative w-full max-w-sm mx-auto overflow-hidden rounded-xl border border-[#E0E5E2] bg-black">
                        <div id="reader" class="w-full"></div>
                    </div>

                    <button type="button"
                            id="btnStopCamera"
                            onclick="stopCameraScanner()"
                            class="mt-3 px-4 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs">
                        <i class="bi bi-x-circle"></i>
                        <span>Tutup kamera</span>
                    </button>
                </div>

                {{-- Tombol Buka Kamera --}}
                <button type="button"
                        id="btnStartCamera"
                        onclick="startCameraScanner()"
                        class="bg-[#173F2E] hover:bg-[#234D39] text-white rounded-xl px-5 py-2.5 font-bold text-xs inline-flex items-center gap-2 mx-auto cursor-pointer transition shadow-2xs active:scale-98">
                    <i class="bi bi-camera-fill text-sm"></i>
                    <span>Aktifkan kamera</span>
                </button>

                {{-- Opsi Upload / Pilih Foto Karcis --}}
                <div class="mt-3 text-center">
                    <input type="file" id="ticketFileInput" accept="image/*" class="hidden" onchange="handleTicketFileUpload(this)">
                    <button type="button"
                            onclick="document.getElementById('ticketFileInput').click()"
                            class="text-xs font-semibold text-[#173F2E] hover:text-[#234D39] inline-flex items-center gap-1.5 hover:underline transition">
                        <i class="bi bi-upload text-sm"></i>
                        <span>Atau unggah / pilih foto karcis</span>
                    </button>
                    <div id="fileScanStatus" class="hidden text-[11px] font-semibold text-[#173F2E] mt-1.5 flex items-center justify-center gap-1.5">
                        <div class="w-3.5 h-3.5 border-2 border-[#173F2E] border-t-transparent rounded-full animate-spin"></div>
                        <span>Memindai gambar karcis...</span>
                    </div>
                </div>

                {{-- Element Helper Hidden untuk scan file --}}
                <div id="readerFileHelper" class="hidden"></div>
            </div>

            {{-- Divider Pemisah --}}
            <div class="relative flex py-5 items-center">
                <div class="flex-grow border-t border-[#E0E5E2]"></div>
                <span class="flex-shrink mx-4 text-[10px] font-bold text-[#8A958F] tracking-widest uppercase">
                    ATAU CARI MANUAL
                </span>
                <div class="flex-grow border-t border-[#E0E5E2]"></div>
            </div>

            {{-- Form Input Manual --}}
            <form method="GET" action="{{ route('check-out.search') }}" id="searchForm">
                <label for="searchInput" class="block text-xs font-bold text-[#102D21] mb-2">
                    Nomor tiket / plat nomor
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1 flex items-center">
                        <i class="bi bi-search absolute left-3.5 text-[#8A958F] text-xs"></i>
                        <input type="text"
                               id="searchInput"
                               name="search"
                               value="{{ $search ?? '' }}"
                               placeholder="Contoh: PP-240618-0840"
                               autofocus
                               autocomplete="off"
                               oninput="this.value = this.value.toUpperCase()"
                               class="w-full rounded-xl border border-[#E0E5E2] pl-9 pr-3 py-2.5 text-xs font-bold uppercase text-[#102D21] placeholder-[#8A958F] focus:outline-none focus:border-[#173F2E] focus:ring-2 focus:ring-[#173F2E]/10 transition">
                    </div>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#173F2E] hover:bg-[#234D39] text-white text-xs font-bold transition shadow-2xs active:scale-98">
                        Cari
                    </button>
                </div>
            </form>

            {{-- Alert Error jika tidak ditemukan --}}
            @if(isset($error) && $error)
                <div class="bg-[#FBEDE4] border border-[#EA6546] text-[#102D21] rounded-xl p-3.5 text-xs font-semibold mt-4 flex items-center gap-2.5 shadow-2xs">
                    <i class="bi bi-exclamation-circle-fill text-[#EA6546] text-sm flex-shrink-0"></i>
                    <span>{{ $error }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Detail Tiket, Pembayaran & Tagihan (7 Kolom) --}}
    <div class="lg:col-span-7">
        @if(!isset($transaction) || !$transaction)
            {{-- State A: Sebelum Tiket Dipilih (Empty State) --}}
            <div class="bg-white border border-[#E0E5E2] rounded-2xl flex flex-col items-center justify-center p-8 text-center min-h-[460px] h-full shadow-xs">
                <div class="w-14 h-14 rounded-full bg-[#EEF2EF] text-[#8A958F] flex items-center justify-center text-2xl mb-4">
                    <i class="bi bi-search"></i>
                </div>
                <h3 class="text-base font-bold text-[#102D21] mb-1">Belum ada tiket dipilih</h3>
                <p class="text-xs text-[#8A958F] font-medium max-w-xs leading-relaxed m-0">
                    Detail durasi dan tagihan akan muncul di sini.
                </p>
            </div>
        @else
            {{-- State B: Setelah Tiket Ditemukan --}}
            <div class="bg-white border border-[#E0E5E2] rounded-2xl p-6 shadow-xs">
                {{-- Header Tiket --}}
                <div class="flex items-start justify-between mb-5 pb-5 border-b border-[#F4F6F4]">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#EDF5D6] text-[#536B20] uppercase mb-1.5">
                            TIKET AKTIF
                        </span>
                        <h2 class="text-2xl font-extrabold text-[#102D21] tracking-wide m-0">
                            {{ $transaction->license_plate }}
                        </h2>
                        <div class="text-xs text-[#8A958F] font-medium mt-0.5">
                            <span class="font-mono">{{ $transaction->ticket_number }}</span> ·
                            <span>{{ $transaction->vehicle_type === 'car' ? 'Mobil' : 'Motor' }}</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-[#EEF2EF] text-[#173F2E] flex items-center justify-center text-lg shadow-2xs">
                        @if($transaction->vehicle_type === 'car')
                            <i class="bi bi-car-front-fill"></i>
                        @else
                            <i class="bi bi-bicycle"></i>
                        @endif
                    </div>
                </div>

                {{-- Grid Info Waktu & Durasi --}}
                <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-left mb-5 pb-5 border-b border-[#F4F6F4]">
                    <div>
                        <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">AREA PARKIR</div>
                        <div class="text-xs font-bold text-[#102D21]">{{ $transaction->parkingArea->name ?? 'Zona A1 · Basement 1' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">WAKTU MASUK</div>
                        <div class="text-xs font-bold text-[#102D21]">
                            {{ $previewData['checkIn']->translatedFormat('d M Y, H.i') }} WIB
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">WAKTU KELUAR · OTOMATIS SERVER WIB</div>
                        <div class="text-xs font-bold text-[#102D21]">
                            {{ $previewData['now']->translatedFormat('d M Y, H.i') }} WIB
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">TOTAL DURASI</div>
                        <div class="text-xs font-bold text-[#102D21]">{{ $previewData['formattedDuration'] }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">JAM TERTAGIH</div>
                        <div class="text-xs font-bold text-[#102D21]">{{ $previewData['billableHours'] }} jam</div>
                    </div>
                </div>

                {{-- Banner Total Tagihan Gelap --}}
                <div class="bg-[#173F2E] text-white rounded-2xl p-5 mb-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-300 font-medium">Total tagihan</span>
                        <span class="text-2xl font-black text-white tracking-tight">
                            Rp{{ number_format($previewData['fee'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-[#D5F067] font-semibold mt-3 pt-2.5 border-t border-[#234D39]">
                        <span>
                            @if($previewData['fee'] == 0)
                                Gratis (Grace Period ≤ 15 menit)
                            @else
                                Jam pertama Rp{{ number_format($previewData['firstHourRate'], 0, ',', '.') }}
                            @endif
                        </span>
                        <span>
                            @if($previewData['fee'] > 0 && $previewData['billableHours'] > 1)
                                {{ $previewData['billableHours'] - 1 }} × Rp{{ number_format($previewData['nextHourRate'], 0, ',', '.') }}
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Pilih Metode Pembayaran (Hanya 2 Opsi) --}}
                <div class="mb-5">
                    <label class="block text-xs font-bold text-[#102D21] mb-2.5">Pilih metode pembayaran</label>
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Tunai --}}
                        <div id="btnPayTunai"
                             onclick="selectPaymentMethod('tunai')"
                             class="cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-[#EFF6FF] border-[#2563EB]">
                            <div id="checkTunai" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#2563EB] text-white flex items-center justify-center text-[10px]">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div class="text-[#2563EB] text-2xl flex-shrink-0">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-[#102D21]">Tunai</div>
                                <div class="text-[11px] text-[#8A958F] font-semibold">Uang cash</div>
                            </div>
                        </div>

                        {{-- QRIS --}}
                        <div id="btnPayQRIS"
                             onclick="selectPaymentMethod('qris')"
                             class="cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-white border-[#E0E5E2] hover:border-[#2563EB]/40">
                            <div id="checkQRIS" class="hidden absolute top-3 right-3 w-5 h-5 rounded-full bg-[#2563EB] text-white flex items-center justify-center text-[10px]">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div class="text-[#102D21] text-2xl flex-shrink-0">
                                <i class="bi bi-qr-code"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-[#102D21]">QRIS</div>
                                <div class="text-[11px] text-[#8A958F] font-semibold">Scan QR</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Konfirmasi Check-Out --}}
                <form method="POST" action="{{ route('check-out.process') }}" id="checkoutProcessForm">
                    @csrf
                    <input type="hidden" name="search" value="{{ $transaction->ticket_number }}">
                    <input type="hidden" name="payment_method" id="selectedPaymentMethod" value="Tunai">

                    {{-- Panel Tunai --}}
                    <div id="panelTunai" class="border border-[#E0E5E2] rounded-2xl p-4 bg-white mb-2 shadow-2xs">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#F4F6F4]">
                            <span class="text-xs font-bold text-[#617068]">Total Tagihan</span>
                            <span class="text-base font-extrabold text-[#102D21]">
                                Rp{{ number_format($previewData['fee'], 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <label for="cashReceived" class="block text-xs font-bold text-[#102D21] mb-1.5">
                                Uang Diterima (Rp)
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-xs font-bold text-[#8A958F]">Rp</span>
                                <input type="number"
                                       id="cashReceived"
                                       name="cash_received"
                                       value="{{ $previewData['fee'] }}"
                                       min="{{ $previewData['fee'] }}"
                                       oninput="calculateChange()"
                                       class="w-full rounded-xl border border-[#E0E5E2] pl-10 pr-3 py-2.5 text-sm font-bold text-[#102D21] focus:outline-none focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/10 transition">
                            </div>
                        </div>

                        {{-- Tombol Nominal Cepat --}}
                        <div class="flex items-center gap-2 mb-3">
                            <button type="button"
                                    onclick="setNominal({{ $previewData['fee'] }})"
                                    class="px-2.5 py-1 rounded-lg border border-[#E0E5E2] text-[11px] font-bold text-[#617068] hover:bg-gray-50 transition">
                                Uang Pas Rp{{ number_format($previewData['fee'], 0, ',', '.') }}
                            </button>
                            @php
                                $round20k = max(20000, ceil($previewData['fee'] / 10000) * 10000);
                                $round50k = max(50000, ceil($previewData['fee'] / 50000) * 50000);
                            @endphp
                            <button type="button"
                                    onclick="setNominal({{ $round20k }})"
                                    class="px-2.5 py-1 rounded-lg border border-[#E0E5E2] text-[11px] font-bold text-[#617068] hover:bg-gray-50 transition">
                                Rp{{ number_format($round20k, 0, ',', '.') }}
                            </button>
                            <button type="button"
                                    onclick="setNominal({{ $round50k }})"
                                    class="px-2.5 py-1 rounded-lg border border-[#E0E5E2] text-[11px] font-bold text-[#617068] hover:bg-gray-50 transition">
                                Rp{{ number_format($round50k, 0, ',', '.') }}
                            </button>
                        </div>

                        {{-- Kotak Kembalian --}}
                        <div class="bg-[#EDF5D6] border border-[#C4DA65] rounded-xl p-3.5 flex items-center justify-between">
                            <span class="text-xs font-bold text-[#536B20]">Kembalian</span>
                            <span id="changeDisplay" class="text-base font-extrabold text-[#102D21]">
                                Rp0
                            </span>
                        </div>

                        <button type="submit"
                                id="btnSubmitTunai"
                                class="w-full mt-4 py-3.5 rounded-xl font-bold text-xs bg-[#00875A] hover:bg-[#006644] text-white flex items-center justify-center gap-2 shadow-xs transition active:scale-98">
                            <i class="bi bi-check-lg text-sm"></i>
                            <span>Konfirmasi Pembayaran Tunai & Selesaikan Check-Out</span>
                        </button>
                    </div>

                    {{-- Panel QRIS --}}
                    <div id="panelQRIS" class="hidden border border-[#E0E5E2] rounded-2xl p-5 bg-white mb-2 shadow-2xs">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-xs font-bold text-[#102D21] m-0">QRIS Dinamis</h4>
                                <p class="text-[11px] text-[#8A958F] font-medium m-0">Kode dibuat sesuai nominal tiket</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#FEF6EE] text-[#B54708] border border-[#F9DBAF]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#B54708] animate-pulse"></span>
                                <span>Menunggu pembayaran</span>
                            </span>
                        </div>

                        <div class="border border-[#E0E5E2] rounded-2xl p-4 bg-white flex flex-col md:flex-row items-center gap-5 shadow-xs mb-4">
                            {{-- Kartu QRIS --}}
                            <div class="border border-[#E0E5E2] rounded-xl p-3 text-center bg-white shadow-2xs flex-shrink-0">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-extrabold tracking-wider text-[#102D21]">QRIS</span>
                                    <span class="bg-[#EA6546] text-white text-[8px] font-black px-1.5 py-0.5 rounded">QPN</span>
                                </div>
                                <div id="qrisContainer" class="p-1 bg-white inline-block"></div>
                                <div class="text-[9px] text-[#8A958F] font-medium mt-2">
                                    ParkirPintar Gedung Perkantoran<br>
                                    <span class="font-mono text-[8px]">NMD: ID1023245084637</span>
                                </div>
                            </div>

                            {{-- Nominal & Info --}}
                            <div class="flex-1 w-full text-left">
                                <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">NOMINAL PEMBAYARAN</div>
                                <div class="text-2xl font-black text-[#102D21] mb-1">
                                    Rp{{ number_format($previewData['fee'], 0, ',', '.') }}
                                </div>
                                <p class="text-xs text-[#8A958F] font-medium mb-3">Menunggu konfirmasi pembayaran dari pelanggan.</p>

                                <div class="grid grid-cols-2 gap-2 bg-[#FAFBFA] border border-[#E0E5E2] rounded-xl p-2.5 mb-3">
                                    <div>
                                        <div class="text-[9px] font-bold text-[#8A958F] uppercase">NOMOR TIKET</div>
                                        <div class="font-mono text-xs font-bold text-[#102D21]">{{ $transaction->ticket_number }}</div>
                                    </div>
                                    <div>
                                        <div class="text-[9px] font-bold text-[#8A958F] uppercase">PLAT NOMOR</div>
                                        <div class="text-xs font-bold text-[#102D21]">{{ $transaction->license_plate }}</div>
                                    </div>
                                </div>

                                {{-- Bank / E-Wallet Support Icons --}}
                                <div class="flex flex-wrap items-center gap-1.5 text-[9px] font-bold text-[#617068]">
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200">GoPay</span>
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200">OVO</span>
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200">DANA</span>
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200">ShopeePay</span>
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200">BCA Mobile</span>
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 border border-gray-200">Mandiri</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full py-3.5 rounded-xl font-bold text-xs bg-[#00875A] hover:bg-[#006644] text-white flex items-center justify-center gap-2 shadow-xs transition active:scale-98">
                            <i class="bi bi-check-lg text-sm"></i>
                            <span>Konfirmasi Pembayaran QRIS & Selesaikan Check-Out</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Library ZXing untuk pemindaian Barcode 1D (Code 128, Code 39) & 2D (QR Code) --}}
<script src="https://cdn.jsdelivr.net/npm/@zxing/library@0.21.3/umd/index.min.js"></script>

{{-- Library html5-qrcode untuk kompatibilitas kamera tambahan --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

{{-- Library qrcodejs untuk render QRIS dinamis --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let isProcessingScan = false;
    let nativeDetectorInterval = null;
    const totalFee = {{ isset($previewData) ? (int)$previewData['fee'] : 0 }};

    // Fungsi Pilihan Metode Pembayaran
    function selectPaymentMethod(method) {
        const btnTunai = document.getElementById('btnPayTunai');
        const btnQRIS = document.getElementById('btnPayQRIS');
        const checkTunai = document.getElementById('checkTunai');
        const checkQRIS = document.getElementById('checkQRIS');
        const panelTunai = document.getElementById('panelTunai');
        const panelQRIS = document.getElementById('panelQRIS');
        const methodInput = document.getElementById('selectedPaymentMethod');

        if (method === 'tunai') {
            methodInput.value = 'Tunai';
            btnTunai.className = 'cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-[#EFF6FF] border-[#2563EB]';
            checkTunai.classList.remove('hidden');

            btnQRIS.className = 'cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-white border-[#E0E5E2] hover:border-[#2563EB]/40';
            checkQRIS.classList.add('hidden');

            panelTunai.classList.remove('hidden');
            panelQRIS.classList.add('hidden');
        } else {
            methodInput.value = 'QRIS';
            btnQRIS.className = 'cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-[#EFF6FF] border-[#2563EB]';
            checkQRIS.classList.remove('hidden');

            btnTunai.className = 'cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-white border-[#E0E5E2] hover:border-[#2563EB]/40';
            checkTunai.classList.add('hidden');

            panelQRIS.classList.remove('hidden');
            panelTunai.classList.add('hidden');

            renderQrisCode();
        }
    }

    // Hitung Kembalian Uang Tunai Real-Time
    function calculateChange() {
        const cashInput = document.getElementById('cashReceived');
        const changeDisplay = document.getElementById('changeDisplay');
        const btnSubmit = document.getElementById('btnSubmitTunai');
        if (!cashInput || !changeDisplay) return;

        const cash = parseInt(cashInput.value) || 0;
        const change = cash - totalFee;

        if (change >= 0) {
            changeDisplay.innerText = 'Rp' + change.toLocaleString('id-ID');
            changeDisplay.className = 'text-base font-extrabold text-[#102D21]';
            if (btnSubmit) {
                btnSubmit.removeAttribute('disabled');
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        } else {
            changeDisplay.innerText = 'Uang Kurang (Rp' + Math.abs(change).toLocaleString('id-ID') + ')';
            changeDisplay.className = 'text-sm font-bold text-red-600';
            if (btnSubmit) {
                btnSubmit.setAttribute('disabled', 'disabled');
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    // Set Nominal Cepat
    function setNominal(amount) {
        const cashInput = document.getElementById('cashReceived');
        if (!cashInput) return;
        cashInput.value = amount;
        calculateChange();
    }

    // Render QR Code QRIS
    function renderQrisCode() {
        const container = document.getElementById('qrisContainer');
        if (!container || container.innerHTML.trim() !== '') return;

        const ticketNo = "{{ isset($transaction) ? $transaction->ticket_number : 'PP' }}";
        const qrisData = `00020101021226600016ID.CO.QRIS.WWW01189360099800000000005204581253033605802ID5912ParkirPintar6007JAKARTA62${ticketNo}6304`;

        new QRCode(container, {
            text: qrisData,
            width: 110,
            height: 110,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    // Audio Feedback Bip Barcode Scanner
    function playScanBeep() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(1400, ctx.currentTime);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.12);
        } catch (e) {}
    }

    // Callback Saat Barcode/QR Berhasil Terbaca (Langsung Masuk ke Pembayaran)
    async function onScanSuccess(decodedText) {
        if (!decodedText || isProcessingScan) return;
        isProcessingScan = true;

        const ticketCode = decodedText.trim().toUpperCase();

        // 1. Suara bip scanner
        playScanBeep();

        // 2. Tampilkan notifikasi visual berhasil
        const successBanner = document.getElementById('scanSuccessBanner');
        const detectedText = document.getElementById('detectedCodeText');
        if (successBanner && detectedText) {
            detectedText.innerText = ticketCode;
            successBanner.classList.remove('hidden');
        }

        // 3. Matikan scanner kamera segera
        await stopCameraScanner();

        // 4. Masukkan kode tiket ke dalam input manual
        const searchInput = document.getElementById('ticket-search-input') || document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        if (searchInput) {
            searchInput.value = ticketCode;
        }

        // 5. Langsung submit form pencarian otomatis untuk memunculkan rincian tagihan & pembayaran
        setTimeout(() => {
            if (searchForm) {
                searchForm.submit();
            }
        }, 200);
    }

    // Buka Scanner Kamera (Mendukung Kamera Laptop, USB, HP, Barcode 1D Code128 & QR)
    async function startCameraScanner() {
        const container = document.getElementById('cameraContainer');
        const btnStart = document.getElementById('btnStartCamera');
        const loadingBox = document.getElementById('cameraLoading');
        const errorBox = document.getElementById('cameraError');
        const successBanner = document.getElementById('scanSuccessBanner');

        if (container) container.classList.remove('hidden');
        if (btnStart) btnStart.classList.add('hidden');
        if (loadingBox) loadingBox.classList.remove('hidden');
        if (errorBox) errorBox.classList.add('hidden');
        if (successBanner) successBanner.classList.add('hidden');

        isProcessingScan = false;

        // Bersihkan instance lama jika ada
        if (html5QrCode) {
            try {
                await html5QrCode.stop();
                html5QrCode.clear();
            } catch(e) {}
            html5QrCode = null;
        }

        // Cek dukungan MediaDevices
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraError("Akses kamera memerlukan koneksi aman. Silakan buka aplikasi melalui http://localhost:8000 atau http://127.0.0.1:8000.");
            return;
        }

        try {
            // Konfigurasi format Barcode 1D (Code128, Code39) dan 2D (QR Code)
            const formatsToSupport = typeof Html5QrcodeSupportedFormats !== 'undefined'
                ? [
                    Html5QrcodeSupportedFormats.QR_CODE,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39
                ]
                : undefined;

            html5QrCode = new Html5Qrcode("reader", {
                formatsToSupport: formatsToSupport,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                },
                verbose: false
            });

            // Kotak pemindai proporsional 260x220 dengan fps 20
            const scanConfig = {
                fps: 20,
                qrbox: { width: 260, height: 220 },
                aspectRatio: 1.777778,
                disableFlip: false
            };

            const devices = await Html5Qrcode.getCameras().catch(() => []);
            let targetCamera = { facingMode: "environment" };

            if (devices && devices.length > 0) {
                const rearCamera = devices.find(d => /back|rear|environment|belakang/i.test(d.label));
                targetCamera = rearCamera ? rearCamera.id : devices[0].id;
            }

            try {
                await html5QrCode.start(targetCamera, scanConfig, onScanSuccess, () => {});
            } catch (cameraErr) {
                // Fallback kamera laptop / depan
                await html5QrCode.start({ facingMode: "user" }, scanConfig, onScanSuccess, () => {});
            }

            if (loadingBox) loadingBox.classList.add('hidden');

            // Booster native BarcodeDetector C++ jika didukung Chromium
            initNativeBooster();

        } catch (err) {
            showCameraError("Kamera tidak dapat diakses (" + (err.message || err) + "). Pastikan izin kamera telah diberikan di peramban.");
        }
    }

    // Hardware Booster BarcodeDetector bawaan browser Chromium
    function initNativeBooster() {
        if (!('BarcodeDetector' in window)) return;
        try {
            BarcodeDetector.getSupportedFormats().then(supported => {
                const desired = ['qr_code', 'code_128', 'code_39'].filter(f => supported.includes(f));
                if (desired.length === 0) return;

                const detector = new BarcodeDetector({ formats: desired });
                nativeDetectorInterval = setInterval(async () => {
                    if (isProcessingScan) return;
                    const video = document.querySelector('#reader video');
                    if (video && video.readyState >= 2) {
                        try {
                            const barcodes = await detector.detect(video);
                            if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
                                onScanSuccess(barcodes[0].rawValue);
                            }
                        } catch (e) {}
                    }
                }, 75);
            }).catch(() => {});
        } catch (e) {}
    }

    // Fitur Scan File Foto / Gambar Karcis
    async function handleTicketFileUpload(input) {
        const file = input.files && input.files[0];
        if (!file) return;

        const statusEl = document.getElementById('fileScanStatus');
        if (statusEl) statusEl.classList.remove('hidden');

        try {
            let scanner = html5QrCode;
            let needClear = false;

            if (!scanner) {
                scanner = new Html5Qrcode("readerFileHelper", {
                    formatsToSupport: [
                        Html5QrcodeSupportedFormats.QR_CODE,
                        Html5QrcodeSupportedFormats.CODE_128,
                        Html5QrcodeSupportedFormats.CODE_39
                    ],
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    },
                    verbose: false
                });
                needClear = true;
            }

            const decodedText = await scanner.scanFile(file, true);
            if (decodedText) {
                await onScanSuccess(decodedText);
            }

            if (needClear) {
                try { scanner.clear(); } catch(e) {}
            }
        } catch (err) {
            alert("Tidak dapat membaca Barcode atau QR Code dari foto ini. Pastikan gambar karcis terlihat jelas dan tidak buram.");
        } finally {
            if (statusEl) statusEl.classList.add('hidden');
            input.value = '';
        }
    }

    function showCameraError(msg) {
        const loadingBox = document.getElementById('cameraLoading');
        const errorBox = document.getElementById('cameraError');
        const errorMsg = document.getElementById('cameraErrorMsg');

        if (loadingBox) loadingBox.classList.add('hidden');
        if (errorBox) errorBox.classList.remove('hidden');
        if (errorMsg) errorMsg.innerText = msg;
    }

    // Tutup Scanner Kamera
    async function stopCameraScanner() {
        if (nativeDetectorInterval) {
            clearInterval(nativeDetectorInterval);
            nativeDetectorInterval = null;
        }
        if (html5QrCode) {
            try {
                await html5QrCode.stop();
                html5QrCode.clear();
            } catch (e) {}
            html5QrCode = null;
        }

        const container = document.getElementById('cameraContainer');
        const btnStart = document.getElementById('btnStartCamera');
        const loadingBox = document.getElementById('cameraLoading');
        const errorBox = document.getElementById('cameraError');

        if (container) container.classList.add('hidden');
        if (btnStart) btnStart.classList.remove('hidden');
        if (loadingBox) loadingBox.classList.add('hidden');
        if (errorBox) errorBox.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        calculateChange();
    });
</script>
@endpush
