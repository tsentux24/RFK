<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Sistem Manajemen Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="https://e-rekrutmen.malutprov.go.id/assets/images/malut.png">

    <style>
        :root {
            --primary: #31326F;
            --primary-light: #4a4d99;
            --primary-dark: #252854;
            --accent: #4FB7B3;
            --accent-light: #6dc9c5;
            --accent-dark: #3a8d89;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
        }

        .sidebar {
            width: 260px;
            transition: all 0.3s ease;
            background: linear-gradient(to bottom, var(--primary), var(--primary-dark));
            color: white;
        }

        .main-content {
            margin-left: 260px;
            transition: all 0.3s ease;
        }

        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        .nav-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-item:hover, .nav-item.active {
            border-left-color: var(--accent);
            background-color: rgba(79, 183, 179, 0.15);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }

        .modal {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .modal.hidden {
            opacity: 0;
            transform: scale(0.9);
            pointer-events: none;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            color: #4b5563;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 4px 0;
        }

        /* Logo Styles */
        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0 0 12px 12px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 5px;
        }

        .logo-icon img {
            max-width: 100%;
            max-height: 100%;
        }

        .logo-text {
            flex: 1;
        }

        .logo-title {
            color: white;
            font-weight: 700;
            font-size: 18px;
            line-height: 1.2;
            margin: 0;
        }

        .logo-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 11px;
            margin: 0;
            font-weight: 400;
        }

        /* Button Styles */
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-accent {
            background-color: var(--accent);
            border-color: var(--accent);
        }

        .btn-accent:hover {
            background-color: var(--accent-dark);
            border-color: var(--accent-dark);
        }

        /* Responsive Logo */
        @media (max-width: 768px) {
            .logo-container {
                padding: 12px 15px;
                border-radius: 0;
            }

            .logo-title {
                font-size: 16px;
            }

            .logo-subtitle {
                font-size: 10px;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
            }
        }

        @media (max-width: 480px) {
            .logo-text {
                display: none;
            }

            .logo-container {
                justify-content: center;
                padding: 15px;
            }

            .logo-icon {
                width: 45px;
                height: 45px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    @include('dashboard.layout.nav')

    <!-- Sidebar -->
    <div class="sidebar fixed top-0 left-0 h-screen shadow-lg z-40">
        <!-- Logo Container -->
        <div class="logo-container">
            <div class="logo-icon">
                <img src="https://e-rekrutmen.malutprov.go.id/assets/images/malut.png" alt="Logo Maluku Utara">
            </div>
            <div class="logo-text">
                <h1 class="logo-title">Sistem Manajemen</h1>
                <p class="logo-subtitle">Pemerintah Maluku Utara</p>
            </div>
        </div>

       <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="/" class="nav-item flex items-center gap-3 px-4 py-3 text-white hover:text-white">
                        <i class="fas fa-home"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                @if(Auth::user()->role !== 'superadmin')
                <li>
                    <a href="/dataopd" class="nav-item flex items-center gap-3 px-4 py-3 text-white hover:text-white">
                        <i class="fas fa-building"></i>
                        <span>Manajemen OPD</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 text-white hover:text-white">
                        <i class="fas fa-project-diagram"></i>
                        <span>Proyek</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('rfk.audit') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-white hover:text-white">
                        <i class="fas fa-search"></i>
                        <span>Audit RFK</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('validation.engine') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-yellow-300 hover:text-yellow-100 bg-red-500 bg-opacity-10 font-medium">
                        <i class="fas fa-microchip"></i>
                        <span>Validation Engine</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="{{ route('laporan.index') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-white hover:text-white">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                @if(Auth::user()->role !== 'superadmin')
                <li>
                    <a href="/users" class="nav-item flex items-center gap-3 px-4 py-3 text-white hover:text-white">
                        <i class="fas fa-user"></i>
                        <span>Users</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="#" class="nav-item flex items-center gap-3 px-4 py-3 text-white hover:text-white">
                        <i class="fas fa-cog"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('panduan') }}" class="nav-item flex items-center gap-3 px-4 py-3 text-white hover:text-white">
                        <i class="fas fa-book"></i>
                        <span>Panduan SI-RAFIKA</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="absolute bottom-0 w-full p-4 border-t border-gray-700">
            <div class="flex items-center gap-3 px-4 py-2">
                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden">
                    <img src="https://eu.ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&bold=true&background=3F3DCE&color=FFFFFF" alt="Profile" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="text-sm font-medium text-white">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-300">{{ ucfirst(Auth::user()->role ?? 'Guest') }}</p>
                </div>
            </div>
        </div>
    </div>
    @yield('content')
</body>
</html>
