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
        <div id="invoiceReceipt" class="border border-[#E0E5E2] rounded-2xl p-6 bg-white shadow-xs max-w-md mx-auto" style="page-break-inside: avoid;">
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
                <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[10px] font-extrabold bg-[#EDF5D6] text-[#536B20]">
                        ✓ LUNAS / PAID
                    </span>
                </div>
            </div>

            {{-- Detail Key-Value Rata Kiri-Kanan Menggunakan Tabel HTML Standar --}}
            @php
                $invoiceNo = 'INV-' . ($transaction->check_out_at ? $transaction->check_out_at->format('Ymd') : date('Ymd')) . '-' . str_pad($transaction->id, 6, '0', STR_PAD_LEFT);
            @endphp
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; border-bottom: 1px solid #F4F6F4;">
                <tbody>
                    <tr>
                        <td style="padding: 3px 0; font-size: 11px; color: #8A958F; text-align: left;">No. Invoice</td>
                        <td style="padding: 3px 0; font-size: 11px; font-weight: bold; font-family: monospace; color: #102D21; text-align: right;">{{ $invoiceNo }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; font-size: 11px; color: #8A958F; text-align: left;">No. Tiket</td>
                        <td style="padding: 3px 0; font-size: 11px; font-weight: bold; font-family: monospace; color: #102D21; text-align: right;">{{ $transaction->ticket_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; font-size: 11px; color: #8A958F; text-align: left;">Plat Nomor</td>
                        <td style="padding: 3px 0; font-size: 12px; font-weight: bold; color: #102D21; text-align: right;">{{ $transaction->license_plate }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; font-size: 11px; color: #8A958F; text-align: left;">Jenis Kendaraan</td>
                        <td style="padding: 3px 0; font-size: 12px; font-weight: bold; color: #102D21; text-align: right;">{{ $transaction->vehicle_type === 'car' ? 'Mobil' : 'Motor' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; font-size: 11px; color: #8A958F; text-align: left;">Area Parkir</td>
                        <td style="padding: 3px 0; font-size: 12px; font-weight: bold; color: #102D21; text-align: right;">{{ $transaction->parkingArea->name ?? 'Zona A1 · Basement 1' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; font-size: 11px; color: #8A958F; text-align: left;">Waktu Masuk</td>
                        <td style="padding: 3px 0; font-size: 11px; font-weight: bold; color: #102D21; text-align: right;">
                            {{ $transaction->check_in_at ? $transaction->check_in_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') . ' WIB' : '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; font-size: 11px; color: #8A958F; text-align: left;">Waktu Keluar</td>
                        <td style="padding: 3px 0; font-size: 11px; font-weight: bold; color: #102D21; text-align: right;">
                            {{ $transaction->check_out_at ? $transaction->check_out_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') . ' WIB' : '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; font-size: 11px; color: #8A958F; text-align: left; vertical-align: top;">Total Durasi</td>
                        <td style="padding: 3px 0; font-size: 12px; font-weight: bold; color: #102D21; text-align: right;">
                            {{ $formattedDuration }}
                            <div style="font-size: 10px; color: #8A958F; font-weight: normal;">{{ $billableHours ?? 1 }} jam tertagih</div>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Rincian Biaya Menggunakan Tabel HTML Standar --}}
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
                <thead>
                    <tr>
                        <th colspan="2" style="font-size: 10px; font-weight: bold; color: #8A958F; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; padding-bottom: 6px;">
                            RINCIAN BIAYA
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 2px 0; font-size: 12px; color: #102D21; text-align: left;">Jam ke-1</td>
                        <td style="padding: 2px 0; font-size: 12px; font-weight: bold; color: #102D21; text-align: right;">Rp{{ number_format($firstHourRate ?? 5000, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $extraHours = max(0, ($billableHours ?? 1) - 1);
                        $extraFee = $extraHours * ($nextHourRate ?? 3000);
                    @endphp
                    <tr>
                        <td style="padding: 2px 0; font-size: 12px; color: #102D21; text-align: left;">Jam berikutnya ({{ $extraHours }} jam × Rp{{ number_format($nextHourRate ?? 3000, 0, ',', '.') }})</td>
                        <td style="padding: 2px 0; font-size: 12px; font-weight: bold; color: #102D21; text-align: right;">Rp{{ number_format($extraFee, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="border-top: 2px dashed #102D21;">
                        <td style="padding: 8px 0 2px 0; font-size: 12px; font-weight: 900; color: #102D21; text-transform: uppercase; text-align: left;">TOTAL DIBAYAR</td>
                        <td style="padding: 8px 0 2px 0; font-size: 18px; font-weight: 900; color: #102D21; text-align: right;">Rp{{ number_format($transaction->fee ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            {{-- Footer Info --}}
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; border-top: 1px solid #F4F6F4; padding-top: 6px;">
                <tbody>
                    <tr>
                        <td style="padding: 4px 0; text-align: left;">
                            <div style="font-size: 10px; color: #8A958F;">Metode Pembayaran</div>
                            <div style="font-size: 12px; font-weight: bold; color: #102D21;">{{ session('payment_method') ?? 'Tunai' }}</div>
                        </td>
                        <td style="padding: 4px 0; text-align: right;">
                            <div style="font-size: 10px; color: #8A958F;">Petugas</div>
                            <div style="font-size: 12px; font-weight: bold; color: #102D21;">{{ $transaction->checkoutUser->name ?? auth()->user()->name ?? 'Penanggung Jawab' }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="text-[10px] text-[#8A958F] font-medium text-center leading-relaxed">
                Terima kasih telah menggunakan layanan ParkirPintar.<br>
                Dokumen ini merupakan bukti pembayaran yang sah.
            </div>
        </div>

        {{-- 3 Tombol Aksi di Bawah Modal (Tinggi Seragam, Sejajar Rapi, 1 Baris) --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2.5 w-full mt-6 no-print">
            {{-- 1. Print Invoice --}}
            <button type="button"
                    onclick="window.print()"
                    class="h-11 px-4 text-sm font-semibold rounded-xl flex items-center justify-center gap-2 border border-[#E0E5E2] bg-white hover:bg-gray-50 text-[#102D21] whitespace-nowrap shadow-2xs transition active:scale-98 cursor-pointer flex-1 w-full sm:w-auto">
                <i class="bi bi-printer text-base"></i>
                <span>Print Invoice</span>
            </button>

            {{-- 2. Download PDF --}}
            <button type="button"
                    onclick="downloadInvoicePdf()"
                    class="h-11 px-4 text-sm font-semibold rounded-xl flex items-center justify-center gap-2 border border-[#E0E5E2] bg-white hover:bg-gray-50 text-[#102D21] whitespace-nowrap shadow-2xs transition active:scale-98 cursor-pointer flex-1 w-full sm:w-auto">
                <i class="bi bi-download text-base"></i>
                <span>Download PDF</span>
            </button>

            {{-- 3. Scan Berikutnya --}}
            <a href="{{ route('check-out.index') }}"
               class="h-11 px-4 text-sm font-semibold rounded-xl flex items-center justify-center gap-2 bg-[#173F2E] hover:bg-[#234D39] text-white whitespace-nowrap shadow-xs transition active:scale-98 no-underline flex-1 w-full sm:w-auto">
                <i class="bi bi-qr-code-scan text-base"></i>
                <span>Scan Berikutnya</span>
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
            margin: [6, 6, 6, 6],
            filename: `${invoiceNo}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2.5, useCORS: true, logging: false },
            jsPDF: { unit: 'mm', format: [95, 195], orientation: 'portrait' },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        };

        html2pdf().set(opt).from(invoice).save();
    }
</script>
@endpush
