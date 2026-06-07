<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Biro Administrasi Pembangunan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
  </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

  <!-- Header -->
  <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
      <div class="flex items-center gap-3">
        <div class="bg-white p-1.5 rounded-lg">
          <img src="https://malutprov.go.id/portal/public/malut.png" alt="Logo Maluku Utara" class="h-8">
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
        <a href="/" class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2 nav-active"><i class="fas fa-home text-sm"></i> Beranda</a>
        <a href="/master-data" class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2"><i class="fas fa-database text-sm"></i> Master Data</a>

        <!-- User Dropdown -->
        <div class="relative">

          <button id="user-menu-button" class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2">
            <img class="img-profile rounded-circle"src="https://eu.ui-avatars.com/api/?name={{ Auth::user()->name }}&bold=true&background=3F3DCE&color=FFFFFF" alt="Profile" class="rounded-circle">
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
            <form action ="{{ route('logout') }}" method="POST">
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
        <a href="/" class="hover:text-gray-200 transition-colors px-3 py-2 rounded bg-indigo-700"><i class="fas fa-home mr-2"></i> Beranda</a>
        <a href="/master-data" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i class="fas fa-database mr-2"></i> Master Data</a>

        <!-- Mobile User Menu -->
        <a href="#" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i class="fas fa-user-circle mr-2"></i> Profil</a>
        <a href="#" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i class="fas fa-cog mr-2"></i> Pengaturan</a>
        <div class="dropdown-divider"></div>
        <form action ="{{ route('logout') }}" method="POST">
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
    <!-- Filter Berdasarkan Kabupaten/Kota -->
    <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-8">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-filter text-indigo-600"></i>
        Filter Data Berdasarkan Kabupaten/Kota
      </h3>
      <div class="flex flex-wrap gap-4">
        <select class="rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
          <option value="">Pilih Kabupaten/Kota</option>
          <option value="ternate">Kota Ternate</option>
          <option value="tidore">Kota Tidore Kepulauan</option>
          <option value="halmahera-barat">Halmahera Barat</option>
          <option value="halmahera-tengah">Halmahera Tengah</option>
          <option value="halmahera-utara">Halmahera Utara</option>
          <option value="halmahera-selatan">Halmahera Selatan</option>
          <option value="halmahera-timur">Halmahera Timur</option>
          <option value="pulau-morotai">Pulau Morotai</option>
          <option value="pulau-taliabu">Pulau Taliabu</option>
        </select>
        <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
          Terapkan Filter
        </button>
        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
          Reset Filter
        </button>
      </div>
    </div>

    <!-- Judul Provinsi Maluku Utara -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-center text-gray-800">PEMERINTAH PROVINSI MALUKU UTARA</h2>
    </div>

    <!-- Statistik Utama DataSetk -->
 <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
      <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm text-gray-500">Total Dataset</p>
            <h3 class="text-2xl font-bold text-gray-800">202.860</h3>
          </div>
          <div class="bg-blue-100 p-3 rounded-lg">
            <i class="fas fa-database text-blue-600 text-xl"></i>
          </div>
        </div>
        <p class="text-xs text-green-600 mt-2"><i class="fas fa-arrow-up"></i> 12% dari bulan lalu</p>
      </div>

      <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm text-gray-500">Data Baru Bulan Ini</p>
            <h3 class="text-2xl font-bold text-gray-800">2.548</h3>
          </div>
          <div class="bg-green-100 p-3 rounded-lg">
            <i class="fas fa-chart-line text-green-600 text-xl"></i>
          </div>
        </div>
        <p class="text-xs text-green-600 mt-2"><i class="fas fa-arrow-up"></i> 8% dari bulan lalu</p>
      </div>

      <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm text-gray-500">Data Belum Di-report</p>
            <h3 class="text-2xl font-bold text-gray-800">348</h3>
          </div>
          <div class="bg-red-100 p-3 rounded-lg">
            <i class="fas fa-flag text-red-600 text-xl"></i>
          </div>
        </div>
        <p class="text-xs text-red-600 mt-2"><i class="fas fa-arrow-down"></i> 5% dari bulan lalu</p>
      </div>

      <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm text-gray-500">Data OPD</p>
            <h3 class="text-2xl font-bold text-gray-800">42</h3>
          </div>
          <div class="bg-purple-100 p-3 rounded-lg">
            <i class="fas fa-building text-purple-600 text-xl"></i>
          </div>
        </div>
        <p class="text-xs text-gray-600 mt-2">Tidak berubah dari bulan lalu</p>
      </div>
    </div>

    <!-- Chart Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Pie Chart: Data Masuk vs Data Belum Di-report -->
      <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-5 flex items-center gap-2">
          <i class="fas fa-chart-pie text-indigo-600"></i>
          Distribusi Data Bulanan
        </h3>
        <div class="chart-container">
          <canvas id="dataPieChart"></canvas>
        </div>
        <div class="mt-4 text-sm text-gray-600 text-center">
          <p>Perbandingan data yang masuk dengan data yang Belum di-report setiap bulan</p>
        </div>
      </div>

      <!-- Data per Kategori -->
      <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800 mb-5 flex items-center gap-2">
          <i class="fas fa-chart-bar text-indigo-600"></i>
          Data per Kategori
        </h3>
        <div class="space-y-4">
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Pendidikan</span>
              <span>42.158 data</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Kesehatan</span>
              <span>38.645 data</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-green-600 h-2 rounded-full" style="width: 60%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Infrastruktur</span>
              <span>35.421 data</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-yellow-600 h-2 rounded-full" style="width: 55%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Pertanian</span>
              <span>28.291 data</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-red-600 h-2 rounded-full" style="width: 45%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Lainnya</span>
              <span>58.345 data</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-purple-600 h-2 rounded-full" style="width: 75%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dataset Terbaru -->
    <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-8">
      <div class="flex justify-between items-center mb-5">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
          <i class="fas fa-history text-indigo-600"></i>
          Dataset Terbaru
        </h3>
        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
      </div>

      <div class="space-y-4">
        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
          <div>
            <h4 class="font-medium text-indigo-700">DATA USULAN MASYARAKAT 2025</h4>
            <p class="text-sm text-gray-600">Ditambahkan 2 jam yang lalu</p>
          </div>
          <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-medium">CSV</span>
        </div>

        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
          <div>
            <h4 class="font-medium text-indigo-700">Data Realisasi Anggaran Triwulan I 2025</h4>
            <p class="text-sm text-gray-600">Ditambahkan 1 hari yang lalu</p>
          </div>
          <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-medium">XLSX</span>
        </div>

        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
          <div>
            <h4 class="font-medium text-indigo-700">Statistik Pariwisata Januari 2025</h4>
            <p class="text-sm text-gray-600">Ditambahkan 2 hari yang lalu</p>
          </div>
          <span class="bg-purple-100 text-purple-800 text-xs px-2.5 py-1 rounded-full font-medium">JSON</span>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-100 text-gray-600 mt-12 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-6">
        <div>
          <h3 class="font-semibold text-gray-800 mb-4">Portal Data</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Kumpulan Data</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Organisasi</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Grup</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">API</a></li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-gray-800 mb-4">Bantuan</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Panduan</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">FAQ</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Kontak</a></li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-gray-800 mb-4">Legal</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Kebijakan Privasi</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Syarat dan Ketentuan</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Lisensi</a></li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold text-gray-800 mb-4">Terhubung</h3>
          <div class="flex gap-4 text-lg">
            <a href="#" class="text-gray-500 hover:text-indigo-600"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-gray-500 hover:text-indigo-600"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-gray-500 hover:text-indigo-600"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-gray-500 hover:text-indigo-600"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
      </div>
      <div class="pt-6 border-t border-gray-200 text-center">
        <p class="text-sm">© 2025 Biro Administrasi Pembangunan. Semua Hak Dilindungi.</p>
      </div>
    </div>
  </footer>


  <script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const menu = document.getElementById('mobile-menu');
      menu.classList.toggle('hidden');
    });

    // User dropdown toggle
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

    if (userMenuButton && userDropdown) {
      userMenuButton.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!userDropdown.contains(e.target) && e.target !== userMenuButton) {
          userDropdown.style.display = 'none';
        }
      });
    }

    // Pie Chart for Data Distribution
    const pieCtx = document.getElementById('dataPieChart').getContext('2d');
    const dataPieChart = new Chart(pieCtx, {
      type: 'pie',
      data: {
        labels: ['Data Masuk', 'Data Belum Di-report'],
        datasets: [{
          data: [2548, 348],
          backgroundColor: [
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 99, 132, 0.8)'
          ],
          borderColor: [
            'rgba(54, 162, 235, 1)',
            'rgba(255, 99, 132, 1)'
          ],
          borderWidth: 1,
          hoverOffset: 15
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              font: {
                family: "'Plus Jakarta Sans', sans-serif",
                size: 12
              },
              padding: 20
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.label || '';
                if (label) {
                  label += ': ';
                }
                label += context.raw.toLocaleString() + ' data';
                return label;
              }
            }
          }
        }
      }
    });
  </script>
</body>
</html>
