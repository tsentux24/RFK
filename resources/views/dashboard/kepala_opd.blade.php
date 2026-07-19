<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Biro Administrasi Pembangunan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .search-input {
            transition: all 0.3s ease;
        }

        .search-input:focus {
            width: 280px !important;
        }

        .opd-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .opd-item:hover {
            border-left-color: #4f46e5;
            background-color: #f8fafc;
        }

        .nav-active {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .search-input:focus {
                width: 180px !important;
            }
        }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
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
    </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
            <div class="flex items-center gap-3">
                <div class="bg-white p-1.5 rounded-lg">
                    <img src="https://e-rekrutmen.malutprov.go.id/assets/images/malut.png" alt="Logo Maluku Utara"
                        class="h-8">
                </div>
                <div>
                    <h2 class="text-xl font-bold">Biro Administrasi Pembangunan</h2>
                    <p class="text-xs opacity-80">Provinsi Maluku Utara</p>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" class="md:hidden text-2xl">
                <i class="fas fa-bars"></i>
            </button>

            <nav class="hidden md:flex gap-2 font-medium">
                <a href="{{ route('dashboard') }}"
                    class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2 nav-active"><i
                        class="fas fa-home text-sm"></i> Beranda</a>
                <a href="{{ route('panduan') }}"
                    class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2"><i
                        class="fas fa-book text-sm"></i> Panduan</a>

                <!-- User Dropdown -->
                <div class="relative">

                    <button id="user-menu-button"
                        class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2">
                        <img class="img-profile rounded-circle"
                            src="https://eu.ui-avatars.com/api/?name={{ Auth::user()->name }}&bold=true&background=3F3DCE&color=FFFFFF"
                            alt="Profile" class="rounded-circle">
                        {{ Auth::user()->name }}<i class="fas fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <div id="user-dropdown" class="dropdown-content">
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user-circle"></i> Profil
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog"></i> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt w-5 text-red-400 mr-3"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>


        </div>

        <!-- Mobile Menu (hidden by default) -->
        <div id="mobile-menu" class="hidden md:hidden bg-indigo-800 p-4">

            <div class="flex flex-col gap-3">
                <a href="{{ route('dashboard') }}"
                    class="hover:text-gray-200 transition-colors px-3 py-2 rounded bg-indigo-700"><i
                        class="fas fa-home mr-2"></i> Beranda</a>
                <a href="{{ route('panduan') }}" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i
                        class="fas fa-book mr-2"></i> Panduan</a>

                <!-- Mobile User Menu -->
                <a href="#" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i
                        class="fas fa-user-circle mr-2"></i> Profil</a>
                <a href="#" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i
                        class="fas fa-cog mr-2"></i> Pengaturan</a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt w-5 text-red-400 mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Konten Beranda -->
    <main class="max-w-7xl mx-auto mt-8 p-4">
        <!-- Judul OPD -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-center text-gray-800">
                {{ Auth::user()->opd ? Auth::user()->opd->nama_opd : 'Dinas / OPD' }}
            </h2>
            <p class="text-center text-gray-600 mt-1">Dashboard Kepala OPD</p>
        </div>

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8" id="summaryCardsContainer">
            <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
                <div class="text-center py-2 text-gray-500">Memuat...</div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
                <div class="text-center py-2 text-gray-500">Memuat...</div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
                <div class="text-center py-2 text-gray-500">Memuat...</div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
                <div class="text-center py-2 text-gray-500">Memuat...</div>
            </div>
        </div>


        <!-- Top 10 Program -->
        <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-indigo-600"></i> Top 10 Program (Berdasarkan Pagu)
            </h3>
            <div class="chart-container" style="height: 350px;">
                <canvas id="top10Chart"></canvas>
            </div>
        </div>

        <!-- Menunggu Approval RFK -->
        <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-8">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-yellow-600"></i>
                    Menunggu Approval
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full"
                        id="pendingApprovalCount">0</span>
                </h3>
                <button onclick="loadPendingApproval()"
                    class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-lg">Program</th>
                            <th class="px-4 py-3">Nilai Keuangan</th>
                            <th class="px-4 py-3">Fisik</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Staff Input</th>
                            <th class="px-4 py-3 rounded-tr-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="approvalTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-4">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Data Program RFK -->
        <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-8">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-indigo-600"></i>
                    Data Program RFK
                </h3>
                <button onclick="loadAllData()"
                    class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>

            <div class="space-y-4" id="allDataContainer">
                <div class="text-center py-4 text-gray-500">Memuat data...</div>
            </div>
        </div>

        <!-- Audit Trail RFK -->
        <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-8">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-600"></i>
                    Riwayat Keseluruhan RFK (Audit Trail)
                </h3>
                <button onclick="loadHistoryRFK()"
                    class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 rounded-tl-lg">Waktu</th>
                            <th class="px-4 py-3">Program</th>
                            <th class="px-4 py-3">Perubahan Status</th>
                            <th class="px-4 py-3">Kegiatan / Sub Kegiatan</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 rounded-tr-lg">Oleh</th>
                        </tr>
                    </thead>
                    <tbody id="auditTableBody">
                        <tr>
                            <td colspan="5" class="text-center py-4">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Detail & Ubah Status Approval -->
        <div id="approvalModal"
            class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-yellow-600 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-white">Detail & Form Ubah Status</h3>
                    <button onclick="closeApprovalModal()" class="text-white hover:text-gray-200"><i
                            class="fas fa-times"></i></button>
                </div>
                <div class="p-5 flex flex-col md:flex-row gap-5">
                    <div class="flex-1 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Rincian Program Master</h4>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Kode Program:</span> <span
                                id="appr_kode" class="font-medium text-gray-900"></span></p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Nama Program:</span> <span
                                id="appr_program" class="font-medium text-gray-900"></span></p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Kegiatan:</span> <span
                                id="appr_kegiatan" class="font-medium text-gray-900"></span></p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Sub Kegiatan:</span> <span
                                id="appr_sub_kegiatan" class="font-medium text-gray-900"></span></p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Sumber Dana:</span> <span
                                id="appr_sumber_dana" class="font-medium text-gray-900"></span></p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Tahun Anggaran:</span> <span
                                id="appr_tahun" class="font-medium text-gray-900"></span></p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Keterangan Master:</span> <span
                                id="appr_master_ket" class="font-medium text-gray-900"></span></p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Sisa Pagu:</span> <span
                                id="appr_sisa_pagu" class="font-medium text-red-600"></span></p>

                        <h4 class="font-bold text-indigo-700 mt-4 mb-3 border-b pb-2">Pengajuan Realisasi (PENDING)</h4>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Nilai Diajukan:</span>
                            <span id="appr_keuangan" class="font-medium text-green-600"></span>
                        </p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Progress Fisik:</span>
                            <span id="appr_fisik" class="font-medium text-blue-600"></span>
                        </p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Proyeksi Sisa Pagu:</span>
                            <span id="appr_sisa_pagu_baru" class="font-medium text-red-600"
                                title="Sisa Pagu jika pengajuan ini disetujui"></span>
                        </p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Keterangan:</span> <span
                                id="appr_ket" class="font-medium text-gray-900"></span></p>
                        <p class="text-sm mb-2"><span class="text-gray-500 w-32 inline-block">Diinput Oleh:</span> <span
                                id="appr_staff" class="font-medium text-gray-900"></span></p>
                    </div>
                    <div class="flex-1">
                        <form id="approvalForm">
                            <input type="hidden" id="appr_id">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ubah Status</label>
                                <select id="appr_status"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                    <option value="">-- Pilih --</option>
                                    <option value="approve">APPROVE (Setujui)</option>
                                    <option value="reject">REJECT (Tolak)</option>
                                </select>
                            </div>
                            <div class="mb-4" id="appr_keterangan_container" style="display:none;">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan
                                    Penolakan</label>
                                <textarea id="appr_keterangan"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                    rows="3" placeholder="Wajib diisi jika ditolak..."></textarea>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-5 border-t border-gray-200">
                    <button onclick="closeApprovalModal()"
                        class="px-4 py-2 text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
                    <button onclick="submitApprovalChange()"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Proses
                        Perubahan</button>
                </div>
            </div>
        </div>

        <!-- Modal Detail Program (Approved) -->
        <div id="detailProgramModal"
            class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl">
                <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-indigo-600 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-white">Detail & Riwayat Program RFK</h3>
                    <button onclick="closeDetailModal()" class="text-white hover:text-gray-200"><i
                            class="fas fa-times"></i></button>
                </div>
                <div class="p-5 max-h-[80vh] overflow-y-auto">
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 mb-5">
                        <h4 class="font-bold text-indigo-800 mb-3" id="det_nama_program"></h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><span class="text-gray-500">Kode Program:</span> <span id="det_kode"
                                    class="font-medium"></span></div>
                            <div><span class="text-gray-500">Pagu:</span> <span id="det_pagu"
                                    class="font-medium"></span></div>
                            <div class="col-span-2"><span class="text-gray-500">Kegiatan:</span> <span id="det_kegiatan"
                                    class="font-medium"></span></div>
                            <div class="col-span-2"><span class="text-gray-500">Sub Kegiatan:</span> <span id="det_sub_kegiatan"
                                    class="font-medium"></span></div>
                            <div class="col-span-2"><span class="text-gray-500">Keterangan Master:</span> <span id="det_master_ket"
                                    class="font-medium text-gray-800"></span></div>
                            <div><span class="text-gray-500">Realisasi Keuangan:</span> <span id="det_keuangan"
                                    class="font-medium text-green-600"></span></div>
                            <div><span class="text-gray-500">Progress Fisik:</span> <span id="det_fisik"
                                    class="font-medium text-blue-600"></span></div>
                            <div><span class="text-gray-500">Tahun Anggaran:</span> <span id="det_tahun"
                                    class="font-medium"></span></div>
                            <div><span class="text-gray-500">Sumber Dana:</span> <span id="det_sumber"
                                    class="font-medium"></span></div>
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-700 mb-3 flex items-center"><i
                            class="fas fa-history mr-2 text-gray-500"></i> Riwayat Pengajuan Realisasi</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100 text-gray-600">
                                <tr>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Fisik</th>
                                    <th class="px-4 py-2">Keuangan</th>
                                    <th class="px-4 py-2">Kegiatan/Sub Kegiatan</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="det_riwayat_body">
                                <!-- Riwayat realisasi -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-5 border-t border-gray-200 text-right">
                    <button onclick="closeDetailModal()"
                        class="px-4 py-2 text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50">Tutup</button>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 text-gray-600 mt-12 py-6 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center">
                <p class="text-sm">© 2025 Biro Administrasi Pembangunan. Semua Hak Dilindungi.</p>
            </div>
        </div>
    </footer>


    <script>

    function formatRupiahManual(angka) {
        if (angka === null || angka === undefined) return '0';
        let parsed = parseFloat(angka);
        if (isNaN(parsed)) return '0';
        let str = Math.round(parsed).toString();
        let isNegative = false;
        if (str.startsWith('-')) {
            isNegative = true;
            str = str.substring(1);
        }
        let sisa = str.length % 3;
        let rupiah = str.substr(0, sisa);
        let ribuan = str.substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return isNegative ? '-' + rupiah : rupiah;
    }

        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // User dropdown toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', function (e) {
                e.stopPropagation();
                userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!userDropdown.contains(e.target) && e.target !== userMenuButton) {
                    userDropdown.style.display = 'none';
                }
            });
        }


        let top10ChartObj = null;
        function renderTop10Chart(data) {
            const sortedData = [...data].sort((a, b) => b.pagu - a.pagu).slice(0, 10);
            
            const labels = sortedData.map(item => (item.nama_program || 'Program').substring(0, 35) + '...');
            const paguData = sortedData.map(item => item.pagu || 0);
            const realisasiData = sortedData.map(item => item.realisasi_keuangan || 0);

            if(top10ChartObj) top10ChartObj.destroy();

            const ctx = document.getElementById('top10Chart').getContext('2d');
            top10ChartObj = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pagu (Rp)',
                            data: paguData,
                            backgroundColor: '#E2E8F0',
                            borderRadius: 4
                        },
                        {
                            label: 'Realisasi Keuangan (Rp)',
                            data: realisasiData,
                            backgroundColor: '#10B981',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + formatRupiahStr(context.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                callback: function(value) {
                                    if(value >= 1e9) return (value/1e9).toFixed(1) + ' M';
                                    if(value >= 1e6) return (value/1e6).toFixed(1) + ' Jt';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }

        function formatRupiahStr(angka) {
            return formatRupiahManual(angka);
        }

        let pendingDataStore = [];
        let allDataStore = [];

        async function loadPendingApproval() {
            try {
                const response = await fetch('{{ route("rfk.pending") }}');
                const result = await response.json();

                if (result.success) {
                    pendingDataStore = result.data;
                    document.getElementById('pendingApprovalCount').innerText = result.data.length;

                    let rows = '';
                    result.data.forEach((item, index) => {
                        const programName = item.nama_program || '-';
                        const staffName = item.user ? item.user.name : '-';

                        const pendingRealisasi = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0] : null;
                        const nilaiDiajukan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_keuangan : 0;
                        const fisikDiajukan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_fisik : 0;
                        const ketDiajukan = pendingRealisasi ? pendingRealisasi.keterangan : item.keterangan;

                        rows += `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">${programName}</td>
                            <td class="px-4 py-3 text-green-600 font-medium">Rp ${formatRupiahStr(nilaiDiajukan)}</td>
                            <td class="px-4 py-3 text-blue-600">${fisikDiajukan}%</td>
                            <td class="px-4 py-3">
                                <div class="text-xs mb-1"><span class="font-semibold text-gray-500">Master:</span> ${item.keterangan || '-'}</div>
                                <div class="text-xs mb-1"><span class="font-semibold text-gray-500">Pengajuan:</span> ${pendingRealisasi && pendingRealisasi.keterangan ? pendingRealisasi.keterangan : '-'}</div>
                                <div class="text-xs mb-1"><span class="font-semibold text-gray-500">Sumber Dana:</span> ${item.sumber_dana || '-'} ${item.sumber_dana_detail ? '('+item.sumber_dana_detail+')' : ''}</div>
                                <div class="text-xs"><span class="font-semibold text-gray-500">Tahun:</span> ${item.tahun_anggaran || '-'}</div>
                            </td>
                            <td class="px-4 py-3">${staffName}</td>
                            <td class="px-4 py-3">
                                <button onclick="openApprovalModal(${index})" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded text-xs shadow-sm"><i class="fas fa-edit mr-1"></i> Form Ubah Status</button>
                            </td>
                        </tr>
                    `;
                    });

                    if (result.data.length === 0) {
                        rows = '<tr><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada pengajuan PENDING</td></tr>';
                    }

                    document.getElementById('approvalTableBody').innerHTML = rows;
                }
            } catch (error) {
                console.error("Error fetching pending data", error);
            }
        }

        // Functions removed, replaced by submitApprovalChange

        async function loadHistoryRFK() {
            try {
                const response = await fetch('{{ route("rfk.history") }}');
                const result = await response.json();

                if (result.success) {
                    let rows = '';
                    result.data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleString('id-ID');
                        const program = item.nama_program || '-';
                        const kode = item.kode_program || '-';
                        const user = item.user ? item.user.name : '-';

                        let statusBadge = '';
                        if (item.status_baru === 'APPROVE') statusBadge = '<span class="bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded text-[10px]">APPROVE</span>';
                        else if (item.status_baru === 'REJECT') statusBadge = '<span class="bg-rose-100 text-rose-700 font-bold px-2 py-0.5 rounded text-[10px]">REJECT</span>';
                        else statusBadge = '<span class="bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded text-[10px]">PENDING</span>';

                        const prevStatus = item.status_sebelumnya ? item.status_sebelumnya : 'Baru';

                        rows += `
                        <tr class="border-b hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">${date}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-slate-800">
                                <div class="whitespace-normal leading-relaxed min-w-[200px]">${program}</div>
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap">${prevStatus} &rarr; <br/> ${statusBadge}</td>
                            <td class="px-4 py-3 text-xs min-w-[250px]">
                                <div class="mb-1 font-mono text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded inline-block">Kode: ${kode}</div>
                                <div class="font-semibold text-indigo-700 mb-1 leading-snug">${item.kegiatan || '-'}</div>
                                <div class="text-[10px] text-slate-500 leading-snug">${item.sub_kegiatan || '-'}</div>
                            </td>
                            <td class="px-4 py-3 text-xs min-w-[150px]">
                                <div class="text-slate-700 font-medium">${item.keterangan || '-'}</div>
                                <div class="text-[10px] text-slate-500 mt-1 italic">Master: ${item.keterangan_realisasi || '-'}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-medium text-slate-600">${user}</td>
                        </tr>
                    `;
                    });

                    if (result.data.length === 0) {
                        rows = '<tr><td colspan="6" class="text-center py-4">Tidak ada data riwayat</td></tr>';
                    }

                    document.getElementById('auditTableBody').innerHTML = rows;
                }
            } catch (error) {
                console.error("Error fetching history", error);
            }
        }

        async function loadAllData() {
            try {
                const response = await fetch('{{ route("rfk.data") }}');
                const result = await response.json();

                if (result.success) {
                    allDataStore = result.data;
                    // Render Top 10 Chart
                    renderTop10Chart(result.data);

                    // Only show APPROVED and SELESAI data in this section
                    const approvedData = result.data.filter(item => item.status === 'APPROVE' || item.status === 'SELESAI');

                    let html = '';
                    approvedData.forEach((item, index) => {
                        const fisik = item.realisasi_fisik;
                        let statusColor = 'bg-blue-600';
                        if (fisik >= 100) statusColor = 'bg-green-600';
                        else if (fisik < 30) statusColor = 'bg-red-600';
                        
                        let statusBadgeClass = 'bg-green-100 text-green-800 border-green-200';
                        if (item.status === 'SELESAI') statusBadgeClass = 'bg-blue-100 text-blue-800 border-blue-200';

                        html += `
                    <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-indigo-700">${item.nama_program}</h4>
                            <div class="flex items-center gap-2">
                                <span class="${statusBadgeClass} border text-xs px-2.5 py-1 rounded-full font-medium">${item.status}</span>
                                <button onclick="openDetailModal(${item.id})" class="text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-2 py-1 rounded border border-indigo-200"><i class="fas fa-list"></i> Rincian & Riwayat</button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${item.user ? item.user.name : '-'}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="${statusColor} h-2 rounded-full progress-bar" style="width: ${fisik}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Progress Fisik: ${fisik}%</span>
                            <span>Realisasi: Rp ${formatRupiahStr(item.realisasi_keuangan)} / Pagu: Rp ${formatRupiahStr(item.pagu)}</span>
                        </div>
                        <div class="text-xs text-right text-red-600 font-medium">
                            Sisa Pagu: Rp ${formatRupiahStr(item.sisa_pagu)}
                        </div>
                    </div>`;
                    });

                    if (approvedData.length === 0) {
                        html = '<div class="text-center py-4 text-gray-500">Tidak ada Program RFK dengan status APPROVED</div>';
                    }
                    document.getElementById('allDataContainer').innerHTML = html;

                    // Update Summary Cards
                    if (result.statistics) {
                        const stats = result.statistics;
                        const statsHtml = `
                        <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Total Program</p>
                                    <h3 class="text-2xl font-bold text-gray-800">${stats.total_program}</h3>
                                </div>
                                <div class="bg-indigo-100 p-3 rounded-lg"><i class="fas fa-folder-open text-indigo-600 text-xl"></i></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Total Pagu</p>
                                    <h3 class="text-lg font-bold text-gray-800">Rp ${formatRupiahStr(stats.total_pagu)}</h3>
                                </div>
                                <div class="bg-cyan-100 p-3 rounded-lg"><i class="fas fa-chart-line text-cyan-600 text-xl"></i></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Rata-rata Fisik</p>
                                    <h3 class="text-2xl font-bold text-gray-800">${stats.avg_fisik}%</h3>
                                </div>
                                <div class="bg-green-100 p-3 rounded-lg"><i class="fas fa-percent text-green-600 text-xl"></i></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm text-gray-500">Program Terlambat</p>
                                    <h3 class="text-2xl font-bold text-gray-800">${stats.terlambat}</h3>
                                </div>
                                <div class="bg-red-100 p-3 rounded-lg"><i class="fas fa-exclamation-triangle text-red-600 text-xl"></i></div>
                            </div>
                        </div>
                    `;
                        document.getElementById('summaryCardsContainer').innerHTML = statsHtml;
                    }

                    setTimeout(() => {
                        const progressBars = document.querySelectorAll('.progress-bar');
                        progressBars.forEach(bar => {
                            const width = bar.style.width;
                            bar.style.width = '0';
                            setTimeout(() => { bar.style.width = width; }, 100);
                        });
                    }, 100);
                }
            } catch (e) {
                console.error("Error fetching all data", e);
            }
        }

        // Modal Logic
        const approvalModal = document.getElementById('approvalModal');
        const apprStatusSelect = document.getElementById('appr_status');
        const apprKeteranganContainer = document.getElementById('appr_keterangan_container');

        apprStatusSelect.addEventListener('change', function () {
            if (this.value === 'reject') {
                apprKeteranganContainer.style.display = 'block';
            } else {
                apprKeteranganContainer.style.display = 'none';
            }
        });

        function openApprovalModal(index) {
            const item = pendingDataStore[index];
            if (!item) return;

            document.getElementById('appr_id').value = item.id;
            document.getElementById('appr_kode').innerText = item.kode_program || '-';
            document.getElementById('appr_program').innerText = item.nama_program || '-';
            document.getElementById('appr_kegiatan').innerText = item.kegiatan || '-';
            document.getElementById('appr_sub_kegiatan').innerText = item.sub_kegiatan || '-';
            document.getElementById('appr_sumber_dana').innerText = item.sumber_dana || '-';
            if (item.sumber_dana_detail) {
                document.getElementById('appr_sumber_dana').innerText += ' (' + item.sumber_dana_detail + ')';
            }
            document.getElementById('appr_tahun').innerText = item.tahun_anggaran || '-';
            document.getElementById('appr_master_ket').innerText = item.keterangan || '-';
            document.getElementById('appr_sisa_pagu').innerText = 'Rp ' + formatRupiahStr(item.sisa_pagu);

            const pendingRealisasi = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0] : null;
            const nilaiDiajukan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_keuangan : 0;
            const fisikDiajukan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_fisik : 0;
            const ketDiajukan = pendingRealisasi ? pendingRealisasi.keterangan : item.keterangan;
            const sisaBaru = Math.max(0, item.sisa_pagu - nilaiDiajukan);

            document.getElementById('appr_keuangan').innerText = 'Rp ' + formatRupiahStr(nilaiDiajukan);
            document.getElementById('appr_fisik').innerText = fisikDiajukan + '%';
            document.getElementById('appr_sisa_pagu_baru').innerText = 'Rp ' + formatRupiahStr(sisaBaru);
            document.getElementById('appr_ket').innerText = ketDiajukan || '-';
            document.getElementById('appr_staff').innerText = item.user ? item.user.name : '-';

            apprStatusSelect.value = '';
            document.getElementById('appr_keterangan').value = '';
            apprKeteranganContainer.style.display = 'none';

            approvalModal.classList.remove('hidden');
        }

        function closeApprovalModal() {
            approvalModal.classList.add('hidden');
        }

        async function submitApprovalChange() {
            const id = document.getElementById('appr_id').value;
            const action = apprStatusSelect.value;
            const keterangan = document.getElementById('appr_keterangan').value;

            if (!action) {
                Swal.fire('Perhatian', 'Pilih status terlebih dahulu!', 'warning');
                return;
            }

            if (action === 'reject' && keterangan.trim() === '') {
                Swal.fire('Perhatian', 'Keterangan / Alasan penolakan wajib diisi!', 'warning');
                return;
            }

            const confirmResult = await Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin memproses perubahan status ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            });

            if (!confirmResult.isConfirmed) return;

            try {
                const url = `/dashboard/rfk/${id}/change-status`;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: action.toUpperCase(), keterangan })
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire('Berhasil!', result.message, 'success');
                    closeApprovalModal();
                    loadPendingApproval();
                    loadAllData();
                    loadHistoryRFK();
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
            }
        }

        // Detail Program Modal
        const detailProgramModal = document.getElementById('detailProgramModal');

        function openDetailModal(id) {
            const item = allDataStore.find(d => d.id === id);
            if (!item) return;

            document.getElementById('det_nama_program').innerText = item.nama_program;
            document.getElementById('det_kode').innerText = item.kode || '-';
            document.getElementById('det_kegiatan').innerText = item.kegiatan || '-';
            document.getElementById('det_sub_kegiatan').innerText = item.sub_kegiatan || '-';
            document.getElementById('det_master_ket').innerText = item.keterangan || '-';
            document.getElementById('det_pagu').innerText = 'Rp ' + formatRupiahStr(item.pagu);
            document.getElementById('det_keuangan').innerText = 'Rp ' + formatRupiahStr(item.realisasi_keuangan);
            document.getElementById('det_fisik').innerText = item.realisasi_fisik + '%';
            document.getElementById('det_tahun').innerText = item.tahun_anggaran;
            document.getElementById('det_sumber').innerText = item.sumber_dana + (item.sumber_dana_detail ? ` (${item.sumber_dana_detail})` : '');

            let rows = '';
            if (item.realisasis && item.realisasis.length > 0) {
                item.realisasis.forEach(r => {
                    const date = new Date(r.created_at).toLocaleString('id-ID');
                    let st = '';
                    if (r.status === 'APPROVE') st = '<span class="bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded text-[10px]">APPROVE</span>';
                    else if (r.status === 'REJECT') st = '<span class="bg-rose-100 text-rose-700 font-bold px-2 py-0.5 rounded text-[10px]">REJECT</span>';
                    else st = '<span class="bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded text-[10px]">PENDING</span>';

                    rows += `
                    <tr class="border-b bg-white hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-xs whitespace-nowrap text-slate-500">${date}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-blue-600">${r.nilai_realisasi_fisik}%</td>
                        <td class="px-4 py-3 text-xs font-semibold text-green-600 whitespace-nowrap">Rp ${formatRupiahStr(r.nilai_realisasi_keuangan)}</td>
                        <td class="px-4 py-3 text-xs min-w-[200px]">
                            <div class="font-semibold text-indigo-700 mb-1 leading-snug">${r.kegiatan || '-'}</div>
                            <div class="text-[10px] text-slate-500 leading-snug">${r.sub_kegiatan || '-'}</div>
                        </td>
                        <td class="px-4 py-3 text-xs">${st}</td>
                        <td class="px-4 py-3 text-xs text-slate-600">${r.keterangan || '-'}</td>
                    </tr>
                `;
                });
            } else {
                rows = '<tr><td colspan="6" class="text-center py-3 text-gray-500">Belum ada riwayat realisasi</td></tr>';
            }
            document.getElementById('det_riwayat_body').innerHTML = rows;

            detailProgramModal.classList.remove('hidden');
        }

        function closeDetailModal() {
            detailProgramModal.classList.add('hidden');
        }

        // Load count initially
        document.addEventListener('DOMContentLoaded', function () {
            loadPendingApproval();
            loadHistoryRFK();
            loadAllData();
        });
    </script>
</body>

</html>