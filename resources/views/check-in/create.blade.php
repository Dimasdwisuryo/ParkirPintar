@extends('layouts.app')

@section('title', 'Check-In Kendaraan — ParkirPintar')

@push('styles')
<style>
    @media print {
        body {
            background-color: #ffffff !important;
        }
        body * {
            visibility: hidden;
        }
        #ticketReceipt, #ticketReceipt * {
            visibility: visible;
        }
        #ticketReceipt {
            position: absolute;
            left: 50%;
            top: 20px;
            transform: translateX(-50%);
            width: 340px;
            border: 1px solid #000000 !important;
            box-shadow: none !important;
        }
        aside, header, main > .flex > div:first-child, .no-print {
            display: none !important;
        }
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
        Check-in kendaraan
    </h1>
    <p class="text-xs text-[#617068] font-medium m-0">
        Masukkan data kendaraan, sistem akan memilih area terbaik secara otomatis.
    </p>
</div>

{{-- Grid Utama 2 Kolom --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    {{-- Kolom Kiri: Form Data Kendaraan (7 Kolom) --}}
    <div class="lg:col-span-7 bg-white border border-[#E0E5E2] rounded-2xl p-6 shadow-xs">
        {{-- Step Badge & Judul --}}
        <div>
            <span class="inline-block bg-[#D5F067] text-[#102D21] font-extrabold text-xs px-2.5 py-1 rounded-lg mb-3 shadow-2xs">
                01
            </span>
            <h2 class="text-base font-extrabold text-[#102D21] m-0">Data kendaraan</h2>
            <p class="text-xs text-[#8A958F] font-medium m-0 mt-0.5 mb-5">
                Pastikan plat nomor sesuai dengan kendaraan.
            </p>
        </div>

        {{-- Form Check-In --}}
        <form method="POST" action="{{ route('check-in.store') }}" id="checkInForm">
            @csrf

            {{-- Hidden Input Parking Area (Alokasi Otomatis) --}}
            <input type="hidden" name="parking_area_id" id="parking_area_id" value="{{ old('parking_area_id', $bestCarAreaId ?? '') }}">

            {{-- Input 1: Plat Nomor --}}
            <div class="mb-5">
                <label for="license_plate" class="block text-xs font-bold text-[#102D21] mb-2">
                    Plat nomor
                </label>
                <div class="relative flex items-center">
                    <input type="text"
                           id="license_plate"
                           name="license_plate"
                           value="{{ old('license_plate', isset($ticket) ? $ticket->license_plate : '') }}"
                           placeholder="B 1234 XYZ"
                           maxlength="15"
                           required
                           autocomplete="off"
                           oninput="handlePlateInput(this)"
                           class="w-full rounded-xl border border-[#E0E5E2] px-4 py-3.5 pr-14 text-lg font-extrabold uppercase tracking-wider text-[#102D21] placeholder-[#8A958F] focus:outline-none focus:border-[#173F2E] focus:ring-2 focus:ring-[#173F2E]/10 transition">
                    <span class="absolute right-3.5 px-2 py-0.5 rounded bg-[#EA6546] text-white text-[10px] font-black uppercase tracking-wider pointer-events-none shadow-2xs">
                        ID
                    </span>
                </div>
                <p class="text-[11px] text-[#617068] font-medium mt-1.5 mb-0">
                    Gunakan format plat kendaraan yang berlaku.
                </p>
            </div>

            {{-- Input 2: Jenis Kendaraan (Radio Cards Sejajar) --}}
            <div class="mb-5">
                <label class="block text-xs font-bold text-[#102D21] mb-2">
                    Jenis kendaraan
                </label>
                <div class="grid grid-cols-2 gap-3">
                    {{-- Option Mobil --}}
                    <label for="type_car"
                           id="card_car"
                           onclick="selectVehicleType('car')"
                           class="cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-[#F1F7E4] border-[#173F2E]">
                        <input type="radio" name="vehicle_type" id="type_car" value="car" class="hidden" checked>
                        <div id="check_car" class="absolute top-3 right-3 w-5 h-5 rounded-full bg-[#173F2E] text-white flex items-center justify-center text-[10px]">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="text-[#173F2E] text-2xl flex-shrink-0">
                            <i class="bi bi-car-front-fill"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-[#102D21]">Mobil</div>
                            <div class="text-[11px] text-[#8A958F] font-semibold">{{ $totalCarCapacity ?? 120 }} slot total</div>
                        </div>
                    </label>

                    {{-- Option Motor --}}
                    <label for="type_motorcycle"
                           id="card_motorcycle"
                           onclick="selectVehicleType('motorcycle')"
                           class="cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-white border-[#E0E5E2] hover:border-[#173F2E]/40">
                        <input type="radio" name="vehicle_type" id="type_motorcycle" value="motorcycle" class="hidden">
                        <div id="check_motorcycle" class="hidden absolute top-3 right-3 w-5 h-5 rounded-full bg-[#173F2E] text-white flex items-center justify-center text-[10px]">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="text-[#718826] text-2xl flex-shrink-0">
                            <i class="bi bi-bicycle"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-[#102D21]">Motor</div>
                            <div class="text-[11px] text-[#8A958F] font-semibold">{{ $totalMotorcycleCapacity ?? 220 }} slot total</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Info Banner Alokasi Otomatis --}}
            <div class="bg-[#F1F7E4] border border-[#E0E5E2] rounded-xl p-4 mb-5">
                <div class="text-xs font-bold text-[#102D21] mb-0.5">Alokasi otomatis aktif</div>
                <div class="text-[11px] text-[#617068] font-medium leading-relaxed">
                    Area kosong atau area dengan okupansi terendah akan diprioritaskan.
                </div>
            </div>

            {{-- Alert Error Validasi Dinamis --}}
            @if($errors->any() || session('error'))
                <div class="bg-[#FBEDE4] border border-[#EA6546] text-[#102D21] rounded-xl p-3.5 text-xs font-semibold mb-5 flex items-center gap-2.5 shadow-2xs">
                    <i class="bi bi-exclamation-circle-fill text-[#EA6546] text-sm flex-shrink-0"></i>
                    <span>{{ session('error') ?? $errors->first() }}</span>
                </div>
            @endif

            {{-- Tombol Submit Utama --}}
            <button type="submit"
                    id="btnSubmitCheckIn"
                    disabled
                    class="w-full py-3.5 rounded-xl font-bold text-xs transition duration-200 flex items-center justify-center gap-2 bg-[#A3B1AA] text-white cursor-not-allowed">
                <span>Proses check-in</span>
                <span class="text-sm">→</span>
            </button>
        </form>
    </div>

    {{-- Kolom Kanan: Karcis Masuk Digital (5 Kolom) --}}
    <div class="lg:col-span-5">
        @if(!isset($ticket))
            {{-- State A: Sebelum Check-in --}}
            <div class="bg-white border border-[#E0E5E2] rounded-2xl flex flex-col items-center justify-center p-8 text-center min-h-[460px] h-full shadow-xs">
                <div class="w-14 h-14 rounded-2xl bg-[#EEF2EF] text-[#8A958F] flex items-center justify-center text-2xl mb-4">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <h3 class="text-base font-bold text-[#102D21] mb-1">Karcis belum dibuat</h3>
                <p class="text-xs text-[#8A958F] font-medium max-w-xs leading-relaxed m-0">
                    Karcis digital akan tampil di sini setelah data kendaraan berhasil diproses.
                </p>
            </div>
        @else
            {{-- State B: Setelah Check-in Berhasil --}}
            <div class="bg-white border border-[#E0E5E2] rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                <div>
                    {{-- Header Sukses --}}
                    <div class="text-center mb-5">
                        <div class="w-11 h-11 rounded-full bg-[#EDF5D6] text-[#536B20] flex items-center justify-center mx-auto mb-2 text-xl shadow-2xs">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <h2 class="text-lg font-bold text-[#102D21] m-0">Check-in berhasil</h2>
                        <p class="text-xs text-[#8A958F] font-medium m-0 mt-0.5">Karcis digital siap digunakan</p>
                    </div>

                    {{-- Struk Karcis Masuk (Printable Target) --}}
                    <div id="ticketReceipt" class="border border-[#E0E5E2] rounded-2xl overflow-hidden bg-white shadow-xs">
                        {{-- Top Dark Banner --}}
                        <div class="bg-[#173F2E] px-6 py-4 text-center">
                            <div class="text-[10px] tracking-widest text-[#D5F067] font-extrabold uppercase mb-0.5">
                                PARKIRPINTAR
                            </div>
                            <div class="text-base font-extrabold text-white tracking-wide">
                                Tiket parkir
                            </div>
                        </div>

                        {{-- Ticket Details --}}
                        <div class="p-5">
                            <div class="grid grid-cols-2 gap-4 text-left">
                                <div>
                                    <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">NOMOR TIKET</div>
                                    <div class="font-mono text-xs font-extrabold text-[#102D21]">{{ $ticket->ticket_number }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">PLAT NOMOR</div>
                                    <div class="text-xs font-extrabold text-[#102D21]">{{ $ticket->license_plate }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">JENIS KENDARAAN</div>
                                    <div class="text-xs font-extrabold text-[#102D21]">{{ $ticket->vehicle_type === 'car' ? 'Mobil' : 'Motor' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">AREA PARKIR</div>
                                    <div class="text-xs font-extrabold text-[#102D21]">{{ $ticket->parkingArea->name ?? 'A-02' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">WAKTU MASUK</div>
                                    <div class="text-xs font-extrabold text-[#102D21]">
                                        {{ $ticket->check_in_at ? $ticket->check_in_at->setTimezone('Asia/Jakarta')->translatedFormat('d M, H:i') : now()->format('d M, H:i') }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-0.5">STATUS</div>
                                    <div class="text-xs font-extrabold text-[#173F2E] uppercase">AKTIF</div>
                                </div>
                            </div>

                            {{-- Perforasi Garis Putus-Putus --}}
                            <div class="border-t border-dashed border-[#D9DFDC] my-4"></div>

                            {{-- Area QR Code 2D & Barcode 1D Code128 Nyata --}}
                            <div class="flex flex-col items-center justify-center pt-2 pb-2 bg-white">
                                {{-- QR Code 2D (Bebas pantulan layar, mudah & cepat dibaca webcam laptop) --}}
                                <div class="bg-white p-3 border border-[#E0E5E2] rounded-xl shadow-xs mb-3 flex items-center justify-center">
                                    <div id="ticketQrcode"></div>
                                </div>

                                {{-- Barcode 1D Code128 --}}
                                <svg id="ticketBarcode" class="max-w-full" style="shape-rendering: crispEdges;"></svg>
                                <div class="font-mono text-[11px] font-bold tracking-[0.2em] text-[#102D21] mt-1 text-center">
                                    {{ $ticket->ticket_number }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 mt-5 no-print">
                    <button type="button"
                            onclick="printTicketReceipt()"
                            class="flex-1 py-2.5 rounded-xl border border-[#E0E5E2] bg-white hover:bg-gray-50 text-[#102D21] text-xs font-bold flex items-center justify-center gap-2 shadow-2xs transition active:scale-98">
                        <i class="bi bi-printer text-sm"></i>
                        <span>Print</span>
                    </button>
                    <button type="button"
                            onclick="downloadTicketPdf()"
                            class="flex-1 py-2.5 rounded-xl bg-[#173F2E] hover:bg-[#234D39] text-white text-xs font-bold flex items-center justify-center gap-2 shadow-2xs transition active:scale-98">
                        <i class="bi bi-download text-sm"></i>
                        <span>Download</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- qrcodejs CDN untuk 2D QR Code Karcis --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

{{-- JsBarcode CDN untuk 1D Code128 Barcode --}}
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

{{-- html2pdf CDN untuk Download Karcis PDF --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    const bestCarAreaId = "{{ $bestCarAreaId ?? '' }}";
    const bestMotorcycleAreaId = "{{ $bestMotorcycleAreaId ?? '' }}";

    // Handle Auto-Uppercase dan Toggle Submit Button
    function handlePlateInput(input) {
        input.value = input.value.toUpperCase();
        toggleSubmitButton();
    }

    function toggleSubmitButton() {
        const input = document.getElementById('license_plate');
        const btn = document.getElementById('btnSubmitCheckIn');
        if (!input || !btn) return;

        if (input.value.trim().length > 0) {
            btn.removeAttribute('disabled');
            btn.classList.remove('bg-[#A3B1AA]', 'cursor-not-allowed');
            btn.classList.add('bg-[#173F2E]', 'hover:bg-[#234D39]', 'cursor-pointer', 'active:scale-98');
        } else {
            btn.setAttribute('disabled', 'disabled');
            btn.classList.remove('bg-[#173F2E]', 'hover:bg-[#234D39]', 'cursor-pointer', 'active:scale-98');
            btn.classList.add('bg-[#A3B1AA]', 'cursor-not-allowed');
        }
    }

    // Handle Pilihan Jenis Kendaraan
    function selectVehicleType(type) {
        const cardCar = document.getElementById('card_car');
        const cardMotorcycle = document.getElementById('card_motorcycle');
        const checkCar = document.getElementById('check_car');
        const checkMotorcycle = document.getElementById('check_motorcycle');
        const radioCar = document.getElementById('type_car');
        const radioMotorcycle = document.getElementById('type_motorcycle');
        const inputArea = document.getElementById('parking_area_id');

        if (type === 'car') {
            radioCar.checked = true;
            radioMotorcycle.checked = false;

            cardCar.className = 'cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-[#F1F7E4] border-[#173F2E]';
            checkCar.classList.remove('hidden');

            cardMotorcycle.className = 'cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-white border-[#E0E5E2] hover:border-[#173F2E]/40';
            checkMotorcycle.classList.add('hidden');

            if (inputArea && bestCarAreaId) {
                inputArea.value = bestCarAreaId;
            }
        } else {
            radioCar.checked = false;
            radioMotorcycle.checked = true;

            cardMotorcycle.className = 'cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-[#F1F7E4] border-[#173F2E]';
            checkMotorcycle.classList.remove('hidden');

            cardCar.className = 'cursor-pointer border-2 rounded-2xl p-4 transition relative flex items-center gap-3.5 select-none bg-white border-[#E0E5E2] hover:border-[#173F2E]/40';
            checkCar.classList.add('hidden');

            if (inputArea && bestMotorcycleAreaId) {
                inputArea.value = bestMotorcycleAreaId;
            }
        }
    }

    // Cetak Struk Tiket Bersih
    function printTicketReceipt() {
        window.print();
    }

    // Download Tiket sebagai File PDF
    function downloadTicketPdf() {
        const receipt = document.getElementById('ticketReceipt');
        if (!receipt) return;

        const ticketNo = "{{ isset($ticket) ? $ticket->ticket_number : 'PP' }}";
        const opt = {
            margin: 8,
            filename: `Tiket-${ticketNo}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 3, useCORS: true },
            jsPDF: { unit: 'mm', format: [95, 175], orientation: 'portrait' }
        };

        html2pdf().set(opt).from(receipt).save();
    }

    // Inisialisasi State Saat Halaman Dimuat
    document.addEventListener('DOMContentLoaded', function () {
        toggleSubmitButton();

        @if(isset($ticket))
            // Render QR Code 2D Nyata Menggunakan qrcodejs (String Murni ticket_number)
            const qrElement = document.getElementById('ticketQrcode');
            if (qrElement && typeof QRCode !== 'undefined') {
                new QRCode(qrElement, {
                    text: "{{ trim($ticket->ticket_number) }}",
                    width: 125,
                    height: 125,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }

            // Render Code128 Barcode Nyata Menggunakan JsBarcode
            const barcodeElement = document.getElementById('ticketBarcode');
            if (barcodeElement && typeof JsBarcode !== 'undefined') {
                JsBarcode(barcodeElement, "{{ trim($ticket->ticket_number) }}", {
                    format: "CODE128",
                    lineColor: "#000000",
                    width: 2,
                    height: 48,
                    displayValue: false,
                    margin: 8,
                    background: "#ffffff"
                });
            }
        @endif
    });
</script>
@endpush
