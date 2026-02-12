<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SGDEA') }} - @yield('title', 'Sistema de Gestión')</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            background-color: #f5f7fa;
            color: #1F2933;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #2767C6 0%, #102544 100%);
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: white;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            height: 35px;
        }

        .navbar-menu {
            display: flex;
            list-style: none;
            gap: 5px;
        }

        .navbar-menu a {
            color: rgba(255,255,255,0.8);
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-menu a:hover,
        .navbar-menu a.active {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .navbar-user .user-name {
            font-size: 14px;
        }

        .btn-logout {
            background: rgba(255,255,255,0.15);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.25);
        }

        /* Main Content */
        .main-content {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #1F2933;
        }

        .page-subtitle {
            color: #6B7280;
            margin-top: 5px;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: #2767C6;
            color: white;
        }

        .btn-primary:hover {
            background: #1e54a8;
        }

        .btn-secondary {
            background: #6B7280;
            color: white;
        }

        .btn-success {
            background: #10B981;
            color: white;
        }

        .btn-danger {
            background: #EF4444;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 13px;
            text-transform: uppercase;
        }

        .table tr:hover {
            background: #f9fafb;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #2767C6;
        }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .alert-warning {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
        }

        .alert-info {
            background: #dbeafe;
            border: 1px solid #93c5fd;
            color: #1e40af;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }

        /* Grid */
        .grid {
            display: grid;
            gap: 20px;
        }

        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }

        /* Stats */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1F2933;
        }

        .stat-label {
            color: #6B7280;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-menu {
                display: none;
            }
            .grid-cols-2, .grid-cols-3, .grid-cols-4 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">
            <span>📊 SGDEA</span>
        </div>

        <ul class="navbar-menu">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    🏠 Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('terceros.index') }}" class="{{ request()->routeIs('terceros.*') ? 'active' : '' }}">
                    👥 Terceros
                </a>
            </li>
            <li>
                <a href="{{ route('facturas.index') }}" class="{{ request()->routeIs('facturas.*') ? 'active' : '' }}">
                    📄 Facturas
                </a>
            </li>
            <li>
                <a href="{{ route('importaciones.index') }}" class="{{ request()->routeIs('importaciones.*') || request()->routeIs('import.*') ? 'active' : '' }}">
                    📥 Importaciones
                </a>
            </li>
            @if(auth()->check() && auth()->user()->role_id === 1)
            <li>
                <a href="{{ route('admin.auditoria.index') }}" class="{{ request()->routeIs('admin.auditoria.*') ? 'active' : '' }}">
                    📋 Auditoría
                </a>
            </li>
            @endif
        </ul>

        <div class="navbar-user">
            @auth
                <span class="user-name">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Salir</button>
                </form>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
