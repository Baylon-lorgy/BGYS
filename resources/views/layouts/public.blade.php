<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }} @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="msapplication-TileColor" content="#8B4513">
    <meta name="theme-color" content="#8B4513">
    
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link href="{{ asset('assets/css/material-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
    <link href="{{ asset('assets/demo/demo.css') }}" rel="stylesheet" />
    <style>
        .logo {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            line-height: 1.2;
            font-size: 1.4rem;
            color: #56CCF2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo svg {
            width: 32px;
            height: 32px;
        }
    </style>
</head>
<body class="dark-edition">
    <div class="main-content">
        <div class="logo">
            <x-application-logo class="w-8 h-8" />
            {{ config('app.name') }}
        </div>
        @yield('content')
    </div>
    <script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap-material-design.min.js') }}"></script>
    <script src="https://unpkg.com/default-passive-events"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/material-dashboard.js?v=2.1.0') }}"></script>
</body>
</html> 