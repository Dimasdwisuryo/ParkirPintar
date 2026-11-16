@extends('layouts.app')

@section('title', 'Invoice Pembayaran Parkir — ParkirPintar')

@push('styles')
<style>
    @media print {
        body {
            background-color: #ffffff !important;
        }
        body * {
            visibility: hidden;
        }
        #invoiceReceipt, #invoiceReceipt * {
            visibility: visible;
        }
        #invoiceReceipt {
            position: absolute;
            left: 50%;
            top: 20px;
            transform: translateX(-50%);
            width: 360px;
            border: 1px solid #000000 !important;
            box-shadow: none !important;
        }
        aside, header, .no-print, .modal-backdrop {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
{{-- Modal Backdrop & Dialog (Sesuai Desain Figma) --}}
<div class="fixed inset-0 bg-[#102D21]/60 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl relative max-h-[96vh] overflow-y-auto">
        {{-- Close Button X --}}
        <a href="{{ route('check-out.index') }}"
           class="absolute top-5 right-5 w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-[#102D21] flex items-center justify-center text-sm font-bold transition no-print"
           title="Tutup">
            <i class="bi bi-x-lg"></i>
        </a>

        {{-- Header Sukses di Atas Struk --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-full bg-[#10B981] text-white flex items-center justify-center mx-auto mb-2 text-2xl shadow-xs">
                <i class="bi bi-check-lg"></i>
            </div>
            <h2 class="text-xl font-extrabold text-[#102D21] tracking-tight m-0">
                Check-Out Berhasil
            </h2>
            <p class="text-xs text-[#8A958F] font-medium m-0 mt-0.5">
                Pembayaran tercatat dan slot kembali tersedia.
            </p>
        </div>

        {{-- Kartu Struk Invoice Thermal Bersih (Printable Target) --}}
        <div id="invoiceReceipt" class="border border-[#E0E5E2] rounded-2xl p-6 bg-white shadow-xs max-w-md mx-auto">
            {{-- Header Logo Struk --}}
            <div class="text-center mb-4">
                <div class="w-8 h-8 rounded-lg bg-[#173F2E] text-[#D5F067] font-black text-sm flex items-center justify-center mx-auto mb-1.5 shadow-2xs">
                    P
                </div>
                <div class="text-xs font-black text-[#102D21] tracking-widest uppercase">
                    PARKIRPINTAR
                </div>
                <div class="text-[9px] font-bold text-[#8A958F] tracking-widest uppercase mt-0.5">
                    INVOICE PEMBAYARAN PARKIR
                </div>
                <div class="mt-2.5">
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-extrabold bg-[#EDF5D6] text-[#536B20]">
                        ✓ LUNAS / PAID
                    </span>
                </div>
            </div>

            {{-- Detail Key-Value Rata Kiri-Kanan --}}
            @php
                $invoiceNo = 'INV-' . ($transaction->check_out_at ? $transaction->check_out_at->format('Ymd') : date('Ymd')) . '-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
            @endphp
            <div class="grid grid-cols-2 gap-y-2 text-left text-xs mb-4 pb-4 border-b border-[#F4F6F4]">
                <div class="text-[11px] text-[#8A958F] font-medium">No. Invoice</div>
                <div class="text-right font-mono text-[11px] font-bold text-[#102D21]">{{ $invoiceNo }}</div>

                <div class="text-[11px] text-[#8A958F] font-medium">No. Tiket</div>
                <div class="text-right font-mono text-[11px] font-bold text-[#102D21]">{{ $transaction->ticket_number }}</div>

                <div class="text-[11px] text-[#8A958F] font-medium">Plat Nomor</div>
                <div class="text-right text-xs font-bold text-[#102D21]">{{ $transaction->license_plate }}</div>

                <div class="text-[11px] text-[#8A958F] font-medium">Jenis Kendaraan</div>
                <div class="text-right text-xs font-bold text-[#102D21]">{{ $transaction->vehicle_type === 'car' ? 'Mobil' : 'Motor' }}</div>

                <div class="text-[11px] text-[#8A958F] font-medium">Area Parkir</div>
                <div class="text-right text-xs font-bold text-[#102D21]">{{ $transaction->parkingArea->name ?? 'Zona A1 · Basement 1' }}</div>

                <div class="text-[11px] text-[#8A958F] font-medium">Waktu Masuk</div>
                <div class="text-right text-[11px] font-bold text-[#102D21]">
                    {{ $transaction->check_in_at ? $transaction->check_in_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') . ' WIB' : '—' }}
                </div>

                <div class="text-[11px] text-[#8A958F] font-medium">Waktu Keluar</div>
                <div class="text-right text-[11px] font-bold text-[#102D21]">
                    {{ $transaction->check_out_at ? $transaction->check_out_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') . ' WIB' : '—' }}
                </div>

                <div class="text-[11px] text-[#8A958F] font-medium">Total Durasi</div>
                <div class="text-right text-xs font-bold text-[#102D21]">
                    {{ $formattedDuration }}
                    <span class="text-[10px] text-[#8A958F] font-normal block">{{ $billableHours ?? 1 }} jam tertagih</span>
                </div>
            </div>

            {{-- Rincian Biaya --}}
            <div class="mb-4">
                <div class="text-[10px] font-bold text-[#8A958F] tracking-wider uppercase mb-2 text-left">
                    RINCIAN BIAYA
                </div>
                <div class="flex items-center justify-between text-xs text-[#102D21] mb-1">
                    <span>Jam ke-1</span>
                    <span class="font-bold">Rp{{ number_format($firstHourRate ?? 5000, 0, ',', '.') }}</span>
                </div>
                @php
                    $extraHours = max(0, ($billableHours ?? 1) - 1);
                    $extraFee = $extraHours * ($nextHourRate ?? 3000);
                @endphp
                <div class="flex items-center justify-between text-xs text-[#102D21] mb-3">
                    <span>Jam berikutnya ({{ $extraHours }} jam × Rp{{ number_format($nextHourRate ?? 3000, 0, ',', '.') }})</span>
                    <span class="font-bold">Rp{{ number_format($extraFee, 0, ',', '.') }}</span>
                </div>

                {{-- Garis Pembatas Tebal --}}
                <div class="border-t-2 border-dashed border-[#102D21] pt-3 pb-1 flex items-center justify-between">
                    <span class="text-xs font-black text-[#102D21] uppercase">TOTAL DIBAYAR</span>
                    <span class="text-xl font-black text-[#102D21]">
                        Rp{{ number_format($transaction->fee ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Footer Info --}}
            <div class="grid grid-cols-2 gap-2 text-left text-xs pt-3 border-t border-[#F4F6F4] text-[#617068] mb-4">
                <div>
                    <span class="text-[10px] text-[#8A958F] block">Metode Pembayaran</span>
                    <span class="font-bold text-[#102D21]">{{ session('payment_method') ?? 'Tunai' }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-[#8A958F] block">Petugas</span>
                    <span class="font-bold text-[#102D21]">{{ auth()->user()->name ?? 'Dimas Arya' }}</span>
                </div>
            </div>

            <div class="text-[10px] text-[#8A958F] font-medium text-center leading-relaxed">
                Terima kasih telah menggunakan layanan ParkirPintar.<br>
                Dokumen ini merupakan bukti pembayaran yang sah.
            </div>
        </div>

        {{-- 3 Tombol Aksi di Bawah Modal --}}
        <div class="flex flex-col sm:flex-row items-center gap-3 mt-6 no-print">
            {{-- 1. Print Invoice --}}
            <button type="button"
                    onclick="window.print()"
                    class="w-full sm:flex-1 py-2.5 rounded-xl border border-[#E0E5E2] bg-white hover:bg-gray-50 text-[#102D21] text-xs font-bold flex items-center justify-center gap-2 shadow-2xs transition active:scale-98">
                <i class="bi bi-printer text-sm"></i>
                <span>Print Invoice</span>
            </button>

            {{-- 2. Download Invoice (PDF) --}}
            <button type="button"
                    onclick="downloadInvoicePdf()"
                    class="w-full sm:flex-1 py-2.5 rounded-xl border border-[#E0E5E2] bg-white hover:bg-gray-50 text-[#102D21] text-xs font-bold flex items-center justify-center gap-2 shadow-2xs transition active:scale-98">
                <i class="bi bi-download text-sm"></i>
                <span>Download Invoice (PDF)</span>
            </button>

            {{-- 3. Scan Kendaraan Berikutnya --}}
            <a href="{{ route('check-out.index') }}"
               class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition active:scale-98 no-underline">
                <i class="bi bi-qr-code-scan text-sm"></i>
                <span>Scan Kendaraan Berikutnya</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- html2pdf CDN untuk download invoice PDF --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadInvoicePdf() {
        const invoice = document.getElementById('invoiceReceipt');
        if (!invoice) return;

        const invoiceNo = "{{ $invoiceNo ?? 'INV' }}";
        const opt = {
            margin: 8,
            filename: `${invoiceNo}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 3, useCORS: true },
            jsPDF: { unit: 'mm', format: [100, 160], orientation: 'portrait' }
        };

        html2pdf().set(opt).from(invoice).save();
    }
</script>
@endpush
