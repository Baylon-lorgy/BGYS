<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="msapplication-TileColor" content="#8B4513">
        <meta name="theme-color" content="#8B4513">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>{{ config('app.name') }} - @yield('title')</title>
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
        <!--     Fonts and icons     -->
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- CSS Files -->
        <link href="{{ asset('assets/css/material-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
        <style>
            :root {
                --primary-gray: #2C3E50;
                --dark-gray: #1A1D21;
                --light-blue: #56CCF2;
                --dark-blue: #2F80ED;
                --text-color: #FFFFFF;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--dark-gray);
                color: var(--text-color);
            }

            .wrapper {
                display: flex;
                min-height: 100vh;
            }

            .sidebar {
                width: 260px;
                background: linear-gradient(180deg, var(--primary-gray), var(--dark-gray));
                border-radius: 0 20px 20px 0;
                padding: 20px;
                position: fixed;
                height: 100vh;
                z-index: 100;
                border-right: 1px solid rgba(255, 255, 255, 0.1);
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px;
                margin-bottom: 30px;
            }

            .logo a {
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
                color: var(--light-blue) !important;
                font-family: 'Poppins', sans-serif;
                font-size: 1.5rem;
                font-weight: 600;
            }

            .logo svg {
                width: 32px;
                height: 32px;
            }

            .nav {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .nav-item {
                margin-bottom: 10px;
            }

            .nav-link {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 15px;
                color: var(--text-color);
                text-decoration: none;
                border-radius: 10px;
                transition: all 0.3s ease;
                opacity: 0.8;
            }

            .nav-link:hover,
            .nav-item.active .nav-link {
                background: rgba(86, 204, 242, 0.1);
                color: var(--light-blue);
                opacity: 1;
            }

            .nav-link i {
                font-size: 20px;
            }

            .main-panel {
                flex: 1;
                margin-left: 260px;
                min-height: 100vh;
                background: var(--dark-gray);
                position: relative;
            }

            .navbar {
                background: linear-gradient(90deg, var(--primary-gray), var(--dark-gray)) !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                border-radius: 0 0 20px 20px;
                padding: 15px 30px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .navbar-brand {
                color: var(--text-color) !important;
                font-family: 'Poppins', sans-serif;
                font-size: 1.2rem;
                font-weight: 500;
                opacity: 0.9;
            }

            .btn-logout {
                background: transparent;
                border: 1px solid var(--light-blue);
                color: var(--light-blue);
                padding: 8px 16px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
                font-weight: 500;
            }

            .btn-logout:hover {
                background: var(--light-blue);
                color: var(--text-color);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }

            .content {
                padding: 30px;
                margin-top: 70px;
            }

            .footer {
                padding: 20px 30px;
                color: var(--text-color);
                text-align: right;
                position: absolute;
                bottom: 0;
                width: 100%;
                opacity: 0.7;
            }

            /* Alert Styling */
            .alert {
                border-radius: 10px;
                padding: 15px 20px;
                margin-bottom: 20px;
                border: none;
                color: var(--text-color);
            }

            .alert-success {
                background: linear-gradient(135deg, rgba(76, 175, 80, 0.9), rgba(46, 125, 50, 0.9));
            }

            .alert .close {
                color: var(--text-color);
                opacity: 0.8;
            }

            .alert .close:hover {
                opacity: 1;
            }

            /* Responsive Design */
            @media (max-width: 991px) {
                .sidebar {
                    transform: translateX(-260px);
                }

                .main-panel {
                    margin-left: 0;
                }

                .sidebar-open .sidebar {
                    transform: translateX(0);
                }
            }
        </style>
    </head>
    <body>
        @if(auth()->check() || Auth::guard('tenant')->check())
        <div class="wrapper">
            <div class="sidebar">
                <div class="logo">
                    <a href="{{ route('dashboard') }}" class="simple-text logo-normal">
                        <x-application-logo class="w-8 h-8" />
                        {{ config('app.name') }}
                    </a>
                </div>
                <ul class="nav">
                    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="material-icons">dashboard</i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('tenants.index') }}">
                            <i class="material-icons">store</i>
                            <span>Bakeries</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="main-panel">
                <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top">
                    <div class="container-fluid">
                        <div class="navbar-wrapper">
                            <a class="navbar-brand" href="javascript:void(0)">@yield('nav')</a>
                        </div>
                        <div class="collapse navbar-collapse justify-content-end">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn-logout">
                                            <i class="material-icons">logout</i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <div class="content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @yield('content')
                </div>
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="copyright">
                            &copy; {{ date('Y') }} {{ config('app.name') }}
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        @else
        <div class="main-content">
            @yield('content')
        </div>
        @endif

        <!--   Core JS Files   -->
        <script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/core/bootstrap-material-design.min.js') }}"></script>
        <script src="https://unpkg.com/default-passive-events"></script>
        <script src="{{ asset('assets/js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/material-dashboard.js?v=2.1.0') }}"></script>
    </body>
</html>
