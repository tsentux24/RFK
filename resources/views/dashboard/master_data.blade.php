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
    .table-container {
      overflow-x: auto;
      border-radius: 12px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }
    th {
      position: sticky;
      top: 0;
      background-color: #4f46e5;
      color: white;
      padding: 12px 16px;
      text-align: left;
      font-weight: 600;
      font-size: 0.875rem;
    }
    td {
      padding: 16px;
      border-bottom: 1px solid #e5e7eb;
      font-size: 0.875rem;
    }
    tr:last-child td {
      border-bottom: none;
    }
    tr:hover {
      background-color: #f9fafb;
    }
    /* Loading overlay styles */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      transition: opacity 0.3s ease;
    }
    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #4f46e5;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .hidden {
      display: none;
    }
    /* Dropdown styles */
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
          <img src="https://malutprov.go.id/portal/public/malut.png" alt="Logo Jawa Tengah" class="h-8">
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
        <a href="/" class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2"><i class="fas fa-home text-sm"></i> Beranda</a>
        <a href="/master-data" class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2 nav-active"><i class="fas fa-database text-sm"></i> Master Data</a>

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
            <a href="#" class="dropdown-item">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </div>
      </nav>


    </div>

    <!-- Mobile Menu (hidden by default) -->
    <div id="mobile-menu" class="hidden md:hidden bg-indigo-800 p-4">
      <div class="flex flex-col gap-3">
        <a href="/" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i class="fas fa-home mr-2"></i> Beranda</a>
        <a href="/master-data" class="hover:text-gray-200 transition-colors px-3 py-2 rounded bg-indigo-700"><i class="fas fa-database mr-2"></i> Master Data</a>

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

  <!-- Loading Overlay -->
  <div id="loading-overlay" class="loading-overlay hidden">
    <div class="loading-spinner"></div>
  </div>

  <!-- Konten Master Data -->
  <main class="max-w-7xl mx-auto mt-8 p-4">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Master Data OPD</h1>
        <p class="text-gray-600">Daftar Organisasi Perangkat Daerah Provinsi Maluku Utara</p>
      </div>
      <div class="flex gap-3">
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
          <i class="fas fa-download"></i> Ekspor Data
        </button>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl p-5 shadow-md border border-gray-100 mb-6">
      <div class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari OPD</label>
            <div class="relative">
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              <input type="text" id="search-opd" placeholder="Nama OPD..." class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl w-full focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status-filter" class="border border-gray-300 rounded-xl px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-indigo-400">
              <option value="all">Semua Status</option>
              <option value="active">Aktif</option>
              <option value="inactive">Non-Aktif</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Dataset</label>
            <select id="dataset-filter" class="border border-gray-300 rounded-xl px-4 py-2.5 w-full focus:outline-none focus:ring-2 focus:ring-indigo-400">
              <option value="all">Semua Jumlah</option>
              <option value="0-1000">0 - 1,000</option>
              <option value="1001-5000">1,001 - 5,000</option>
              <option value="5001-10000">5,001 - 10,000</option>
              <option value="10001+">10,001+</option>
            </select>
          </div>
        </div>
        <div>
          <button id="apply-filter" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl flex items-center gap-2 transition-colors">
            <i class="fas fa-filter"></i> Terapkan Filter
          </button>
        </div>
      </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="table-container">
        <table id="opd-table">
          <thead>
            <tr>
              <th class="rounded-tl-xl">
                <div class="flex items-center gap-2">
                  <span>Nama OPD</span>
                  <button class="text-indigo-200 hover:text-white">
                    <i class="fas fa-sort"></i>
                  </button>
                </div>
              </th>
              <th>Kode OPD</th>
              <th>
                <div class="flex items-center gap-2">
                  <span>Jumlah Dataset</span>
                  <button class="text-indigo-200 hover:text-white">
                    <i class="fas fa-sort"></i>
                  </button>
                </div>
              </th>
              <th>Status</th>
              <th>Tanggal Update</th>
              <th class="rounded-tr-xl">Aksi</th>
            </tr>
          </thead>
          <tbody id="table-body">
            <!-- Data akan dimuat melalui JavaScript -->
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col md:flex-row justify-between items-center p-5 border-t border-gray-200">
        <div id="pagination-info" class="text-sm text-gray-600 mb-4 md:mb-0">
          Menampilkan 0 dari 0 entri
        </div>
        <div class="flex items-center gap-1 bg-white rounded-xl shadow-sm p-1">
          <button id="prev-page" class="h-9 w-9 flex items-center justify-center rounded-lg hover:bg-gray-100">
            <i class="fas fa-chevron-left text-sm text-gray-600"></i>
          </button>
          <button class="h-9 w-9 flex items-center justify-center rounded-lg bg-indigo-600 text-white font-medium">1</button>
          <button class="h-9 w-9 flex items-center justify-center rounded-lg hover:bg-gray-100">2</button>
          <button class="h-9 w-9 flex items-center justify-center rounded-lg hover:bg-gray-100">3</button>
          <button class="h-9 w-9 flex items-center justify-center rounded-lg hover:bg-gray-100">...</button>
          <button class="h-9 w-9 flex items-center justify-center rounded-lg hover:bg-gray-100">6</button>
          <button id="next-page" class="h-9 w-9 flex items-center justify-center rounded-lg hover:bg-gray-100">
            <i class="fas fa-chevron-right text-sm text-gray-600"></i>
          </button>
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
    // Data contoh OPD
    const opdData = [
      {
        name: "Dinas Pendidikan Provinsi Maluku Utara",
        code: "OPD-001",
        datasets: 42158,
        status: "Aktif",
        lastUpdate: "12 Jan 2025",
        growth: "+12%"
      },
	  {
        name: "PU",
        code: "OPD-0011",
        datasets: 42158,
        status: "Aktif",
        lastUpdate: "12 Jan 2025",
        growth: "+12%"
      },
      {
        name: "Dinas Kesehatan Provinsi Maluku Utara",
        code: "OPD-002",
        datasets: 38645,
        status: "Aktif",
        lastUpdate: "10 Jan 2025",
        growth: "+8%"
      },
      {
        name: "Dinas Pekerjaan Umum dan Penataan Ruang",
        code: "OPD-003",
        datasets: 35421,
        status: "Aktif",
        lastUpdate: "08 Jan 2025",
        growth: "+5%"
      },
      {
        name: "Dinas Pertanian dan Ketahanan Pangan",
        code: "OPD-004",
        datasets: 28291,
        status: "Aktif",
        lastUpdate: "05 Jan 2025",
        growth: "+3%"
      },
      {
        name: "Dinas Perhubungan",
        code: "OPD-005",
        datasets: 27445,
        status: "Aktif",
        lastUpdate: "03 Jan 2025",
        growth: "+7%"
      },
      {
        name: "Dinas Sosial",
        code: "OPD-006",
        datasets: 15328,
        status: "Aktif",
        lastUpdate: "02 Jan 2025",
        growth: "+2%"
      },
      {
        name: "Dinas Pariwisata",
        code: "OPD-007",
        datasets: 12487,
        status: "Aktif",
        lastUpdate: "01 Jan 2025",
        growth: "+15%"
      },
      {
        name: "Dinas Komunikasi dan Informatika",
        code: "OPD-008",
        datasets: 9856,
        status: "Non-Aktif",
        lastUpdate: "28 Des 2024",
        growth: "-5%"
      },
      {
        name: "Dinas Perindustrian dan Perdagangan",
        code: "OPD-009",
        datasets: 8765,
        status: "Aktif",
        lastUpdate: "25 Des 2024",
        growth: "+4%"
      },
      {
        name: "Dinas Kependudukan dan Pencatatan Sipil",
        code: "OPD-010",
        datasets: 7654,
        status: "Aktif",
        lastUpdate: "22 Des 2024",
        growth: "+9%"
      }
    ];

    // Elemen DOM
    const tableBody = document.getElementById('table-body');
    const searchInput = document.getElementById('search-opd');
    const statusFilter = document.getElementById('status-filter');
    const datasetFilter = document.getElementById('dataset-filter');
    const applyFilterBtn = document.getElementById('apply-filter');
    const loadingOverlay = document.getElementById('loading-overlay');
    const paginationInfo = document.getElementById('pagination-info');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

    // Variabel state
    let currentPage = 1;
    const itemsPerPage = 5;
    let filteredData = [...opdData];

    // Fungsi untuk menampilkan data dalam tabel
    function renderTable(data, page = 1) {
      const startIndex = (page - 1) * itemsPerPage;
      const endIndex = startIndex + itemsPerPage;
      const paginatedData = data.slice(startIndex, endIndex);

      tableBody.innerHTML = '';

      if (paginatedData.length === 0) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="6" class="text-center py-8 text-gray-500">
              <i class="fas fa-database text-3xl mb-2 block"></i>
              Tidak ada data yang ditemukan
            </td>
          </tr>
        `;
        return;
      }

      paginatedData.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td class="font-medium">${item.name}</td>
          <td>${item.code}</td>
          <td>
            <div class="flex items-center gap-2">
              <span class="font-medium">${item.datasets.toLocaleString()}</span>
              <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">${item.growth}</span>
            </div>
          </td>
          <td>
            <span class="${item.status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} text-xs px-2.5 py-1 rounded-full font-medium">${item.status}</span>
          </td>
          <td>${item.lastUpdate}</td>
          <td>
            <div class="flex gap-2">
              <button class="text-indigo-600 hover:text-indigo-800 p-1.5 rounded-lg hover:bg-indigo-50 transition-colors" title="Lihat Detail">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </td>
        `;
        tableBody.appendChild(row);
      });

      // Update info pagination
      const totalItems = data.length;
      const startItem = totalItems > 0 ? startIndex + 1 : 0;
      const endItem = Math.min(endIndex, totalItems);
      paginationInfo.textContent = `Menampilkan ${startItem} sampai ${endItem} dari ${totalItems} entri`;

      // Update status tombol pagination
      prevPageBtn.disabled = page === 1;
      nextPageBtn.disabled = endIndex >= totalItems;
    }

    // Fungsi untuk memfilter data
    function filterData() {
      const searchTerm = searchInput.value.toLowerCase();
      const statusValue = statusFilter.value;
      const datasetValue = datasetFilter.value;

      // Tampilkan loading overlay
      loadingOverlay.classList.remove('hidden');

      // Simulasi loading dengan setTimeout
      setTimeout(() => {
        filteredData = opdData.filter(item => {
          // Filter berdasarkan pencarian
          const matchesSearch = item.name.toLowerCase().includes(searchTerm) ||
                               item.code.toLowerCase().includes(searchTerm);

          // Filter berdasarkan status
          const matchesStatus = statusValue === 'all' ||
                              (statusValue === 'active' && item.status === 'Aktif') ||
                              (statusValue === 'inactive' && item.status === 'Non-Aktif');

          // Filter berdasarkan jumlah dataset
          let matchesDataset = true;
          if (datasetValue !== 'all') {
            switch(datasetValue) {
              case '0-1000':
                matchesDataset = item.datasets <= 1000;
                break;
              case '1001-5000':
                matchesDataset = item.datasets > 1000 && item.datasets <= 5000;
                break;
              case '5001-10000':
                matchesDataset = item.datasets > 5000 && item.datasets <= 10000;
                break;
              case '10001+':
                matchesDataset = item.datasets > 10000;
                break;
            }
          }

          return matchesSearch && matchesStatus && matchesDataset;
        });

        // Reset ke halaman pertama setelah filter
        currentPage = 1;
        renderTable(filteredData, currentPage);

        // Sembunyikan loading overlay
        loadingOverlay.classList.add('hidden');
      }, 1000); // Simulasi loading 1 detik
    }

    // Fungsi untuk toggle dropdown user menu
    function toggleUserDropdown() {
      if (userDropdown.style.display === 'block') {
        userDropdown.style.display = 'none';
      } else {
        userDropdown.style.display = 'block';
      }
    }

    // Event listener untuk tombol filter
    applyFilterBtn.addEventListener('click', filterData);

    // Event listener untuk input pencarian (bisa juga dengan tekan enter)
    searchInput.addEventListener('keyup', (e) => {
      if (e.key === 'Enter') {
        filterData();
      }
    });

    // Event listener untuk pagination
    prevPageBtn.addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        renderTable(filteredData, currentPage);
      }
    });

    nextPageBtn.addEventListener('click', () => {
      if (currentPage * itemsPerPage < filteredData.length) {
        currentPage++;
        renderTable(filteredData, currentPage);
      }
    });

    // Event listener untuk user dropdown
    if (userMenuButton && userDropdown) {
      userMenuButton.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleUserDropdown();
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!userDropdown.contains(e.target) && e.target !== userMenuButton) {
          userDropdown.style.display = 'none';
        }
      });
    }

    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
      const menu = document.getElementById('mobile-menu');
      menu.classList.toggle('hidden');
    });

    // Inisialisasi tabel dengan data awal
    document.addEventListener('DOMContentLoaded', () => {
      renderTable(opdData, currentPage);
    });
  </script>
</body>
</html>
