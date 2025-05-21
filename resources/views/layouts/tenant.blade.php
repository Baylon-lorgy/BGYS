<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ Auth::guard('tenant')->user()->bakery_name }}</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="msapplication-TileColor" content="#8B4513">
    <meta name="theme-color" content="#8B4513">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Font Awesome CDN for action icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Material Dashboard CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-dashboard@3.0.8/assets/css/material-dashboard.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #e2e8f0;
            color: #1e293b;
            font-family: 'Figtree', sans-serif;
        }
        .wrapper {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 280px;
            background-color: #1e293b;
            padding: 1.5rem;
            border-right: 1px solid rgba(148, 163, 184, 0.1);
        }
        .main-panel {
            flex: 1;
            background-color: #e2e8f0;
            padding: 1.5rem;
        }
        .sidebar .logo {
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        }
        .sidebar .logo a {
            color: #f8fafc;
            font-size: 1.5rem;
            text-decoration: none;
            font-weight: 600;
        }
        .nav-item {
            margin-bottom: 0.5rem;
        }
        .nav-link {
            color: #cbd5e1 !important;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            background-color: #334155;
            color: #f8fafc !important;
        }
        .nav-link.active {
            background-color: #3b82f6;
            color: #ffffff !important;
        }
        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        .navbar {
            background-color: #f1f5f9;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .navbar-brand {
            color: #1e293b !important;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .logout-button {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #ffffff;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }
        .logout-button:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.3);
            color: #ffffff;
            text-decoration: none;
        }
        .logout-button i {
            margin-right: 0.5rem;
            font-size: 1.25rem;
        }
        .card {
            background-color: #252b42;
            border: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .card-stats {
            padding: 20px;
        }
        .card-header {
            background-color: transparent;
            border-bottom: none;
            padding: 15px 20px;
        }
        .card-header-primary {
            background-color: #9c27b0;
            color: white;
            border-radius: 8px;
            margin: -20px 15px 0;
        }
        .stat-card {
            background-color: #252b42;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            min-height: 120px;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .stat-icon i {
            font-size: 30px;
            color: #ffffff;
        }
        .stat-title {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            margin-bottom: 5px;
        }
        .stat-value {
            color: #ffffff;
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .stat-subtitle {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
        }
        .table {
            color: rgba(255,255,255,0.7);
        }
        .table thead th {
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.5);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 12px;
        }
        .table td {
            border-top: 1px solid rgba(255,255,255,0.1);
            vertical-align: middle;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 12px;
        }
        .badge-success {
            background-color: #4caf50;
        }
        .badge-warning {
            background-color: #ff9800;
        }
        .badge-danger {
            background-color: #f44336;
        }
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            margin: 0 3px;
        }
        .btn-icon i {
            font-size: 18px;
        }
        .dropdown-menu {
            background-color: #252b42;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .dropdown-item {
            color: rgba(255,255,255,0.7);
        }
        .dropdown-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff;
        }
        .dropdown-divider {
            border-top: 1px solid rgba(255,255,255,0.1);
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <div class="logo">
                <a href="{{ route('tenant.dashboard') }}" class="simple-text">
                    Master Baker
                </a>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}" href="{{ route('tenant.dashboard') }}">
                        <i class="material-icons">dashboard</i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tenant.products.*') ? 'active' : '' }}" href="{{ route('tenant.products.index') }}">
                        <i class="material-icons">bakery_dining</i>
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tenant.subscription.*') ? 'active' : '' }}" href="{{ route('tenant.subscription.index') }}">
                        <i class="material-icons">card_membership</i>
                        Subscription
                    </a>
                </li>
            </ul>
        </div>
        <div class="main-panel">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">@yield('title')</a>
                    <div class="ml-auto">
                        <a href="{{ route('tenant.logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="logout-button">
                            <i class="material-icons">logout</i>
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('tenant.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </nav>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="content" style="background-color: #f1f5f9; min-height: 100vh; padding: 20px;">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/material-dashboard@3.0.8/assets/js/material-dashboard.min.js"></script>

    @stack('scripts')
</body>
</html> 