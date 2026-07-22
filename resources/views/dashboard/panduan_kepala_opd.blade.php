<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="icon" type="image/png" href="{{ asset('images/malut.webp') }}">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Panduan SI-RAFIKA - Kepala OPD</title>
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
      background-color: #4f46e5;
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
      background-color: #e0e7ff;
      height: 32px;
      margin-left: 22px;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

  <!-- Header (identik dengan kepala_opd.blade.php) -->
  <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
      <div class="flex items-center gap-3">
        <div class="bg-white p-1.5 rounded-lg">
          <img src="{{ asset('images/malut.webp') }}" alt="Logo Maluku Utara" class="h-8">
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
              src="https://eu.ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&bold=true&background=3F3DCE&color=FFFFFF"
              alt="Profile" class="h-7 w-7 rounded-full mr-1">
            {{ Auth::user()->name }} <i class="fas fa-chevron-down text-xs ml-1"></i>
          </button>
          <div id="user-dropdown" class="dropdown-content">
            <a href="#" class="dropdown-item">
              <i class="fas fa-user-circle"></i> Profil
            </a>
            <a href="#" class="dropdown-item">
              <i class="fas fa-cog"></i> Pengaturan
            </a>
            <div class="dropdown-divider"></div>
            <form autocomplete="off" action="{{ route('logout') }}" method="POST">
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
    <div id="mobile-menu" class="hidden md:hidden bg-indigo-800 p-4">
      <div class="flex flex-col gap-3">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-200 transition-colors px-3 py-2 rounded"><i
            class="fas fa-home mr-2"></i> Beranda</a>
        <a href="{{ route('panduan') }}"
          class="hover:text-gray-200 transition-colors px-3 py-2 rounded bg-indigo-700"><i class="fas fa-book mr-2"></i>
          Panduan</a>
        <div class="dropdown-divider"></div>
        <form autocomplete="off" action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="flex items-center px-3 py-2 text-red-400 hover:text-red-300">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </button>
        </form>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white pt-12 pb-20 px-4">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-8">
      <div class="flex-1 text-center md:text-left">
        <div class="inline-flex items-center gap-2 bg-white/20 px-4 py-1.5 rounded-full text-sm font-medium mb-4">
          <i class="fas fa-check-double"></i> Panduan untuk Kepala OPD
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold mb-3 tracking-tight">Panduan Digital SI-RAFIKA</h1>
        <p class="text-blue-100 text-lg mb-6">
          Halo, <strong>{{ Auth::user()->name }}</strong>! Pelajari cara memonitor, mengaudit, dan menyetujui laporan
          realisasi dari Staff OPD Anda.
        </p>
        <div class="flex flex-wrap gap-3 justify-center md:justify-start">
          <button onclick="switchTab('alur')"
            class="bg-white text-indigo-700 hover:bg-gray-100 font-semibold py-2.5 px-6 rounded-full shadow-md transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-route mr-2"></i> Lihat Alur Kerja
          </button>
          <button onclick="switchTab('menu')"
            class="bg-indigo-500 hover:bg-indigo-400 border border-indigo-300 text-white font-medium py-2.5 px-6 rounded-full transition-all">
            <i class="fas fa-book-open mr-2"></i> Panduan Menu
          </button>
        </div>
      </div>
      <div class="hidden md:flex bg-white/20 p-8 rounded-2xl border border-white/30 shadow-2xl">
        <i class="fas fa-check-double text-7xl text-white opacity-90"></i>
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
            <i class="fas fa-home w-5 text-center text-indigo-500"></i> Beranda
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
          <button onclick="switchTab('faq')" data-tab="faq"
            class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 flex items-center gap-3 hover:bg-gray-100 transition-all">
            <i class="fas fa-question-circle w-5 text-center text-gray-400"></i> FAQ
          </button>
          <button onclick="switchTab('kontak')" data-tab="kontak"
            class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-gray-600 flex items-center gap-3 hover:bg-gray-100 transition-all">
            <i class="fas fa-headset w-5 text-center text-gray-400"></i> Kontak Admin
          </button>
        </nav>
      </div>

      <!-- Content Area -->
      <div class="flex-1 p-6 md:p-10 overflow-y-auto">

        <!-- BERANDA -->
        <div id="tab-beranda" class="tab-content active">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Selamat Datang, Kepala OPD!
          </h3>
          <p class="text-gray-600 leading-relaxed mb-6 mt-4">
            Sebagai <strong>Kepala OPD</strong>, Anda berperan sebagai <em>reviewer</em> dan <em>approver</em> utama di
            dalam sistem SI-RAFIKA. Semua laporan realisasi yang dibuat oleh Staff OPD Anda harus melalui persetujuan
            Anda sebelum data resmi tercatat.
          </p>
          <div class="bg-green-50 border border-green-100 p-6 rounded-2xl">
            <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
              <i class="fas fa-crosshairs text-green-600 text-xl"></i> Fokus Tugas Anda:
            </h4>
            <ul class="space-y-2 text-sm text-gray-700">
              <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-0.5 flex-shrink-0"></i>
                Memonitor analitik Dashboard OPD dan Status Traffic Light.</li>
              <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-0.5 flex-shrink-0"></i>
                Mengaudit laporan realisasi dari Staff (Approve atau Reject).</li>
              <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-0.5 flex-shrink-0"></i>
                Mencetak & mengekspor laporan pencapaian OPD.</li>
            </ul>
          </div>

          <!-- Quick Info Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-5 text-center">
              <i class="fas fa-clock text-3xl text-yellow-500 mb-2"></i>
              <p class="text-xs text-gray-500 mb-1">Status Menunggu</p>
              <p class="font-bold text-yellow-700 text-lg">PENDING</p>
              <p class="text-xs text-gray-400 mt-1">Perlu di-review</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl p-5 text-center">
              <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
              <p class="text-xs text-gray-500 mb-1">Sudah Disetujui</p>
              <p class="font-bold text-green-700 text-lg">APPROVE</p>
              <p class="text-xs text-gray-400 mt-1">Data resmi tercatat</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-xl p-5 text-center">
              <i class="fas fa-times-circle text-3xl text-red-500 mb-2"></i>
              <p class="text-xs text-gray-500 mb-1">Perlu Revisi</p>
              <p class="font-bold text-red-700 text-lg">REJECT</p>
              <p class="text-xs text-gray-400 mt-1">Staff wajib perbaiki</p>
            </div>
          </div>
        </div>

        <!-- PENGENALAN -->
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
            <li class="flex items-start gap-2"><i class="fas fa-angle-right text-indigo-500 mt-0.5"></i> Meningkatkan
              transparansi dan akuntabilitas penggunaan anggaran daerah.</li>
            <li class="flex items-start gap-2"><i class="fas fa-angle-right text-indigo-500 mt-0.5"></i> Mempercepat
              proses pelaporan progres dari level Staff hingga Pimpinan.</li>
            <li class="flex items-start gap-2"><i class="fas fa-angle-right text-indigo-500 mt-0.5"></i> Menyediakan
              dashboard analitik <em>real-time</em> untuk pengambilan keputusan.</li>
          </ul>
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-5 rounded-r-xl">
            <div class="flex gap-3">
              <i class="fas fa-lightbulb text-yellow-500 text-xl flex-shrink-0 mt-0.5"></i>
              <p class="text-sm text-yellow-700">
                Tahukah Anda? Data realisasi yang dimasukkan Staff secara otomatis menghasilkan <strong>Traffic
                  Light</strong>: 🔴 Merah (< 70%), 🟡 Kuning (70–89%), 🟢 Hijau (≥ 90%) — berdasarkan persentase
                  serapan anggaran. </p>
            </div>
          </div>
        </div>

        <!-- ALUR PENGGUNAAN -->
        <div id="tab-alur" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Alur Kerja Kepala OPD</h3>
          <p class="text-gray-500 text-sm mb-8 mt-4">Ikuti langkah-langkah berikut untuk memproses laporan dari Staff
            OPD Anda dengan benar.</p>

          <!-- Timeline Steps -->
          <div class="space-y-0">
            <!-- Step 1 -->
            <div class="flex gap-4">
              <div class="flex flex-col items-center">
                <div
                  class="w-11 h-11 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">
                  1</div>
                <div class="step-connector"></div>
              </div>
              <div class="pb-8 pt-1">
                <h4 class="font-bold text-indigo-700 text-base mb-1">Terima Notifikasi Pengajuan</h4>
                <p class="text-gray-600 text-sm leading-relaxed">
                  Setiap kali Staff OPD Anda membuat program baru atau menambah laporan realisasi bulanan, data tersebut
                  akan berstatus <span
                    class="inline-flex items-center bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded-full">PENDING</span>
                  dan muncul di halaman dashboard Anda pada bagian <strong>"Menunggu Approval"</strong>.
                </p>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4">
              <div class="flex flex-col items-center">
                <div
                  class="w-11 h-11 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">
                  2</div>
                <div class="step-connector"></div>
              </div>
              <div class="pb-8 pt-1">
                <h4 class="font-bold text-blue-700 text-base mb-1">Periksa Detail Pengajuan (Audit RFK)</h4>
                <p class="text-gray-600 text-sm leading-relaxed">
                  Klik <strong>"Detail"</strong> pada pengajuan yang masuk. Pastikan nilai progres keuangan, persentase
                  fisik, dan keterangan lainnya sesuai dengan kondisi riil di lapangan. Periksa apakah nilai realisasi
                  tidak melebihi Sisa Pagu yang tersedia.
                </p>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4">
              <div class="flex flex-col items-center">
                <div
                  class="w-11 h-11 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">
                  3</div>
              </div>
              <div class="pb-2 pt-1">
                <h4 class="font-bold text-green-700 text-base mb-1">Action: Approve atau Reject</h4>
                <p class="text-gray-600 text-sm leading-relaxed mb-3">
                  Setelah memeriksa, ambil keputusan:
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                  <div class="flex-1 bg-green-50 border border-green-200 rounded-xl p-4">
                    <h6 class="font-bold text-green-700 mb-1 flex items-center gap-2"><i
                        class="fas fa-check-circle"></i> Approve</h6>
                    <p class="text-xs text-gray-600">Data benar dan sesuai. Pagu Master akan ter-update otomatis. Status
                      menjadi <strong class="text-green-700">APPROVE</strong>. Apabila sudah 100%, akan otomatis <strong
                        class="text-blue-700">SELESAI</strong>.</p>
                  </div>
                  <div class="flex-1 bg-red-50 border border-red-200 rounded-xl p-4">
                    <h6 class="font-bold text-red-700 mb-1 flex items-center gap-2"><i class="fas fa-times-circle"></i>
                      Reject</h6>
                    <p class="text-xs text-gray-600">Ada kesalahan/ketidaksesuaian. Staff akan diminta merevisi. Status
                      menjadi <strong class="text-red-700">REJECT</strong>.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- PANDUAN PER MENU -->
        <div id="tab-menu" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Panduan per Menu</h3>

          <div class="space-y-10 mt-4">
            <!-- Dashboard -->
            <div>
              <h4 class="text-lg font-bold text-indigo-700 mb-3 flex items-center gap-2">
                <i class="fas fa-home w-6 text-center"></i> Dashboard & Analitik
              </h4>
              <div class="bg-gray-100 rounded-xl overflow-hidden mb-4 border shadow-sm p-2">
                <img src="{{ asset('images/panduan/sirafika_dashboard_1784204227842.png') }}" alt="Dashboard Screenshot"
                  class="w-full h-auto object-cover rounded-lg border border-gray-200">
              </div>
              <p class="text-gray-600 text-sm leading-relaxed">
                Dashboard memberikan visualisasi data OPD Anda secara real-time: ringkasan <strong>Total Pagu</strong>,
                serapan anggaran, rata-rata fisik, dan status <strong>Traffic Light</strong> program-program di OPD
                Anda.
              </p>
            </div>

            <hr class="border-gray-100">

            <!-- Audit & Approval -->
            <div>
              <h4 class="text-lg font-bold text-indigo-700 mb-3 flex items-center gap-2">
                <i class="fas fa-check-double w-6 text-center"></i> Audit & Approval RFK
              </h4>
              <div class="bg-gray-100 rounded-xl overflow-hidden mb-4 border shadow-sm p-2">
                <img src="{{ asset('images/panduan/sirafika_approval_1784204247524.png') }}" alt="Approval Screenshot"
                  class="w-full h-auto object-cover rounded-lg border border-gray-200">
              </div>
              <p class="text-gray-600 text-sm leading-relaxed mb-3"><strong>Cara Menyetujui / Menolak Pengajuan
                  Staff:</strong></p>
              <ol class="list-decimal pl-5 text-gray-600 text-sm space-y-2">
                <li>Di Dashboard, scroll ke bagian <strong>"Menunggu Approval"</strong>.</li>
                <li>Klik <strong>Detail</strong> untuk melihat rincian pengajuan.</li>
                <li>Jika data sudah benar, klik tombol hijau <strong>Approve</strong>.</li>
                <li>Jika ada kekeliruan data, klik tombol merah <strong>Reject</strong> — Staff akan diminta merevisi.
                </li>
              </ol>
              <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl mt-4">
                <p class="text-sm text-blue-700 flex gap-2">
                  <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                  Pastikan nilai realisasi tidak melebihi <strong>Sisa Pagu</strong> yang tersedia. Jika melebihi, tolak
                  dan minta Staff merevisi nominalnya.
                </p>
              </div>
            </div>

            <hr class="border-gray-100">

            <!-- Laporan -->
            <div>
              <h4 class="text-lg font-bold text-indigo-700 mb-3 flex items-center gap-2">
                <i class="fas fa-file-pdf w-6 text-center"></i> Cetak Laporan
              </h4>
              <div class="bg-gray-100 rounded-xl overflow-hidden mb-4 border shadow-sm p-2">
                <img src="{{ asset('images/panduan/sirafika_laporan_1784204257941.png') }}" alt="Laporan Screenshot"
                  class="w-full h-auto object-cover rounded-lg border border-gray-200">
              </div>
              <p class="text-gray-600 text-sm leading-relaxed">
                Gunakan menu <strong>Laporan</strong> untuk memfilter berdasarkan OPD atau Status program, kemudian klik
                <strong>Export to PDF</strong> untuk mencetak dokumen resmi laporan rekapitulasi.
              </p>
            </div>
          </div>
        </div>

        <!-- FAQ -->
        <div id="tab-faq" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Pertanyaan yang Sering
            Diajukan</h3>

          <div class="space-y-3 mt-4">
            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button
                class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Apakah saya bisa membatalkan Approval yang sudah disetujui?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Sistem secara <em>default</em> tidak mengizinkan pembatalan langsung setelah persetujuan untuk menjaga
                integritas saldo sisa pagu. Jika terjadi kesalahan fatal, Anda harus berkoordinasi dengan
                <strong>Administrator Pusat</strong> untuk koreksi data.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button
                class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Bagaimana jika nilai realisasi Staff melebihi Pagu?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Sistem akan menampilkan peringatan. Anda wajib <strong>Reject</strong> pengajuan tersebut dan minta
                Staff menyesuaikan nilai realisasi agar tidak melebihi sisa pagu yang tersedia.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button
                class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Apakah data laporan bisa langsung dicetak menjadi PDF?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Ya. Menu <strong>Laporan</strong> menyediakan fitur <em>Export to PDF</em> yang bisa difilter
                berdasarkan OPD, status program, atau periode waktu tertentu.
              </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
              <button
                class="faq-btn w-full text-left px-6 py-4 font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                <span>Lupa kata sandi (Password) login?</span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
              </button>
              <div class="faq-content px-6 py-4 text-gray-600 text-sm border-t bg-white">
                Silakan hubungi Administrator Pusat via menu Kontak untuk mereset kata sandi akun Anda.
              </div>
            </div>
          </div>
        </div>

        <!-- KONTAK ADMIN -->
        <div id="tab-kontak" class="tab-content">
          <h3 class="text-2xl font-bold text-gray-800 mb-2 border-b border-gray-100 pb-4">Kontak Dukungan</h3>
          <p class="text-gray-600 text-sm mt-4 mb-8">
            Jika Anda mengalami kendala teknis (sistem error, data tidak muncul, perlu koreksi data approval) silakan
            hubungi tim dukungan kami:
          </p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              class="bg-green-50 border border-green-100 rounded-2xl p-6 text-center hover:shadow-md transition-shadow">
              <div
                class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-green-500 text-3xl mx-auto mb-4 shadow-sm">
                <i class="fab fa-whatsapp"></i>
              </div>
              <h5 class="font-bold text-gray-800 mb-1">WhatsApp Support</h5>
              <p class="text-green-600 font-semibold mb-2">+62 821-8986-0629</p>
              <p class="text-xs text-gray-500">Senin – Jumat (08:00 – 16:00 WIT)</p>
            </div>
            <div
              class="bg-blue-50 border border-blue-100 rounded-2xl p-6 text-center hover:shadow-md transition-shadow">
              <div
                class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-blue-500 text-3xl mx-auto mb-4 shadow-sm">
                <i class="far fa-envelope"></i>
              </div>
              <h5 class="font-bold text-gray-800 mb-1">Email IT Support</h5>
              <p class="text-blue-600 font-semibold mb-2">support.sirafika@malutprov.go.id</p>
              <p class="text-xs text-gray-500">Respon dalam 1×24 jam kerja</p>
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
        btn.classList.remove('active', 'bg-indigo-600', 'text-white');
        btn.classList.add('text-gray-600');
        const icon = btn.querySelector('i');
        if (icon) { icon.classList.remove('text-white'); icon.classList.add('text-gray-400'); }
      });

      const target = document.getElementById('tab-' + tabId);
      if (target) target.classList.add('active');

      const activeBtn = document.querySelector(`[data-tab="${tabId}"]`);
      if (activeBtn) {
        activeBtn.classList.add('active', 'bg-indigo-600', 'text-white');
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