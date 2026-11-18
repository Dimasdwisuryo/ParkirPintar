<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — ParkirPintar</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        forest: {
                            900: '#102D21',
                            850: '#14271E',
                            800: '#173F2E',
                            700: '#234D39',
                            600: '#294E3B',
                        },
                        lime: {
                            brand: '#D5F067',
                            hover: '#C4DA65',
                            muted: '#B8CF54',
                            dark: '#A7C43D',
                        },
                        sage: {
                            muted: '#8A958F',
                            light: '#929C97',
                        },
                        canvas: '#F4F6F4',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #F4F6F4;
            color: #102D21;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="bg-[#F4F6F4] text-[#102D21] antialiased">

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
    {{-- ========================================================================= --}}
    {{-- PANEL KIRI: Deep Forest Green (#102D21) --}}
    {{-- ========================================================================= --}}
    <div class="bg-[#102D21] p-8 sm:p-12 lg:p-16 text-white flex flex-col justify-between relative overflow-hidden">
        {{-- Background decorative subtle pattern --}}
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-[#173F2E]/40 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-[#D5F067]/10 blur-3xl pointer-events-none"></div>

        {{-- Top Brand Logo --}}
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#D5F067] text-[#102D21] font-black text-xl flex items-center justify-center shadow-xs">
                    P
                </div>
                <span class="text-white font-bold text-xl tracking-tight">ParkirPintar</span>
            </div>
        </div>

        {{-- Middle Content: Headline & 3 Feature Highlights --}}
        <div class="my-10 lg:my-0 relative z-10 max-w-lg">
            {{-- Badge Pill --}}
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-[#173F2E] text-[#D5F067] border border-[#294E3B] mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#D5F067]"></span>
                OPERASIONAL PARKIR TERPADU
            </div>

            {{-- Headline & Subtitle --}}
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight tracking-tight mb-3">
                Sistem Manajemen Parkir Gedung Perkantoran Terintegrasi
            </h1>
            <p class="text-sm text-[#8A958F] font-normal leading-relaxed mb-8">
                Otomatisasi alokasi slot, karcis digital barcode &amp; QR, serta pemantauan okupansi secara real-time.
            </p>

            {{-- 3 Feature Highlight Cards --}}
            <div class="space-y-3.5">
                {{-- 1. Alokasi Slot Otomatis --}}
                <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-[#173F2E]/70 border border-[#234D39]/60 backdrop-blur-xs">
                    <div class="w-9 h-9 rounded-xl bg-[#102D21] text-[#D5F067] flex items-center justify-center text-base flex-shrink-0">
                        <i class="bi bi-grid-fill"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white tracking-tight">Alokasi Slot Otomatis</div>
                        <div class="text-[11px] text-[#8A958F] font-normal mt-0.5">Prioritas area kosong &amp; okupansi terendah</div>
                    </div>
                </div>

                {{-- 2. Karcis Digital & Scan Cepat --}}
                <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-[#173F2E]/70 border border-[#234D39]/60 backdrop-blur-xs">
                    <div class="w-9 h-9 rounded-xl bg-[#102D21] text-[#D5F067] flex items-center justify-center text-base flex-shrink-0">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white tracking-tight">Karcis Digital &amp; Scan Cepat</div>
                        <div class="text-[11px] text-[#8A958F] font-normal mt-0.5">Check-in &amp; check-out barcode/QR instan</div>
                    </div>
                </div>

                {{-- 3. Laporan Real-Time --}}
                <div class="flex items-start gap-3.5 p-3.5 rounded-2xl bg-[#173F2E]/70 border border-[#234D39]/60 backdrop-blur-xs">
                    <div class="w-9 h-9 rounded-xl bg-[#102D21] text-[#D5F067] flex items-center justify-center text-base flex-shrink-0">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white tracking-tight">Laporan Real-Time</div>
                        <div class="text-[11px] text-[#8A958F] font-normal mt-0.5">Pantau okupansi &amp; pendapatan harian akurat</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Status Footer --}}
        <div class="pt-8 border-t border-[#173F2E] flex items-center justify-between text-xs text-[#8A958F] relative z-10">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#B8CF54] shadow-[0_0_8px_#B8CF54]"></span>
                <span class="text-slate-300 font-semibold">Sistem beroperasi normal</span>
                <span>·</span>
                <span>{{ now()->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</span>
            </div>
            <div class="text-[11px] text-[#617068]">
                v2.0 &bull; ParkirPintar
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- PANEL KANAN: Background Canvas (#F4F6F4), Kartu Putih (#FFFFFF) --}}
    {{-- ========================================================================= --}}
    <div class="bg-[#F4F6F4] p-6 sm:p-12 lg:p-16 flex items-center justify-center">
        <div class="w-full max-w-md bg-white border border-[#E0E5E2] rounded-2xl p-7 sm:p-9 shadow-sm">
            {{-- Lock Icon --}}
            <div class="w-12 h-12 rounded-2xl bg-[#EEF2EF] text-[#173F2E] flex items-center justify-center text-xl mb-5 shadow-2xs">
                <i class="bi bi-lock-fill"></i>
            </div>

            {{-- Header Form --}}
            <h2 class="text-2xl font-extrabold text-[#102D21] tracking-tight mb-1">
                Masuk ke ParkirPintar
            </h2>
            <p class="text-xs text-[#617068] font-medium mb-6">
                Gunakan akun penanggung jawab operasional yang terdaftar.
            </p>

            {{-- Flash Alert Messages --}}
            @if(session('success'))
                <div class="bg-[#EDF5D6] border border-[#C4DA65] text-[#536B20] rounded-xl px-4 py-3 mb-5 flex items-center gap-2 text-xs font-semibold">
                    <i class="bi bi-check-circle-fill text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-[#FEE4E2] border border-[#FECDCA] text-[#D92D20] rounded-xl px-4 py-3 mb-5 flex items-center gap-2 text-xs font-semibold">
                    <i class="bi bi-x-circle-fill text-sm"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-[#FEE4E2] border border-[#FECDCA] text-[#D92D20] rounded-xl px-4 py-3 mb-5 flex items-center gap-2 text-xs font-semibold">
                    <i class="bi bi-exclamation-circle-fill text-sm"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Login Form --}}
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Email / Username Input --}}
                <div>
                    <label for="loginInput" class="block text-xs font-bold text-[#102D21] uppercase tracking-wider mb-1.5">
                        Email atau Username
                    </label>
                    <div class="relative flex items-center">
                        <i class="bi bi-person absolute left-3.5 text-[#8A958F] text-sm pointer-events-none"></i>
                        <input type="text"
                               id="loginInput"
                               name="login"
                               value="{{ old('login') }}"
                               required
                               autofocus
                               placeholder="windah@parkirpintar.id"
                               class="w-full pl-10 pr-4 py-3 bg-white border border-[#E0E5E2] rounded-xl text-sm font-medium text-[#102D21] placeholder-[#8A958F] focus:outline-none focus:border-[#173F2E] focus:ring-2 focus:ring-[#173F2E]/10 transition @error('login') border-red-500 @enderror">
                    </div>
                </div>

                {{-- Password Input with Eye Toggle --}}
                <div>
                    <label for="passwordInput" class="block text-xs font-bold text-[#102D21] uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative flex items-center">
                        <i class="bi bi-key absolute left-3.5 text-[#8A958F] text-sm pointer-events-none"></i>
                        <input type="password"
                               id="passwordInput"
                               name="password"
                               required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-11 py-3 bg-white border border-[#E0E5E2] rounded-xl text-sm font-medium text-[#102D21] placeholder-[#8A958F] focus:outline-none focus:border-[#173F2E] focus:ring-2 focus:ring-[#173F2E]/10 transition @error('password') border-red-500 @enderror">
                        <button type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute right-3 text-[#8A958F] hover:text-[#102D21] p-1.5 rounded-lg transition"
                                title="Lihat / sembunyikan kata sandi"
                                aria-label="Toggle password visibility">
                            <i id="passwordToggleIcon" class="bi bi-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember Me (NO Registration Link) --}}
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox"
                               name="remember"
                               value="1"
                               {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-[#E0E5E2] text-[#173F2E] focus:ring-[#173F2E]">
                        <span class="text-xs font-semibold text-[#617068]">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3.5 px-4 bg-[#173F2E] hover:bg-[#234D39] text-white text-sm font-bold rounded-xl flex items-center justify-center gap-2 shadow-xs transition active:scale-98 cursor-pointer">
                        <span>Masuk</span>
                        <i class="bi bi-arrow-right font-bold text-sm"></i>
                    </button>
                </div>
            </form>

            {{-- Petunjuk Akun Penanggung Jawab (Demo / Fast Login Helper) --}}
            <div class="mt-8 pt-5 border-t border-[#E0E5E2]">
                <div class="text-[11px] font-bold text-[#8A958F] uppercase tracking-wider mb-2">
                    Akun Penanggung Jawab:
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                    <div onclick="fillLogin('windah@parkirpintar.id', '12345678')"
                         class="p-2.5 rounded-xl border border-[#E0E5E2] bg-[#FAFBFA] hover:bg-[#EEF2EF] hover:border-[#173F2E]/40 cursor-pointer transition">
                        <div class="font-bold text-[#102D21]">Windah Lamuri</div>
                        <div class="text-[#617068] text-[10px]">windah@parkirpintar.id</div>
                    </div>
                    <div onclick="fillLogin('samuel@parkirpintar.id', '12345678')"
                         class="p-2.5 rounded-xl border border-[#E0E5E2] bg-[#FAFBFA] hover:bg-[#EEF2EF] hover:border-[#173F2E]/40 cursor-pointer transition">
                        <div class="font-bold text-[#102D21]">Samuel Shiva</div>
                        <div class="text-[#617068] text-[10px]">samuel@parkirpintar.id</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('passwordToggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function fillLogin(email, password) {
        document.getElementById('loginInput').value = email;
        document.getElementById('passwordInput').value = password;
    }
</script>

</body>
</html>
