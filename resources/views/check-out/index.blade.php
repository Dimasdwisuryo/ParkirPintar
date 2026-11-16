@extends('layouts.app')

@section('title', 'Check-Out Kendaraan — ParkirPintar')

@push('styles')
<style>
    /* Styling khusus scanner camera */
    #cameraReader video {
        border-radius: 1rem;
        object-fit: cover;
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
                    <div id="cameraReader" class="w-full max-w-sm mx-auto overflow-hidden rounded-xl border border-[#E0E5E2]"></div>
                    <button type="button"
                            id="btnStopCamera"
                            onclick="stopCameraScanner()"
                            class="mt-3 px-4 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition">
                        <i class="bi bi-x-circle me-1"></i>Tutup kamera
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
{{-- Library html5-qrcode untuk kamera scanner barcode / QR --}}
<script src="https://unpkg.com/html5-qrcode"></script>

{{-- Library qrcodejs untuk render QRIS dinamis --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
    let html5QrCode = null;
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

    // Buka Scanner Kamera
    function startCameraScanner() {
        const container = document.getElementById('cameraContainer');
        const btnStart = document.getElementById('btnStartCamera');
        container.classList.remove('hidden');
        btnStart.classList.add('hidden');

        html5QrCode = new Html5Qrcode("cameraReader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                // Barcode/QR berhasil terbaca
                stopCameraScanner();
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = decodedText.trim().toUpperCase();
                    document.getElementById('searchForm').submit();
                }
            },
            (errorMessage) => {
                // Scanning frame terus berlanjut
            }
        ).catch((err) => {
            alert('Tidak dapat mengakses kamera: ' + err);
            stopCameraScanner();
        });
    }

    // Tutup Scanner Kamera
    function stopCameraScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
            }).catch(() => {});
        }
        document.getElementById('cameraContainer').classList.add('hidden');
        document.getElementById('btnStartCamera').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        calculateChange();
    });
</script>
@endpush
