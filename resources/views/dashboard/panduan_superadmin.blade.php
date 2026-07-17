<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Panduan SI-RAFIKA - Super Administrator</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
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

    .nav-active {
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 6px;
    }

    .tab-btn.active {
      background-color: #7c3aed;
      color: white;
    }

    .tab-btn.active i {
      color: white;
    }

    .tab-content {
      display: none;
      animation: fadeIn 0.4s ease;
    }

    .tab-content.active {
      display: block;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(8px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .faq-content {
      display: none;
    }

    .step-connector {
      width: 2px;
      background-color: #ede9fe;
      height: 32px;
      margin-left: 22px;
    }

    .feature-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 0.7rem;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 9999px;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

  <!-- Header -->
  <header class="bg-gradient-to-r from-violet-700 to-purple-800 text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
      <div class="flex items-center gap-3">
        <div class="bg-white p-1.5 rounded-lg">
          <img src="https://e-rekrutmen.malutprov.go.id/assets/images/malut.png" alt="Logo Maluku Utara" class="h-8">
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
          class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2">
          <i class="fas fa-home text-sm"></i> Beranda
        </a>
        <a href="{{ route('panduan') }}"
          class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2 nav-active">
          <i class="fas fa-book text-sm"></i> Panduan
        </a>

        <!-- User Dropdown -->
        <div class="relative">
          <button id="user-menu-button" class="hover:text-gray-200 transition-colors flex items-center gap-1 px-3 py-2">
            <img
              src="https://eu.ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&bold=true&background=5B21B6&color=FFFFFF"
              alt="Profile" class="h-7 w-7 rounded-full mr-1">
            {{ Auth::user()->name }} <i class="fas fa-chevron-down text-xs ml-1"></i>
          </button>
          <div id="user-dropdown" class="dropdown-content">
            <a href="#" class="dropdown-item">
              <i class="fas fa-user-circle"></i> Profil
            </a>
            <div class="dropdown-divider"></div>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item w-full text-left text-red-600">
                <i class="fas fa-sign-out-alt text-red-400"></i> Logout
              </button>
            </form>
          </div>
        </div>
      </nav>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-purple-900 p-4">
      <div class="flex flex-col gap-3">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i
            class="fas fa-home mr-2"></i> Beranda</a>
        <a href="{{ route('panduan') }}"
          class="hover:text-gray-200 transition-colors px-3 py-2 rounded bg-purple-700"><i class="fas fa-book mr-2"></i>
          Panduan</a>
        <div class="dropdown-divider"></div>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="flex items-center px-3 py-2 text-red-400 hover:text-red-300">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <div class="bg-gradient-to-r from-violet-700 to-purple-800 text-white pt-12 pb-20 px-4">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-8">
      <div class="flex-1 text-center md:text-left">
        <div class="inline-flex items-center gap-2 bg-white/20 px-4 py-1.5 rounded-full text-sm font-medium mb-4">
          <i class="fas fa-crown"></i> Panduan untuk Super Administrator
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold mb-3 tracking-tight">Panduan Digital SI-RAFIKA</h1>
        <p class="text-purple-100 text-lg mb-6">
          Halo, <strong>{{ Auth::user()->name }}</strong>! Anda memiliki akses penuh ke seluruh fitur sistem.
          Panduan ini mencakup semua menu, analitik, dan alat pengawasan yang tersedia khusus untuk Super Administrator.
        </p>
        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
          <button onclick="switchTab('alur')"
            class="bg-white text-purple-700 hover:bg-gray-100 font-semibold py-2.5 px-6 rounded-full shadow-md transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-route mr-2"></i> Lihat Alur Kerja
          </button>
          <button onclick="switchTab('menu')"
            class="bg-purple-500 hover:bg-purple-400 border border-purple-300 text-white font-medium py-2.5 px-6 rounded-full transition-all">
            <i class="fas fa-book-open mr-2"></i> Panduan Menu
          </button>
        </div>
      </div>
      <div class="hidden md:flex bg-white/20 p-8 rounded-2xl border border-white/30 shadow-2xl">
        <i class="fas fa-crown text-7xl text-white opacity-90"></i>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto -mt-10 p-4 pb-16">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row" style="min-height: 580px;">

      <!-- Sidebar Tab Nav -->
      <div class="w-full md:w-64 bg-gray-50 border-r border-gray-100 flex-shrink-0 p-4">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3 px-2">Navigasi</p>
        <nav class="flex flex-col gap-1" id="panduan-nav">
          <button onclick="switchTab('beranda')" data-tab="beranda"
            class="tab-btn active w-full text-left px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-3 transition-all">
            <i class="fas fa-home w-5 text-center text-white"></i> Beranda
          </button>
          <button onclick="switchTab('pengenalan')" data-tab="pengenalan"
            class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 flex items-center gap-3 hover:bg-gray-100 transition-all">
            <i class="fas fa-info-circle w-5 text-center text-gray-400"></i> Pengenalan
          </button>
          <button onclick="switchTab('alur')" data-tab="alur"
            class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 flex items-center gap-3 hover:bg-gray-100 transition-all">
            <i class="fas fa-route w-5 text-center text-gray-400"></i> Alur Penggunaan
          </button>
          <button onclick="switchTab('menu')" data-tab="menu"
            class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 flex items-center gap-3 hover:bg-gray-100 transition-all">
            <i class="fas fa-list w-5 text-center text-gray-400"></i> Panduan per Menu
          </button>
          <button onclick="switchTab('analitik')" data-tab="analitik"
            class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 flex items-center gap-3 hover:bg-gray-100 transition-all">
            <i class="fas fa-chart-bar w-5 text-center text-gray-400"></i> Analitik & Laporan
          </button>
          <button onclick="switchTab('faq')" data-tab="faq"
            class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 flex items-center gap-3 hover:bg-gray-100 transition-all">
            <i class="fas fa-question-circle w-5 text-center text-gray-400"></i> FAQ
          </button>
          <button onclick="switchTab('kontak')" data-tab="kontak"
            class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 flex items-center gap-3 hover:bg-gray-100 transition-all">
            <i class="fas fa-headset w-5 text-center text-gray-400"></i> Kontak Admin
          </button>
        </nav>

        <!-- Role badge -->
        <div class="mt-6 px-2">
          <div class="bg-purple-50 border border-purple-100 rounded-xl p-3 text-center">
            <i class="fas fa-crown text-purple-500 text-xl mb-1"></i>
            <p class="text-xs font-bold text-purple-700">SUPER ADMINISTRATOR</p>
            <p class="text-xs text-gray-500 mt-1">Akses penuh ke semua fitur</p>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="flex-1 p-6 md:p-10 overflow-y-auto">

        <!-- =================== BERANDA =================== -->
        <div id="tab-beranda" class="tab-content active">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Selamat Datang, Super
            Administrator!</h3>
          <p class="text-gray-600 leading-relaxed mb-6 mt-4">
            Sebagai <strong>Super Administrator</strong>, Anda adalah pengguna dengan level akses tertinggi dalam sistem
            SI-RAFIKA. Anda dapat memantau, menganalisis, dan mengawasi seluruh data realisasi anggaran dari semua
            Organisasi Perangkat Daerah (OPD) secara komprehensif dan <em>real-time</em>.
          </p>
          <div class="bg-purple-50 border border-purple-100 p-6 rounded-2xl mb-6">
            <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
              <i class="fas fa-crosshairs text-purple-600 text-xl"></i> Fokus Tugas Anda:
            </h4>
            <ul class="space-y-2 text-sm text-gray-700">
              <li class="flex items-start gap-2"><i class="fas fa-check text-purple-500 mt-0.5 flex-shrink-0"></i>
                Memantau <strong>Executive Dashboard</strong> — serapan anggaran harian, bulanan, dan tahunan.</li>
              <li class="flex items-start gap-2"><i class="fas fa-check text-purple-500 mt-0.5 flex-shrink-0"></i>
                Menganalisis <strong>kinerja seluruh OPD</strong> melalui chart, ranking, dan traffic light.</li>
              <li class="flex items-start gap-2"><i class="fas fa-check text-purple-500 mt-0.5 flex-shrink-0"></i>
                Mengelola <strong>Master Data OPD</strong> dan <strong>Akun Pengguna</strong>.</li>
              <li class="flex items-start gap-2"><i class="fas fa-check text-purple-500 mt-0.5 flex-shrink-0"></i>
                Menjalankan <strong>Validation Engine</strong> untuk mendeteksi anomali data.</li>
              <li class="flex items-start gap-2"><i class="fas fa-check text-purple-500 mt-0.5 flex-shrink-0"></i>
                Mengekspor <strong>Laporan PDF</strong> lintas OPD dengan filter fleksibel.</li>
            </ul>
          </div>

          <!-- Menu Quick Cards -->
          <h4 class="font-bold text-gray-700 mb-4">Menu yang Tersedia untuk Anda:</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="bg-violet-50 border border-violet-100 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow" onclick="switchTab('menu')">
              <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-tachometer-alt text-violet-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">Dashboard Executive Insight</p>
                <p class="text-xs text-gray-500 mt-0.5">Analitik global seluruh OPD</p>
              </div>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow" onclick="switchTab('menu')">
              <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-building text-blue-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">Manajemen Data OPD</p>
                <p class="text-xs text-gray-500 mt-0.5">Tambah, edit, hapus instansi</p>
              </div>
            </div>
            <div class="bg-teal-50 border border-teal-100 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow" onclick="switchTab('menu')">
              <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-teal-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">Manajemen Pengguna</p>
                <p class="text-xs text-gray-500 mt-0.5">CRUD akun & pengaturan role</p>
              </div>
            </div>
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow" onclick="switchTab('analitik')">
              <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-file-pdf text-amber-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">Laporan & Ekspor PDF</p>
                <p class="text-xs text-gray-500 mt-0.5">Cetak laporan lintas OPD</p>
              </div>
            </div>
            <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow" onclick="switchTab('analitik')">
              <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-rose-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">Validation Engine</p>
                <p class="text-xs text-gray-500 mt-0.5">Deteksi anomali & duplikasi data</p>
              </div>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:shadow-md transition-shadow" onclick="switchTab('analitik')">
              <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-history text-slate-600"></i>
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-sm">Audit Trail RFK</p>
                <p class="text-xs text-gray-500 mt-0.5">Riwayat lengkap perubahan status</p>
              </div>
            </div>
          </div>
        </div>

        <!-- =================== PENGENALAN =================== -->
        <div id="tab-pengenalan" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Pengenalan SI-RAFIKA</h3>
          <p class="text-gray-600 leading-relaxed mt-4 mb-6">
            <strong>SI-RAFIKA</strong> (Sistem Informasi Rekapitulasi Fisik dan Keuangan) adalah platform digital
            terpadu yang dikembangkan untuk mempermudah Pemerintah Daerah dalam memonitor, mengevaluasi, dan
            merekapitulasi serapan anggaran (Keuangan) dan progres pembangunan (Fisik) di seluruh Organisasi Perangkat
            Daerah (OPD).
          </p>

          <h5 class="font-bold text-gray-800 mb-3">Tujuan Utama</h5>
          <ul class="space-y-2 text-sm text-gray-600 mb-6">
            <li class="flex items-start gap-2"><i class="fas fa-angle-right text-purple-500 mt-0.5"></i> Meningkatkan
              transparansi dan akuntabilitas penggunaan anggaran daerah.</li>
            <li class="flex items-start gap-2"><i class="fas fa-angle-right text-purple-500 mt-0.5"></i> Mempercepat
              proses pelaporan progres dari level Staff hingga Pimpinan.</li>
            <li class="flex items-start gap-2"><i class="fas fa-angle-right text-purple-500 mt-0.5"></i> Menyediakan
              dashboard analitik <em>real-time</em> untuk pengambilan keputusan strategis.</li>
            <li class="flex items-start gap-2"><i class="fas fa-angle-right text-purple-500 mt-0.5"></i> Mendeteksi
              anomali dan ketidakwajaran data realisasi secara otomatis.</li>
          </ul>

          <h5 class="font-bold text-gray-800 mb-4">Hierarki Pengguna Sistem</h5>
          <div class="space-y-3 mb-6">
            <div class="flex items-center gap-4 bg-violet-50 border border-violet-100 rounded-xl p-4">
              <div class="w-10 h-10 bg-violet-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"><i class="fas fa-crown"></i></div>
              <div>
                <p class="font-bold text-violet-700">Super Administrator</p>
                <p class="text-xs text-gray-500">Akses penuh: monitoring global, manajemen data, laporan lintas OPD, validation engine</p>
              </div>
            </div>
            <div class="flex items-center gap-4 bg-blue-50 border border-blue-100 rounded-xl p-4">
              <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"><i class="fas fa-user-shield"></i></div>
              <div>
                <p class="font-bold text-blue-700">Administrator</p>
                <p class="text-xs text-gray-500">Kelola master data OPD dan akun pengguna</p>
              </div>
            </div>
            <div class="flex items-center gap-4 bg-green-50 border border-green-100 rounded-xl p-4">
              <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"><i class="fas fa-user-tie"></i></div>
              <div>
                <p class="font-bold text-green-700">Kepala OPD</p>
                <p class="text-xs text-gray-500">Approve / Reject realisasi dari staff di OPD-nya</p>
              </div>
            </div>
            <div class="flex items-center gap-4 bg-gray-50 border border-gray-200 rounded-xl p-4">
              <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"><i class="fas fa-user"></i></div>
              <div>
                <p class="font-bold text-gray-700">Staff OPD</p>
                <p class="text-xs text-gray-500">Input program RFK dan mengajukan realisasi keuangan</p>
              </div>
            </div>
          </div>

          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-5 rounded-r-xl">
            <div class="flex gap-3">
              <i class="fas fa-lightbulb text-yellow-500 text-xl flex-shrink-0 mt-0.5"></i>
              <p class="text-sm text-yellow-700">
                Sebagai Super Administrator, sistem secara otomatis menghasilkan <strong>Traffic Light</strong>:
                🔴 Merah (< 70%), 🟡 Kuning (70–89%), 🟢 Hijau (≥ 90%) berdasarkan persentase serapan anggaran setiap OPD.
              </p>
            </div>
          </div>
        </div>

        <!-- =================== ALUR PENGGUNAAN =================== -->
        <div id="tab-alur" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Alur Kerja Super
            Administrator</h3>
          <p class="text-gray-500 text-sm mb-8 mt-4">Ikuti langkah-langkah berikut untuk mengoperasikan sistem
            SI-RAFIKA secara efektif sebagai Super Administrator.</p>

          <!-- Timeline Steps -->
          <div class="space-y-0">
            <!-- Step 1 -->
            <div class="flex gap-4">
              <div class="flex flex-col items-center">
                <div class="w-11 h-11 bg-violet-700 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">1</div>
                <div class="step-connector"></div>
              </div>
              <div class="pb-8 pt-1">
                <h4 class="font-bold text-violet-700 text-base mb-1">Setup Master Data (Awal Sistem)</h4>
                <p class="text-gray-600 text-sm leading-relaxed mb-3">
                  Sebelum OPD dapat menginput data, Anda perlu mendaftarkan seluruh OPD dan membuat akun pengguna
                  (Kepala OPD & Staff) terlebih dahulu melalui menu <strong>Manajemen OPD</strong> dan
                  <strong>Manajemen Pengguna</strong>.
                </p>
                <div class="flex flex-wrap gap-2">
                  <span class="feature-badge bg-violet-100 text-violet-700">Manajemen OPD</span>
                  <span class="feature-badge bg-blue-100 text-blue-700">Manajemen Pengguna</span>
                </div>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4">
              <div class="flex flex-col items-center">
                <div class="w-11 h-11 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">2</div>
                <div class="step-connector"></div>
              </div>
              <div class="pb-8 pt-1">
                <h4 class="font-bold text-blue-700 text-base mb-1">Monitoring Global — Executive Dashboard</h4>
                <p class="text-gray-600 text-sm leading-relaxed mb-3">
                  Pantau serapan anggaran seluruh OPD melalui Dashboard. Gunakan filter <strong>Tahun</strong> dan
                  <strong>OPD</strong> untuk mempersempit tampilan data. Perhatikan indikator penting:
                  Realisasi Harian/Bulanan/Tahunan, Status Validasi, Traffic Light, serta Ranking OPD.
                </p>
                <div class="flex flex-wrap gap-2">
                  <span class="feature-badge bg-green-100 text-green-700"><i class="fas fa-circle text-green-500 text-[8px]"></i> Traffic Light</span>
                  <span class="feature-badge bg-amber-100 text-amber-700">Ranking OPD</span>
                  <span class="feature-badge bg-indigo-100 text-indigo-700">Chart Sumber Dana</span>
                </div>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4">
              <div class="flex flex-col items-center">
                <div class="w-11 h-11 bg-teal-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">3</div>
                <div class="step-connector"></div>
              </div>
              <div class="pb-8 pt-1">
                <h4 class="font-bold text-teal-700 text-base mb-1">Buka Detail per OPD</h4>
                <p class="text-gray-600 text-sm leading-relaxed mb-3">
                  Klik kartu OPD di bagian bawah dashboard untuk melihat analisis per instansi. Klik tombol
                  <strong>"Detail Program"</strong> untuk membuka modal yang berisi semua program RFK beserta status
                  masing-masing. Klik <strong>"Rekap Seluruh OPD"</strong> untuk tabel komparasi global.
                </p>
                <div class="flex flex-wrap gap-2">
                  <span class="feature-badge bg-slate-100 text-slate-700">Modal Detail Program</span>
                  <span class="feature-badge bg-slate-100 text-slate-700">Modal Rekap Global</span>
                </div>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="flex gap-4">
              <div class="flex flex-col items-center">
                <div class="w-11 h-11 bg-rose-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">4</div>
                <div class="step-connector"></div>
              </div>
              <div class="pb-8 pt-1">
                <h4 class="font-bold text-rose-700 text-base mb-1">Jalankan Validation Engine</h4>
                <p class="text-gray-600 text-sm leading-relaxed mb-3">
                  Secara berkala, jalankan <strong>Validation Engine</strong> dari menu yang tersedia. Sistem akan
                  memindai seluruh data dan menampilkan anomali seperti realisasi melebihi pagu, deviasi ekstrem
                  antara fisik dan keuangan, serta dugaan copy-paste input.
                </p>
                <div class="flex flex-wrap gap-2">
                  <span class="feature-badge bg-rose-100 text-rose-700">Over Pagu</span>
                  <span class="feature-badge bg-orange-100 text-orange-700">Deviasi Ekstrem</span>
                  <span class="feature-badge bg-red-100 text-red-700">Copy-Paste Detection</span>
                </div>
              </div>
            </div>

            <!-- Step 5 -->
            <div class="flex gap-4">
              <div class="flex flex-col items-center">
                <div class="w-11 h-11 bg-amber-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">5</div>
              </div>
              <div class="pb-2 pt-1">
                <h4 class="font-bold text-amber-700 text-base mb-1">Ekspor Laporan PDF</h4>
                <p class="text-gray-600 text-sm leading-relaxed mb-3">
                  Gunakan menu <strong>Laporan</strong> untuk menghasilkan dokumen PDF resmi rekapitulasi anggaran.
                  Filter berdasarkan Program, OPD, Tahun Anggaran, atau Status sebelum mengklik tombol
                  <strong>Generate PDF</strong>. File PDF tersimpan dan dapat diunduh langsung.
                </p>
                <div class="flex flex-wrap gap-2">
                  <span class="feature-badge bg-amber-100 text-amber-700">Filter Multi-kriteria</span>
                  <span class="feature-badge bg-amber-100 text-amber-700">Export PDF A4 Landscape</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- =================== PANDUAN PER MENU =================== -->
        <div id="tab-menu" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Panduan per Menu</h3>

          <div class="space-y-10 mt-4">

            <!-- 1. Dashboard Executive -->
            <div>
              <h4 class="text-lg font-bold text-purple-700 mb-1 flex items-center gap-2">
                <i class="fas fa-tachometer-alt w-6 text-center"></i> Dashboard — Executive Insight
              </h4>
              <p class="text-xs text-gray-400 mb-4">Rute: <code class="bg-gray-100 px-2 py-0.5 rounded font-mono">/</code> (Halaman Utama)</p>

              <div class="space-y-5">
                <!-- Filter -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                  <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-filter text-purple-500"></i> Panel Filter & Refresh</h5>
                  <p class="text-sm text-gray-600 mb-3">Di pojok kanan atas halaman terdapat dua filter utama dan tombol refresh:</p>
                  <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex gap-2"><i class="fas fa-calendar-alt text-gray-400 mt-0.5 flex-shrink-0"></i>
                      <span><strong>Filter Tahun</strong> — Pilih 2024, 2025, atau 2026 untuk membatasi data sesuai tahun anggaran.</span>
                    </li>
                    <li class="flex gap-2"><i class="fas fa-building text-gray-400 mt-0.5 flex-shrink-0"></i>
                      <span><strong>Filter OPD</strong> — Pilih "Seluruh Organisasi" atau satu OPD spesifik. Data seluruh chart akan menyesuaikan otomatis.</span>
                    </li>
                    <li class="flex gap-2"><i class="fas fa-sync-alt text-gray-400 mt-0.5 flex-shrink-0"></i>
                      <span><strong>Tombol Refresh</strong> — Muat ulang data terbaru dari server.</span>
                    </li>
                  </ul>
                </div>

                <!-- Kartu Atas -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                  <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-th-large text-blue-500"></i> Kartu Realisasi SI-RAFIKA (Baris Atas)</h5>
                  <p class="text-sm text-gray-600 mb-3">Empat kartu di baris paling atas menampilkan ringkasan waktu:</p>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                      <i class="fas fa-calendar-day text-blue-500 mb-1"></i>
                      <p class="text-xs font-bold text-blue-700">Realisasi Harian</p>
                      <p class="text-xs text-gray-500">Total serapan hari ini</p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-3 text-center">
                      <i class="fas fa-calendar-alt text-indigo-500 mb-1"></i>
                      <p class="text-xs font-bold text-indigo-700">Realisasi Bulanan</p>
                      <p class="text-xs text-gray-500">Total serapan bulan ini</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 text-center">
                      <i class="fas fa-calendar-check text-purple-500 mb-1"></i>
                      <p class="text-xs font-bold text-purple-700">Realisasi Tahunan</p>
                      <p class="text-xs text-gray-500">Total serapan tahun ini</p>
                    </div>
                    <div class="bg-emerald-50 rounded-lg p-3 text-center">
                      <i class="fas fa-building text-emerald-500 mb-1"></i>
                      <p class="text-xs font-bold text-emerald-700">OPD Tercatat</p>
                      <p class="text-xs text-gray-500">Jumlah instansi aktif</p>
                    </div>
                  </div>
                </div>

                <!-- Kartu Statistik Utama -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                  <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-chart-pie text-indigo-500"></i> Kartu Statistik Utama (Baris Kedua)</h5>
                  <p class="text-sm text-gray-600 mb-2">Empat kartu ini menampilkan agregat keuangan keseluruhan. <strong>Klik salah satu kartu</strong> untuk membuka modal <em>"Rekapitulasi Anggaran Seluruh OPD"</em> dalam bentuk tabel.</p>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-white rounded-lg p-2 border"><i class="fas fa-wallet text-blue-500"></i> <span><strong>Total Pagu</strong> — Jumlah anggaran semua OPD</span></div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-white rounded-lg p-2 border"><i class="fas fa-chart-line text-emerald-500"></i> <span><strong>Realisasi Keuangan</strong> — Total yang sudah terserap</span></div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-white rounded-lg p-2 border"><i class="fas fa-hand-holding-usd text-rose-500"></i> <span><strong>Sisa Anggaran</strong> — Pagu dikurangi realisasi</span></div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-white rounded-lg p-2 border"><i class="fas fa-hammer text-indigo-500"></i> <span><strong>Rata-rata Fisik</strong> — % progres pembangunan</span></div>
                  </div>
                </div>

                <!-- Chart Baris -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                  <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-chart-bar text-teal-500"></i> Grafik & Visualisasi Analitik</h5>
                  <div class="space-y-3">
                    <div class="bg-white border rounded-lg p-3">
                      <p class="font-semibold text-sm text-gray-700 mb-1"><i class="fas fa-chart-pie text-gray-400 mr-1"></i> Status Validasi SI-RAFIKA (Pie Chart)</p>
                      <p class="text-xs text-gray-500">Menampilkan distribusi program berdasarkan status: <span class="text-emerald-600 font-bold">APPROVE</span>, <span class="text-amber-600 font-bold">PENDING</span>, <span class="text-rose-600 font-bold">REJECT</span>. Klik tombol <strong>"Lihat Detail Status RFK"</strong> untuk membuka tabel lengkap yang bisa difilter per status.</p>
                    </div>
                    <div class="bg-white border rounded-lg p-3">
                      <p class="font-semibold text-sm text-gray-700 mb-1"><i class="fas fa-chart-bar text-gray-400 mr-1"></i> Progress Fisik vs Keuangan per OPD (Bar Chart)</p>
                      <p class="text-xs text-gray-500">Grafik kombinasi Bar (realisasi keuangan) dan Line (rata-rata fisik) untuk membandingkan pencairan uang dengan progres lapangan per OPD. Klik <strong>"Data Seluruh OPD"</strong> untuk tabel rekap.</p>
                    </div>
                    <div class="bg-white border rounded-lg p-3">
                      <p class="font-semibold text-sm text-gray-700 mb-1"><i class="fas fa-coins text-gray-400 mr-1"></i> Alokasi & Penyerapan per Sumber Dana</p>
                      <p class="text-xs text-gray-500">Perbandingan pagu dan realisasi berdasarkan sumber dana (APBD, APBN, dll). Klik <strong>"Buka Matrix per OPD"</strong> untuk melihat breakdown sumber dana di tiap OPD.</p>
                    </div>
                    <div class="bg-white border rounded-lg p-3">
                      <p class="font-semibold text-sm text-gray-700 mb-1"><i class="fas fa-times-circle text-gray-400 mr-1"></i> Radar Kepatuhan (Top 5 OPD REJECT)</p>
                      <p class="text-xs text-gray-500">Daftar 5 OPD dengan frekuensi penolakan terbanyak dari histori verifikasi. Di bawahnya terdapat <strong>Skor Efektivitas Keuangan</strong> (%) sistem secara keseluruhan.</p>
                    </div>
                  </div>
                </div>

                <!-- Traffic Light -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                  <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-traffic-light text-gray-600"></i> Traffic Light Penyerapan</h5>
                  <p class="text-sm text-gray-600 mb-3">Visualisasi lampu lalu lintas menunjukkan kondisi serapan anggaran OPD secara keseluruhan:</p>
                  <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-lg p-3">
                      <div class="w-6 h-6 rounded-full bg-emerald-500 flex-shrink-0"></div>
                      <p class="text-sm text-gray-700"><strong class="text-emerald-700">Hijau — Optimal</strong>: Serapan ≥ 90%. OPD dalam kondisi sangat baik.</p>
                    </div>
                    <div class="flex items-center gap-3 bg-amber-50 border border-amber-100 rounded-lg p-3">
                      <div class="w-6 h-6 rounded-full bg-amber-500 flex-shrink-0"></div>
                      <p class="text-sm text-gray-700"><strong class="text-amber-700">Kuning — Waspada</strong>: Serapan 70–89%. Perlu dipantau lebih seksama.</p>
                    </div>
                    <div class="flex items-center gap-3 bg-rose-50 border border-rose-100 rounded-lg p-3">
                      <div class="w-6 h-6 rounded-full bg-rose-500 flex-shrink-0"></div>
                      <p class="text-sm text-gray-700"><strong class="text-rose-700">Merah — Kritis</strong>: Serapan < 70%. Diperlukan tindakan intervensi segera.</p>
                    </div>
                  </div>
                  <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-3 mt-3">
                    <p class="text-xs text-indigo-700"><i class="fas fa-info-circle mr-1"></i> <strong>Tip:</strong> Klik salah satu lampu untuk memfilter daftar OPD di bagian bawah halaman sesuai kategori traffic light yang dipilih.</p>
                  </div>
                </div>

                <!-- Ranking & Sorotan Ekstrem -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                  <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-trophy text-amber-500"></i> Leaderboard & Sorotan Ekstrem Program</h5>
                  <div class="space-y-3">
                    <div class="bg-white border rounded-lg p-3">
                      <p class="font-semibold text-sm text-gray-700 mb-1">Leaderboard Klasemen OPD</p>
                      <p class="text-xs text-gray-500">Peringkat seluruh OPD dari persentase serapan tertinggi hingga terendah. Cocok untuk evaluasi kinerja antar instansi.</p>
                    </div>
                    <div class="bg-white border rounded-lg p-3">
                      <p class="font-semibold text-sm text-gray-700 mb-1">Top 10 Paket Anggaran Terbesar</p>
                      <p class="text-xs text-gray-500">Program-program dengan nilai pagu terbesar dari seluruh OPD. Membantu fokus pengawasan pada proyek bernilai tinggi.</p>
                    </div>
                    <div class="bg-white border rounded-lg p-3">
                      <p class="font-semibold text-sm text-gray-700 mb-1">Sorotan Ekstrem Program</p>
                      <p class="text-xs text-gray-500">Menampilkan <strong>5 Program Serapan Tertinggi</strong> dan <strong>5 Program Serapan Terendah</strong> berdasarkan persentase realisasi keuangan terhadap pagu.</p>
                    </div>
                  </div>
                </div>

                <!-- OPD Grid -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5">
                  <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-th text-purple-500"></i> Grid Analisis Kinerja per Instansi (OPD)</h5>
                  <p class="text-sm text-gray-600 mb-3">Bagian paling bawah halaman menampilkan kartu untuk setiap OPD. Setiap kartu berisi:</p>
                  <ul class="text-sm text-gray-600 space-y-1.5">
                    <li class="flex gap-2"><i class="fas fa-dot-circle text-purple-400 mt-0.5 flex-shrink-0"></i> Nama OPD & progress bar persentase serapan</li>
                    <li class="flex gap-2"><i class="fas fa-dot-circle text-purple-400 mt-0.5 flex-shrink-0"></i> Total Pagu, Realisasi, dan Sisa Anggaran</li>
                    <li class="flex gap-2"><i class="fas fa-dot-circle text-purple-400 mt-0.5 flex-shrink-0"></i> Rata-rata Fisik (%) dan Jumlah Program</li>
                    <li class="flex gap-2"><i class="fas fa-dot-circle text-purple-400 mt-0.5 flex-shrink-0"></i> Tombol <strong>"Detail Program"</strong> untuk membuka modal seluruh program OPD tersebut</li>
                    <li class="flex gap-2"><i class="fas fa-dot-circle text-purple-400 mt-0.5 flex-shrink-0"></i> Mini Doughnut Chart serapan per OPD</li>
                  </ul>
                </div>
              </div>
            </div>

            <hr class="border-gray-100">

            <!-- 2. Manajemen OPD -->
            <div>
              <h4 class="text-lg font-bold text-purple-700 mb-1 flex items-center gap-2">
                <i class="fas fa-building w-6 text-center"></i> Manajemen Data OPD
              </h4>
              <p class="text-xs text-gray-400 mb-4">Rute: <code class="bg-gray-100 px-2 py-0.5 rounded font-mono">/dataopd</code></p>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Halaman ini adalah tempat untuk mendaftarkan dan mengelola daftar seluruh Organisasi Perangkat Daerah
                (OPD) yang ada di sistem. Data OPD merupakan fondasi sistem karena setiap pengguna Staff dan Kepala OPD
                dikaitkan dengan satu OPD.
              </p>
              <h6 class="font-semibold text-sm text-gray-700 mb-2">Cara Penggunaan:</h6>
              <ol class="list-decimal pl-5 text-gray-600 text-sm space-y-2">
                <li>Buka menu <strong>Manajemen OPD</strong> dari sidebar.</li>
                <li>Tabel menampilkan seluruh OPD yang sudah terdaftar.</li>
                <li>Klik <strong>"+ Tambah OPD"</strong> untuk mendaftarkan instansi baru, isi nama OPD lalu simpan.</li>
                <li>Klik ikon <strong>edit</strong> (pensil) pada baris OPD untuk mengubah nama instansi.</li>
                <li>Klik ikon <strong>hapus</strong> (tempat sampah) untuk menghapus OPD. <span class="text-rose-600 font-semibold">Perhatian: pastikan tidak ada pengguna atau data RFK yang masih terhubung sebelum menghapus.</span></li>
              </ol>
            </div>

            <hr class="border-gray-100">

            <!-- 3. Manajemen Pengguna -->
            <div>
              <h4 class="text-lg font-bold text-purple-700 mb-1 flex items-center gap-2">
                <i class="fas fa-users w-6 text-center"></i> Manajemen Pengguna (Users)
              </h4>
              <p class="text-xs text-gray-400 mb-4">Rute: <code class="bg-gray-100 px-2 py-0.5 rounded font-mono">/users</code></p>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Kelola seluruh akun pengguna sistem — mulai dari mendaftarkan akun baru, mengatur peran (role),
                mengaitkan dengan OPD, hingga menonaktifkan akun yang tidak lagi digunakan.
              </p>
              <h6 class="font-semibold text-sm text-gray-700 mb-2">Fitur yang Tersedia:</h6>
              <div class="space-y-2 mb-4">
                <div class="bg-white border rounded-lg p-3 flex items-start gap-3">
                  <i class="fas fa-search text-gray-400 mt-0.5 flex-shrink-0 text-sm"></i>
                  <div>
                    <p class="text-sm font-semibold text-gray-700">Filter & Pencarian</p>
                    <p class="text-xs text-gray-500">Cari pengguna berdasarkan nama/email. Filter berdasarkan role (superadmin, administrator, kepala_opd, staff), status (aktif/nonaktif), atau OPD tertentu.</p>
                  </div>
                </div>
                <div class="bg-white border rounded-lg p-3 flex items-start gap-3">
                  <i class="fas fa-user-plus text-gray-400 mt-0.5 flex-shrink-0 text-sm"></i>
                  <div>
                    <p class="text-sm font-semibold text-gray-700">Tambah Pengguna Baru</p>
                    <p class="text-xs text-gray-500">Isi nama, email, password, role, OPD (jika Staff/Kepala OPD), dan status aktif/nonaktif. Klik <strong>Simpan</strong>.</p>
                  </div>
                </div>
                <div class="bg-white border rounded-lg p-3 flex items-start gap-3">
                  <i class="fas fa-user-edit text-gray-400 mt-0.5 flex-shrink-0 text-sm"></i>
                  <div>
                    <p class="text-sm font-semibold text-gray-700">Edit Pengguna</p>
                    <p class="text-xs text-gray-500">Ubah nama, email, role, OPD, status, atau reset password. Jika kolom password dikosongkan, password lama tidak akan berubah.</p>
                  </div>
                </div>
                <div class="bg-white border rounded-lg p-3 flex items-start gap-3">
                  <i class="fas fa-file-export text-gray-400 mt-0.5 flex-shrink-0 text-sm"></i>
                  <div>
                    <p class="text-sm font-semibold text-gray-700">Ekspor Data Pengguna</p>
                    <p class="text-xs text-gray-500">Unduh daftar pengguna yang sudah difilter dalam format yang dapat diolah lebih lanjut.</p>
                  </div>
                </div>
              </div>
              <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl">
                <p class="text-sm text-amber-700 flex gap-2">
                  <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
                  <span>Setiap pengguna dengan role <strong>Staff</strong> atau <strong>Kepala OPD</strong> wajib dikaitkan dengan satu OPD. Jika tidak, pengguna tersebut tidak akan bisa melihat data apapun setelah login.</span>
                </p>
              </div>
            </div>

            <hr class="border-gray-100">

            <!-- 4. Audit RFK -->
            <div>
              <h4 class="text-lg font-bold text-purple-700 mb-1 flex items-center gap-2">
                <i class="fas fa-clipboard-list w-6 text-center"></i> Audit Trail RFK
              </h4>
              <p class="text-xs text-gray-400 mb-4">Rute: <code class="bg-gray-100 px-2 py-0.5 rounded font-mono">/dashboard/rfk/audit</code></p>
              <p class="text-gray-600 text-sm leading-relaxed mb-3">
                Halaman Audit RFK menampilkan seluruh riwayat perubahan status dari semua pengajuan realisasi di seluruh
                OPD. Setiap tindakan (submit, approve, reject, revisi) tercatat lengkap beserta nama pelaku, waktu, dan
                keterangan.
              </p>
              <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <p class="text-sm text-slate-700"><i class="fas fa-info-circle text-slate-500 mr-1"></i>
                  Audit Trail bersifat <strong>read-only</strong> — hanya untuk keperluan pengawasan dan investigasi. Tidak ada aksi yang bisa dilakukan dari halaman ini.</p>
              </div>
            </div>

          </div>
        </div>

        <!-- =================== ANALITIK & LAPORAN =================== -->
        <div id="tab-analitik" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Analitik Lanjutan &
            Laporan</h3>

          <div class="space-y-10 mt-4">

            <!-- Laporan PDF -->
            <div>
              <h4 class="text-lg font-bold text-purple-700 mb-1 flex items-center gap-2">
                <i class="fas fa-file-pdf w-6 text-center"></i> Cetak Laporan PDF
              </h4>
              <p class="text-xs text-gray-400 mb-4">Rute: <code class="bg-gray-100 px-2 py-0.5 rounded font-mono">/dashboard/laporan</code></p>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Menu Laporan memungkinkan Anda menghasilkan dokumen PDF resmi rekapitulasi anggaran. Sebagai Super
                Administrator, Anda bisa melihat dan mencetak laporan dari seluruh OPD tanpa batasan.
              </p>
              <h6 class="font-semibold text-sm text-gray-700 mb-3">Langkah Generate PDF:</h6>
              <ol class="list-decimal pl-5 text-gray-600 text-sm space-y-2 mb-4">
                <li>Buka menu <strong>Laporan</strong> dari sidebar.</li>
                <li>Gunakan filter yang tersedia:
                  <ul class="list-disc pl-5 mt-1 space-y-1">
                    <li><strong>Program</strong> — Cari berdasarkan nama atau kode program</li>
                    <li><strong>OPD</strong> — Filter satu atau semua instansi</li>
                    <li><strong>Tahun Anggaran</strong> — Batasi periode laporan</li>
                    <li><strong>Status</strong> — Filter APPROVE, PENDING, atau REJECT</li>
                  </ul>
                </li>
                <li>Klik tombol <strong>"Generate PDF"</strong>.</li>
                <li>Tunggu proses selesai, lalu klik link unduhan yang muncul untuk membuka atau menyimpan file PDF.</li>
              </ol>
              <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl">
                <p class="text-sm text-blue-700 flex gap-2">
                  <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                  <span>Laporan PDF dicetak dalam format <strong>A4 Landscape</strong> dan disimpan di server. File dapat diunduh berulang kali dari link yang sama selama sesi berlangsung.</span>
                </p>
              </div>
            </div>

            <hr class="border-gray-100">

            <!-- Validation Engine -->
            <div>
              <h4 class="text-lg font-bold text-rose-600 mb-1 flex items-center gap-2">
                <i class="fas fa-shield-alt w-6 text-center"></i> Validation Engine
              </h4>
              <p class="text-xs text-gray-400 mb-4">Rute: <code class="bg-gray-100 px-2 py-0.5 rounded font-mono">/dashboard/validation-engine</code></p>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">
                Validation Engine adalah alat audit otomatis yang memindai seluruh database untuk mendeteksi
                ketidakwajaran dan anomali data realisasi. Fitur ini sangat berguna untuk memastikan integritas data
                sebelum pelaporan resmi.
              </p>

              <h6 class="font-semibold text-sm text-gray-700 mb-3">Cara Menjalankan:</h6>
              <ol class="list-decimal pl-5 text-gray-600 text-sm space-y-2 mb-5">
                <li>Buka menu <strong>Validation Engine</strong> dari sidebar.</li>
                <li>Klik tombol <strong>"Jalankan Validasi"</strong>.</li>
                <li>Sistem akan memproses seluruh data dan menampilkan dua kategori hasil.</li>
              </ol>

              <h6 class="font-semibold text-sm text-gray-700 mb-3">Jenis Anomali yang Dideteksi:</h6>
              <div class="space-y-3">
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-4">
                  <p class="font-bold text-rose-700 text-sm mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Pilar 1 — Data Mismatch</p>
                  <div class="space-y-2">
                    <div class="bg-white border border-rose-100 rounded-lg p-3">
                      <p class="text-xs font-bold text-gray-700">🔴 OVER_PAGU</p>
                      <p class="text-xs text-gray-600 mt-0.5">Realisasi keuangan melebihi nilai pagu yang ditetapkan. Kondisi ini tidak boleh terjadi dan menandakan input yang keliru.</p>
                    </div>
                    <div class="bg-white border border-rose-100 rounded-lg p-3">
                      <p class="text-xs font-bold text-gray-700">🔴 OVER_FISIK</p>
                      <p class="text-xs text-gray-600 mt-0.5">Realisasi fisik (%) melebihi 100%. Tidak mungkin progres fisik lebih dari selesai.</p>
                    </div>
                    <div class="bg-white border border-rose-100 rounded-lg p-3">
                      <p class="text-xs font-bold text-gray-700">🟠 EXTREME_DEVIATION</p>
                      <p class="text-xs text-gray-600 mt-0.5">Selisih antara persentase realisasi keuangan dan realisasi fisik melebihi <strong>30%</strong>. Menunjukkan potensi ketidaksesuaian laporan keuangan dengan kondisi lapangan.</p>
                    </div>
                  </div>
                </div>

                <div class="bg-orange-50 border border-orange-100 rounded-xl p-4">
                  <p class="font-bold text-orange-700 text-sm mb-2"><i class="fas fa-copy mr-1"></i> Pilar 2 — Suspicious Similarity (Copy-Paste Detection)</p>
                  <div class="bg-white border border-orange-100 rounded-lg p-3">
                    <p class="text-xs font-bold text-gray-700">🟡 COPY_PASTE_INPUT</p>
                    <p class="text-xs text-gray-600 mt-0.5">Terdeteksi bila dalam <strong>satu OPD, pada hari yang sama</strong>, terdapat 2 program atau lebih yang diinput dengan nilai realisasi keuangan dan fisik yang <strong>persis identik</strong>. Hal ini mengindikasikan kemungkinan kesalahan copy-paste dalam pencatatan.</p>
                  </div>
                </div>
              </div>

              <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl mt-4">
                <p class="text-sm text-yellow-700 flex gap-2">
                  <i class="fas fa-lightbulb mt-0.5 flex-shrink-0"></i>
                  <span>Jika Validation Engine menemukan anomali, koordinasikan dengan Administrator atau Kepala OPD terkait untuk melakukan verifikasi dan koreksi data di lapangan.</span>
                </p>
              </div>
            </div>

            <hr class="border-gray-100">

            <!-- Riwayat Histori -->
            <div>
              <h4 class="text-lg font-bold text-purple-700 mb-1 flex items-center gap-2">
                <i class="fas fa-history w-6 text-center"></i> Riwayat (History) Perubahan Status
              </h4>
              <p class="text-xs text-gray-400 mb-4">Rute: <code class="bg-gray-100 px-2 py-0.5 rounded font-mono">/dashboard/rfk/history</code></p>
              <p class="text-gray-600 text-sm leading-relaxed">
                Menyediakan log lengkap setiap perubahan status pengajuan realisasi dari seluruh OPD: siapa yang mengubah,
                dari status apa ke status apa, kapan, dan keterangannya. Berguna untuk investigasi sengketa atau
                pemeriksaan audit internal.
              </p>
            </div>

          </div>
        </div>

        <!-- =================== FAQ =================== -->
        <div id="tab-faq" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Pertanyaan yang Sering
            Diajukan</h3>

          <div class="space-y-3 mt-4">

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Bagaimana cara memfilter data dashboard hanya untuk satu OPD tertentu?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Gunakan dropdown <strong>"Seluruh Organisasi (OPD)"</strong> di pojok kanan atas dashboard. Pilih nama OPD yang diinginkan. Semua chart, kartu statistik, dan daftar program akan otomatis menyesuaikan berdasarkan pilihan Anda.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Apa arti "Skor Efektivitas Keuangan" di dashboard?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Skor Efektivitas Keuangan adalah <strong>persentase total realisasi keuangan dibagi total pagu</strong> dari semua OPD. Semakin tinggi nilainya, semakin baik serapan anggaran secara keseluruhan. Angka ini muncul di kartu "Radar Kepatuhan" dengan latar gelap.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Dapatkah Super Administrator menghapus program RFK milik OPD lain?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Secara teknis, program RFK hanya bisa dihapus jika <strong>belum ada realisasi yang di-APPROVE</strong>. Jika sudah ada yang di-approve, data tidak bisa dihapus untuk menjaga integritas. Penghapusan dilakukan melalui antarmuka Staff OPD masing-masing.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Kapan sebaiknya menjalankan Validation Engine?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Disarankan dijalankan secara <strong>berkala</strong> — minimal sekali sebulan, atau sebelum proses pelaporan resmi ke pimpinan. Jalankan juga setiap kali ada kecurigaan terhadap keakuratan data dari suatu OPD.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Bagaimana cara mengaktifkan kembali akun pengguna yang nonaktif?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Buka menu <strong>Manajemen Pengguna</strong>, cari nama pengguna yang ingin diaktifkan (gunakan filter status), klik ikon <strong>Edit</strong>, ubah status dari "Nonaktif" menjadi "Aktif", lalu klik <strong>Simpan</strong>.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Mengapa klik lampu Traffic Light tidak memfilter OPD?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Pastikan data sudah dimuat terlebih dahulu (tunggu spinner selesai). Setelah data termuat, klik salah satu lampu (hijau, kuning, atau merah) — grid OPD di bawah akan langsung difilter sesuai kategori yang dipilih. Klik lampu yang sama lagi untuk menampilkan semua OPD kembali.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Lupa kata sandi (password) login?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Sebagai Super Administrator, Anda dapat mereset password akun lain melalui menu <strong>Manajemen Pengguna → Edit</strong>. Untuk reset password akun Anda sendiri, hubungi tim teknis pengelola server.
              </div>
            </div>

          </div>
        </div>

        <!-- =================== KONTAK ADMIN =================== -->
        <div id="tab-kontak" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Kontak Dukungan</h3>
          <p class="text-gray-600 text-sm mt-4 mb-8">
            Jika Anda mengalami kendala teknis (sistem error, data tidak muncul, bug aplikasi) atau membutuhkan
            konsultasi pengembangan lebih lanjut, silakan hubungi tim teknis kami:
          </p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-green-50 border border-green-100 rounded-2xl p-6 text-center hover:shadow-md transition-shadow">
              <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-green-500 text-3xl mx-auto mb-4 shadow-sm">
                <i class="fab fa-whatsapp"></i>
              </div>
              <h5 class="font-bold text-gray-800 mb-1">WhatsApp Support</h5>
              <p class="text-green-600 font-semibold mb-2">+62 812-3456-7890</p>
              <p class="text-xs text-gray-500">Senin – Jumat (08:00 – 16:00 WIT)</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 text-center hover:shadow-md transition-shadow">
              <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-blue-500 text-3xl mx-auto mb-4 shadow-sm">
                <i class="far fa-envelope"></i>
              </div>
              <h5 class="font-bold text-gray-800 mb-1">Email IT Support</h5>
              <p class="text-blue-600 font-semibold mb-2">support.sirafika@malutprov.go.id</p>
              <p class="text-xs text-gray-500">Respon dalam 1×24 jam kerja</p>
            </div>
          </div>

          <div class="mt-8 bg-purple-50 border border-purple-100 rounded-2xl p-6">
            <h5 class="font-bold text-purple-700 mb-3 flex items-center gap-2"><i class="fas fa-code"></i> Tim Pengembang</h5>
            <p class="text-sm text-gray-600 mb-2">Untuk pengembangan fitur lanjutan atau permintaan kustomisasi sistem, hubungi:</p>
            <div class="flex items-center gap-3 bg-white rounded-xl p-3 border border-purple-100">
              <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-laptop-code text-purple-600"></i>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800">Biro Administrasi Pembangunan</p>
                <p class="text-xs text-gray-500">Provinsi Maluku Utara — Divisi Teknologi Informasi</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <script>
    // Tab switching
    function switchTab(tabId) {
      document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-violet-700', 'text-white');
        btn.classList.add('text-gray-600');
        const icon = btn.querySelector('i');
        if (icon) { icon.classList.remove('text-white'); icon.classList.add('text-gray-400'); }
      });

      const target = document.getElementById('tab-' + tabId);
      if (target) target.classList.add('active');

      const activeBtn = document.querySelector(`[data-tab="${tabId}"]`);
      if (activeBtn) {
        activeBtn.classList.add('active', 'bg-violet-700', 'text-white');
        activeBtn.classList.remove('text-gray-600');
        const icon = activeBtn.querySelector('i');
        if (icon) { icon.classList.remove('text-gray-400'); icon.classList.add('text-white'); }
      }
    }

    // FAQ Accordion
    document.querySelectorAll('.faq-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const content = btn.nextElementSibling;
        const icon = btn.querySelector('i');
        const isOpen = content.style.display === 'block';
        // Close all
        document.querySelectorAll('.faq-content').forEach(c => c.style.display = 'none');
        document.querySelectorAll('.faq-btn i').forEach(i => i.style.transform = 'rotate(0deg)');
        // Toggle selected
        if (!isOpen) {
          content.style.display = 'block';
          icon.style.transform = 'rotate(180deg)';
        }
      });
    });

    // User Dropdown
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');
    if (userMenuButton && userDropdown) {
      userMenuButton.addEventListener('click', (e) => {
        e.stopPropagation();
        const visible = userDropdown.style.display === 'block';
        userDropdown.style.display = visible ? 'none' : 'block';
      });
      document.addEventListener('click', () => { userDropdown.style.display = 'none'; });
    }

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuButton && mobileMenu) {
      mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });
    }
  </script>
</body>

</html>
