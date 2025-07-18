<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Bonline') }}</title>

    <!-- Scripts -->
    @stack('head_scripts')
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,.04);
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .navbar-brand {
            font-weight: 600;
            color: #2563eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0;
        }
        .navbar-brand img {
            height: 38px;
            width: auto;
        }
        .navbar-brand span {
            font-size: 0.875rem;
            font-weight: 700;
            color: #1e40af;
            letter-spacing: -0.25px;
            padding: 0.15rem 0.5rem;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 4px;
            text-transform: uppercase;
        }
        .nav-link {
            font-weight: 500;
            color: #4b5563;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
            margin: 0 0.25rem;
        }
        .nav-link:hover, .nav-link.active {
            color: #2563eb;
            background-color: #eff6ff;
        }
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        main {
            flex: 1;
            padding: 2rem 0;
        }
        .footer {
            background-color: #ffffff;
            padding: 1.5rem 0;
            margin-top: auto;
            box-shadow: 0 -2px 4px rgba(0,0,0,.04);
            border-top: 1px solid #e5e7eb;
        }
        @media (max-width: 991.98px) {
            .navbar-brand span {
                font-size: 0.8rem;
                padding: 0.1rem 0.4rem;
            }
            .navbar-nav {
                padding: 1rem 0;
            }
            .nav-link {
                padding: 0.75rem 1rem;
                margin: 0.25rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('invoices.index') }}">
                <img src="{{ asset('assets/images/bonline-logo-en.svg') }}" alt="Bonline Logo">
                <span>ERP</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                            <i class="fas fa-users me-2"></i>Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                            <i class="fas fa-file-invoice me-2"></i>Invoices
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}" href="{{ route('receipts.index') }}">
                            <i class="fas fa-receipt me-2"></i>Receipts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}" href="{{ route('quotes.index') }}">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Quotes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('service-templates.*') ? 'active' : '' }}" href="{{ route('service-templates.index') }}">
                            <i class="fas fa-tools me-2"></i>Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('subscriptions.*') ? 'active' : '' }}" href="{{ route('subscriptions.index') }}">
                            <i class="fas fa-calendar-check me-2"></i>Subscriptions
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-decoration-none border-0 p-0" style="color: #dc3545; background: none;">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer text-muted">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>&copy; {{ date('Y') }} Bonline Co. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Version 1.0.0</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>