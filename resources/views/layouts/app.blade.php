<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ParkirPintar — Sistem Manajemen Parkir')</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 CSS (untuk kompatibilitas modul bawaan) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #F4F6F4;
            color: #102D21;
            margin: 0;
            padding: 0;
        }

        /* Normalisasi anchor di Tailwind & Bootstrap */
        a {
            text-decoration: none !important;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F4F6F4;
        }
        ::-webkit-scrollbar-thumb {
            background: #D0D7D3;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #B0B8B3;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-[#F4F6F4] text-[#102D21] antialiased">

<div class="flex min-h-screen">
    {{-- Left Sidebar (Fixed 256px / w-64) --}}
    <aside class="w-64 fixed inset-y-0 left-0 bg-gradient-to-b from-[#102D21] to-[#14271E] text-white flex flex-col justify-between p-5 z-40 select-none shadow-lg">
        <div>
            {{-- Brand Header --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-2 py-1 group">
                <div class="w-9 h-9 rounded-xl bg-[#D5F067] text-[#102D21] font-black text-lg flex items-center justify-center shadow-xs transition group-hover:scale-105">
                    P
                </div>
                <span class="text-white font-bold text-lg tracking-tight">ParkirPintar</span>
            </a>

            {{-- Label Section --}}
            <div class="text-[11px] font-bold tracking-widest text-[#8A958F] uppercase mt-7 mb-3 px-3">
                MENU UTAMA
            </div>

            {{-- Menu Navigasi --}}
            <nav class="flex flex-col gap-1.5">
                {{-- 1. Dashboard --}}
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('dashboard') ? 'bg-[#D5F067] text-[#102D21] shadow-xs' : 'text-[#8A958F] hover:text-white hover:bg-[#173F2E]' }}">
                    <i class="bi bi-grid-fill text-base {{ request()->routeIs('dashboard') ? 'text-[#102D21]' : 'text-[#8A958F]' }}"></i>
                    <span>Dashboard</span>
                </a>

                {{-- 2. Check-in Kendaraan --}}
                <a href="{{ route('check-in.create') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('check-in.*') ? 'bg-[#D5F067] text-[#102D21] shadow-xs' : 'text-[#8A958F] hover:text-white hover:bg-[#173F2E]' }}">
                    <i class="bi bi-box-arrow-in-right text-base {{ request()->routeIs('check-in.*') ? 'text-[#102D21]' : 'text-[#8A958F]' }}"></i>
                    <span>Check-in Kendaraan</span>
                </a>

                {{-- 3. Check-out Kendaraan --}}
                <a href="{{ route('check-out.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('check-out.*') ? 'bg-[#D5F067] text-[#102D21] shadow-xs' : 'text-[#8A958F] hover:text-white hover:bg-[#173F2E]' }}">
                    <i class="bi bi-box-arrow-left text-base {{ request()->routeIs('check-out.*') ? 'text-[#102D21]' : 'text-[#8A958F]' }}"></i>
                    <span>Check-out Kendaraan</span>
                </a>

                {{-- 4. Area Parkir --}}
                <a href="{{ route('parking-areas.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('parking-areas.*') ? 'bg-[#D5F067] text-[#102D21] shadow-xs' : 'text-[#8A958F] hover:text-white hover:bg-[#173F2E]' }}">
                    <i class="bi bi-geo-alt-fill text-base {{ request()->routeIs('parking-areas.*') ? 'text-[#102D21]' : 'text-[#8A958F]' }}"></i>
                    <span>Area Parkir</span>
                </a>

                {{-- 5. Laporan --}}
                <a href="{{ route('reports.occupancy') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('reports.*') ? 'bg-[#D5F067] text-[#102D21] shadow-xs' : 'text-[#8A958F] hover:text-white hover:bg-[#173F2E]' }}">
                    <i class="bi bi-bar-chart-fill text-base {{ request()->routeIs('reports.*') ? 'text-[#102D21]' : 'text-[#8A958F]' }}"></i>
                    <span>Laporan</span>
                </a>
            </nav>
        </div>

        {{-- Bottom Sidebar --}}
        <div>
            {{-- Status Card --}}
            <div class="bg-[#173F2E]/75 border border-[#294E3B] rounded-2xl p-3.5 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B8CF54] shadow-[0_0_8px_#B8CF54]"></span>
                    <span class="text-white text-xs font-bold">Sistem beroperasi normal</span>
                </div>
                <div class="text-[10px] text-[#8A958F] font-medium mt-1.5 pl-4">
                    Terakhir disinkronkan<br>
                    <span class="text-slate-300 font-semibold">{{ now()->setTimezone('Asia/Jakarta')->isoFormat('D MMM YYYY, HH:mm') }} WIB</span>
                </div>
            </div>

            {{-- User Profile Footer --}}
            <div class="flex items-center gap-3 pt-3 border-t border-[#173F2E]/80">
                <div class="w-9 h-9 rounded-full bg-[#E7B89B] text-[#102D21] font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name ?? 'Ahmad Rizky', 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-white text-xs font-bold truncate">
                        {{ auth()->user()->name ?? 'Ahmad Rizky' }}
                    </div>
                    <div class="text-[#8A958F] text-[11px] font-medium truncate">
                        Administrator
                    </div>
                </div>
                <div class="text-[#8A958F] hover:text-white cursor-pointer px-1 py-0.5 text-xs font-black">
                    •••
                </div>
            </div>
        </div>
    </aside>

    {{-- Main Content Canvas (Offset 256px / ml-64) --}}
    <div class="ml-64 flex-1 bg-[#F4F6F4] min-h-screen flex flex-col">
        {{-- Top Header Bar --}}
        <header class="px-8 py-5 flex items-center justify-between sticky top-0 bg-[#F4F6F4]/90 backdrop-blur-md z-30">
            {{-- Search Bar --}}
            <form action="{{ route('check-out.index') }}" method="GET" class="relative flex items-center">
                <i class="bi bi-search absolute left-4 text-[#8A958F] text-xs"></i>
                <input type="text"
                       id="quickSearchInput"
                       name="search"
                       placeholder="Cari tiket atau plat nomor..."
                       class="w-80 pl-9 pr-12 py-2 bg-white border border-[#E0E5E2] rounded-full text-xs font-medium text-[#102D21] placeholder-[#8A958F] focus:outline-none focus:border-[#173F2E] focus:ring-2 focus:ring-[#173F2E]/10 transition shadow-xs">
                <span class="absolute right-3 px-1.5 py-0.5 text-[10px] font-bold text-[#8A958F] bg-[#F4F6F4] rounded-md border border-[#E0E5E2] pointer-events-none">
                    ⌘K
                </span>
            </form>

            {{-- Right Header Actions --}}
            <div class="flex items-center gap-3">
                {{-- Notification Button --}}
                <div class="relative w-9 h-9 rounded-full bg-white border border-[#E0E5E2] flex items-center justify-center text-[#102D21] shadow-xs cursor-pointer hover:bg-gray-50 transition" title="Notifikasi">
                    <i class="bi bi-bell text-xs"></i>
                    <span class="w-2 h-2 rounded-full bg-[#EA6546] border border-white absolute top-2 right-2"></span>
                </div>

                {{-- Primary Action Button --}}
                <a href="{{ route('check-in.create') }}"
                   class="px-4 py-2 rounded-xl bg-[#173F2E] hover:bg-[#234D39] text-white text-xs font-bold flex items-center gap-2 shadow-xs transition active:scale-98">
                    <i class="bi bi-plus-lg text-xs font-bold"></i>
                    <span>Check-in Baru</span>
                </a>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-8 pt-2">
            @if(session('success'))
                <div class="bg-[#EDF5D6] border border-[#C4DA65] text-[#536B20] rounded-2xl px-4 py-3 mb-4 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-sm"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close text-xs" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove()"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-[#FEF6EE] border border-[#F9DBAF] text-[#B54708] rounded-2xl px-4 py-3 mb-4 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button type="button" class="btn-close text-xs" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove()"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-[#FEE4E2] border border-[#FECDCA] text-[#D92D20] rounded-2xl px-4 py-3 mb-4 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-x-circle-fill text-sm"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close text-xs" data-bs-dismiss="alert" aria-label="Close" onclick="this.parentElement.remove()"></button>
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="px-8 pb-10 flex-1">
            @yield('content')
        </main>
    </div>
</div>

{{-- Bootstrap 5 Bundle JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmMoRxFq9WzuNDFkmpCfJYTnMjr6" crossorigin="anonymous"></script>

{{-- Shortcut ⌘K / Ctrl+K Script --}}
<script>
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const input = document.getElementById('quickSearchInput');
            if (input) {
                input.focus();
                input.select();
            }
        }
    });
</script>

@stack('scripts')
</body>
</html>
