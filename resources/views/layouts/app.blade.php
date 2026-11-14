<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ParkirPintar')</title>

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: 1px; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #343a40; }
        .sidebar .nav-link { color: rgba(255,255,255,0.75); padding: .5rem 1rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: rgba(255,255,255,0.1); border-radius: .375rem; }
        .sidebar .nav-link i { margin-right: .5rem; }
        .sidebar-heading { font-size: .75rem; text-transform: uppercase; color: rgba(255,255,255,0.4); padding: .75rem 1rem .25rem; }
        .content-area { padding: 1.5rem; }
        .slot-badge { font-size: .85rem; }
    </style>

    @stack('styles')
</head>
<body>
{{-- Navbar --}}
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-p-square-fill me-2"></i>ParkirPintar
        </a>
        <span class="navbar-text text-muted small">Kelompok 5 — Sistem Manajemen Parkir</span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        {{-- Sidebar --}}
        <nav class="col-md-3 col-lg-2 d-md-block sidebar py-3">
            <div class="sidebar-heading">Operasional</div>
            <ul class="nav flex-column px-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('check-in.*') ? 'active' : '' }}"
                       href="{{ route('check-in.create') }}">
                        <i class="bi bi-box-arrow-in-right"></i> Check-In
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('check-out.*') ? 'active' : '' }}"
                       href="{{ route('check-out.index') }}">
                        <i class="bi bi-box-arrow-left"></i> Check-Out
                    </a>
                </li>
            </ul>

            <div class="sidebar-heading mt-3">Manajemen</div>
            <ul class="nav flex-column px-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('parking-areas.*') ? 'active' : '' }}"
                       href="{{ route('parking-areas.index') }}">
                        <i class="bi bi-geo-alt"></i> Area Parkir
                    </a>
                </li>
            </ul>

            <div class="sidebar-heading mt-3">Laporan</div>
            <ul class="nav flex-column px-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.occupancy') ? 'active' : '' }}"
                       href="{{ route('reports.occupancy') }}">
                        <i class="bi bi-pie-chart"></i> Okupansi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.active') ? 'active' : '' }}"
                       href="{{ route('reports.active') }}">
                        <i class="bi bi-car-front"></i> Kendaraan Parkir
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.history') ? 'active' : '' }}"
                       href="{{ route('reports.history') }}">
                        <i class="bi bi-clock-history"></i> Riwayat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.revenue') ? 'active' : '' }}"
                       href="{{ route('reports.revenue') }}">
                        <i class="bi bi-currency-dollar"></i> Pendapatan
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Main Content --}}
        <main class="col-md-9 ms-sm-auto col-lg-10 content-area">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmMoRxFq9WzuNDFkmpCfJYTnMjr6"
        crossorigin="anonymous"></script>
@stack('scripts')
</body>
</html>
