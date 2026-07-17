<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SI-RAFIKA (Realisasi Fisik Dan Keuangan) - Dashboard OPD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    /* ============ STYLE SAMA SEPERTI SEBELUMNYA ============ */
    :root {
      --primary: #31326F;
      --secondary: #4FB7B3;
      --accent: #1F8ECD;
      --success: #4cc9f0;
      --warning: #f72585;
      --light: #f8f9fa;
      --dark: #212529;
      --bg-light: #f0f2f5;
      --bg-dark: #121212;
      --card-light: #ffffff;
      --card-dark: #1e1e1e;
      --text-light: #333333;
      --text-dark: #f1f1f1;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--bg-light);
      color: var(--text-light);
      transition: background-color 0.4s, color 0.4s;
      padding-bottom: 80px;
      position: relative;
      min-height: 100vh;
    }

    body.dark-mode {
      background-color: var(--bg-dark);
      color: var(--text-dark);
    }

    .app-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      border-radius: 0 0 20px 20px;
      padding: 20px;
      color: white;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      margin-bottom: 24px;
    }

    .logo-container {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 10px;
      flex-wrap: wrap;
    }

    .logo-img {
      height: 40px;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .card-custom {
      border: none;
      border-radius: 16px;
      background: var(--card-light);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      overflow: hidden;
    }

    body.dark-mode .card-custom {
      background: var(--card-dark);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .summary-card {
      position: relative;
      padding: 20px;
      text-align: center;
    }

    .summary-card h6 {
      font-size: 0.85rem;
      margin-bottom: 8px;
      color: #6c757d;
    }

    body.dark-mode .summary-card h6 {
      color: #adb5bd;
    }

    .summary-card p {
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: 0;
    }

    .summary-icon {
      position: absolute;
      top: 15px;
      right: 15px;
      opacity: 0.2;
      font-size: 1.8rem;
    }

    .progress {
      height: 8px;
      border-radius: 10px;
      background-color: #e9ecef;
      margin-top: 10px;
    }

    .progress-bar {
      border-radius: 10px;
      background: linear-gradient(to right, var(--primary), var(--secondary));
    }

    .menu-card {
      padding: 25px 15px;
      text-align: center;
      cursor: pointer;
    }

    .menu-icon {
      font-size: 24px;
      width: 50px;
      height: 50px;
      line-height: 50px;
      border-radius: 50%;
      margin: 0 auto 15px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: white;
      transition: all 0.3s ease;
    }

    .menu-card:hover .menu-icon {
      transform: scale(1.1);
    }

    .footer-nav {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: var(--card-light);
      border-top: 1px solid rgba(0, 0, 0, 0.05);
      padding: 12px 0;
      display: flex;
      justify-content: space-around;
      z-index: 1000;
    }

    body.dark-mode .footer-nav {
      background: var(--card-dark);
    }

    .footer-nav a {
      text-decoration: none;
      color: inherit;
      font-size: 12px;
      text-align: center;
      opacity: 0.7;
      transition: all 0.3s ease;
      padding: 5px 10px;
      border-radius: 15px;
    }

    .footer-nav a.active {
      opacity: 1;
      background: rgba(49, 50, 111, 0.1);
      color: var(--primary);
    }

    .footer-icon {
      display: block;
      font-size: 20px;
      margin-bottom: 4px;
    }

    .mode-toggle {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      transition: all 0.3s ease;
    }

    .section-title {
      position: relative;
      padding-left: 15px;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .section-title::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      height: 20px;
      width: 5px;
      background: linear-gradient(to bottom, var(--primary), var(--secondary));
      border-radius: 10px;
    }

    .stats-highlight {
      background: linear-gradient(135deg, rgba(49, 50, 111, 0.1) 0%, rgba(79, 183, 179, 0.1) 100%);
      border-radius: 12px;
      padding: 15px;
      margin-top: 15px;
    }

    .mini-chart {
      height: 40px;
      display: flex;
      align-items: flex-end;
      margin-top: 10px;
    }

    .chart-bar {
      flex: 1;
      background: linear-gradient(to top, var(--primary), var(--secondary));
      margin: 0 2px;
      border-radius: 3px 3px 0 0;
    }

    .modal-content {
      border-radius: 24px;
    }

    body.dark-mode .modal-content {
      background: var(--card-dark);
      color: var(--text-dark);
    }

    .form-control,
    .form-select {
      border-radius: 12px;
      border: 1px solid #dee2e6;
      padding: 10px 14px;
      transition: all 0.2s;
    }

    body.dark-mode .form-control,
    body.dark-mode .form-select {
      background-color: #2c2c2c;
      border-color: #444;
      color: #f1f1f1;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--secondary);
      box-shadow: 0 0 0 0.2rem rgba(79, 183, 179, 0.25);
    }

    .btn-primary-gradient {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      border-radius: 40px;
      padding: 10px 20px;
      font-weight: 600;
      transition: transform 0.2s;
    }

    .btn-primary-gradient:hover {
      transform: scale(1.02);
      color: white;
    }

    .input-group-text-custom {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      border: none;
      border-radius: 12px 0 0 12px;
    }

    .toast-notif {
      position: fixed;
      bottom: 80px;
      right: 16px;
      z-index: 1100;
      min-width: 240px;
      background: var(--card-light);
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      border-left: 5px solid var(--secondary);
    }

    .badge-auto {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      font-size: 0.7rem;
      padding: 4px 8px;
      border-radius: 20px;
    }

    .form-section {
      background: rgba(0, 0, 0, 0.02);
      padding: 15px;
      border-radius: 16px;
      margin-bottom: 15px;
    }

    body.dark-mode .form-section {
      background: rgba(255, 255, 255, 0.05);
    }

    .form-section-title {
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 12px;
      color: var(--primary);
    }

    .status-badge-pending {
      background: #ffc107;
      color: #000;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.7rem;
      font-weight: 600;
    }

    .status-badge-approve {
      background: #28a745;
      color: #fff;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.7rem;
      font-weight: 600;
    }

    .status-badge-reject {
      background: #dc3545;
      color: #fff;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.7rem;
      font-weight: 600;
    }

    /* Base Button Styling */
    .logout-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      font-family: system-ui, -apple-system, sans-serif;
      font-size: 14px;
      font-weight: 600;
      color: #dc2626;
      background-color: #fef2f2;
      border: 1px solid #fca5a5;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
    }

    /* Hover State */
    .logout-btn:hover {
      color: #ffffff;
      background-color: #dc2626;
      border-color: #dc2626;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
    }

    /* Active/Click State */
    .logout-btn:active {
      transform: translateY(0);
      box-shadow: none;
    }

    /* Focus State for Accessibility */
    .logout-btn:focus-visible {
      outline: 2px solid #dc2626;
      outline-offset: 2px;
    }

    /* Optional Icon Rotation Animation */
    .logout-btn:hover .logout-icon {
      transform: translateX(2px);
      transition: transform 0.2s ease;
    }
  </style>
</head>

<body>

  <div class="app-header">
    <div class="container">
      <div class="logo-container">
        <img src="https://e-rekrutmen.malutprov.go.id/assets/images/malut.png" alt="Logo" class="logo-img">
        <div>
          <h2 class="fw-bold mb-0">SI-RAFIKA (Realisasi Fisik Dan Keuangan)</h2>
          <p class="mb-0 opacity-75">Biro Administrasi Pembangunan Setda Provinsi Maluku Utara</p>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center mt-2">
        <p class="mb-0">Selamat datang, {{ Auth::user()->name }} - {{ Auth::user()->opd->nama_opd ?? 'OPD' }}</p>
        <button id="toggleMode" class="mode-toggle"><i class="fas fa-moon"></i></button>
      </div>
    </div>
  </div>

  <div class="container">
    <h5 class="section-title">Ringkasan</h5>
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="card-custom summary-card">
          <i class="fas fa-folder-open summary-icon"></i>
          <h6>Total Program</h6>
          <p id="totalProgramCount" style="color: var(--primary);">0</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card-custom summary-card">
          <i class="fas fa-chart-line summary-icon"></i>
          <h6>TOTAL PAGU (Rp)</h6>
          <p id="totalPaguDisplay" style="color: var(--secondary);">0</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card-custom summary-card">
          <i class="fas fa-spinner summary-icon"></i>
          <h6>Progress Berjalan</h6>
          <p id="progressBerjalan" style="color: var(--accent);">0</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card-custom summary-card">
          <i class="fas fa-exclamation-triangle summary-icon"></i>
          <h6>Terlambat</h6>
          <p id="terlambatCount" style="color: var(--warning);">0</p>
        </div>
      </div>
    </div>

    <div class="card-custom p-3 mb-4">
      <div class="stats-highlight">
        <div class="row text-center">
          <div class="col-4">
            <h4 class="mb-0" id="avgFisik" style="color: var(--primary);">0%</h4><small>Rata-rata Fisik</small>
          </div>
          <div class="col-4">
            <h4 class="mb-0" id="avgKeuanganPersen" style="color: var(--secondary);">0%</h4><small>Rata-rata Realisasi
              Keuangan</small>
          </div>
          <div class="col-4">
            <h4 class="mb-0" id="totalSisaPag" style="color: var(--accent);">Rp 0</h4><small>Total Sisa Pagu</small>
          </div>
        </div>
      </div>
    </div>

    <h5 class="section-title">Progress Fisik Program</h5>
    <div class="card-custom p-3 mb-4">
      <div class="mini-chart" id="dynamicChart"></div>
      <div class="d-flex justify-content-between mt-2"><small>Program terbaru</small><small>Realisasi Fisik %</small>
      </div>
    </div>

    <h5 class="section-title">Menu Pilihan</h5>
    <div class="row g-3 mb-5">
      <div class="col-6 col-md-4">
        <div class="card-custom menu-card" data-bs-toggle="modal" data-bs-target="#inputRFKModal">
          <div class="menu-icon"><i class="fas fa-tasks"></i></div>
          <p>Input RFK</p>
        </div>
      </div>
      <div class="col-6 col-md-4">
        <div class="card-custom menu-card" id="laporanSayaBtn">
          <div class="menu-icon"><i class="fas fa-chart-bar"></i></div>
          <p>Laporan Saya</p>
        </div>
      </div>
      <div class="col-6 col-md-4">
        <a href="{{ route('panduan') }}" style="text-decoration: none; color: inherit;">
          <div class="card-custom menu-card">
            <div class="menu-icon" style="background: linear-gradient(135deg, #1F8ECD 0%, #4cc9f0 100%);"><i
                class="fas fa-book"></i></div>
            <p>Panduan</p>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="footer-nav">
    <a href="#" class="active"><i class="fas fa-home footer-icon"></i><span>Beranda</span></a>
    <a href="#"><i class="fas fa-user footer-icon"></i><span>Profil</span></a>
    <a href="#"><i class="fas fa-bell footer-icon"></i><span>Notifikasi</span></a>
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button class="logout-btn">
        <svg xmlns="http://w3.org" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logout-icon">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
          <polyline points="16 17 21 12 16 7"></polyline>
          <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        <span>Logout</span>
      </button>
    </form>
  </div>

  <!-- ============ MODAL INPUT RFK DENGAN FORM YANG DIPERBAIKI ============ -->
  <div class="modal fade" id="inputRFKModal" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="inputRFKModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold"><i class="fas fa-pen-ruler me-2" style="color: var(--secondary);"></i>Form
            Input Realisasi RFK</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body pt-3">
          <form id="rfkForm">
            @csrf

            <!-- Section 1: Data Program -->
            <div class="form-section">
              <div class="form-section-title"><i class="fas fa-tag me-2"></i>Data Program</div>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Kode Program <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="kodeProgram" placeholder="Contoh: PRG-001" required>
                </div>
                <div class="col-md-8">
                  <label class="form-label fw-semibold">Nama Program <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="namaProgram"
                    placeholder="Contoh: Peningkatan Infrastruktur Jalan" required>
                </div>
                <div class="col-md-12">
                  <label class="form-label fw-semibold">Sub Kategori Program</label>
                  <input type="text" class="form-control" id="subKategoriProgram"
                    placeholder="Contoh: Pembangunan Fisik / Non Fisik">
                </div>
              </div>
            </div>

            <!-- Section 2: Sumber Dana -->
            <div class="form-section">
              <div class="form-section-title"><i class="fas fa-coins me-2"></i>Sumber Dana</div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Sumber Dana <span class="text-danger">*</span></label>
                  <select class="form-select" id="sumberDana" required>
                    <option value="">Pilih Sumber Dana</option>
                    <option value="APBD">APBD</option>
                    <option value="APBN">APBN</option>
                  </select>
                </div>
                <div class="col-md-6" id="kategoriAnggaranContainer" style="display:none;">
                  <label class="form-label fw-semibold">Kategori Anggaran</label>
                  <select class="form-select" id="kategoriAnggaran">
                    <option value="">Pilih Kategori</option>
                  </select>
                </div>
                <div class="col-md-6" id="subKategoriAnggaranContainer" style="display:none;">
                  <label class="form-label fw-semibold">Sub Kategori Anggaran</label>
                  <select class="form-select" id="subKategoriAnggaran">
                    <option value="">Pilih Sub Kategori</option>
                  </select>
                </div>
                <div class="col-md-6" id="sumberDanaDetailContainer" style="display:none;">
                  <label class="form-label fw-semibold">Sumber Dana Detail</label>
                  <select class="form-select" id="sumberDanaDetail">
                    <option value="">Pilih Sumber Dana</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Section 3: Anggaran -->
            <div class="form-section">
              <div class="form-section-title"><i class="fas fa-chart-line me-2"></i>Detail Anggaran</div>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Tahun Anggaran <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" id="tahunAnggaran" value="{{ date('Y') }}" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold">PAGU (Rp) <span class="text-danger">*</span></label>
                  <input type="text" class="form-control rupiah-input" id="pagu" placeholder="Total Pagu" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Realisasi Keuangan (Rp) <span
                      class="text-danger">*</span></label>
                  <input type="text" class="form-control rupiah-input" id="realKeuanganRupiah" placeholder="Realisasi"
                    required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Realisasi Fisik (%) <i
                      class="fas fa-magic fa-xs text-muted"></i></label>
                  <input type="text" class="form-control bg-light" id="realFisikOtomatis" readonly
                    placeholder="Terisi otomatis">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Sisa PAGU (Rp)</label>
                  <input type="text" class="form-control bg-light" id="sisaPagu" readonly placeholder="Terisi otomatis">
                </div>
              </div>
            </div>

            <!-- Section 4: Keterangan -->
            <div class="form-section">
              <div class="form-section-title"><i class="fas fa-sticky-note me-2"></i>Keterangan</div>
              <div class="row">
                <div class="col-12">
                  <textarea class="form-control" id="keterangan" rows="3" placeholder="Catatan tambahan..."></textarea>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
              <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary-gradient px-4 text-white">Simpan RFK <i
                  class="fas fa-save ms-1"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div><!-- Modal Edit Realisasi -->
  <div class="modal fade" id="editRealisasiModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2" style="color: var(--danger);"></i>Perbaiki
            Realisasi (Ditolak)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body pt-3">
          <form id="formEditRealisasi">
            <input type="hidden" id="er_realisasi_id">
            <div class="mb-3">
              <label class="form-label fw-semibold">Program</label>
              <input type="text" class="form-control bg-light" id="er_nama_program" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Sisa PAGU (Rp)</label>
              <input type="text" class="form-control bg-light" id="er_sisa_pagu_display" readonly>
              <input type="hidden" id="er_sisa_pagu">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Nilai Pengajuan Ulang (Rp) <span
                  class="text-danger">*</span></label>
              <input type="text" class="form-control rupiah-input" id="er_nilai" required>
              <small class="text-danger" id="er_warning" style="display:none;">Nilai melebihi sisa pagu!</small>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Keterangan Tambahan</label>
              <textarea class="form-control" id="er_keterangan" rows="2"></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
              <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger px-4 text-white">Ajukan Ulang <i
                  class="fas fa-paper-plane ms-1"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Realisasi -->
  <div class="modal fade" id="tambahRealisasiModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2" style="color: var(--secondary);"></i>Tambah
            Realisasi (Bertahap)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body pt-3">
          <form id="formTambahRealisasi">
            <input type="hidden" id="tr_rfk_id">
            <div class="mb-3">
              <label class="form-label fw-semibold">Program</label>
              <input type="text" class="form-control bg-light" id="tr_nama_program" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Sisa PAGU (Rp)</label>
              <input type="text" class="form-control bg-light" id="tr_sisa_pagu_display" readonly>
              <input type="hidden" id="tr_sisa_pagu">
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Nilai Tambahan Keuangan (Rp) <span
                  class="text-danger">*</span></label>
              <input type="text" class="form-control rupiah-input" id="tr_nilai" required>
              <small class="text-danger" id="tr_warning" style="display:none;">Nilai melebihi sisa pagu!</small>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Keterangan Tambahan</label>
              <textarea class="form-control" id="tr_keterangan" rows="2"></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
              <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary-gradient px-4 text-white">Ajukan Realisasi <i
                  class="fas fa-paper-plane ms-1"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="liveToast" class="toast-notif p-3" style="display: none;">
    <div class="d-flex align-items-center">
      <i class="fas fa-check-circle me-2 fs-4" style="color: var(--secondary);"></i>
      <div class="fw-semibold" id="toastMessage">Data tersimpan</div>
      <button type="button" class="btn-close ms-auto" onclick="closeToast()"></button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // ============ LOGIKA FORM DINAMIS SUMBER DANA ============
    const sumberDanaSelect = document.getElementById('sumberDana');
    const kategoriAnggaranContainer = document.getElementById('kategoriAnggaranContainer');
    const kategoriAnggaranSelect = document.getElementById('kategoriAnggaran');
    const subKategoriAnggaranContainer = document.getElementById('subKategoriAnggaranContainer');
    const subKategoriAnggaranSelect = document.getElementById('subKategoriAnggaran');
    const sumberDanaDetailContainer = document.getElementById('sumberDanaDetailContainer');
    const sumberDanaDetailSelect = document.getElementById('sumberDanaDetail');

    // Data untuk APBD
    const apbdKategori = [
      { value: 'BELANJA_OPERASI', label: 'Belanja Operasi' },
      { value: 'BELANJA_MODAL', label: 'Belanja Modal' }
    ];

    const apbdSubOperasi = [
      { value: 'BELANJA_PEGAWAI', label: 'Belanja Pegawai' },
      { value: 'BELANJA_BARANG_JASA', label: 'Belanja Barang Dan Jasa' },
      { value: 'BELANJA_HIBAH', label: 'Belanja Hibah' }
    ];

    const apbdSubModal = [
      { value: 'BELANJA_MODAL', label: 'Belanja Modal' },
      { value: 'BELANJA_MODAL_PERALATAN_MESIN', label: 'Belanja Modal Peralatan Dan Mesin' },
      { value: 'BELANJA_MODAL_JALAN_IRIGASI', label: 'Belanja Modal Jalan, Irigasi' },
      { value: 'BELANJA_MODAL_BANGUNAN_GEDUNG', label: 'Belanja Modal Bangunan Gedung' }
    ];

    // Data untuk APBN
    const apbnSumberDana = [
      { value: 'DAU', label: 'DAU (Dana Alokasi Umum)' },
      { value: 'DAK', label: 'DAK (Dana Alokasi Khusus)' },
      { value: 'DBH', label: 'DBH (Dana Bagi Hasil)' },
      { value: 'DEKOM', label: 'DEKOM (Dana Dekonsentrasi)' }
    ];

    // Event listener untuk Sumber Dana
    sumberDanaSelect.addEventListener('change', function () {
      const value = this.value;

      // Reset semua
      kategoriAnggaranContainer.style.display = 'none';
      subKategoriAnggaranContainer.style.display = 'none';
      sumberDanaDetailContainer.style.display = 'none';
      kategoriAnggaranSelect.value = '';
      subKategoriAnggaranSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
      sumberDanaDetailSelect.innerHTML = '<option value="">Pilih Sumber Dana</option>';

      if (value === 'APBD') {
        // Tampilkan kategori anggaran
        kategoriAnggaranContainer.style.display = 'block';
        kategoriAnggaranSelect.innerHTML = '<option value="">Pilih Kategori</option>';
        apbdKategori.forEach(k => {
          kategoriAnggaranSelect.innerHTML += `<option value="${k.value}">${k.label}</option>`;
        });
      } else if (value === 'APBN') {
        // Tampilkan sumber dana detail
        sumberDanaDetailContainer.style.display = 'block';
        apbnSumberDana.forEach(s => {
          sumberDanaDetailSelect.innerHTML += `<option value="${s.value}">${s.label}</option>`;
        });
      }
    });

    // Event listener untuk Kategori Anggaran (APBD)
    kategoriAnggaranSelect.addEventListener('change', function () {
      const value = this.value;
      subKategoriAnggaranContainer.style.display = 'none';
      subKategoriAnggaranSelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';

      if (value === 'BELANJA_OPERASI') {
        subKategoriAnggaranContainer.style.display = 'block';
        apbdSubOperasi.forEach(s => {
          subKategoriAnggaranSelect.innerHTML += `<option value="${s.value}">${s.label}</option>`;
        });
      } else if (value === 'BELANJA_MODAL') {
        subKategoriAnggaranContainer.style.display = 'block';
        apbdSubModal.forEach(s => {
          subKategoriAnggaranSelect.innerHTML += `<option value="${s.value}">${s.label}</option>`;
        });
      }
    });

    // ============ HITUNG OTOMATIS ============
    function parseRupiahStr(str) {
      if (!str) return 0;
      return parseFloat(str.replace(/\./g, '').replace(/,/g, '.')) || 0;
    }

    function formatRupiahInput(input) {
      let value = input.value.replace(/[^,\d]/g, '').toString();
      let split = value.split(',');
      let sisa = split[0].length % 3;
      let rupiah = split[0].substr(0, sisa);
      let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }

      rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
      input.value = rupiah;
    }

    const rupiahInputs = document.querySelectorAll('.rupiah-input');
    rupiahInputs.forEach(input => {
      input.addEventListener('input', function () {
        formatRupiahInput(this);
      });
    });

    const paguInput = document.getElementById('pagu');
    const realKeuInput = document.getElementById('realKeuanganRupiah');
    const realFisikField = document.getElementById('realFisikOtomatis');
    const sisaPaguField = document.getElementById('sisaPagu');

    function hitungOtomatis() {
      const pagu = parseRupiahStr(paguInput.value);
      const realKeu = parseRupiahStr(realKeuInput.value);

      // Hitung Sisa Pagu
      const sisa = pagu - realKeu;
      sisaPaguField.value = sisa >= 0 ? formatRupiah(sisa) : '0';

      // Hitung Realisasi Fisik
      let fisik = 0;
      if (pagu > 0 && realKeu > 0) {
        fisik = (realKeu / pagu) * 100;
        fisik = Math.min(100, fisik);
        realFisikField.value = fisik.toFixed(2) + '%';
      } else {
        realFisikField.value = '0%';
      }
    }

    function formatRupiah(angka) {
      return new Intl.NumberFormat('id-ID').format(angka);
    }

    paguInput.addEventListener('input', hitungOtomatis);
    realKeuInput.addEventListener('input', hitungOtomatis);

    // ============ SUBMIT FORM ============
    const formRFK = document.getElementById('rfkForm');
    formRFK.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Kumpulkan data
      const formData = {
        kode_program: document.getElementById('kodeProgram').value,
        nama_program: document.getElementById('namaProgram').value,
        sub_kategori_program: document.getElementById('subKategoriProgram').value,
        sumber_dana: document.getElementById('sumberDana').value,
        kategori_anggaran: document.getElementById('kategoriAnggaran').value,
        sub_kategori_anggaran: document.getElementById('subKategoriAnggaran').value,
        sumber_dana_detail: document.getElementById('sumberDanaDetail').value,
        tahun_anggaran: document.getElementById('tahunAnggaran').value,
        pagu: parseRupiahStr(document.getElementById('pagu').value),
        realisasi_keuangan: parseRupiahStr(document.getElementById('realKeuanganRupiah').value),
        keterangan: document.getElementById('keterangan').value
      };

      // Validasi
      if (!formData.kode_program || !formData.nama_program || !formData.sumber_dana) {
        showToast('Isi Kode Program, Nama Program, dan Sumber Dana!', 'error');
        return;
      }

      if (!formData.pagu || formData.pagu <= 0) {
        showToast('PAGU harus diisi dan lebih dari 0!', 'error');
        return;
      }

      if (formData.realisasi_keuangan > formData.pagu) {
        showToast('Realisasi Keuangan tidak boleh melebihi PAGU!', 'error');
        return;
      }

      try {
        const response = await fetch('{{ route("rfk.store") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('inputRFKModal'));
          modal.hide();
          formRFK.reset();
          sisaPaguField.value = '';
          realFisikField.value = '';
          showToast(result.message, 'success');
          loadDashboardData();
        } else {
          showToast(result.message, 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menyimpan data', 'error');
      }
    });

    // ============ LOAD DASHBOARD DATA ============
    async function loadDashboardData() {
      try {
        const response = await fetch('{{ route("rfk.data") }}');
        const result = await response.json();

        if (result.success) {
          const stats = result.statistics;
          const data = result.data;

          // Update ringkasan
          document.getElementById('totalProgramCount').innerText = stats.total_program;
          document.getElementById('totalPaguDisplay').innerText = 'Rp ' + formatRupiah(stats.total_pagu);
          document.getElementById('progressBerjalan').innerText = stats.progress_berjalan;
          document.getElementById('terlambatCount').innerText = stats.terlambat;
          document.getElementById('avgFisik').innerHTML = stats.avg_fisik + '%';
          document.getElementById('avgKeuanganPersen').innerHTML = stats.avg_keuangan_persen + '%';
          document.getElementById('totalSisaPag').innerHTML = 'Rp ' + formatRupiah(stats.total_sisa_pagu);

          // Update chart
          updateChart(data);
        }
      } catch (error) {
        console.error('Error loading data:', error);
      }
    }

    function updateChart(data) {
      const chartContainer = document.getElementById('dynamicChart');
      chartContainer.innerHTML = '';

      const lastPrograms = data.slice(-7);
      if (lastPrograms.length === 0) {
        for (let i = 0; i < 7; i++) {
          const bar = document.createElement('div');
          bar.className = 'chart-bar';
          bar.style.height = '20%';
          chartContainer.appendChild(bar);
        }
      } else {
        lastPrograms.forEach(prog => {
          const bar = document.createElement('div');
          bar.className = 'chart-bar';
          const tinggi = Math.min(95, Math.max(5, prog.realisasi_fisik || 0));
          bar.style.height = tinggi + '%';
          bar.setAttribute('title', `${prog.nama_program}: ${prog.realisasi_fisik}%`);
          chartContainer.appendChild(bar);
        });
      }
    }

    // ============ LAPORAN ============
    const laporanBtn = document.getElementById('laporanSayaBtn');
    laporanBtn.addEventListener('click', async () => {
      try {
        const response = await fetch('{{ route("rfk.data") }}');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
          let tableRows = '';
          result.data.forEach(item => {
            const statusBadge = getStatusBadge(item.status);

            let actionBtn = '';
            if (item.status === 'PENDING') {
              actionBtn = `<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu Approval</span>`;
            } else if (item.status === 'REJECT') {
              const rRealisasiId = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0].id : null;
              const rNilai = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0].nilai_realisasi_keuangan : '';
              const rKet = (item.realisasis && item.realisasis.length > 0) ? (item.realisasis[0].keterangan || '') : '';

              actionBtn = `<button class="btn btn-sm btn-outline-danger" onclick="bukaModalEditRealisasi(${rRealisasiId}, '${item.nama_program}', ${item.sisa_pagu}, ${rNilai}, '${rKet}')"><i class="fas fa-edit"></i> Perbaiki</button>`;
            } else if (item.sisa_pagu <= 0) {
              actionBtn = `<span class="badge bg-success"><i class="fas fa-check-double"></i> Pagu Habis</span>`;
            } else {
              actionBtn = `<button class="btn btn-sm btn-outline-primary" onclick="bukaModalRealisasi(${item.id}, '${item.nama_program}', ${item.sisa_pagu})"><i class="fas fa-plus"></i> Tambah Realisasi</button>`;
            }

            tableRows += `
          <tr>
            <td class="small">${item.kode_program}</td>
            <td class="small fw-semibold">${item.nama_program.substring(0, 40)}</td>
            <td class="small">${item.sumber_dana}</td>
            <td class="small">${item.realisasi_fisik}%</td>
            <td class="small">Rp ${formatRupiah(item.realisasi_keuangan)}</td>
            <td class="small">Rp ${formatRupiah(item.sisa_pagu)}</td>
            <td class="small">${statusBadge}</td>
            <td class="small">${actionBtn}</td>
          </tr>
        `;
          });

          const modalLaporan = document.createElement('div');
          modalLaporan.className = 'modal fade';
          modalLaporan.id = 'laporanModalInstance';
          modalLaporan.innerHTML = `
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
          <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white;">
              <h5 class="modal-title"><i class="fas fa-chart-line me-2"></i>Laporan Realisasi RFK</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table table-sm table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Kode</th><th>Program</th><th>Sumber Dana</th><th>Fisik %</th>
                      <th>Realisasi (Rp)</th><th>Sisa Pagu (Rp)</th><th>Status</th><th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>${tableRows}</tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      `;
          document.body.appendChild(modalLaporan);
          const modalInstance = new bootstrap.Modal(modalLaporan);
          modalInstance.show();
          modalLaporan.addEventListener('hidden.bs.modal', () => modalLaporan.remove());
        } else {
          showToast('Belum ada data RFK. Silakan input terlebih dahulu.', 'info');
        }
      } catch (error) {
        showToast('Gagal memuat laporan', 'error');
      }
    });

    function getStatusBadge(status) {
      const badges = {
        'PENDING': '<span class="status-badge-pending"><i class="fas fa-clock me-1"></i>PENDING</span>',
        'APPROVE': '<span class="status-badge-approve"><i class="fas fa-check me-1"></i>APPROVE</span>',
        'REJECT': '<span class="status-badge-reject"><i class="fas fa-times me-1"></i>REJECT</span>'
      };
      return badges[status] || badges['PENDING'];
    }

    function showToast(msg, type = 'success') {
      const toastEl = document.getElementById('liveToast');
      const toastMsg = document.getElementById('toastMessage');
      toastMsg.innerText = msg;
      toastEl.style.display = 'flex';
      setTimeout(() => { toastEl.style.display = 'none'; }, 3000);
    }

    window.closeToast = function () {
      document.getElementById('liveToast').style.display = 'none';
    };

    // ============ TAMBAH REALISASI (BERTAHAP) ============
    window.bukaModalRealisasi = function (id, nama, sisaPagu) {
      // Tutup modal laporan jika ada
      const laporanModal = bootstrap.Modal.getInstance(document.getElementById('laporanModalInstance'));
      if (laporanModal) laporanModal.hide();

      document.getElementById('tr_rfk_id').value = id;
      document.getElementById('tr_nama_program').value = nama;
      document.getElementById('tr_sisa_pagu_display').value = 'Rp ' + formatRupiah(sisaPagu);
      document.getElementById('tr_sisa_pagu').value = sisaPagu;
      document.getElementById('tr_nilai').value = '';
      document.getElementById('tr_keterangan').value = '';
      document.getElementById('tr_warning').style.display = 'none';

      const modal = new bootstrap.Modal(document.getElementById('tambahRealisasiModal'));
      modal.show();
    };

    document.getElementById('tr_nilai').addEventListener('input', function () {
      const sisaPagu = parseFloat(document.getElementById('tr_sisa_pagu').value) || 0;
      const nilai = parseRupiahStr(this.value);
      if (nilai > sisaPagu) {
        document.getElementById('tr_warning').style.display = 'block';
      } else {
        document.getElementById('tr_warning').style.display = 'none';
      }
    });

    const formTambahRealisasi = document.getElementById('formTambahRealisasi');
    formTambahRealisasi.addEventListener('submit', async (e) => {
      e.preventDefault();

      const id = document.getElementById('tr_rfk_id').value;
      const sisaPagu = parseFloat(document.getElementById('tr_sisa_pagu').value) || 0;
      const nilai = parseRupiahStr(document.getElementById('tr_nilai').value);

      if (nilai <= 0 || nilai > sisaPagu) {
        showToast('Nilai realisasi tidak valid atau melebihi sisa pagu', 'error');
        return;
      }

      const formData = {
        nilai_realisasi_keuangan: nilai,
        keterangan: document.getElementById('tr_keterangan').value
      };

      try {
        const response = await fetch(`/dashboard/rfk/${id}/realisasi`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('tambahRealisasiModal'));
          modal.hide();
          formTambahRealisasi.reset();
          showToast(result.message, 'success');
          loadDashboardData();
        } else {
          showToast(result.message, 'error');
        }
      } catch (error) {
        showToast('Terjadi kesalahan saat mengajukan realisasi', 'error');
      }
    });

    // ============ EDIT REALISASI (DITOLAK) ============
    window.bukaModalEditRealisasi = function (realisasiId, nama, sisaPagu, nilaiLama, ketLama) {
      if (!realisasiId) {
        showToast('Data realisasi tidak ditemukan.', 'error');
        return;
      }
      const laporanModal = bootstrap.Modal.getInstance(document.getElementById('laporanModalInstance'));
      if (laporanModal) laporanModal.hide();

      document.getElementById('er_realisasi_id').value = realisasiId;
      document.getElementById('er_nama_program').value = nama;
      document.getElementById('er_sisa_pagu_display').value = 'Rp ' + formatRupiah(sisaPagu);
      document.getElementById('er_sisa_pagu').value = sisaPagu;
      document.getElementById('er_nilai').value = nilaiLama ? formatRupiah(nilaiLama) : '';
      document.getElementById('er_keterangan').value = ketLama || '';
      document.getElementById('er_warning').style.display = 'none';

      const modal = new bootstrap.Modal(document.getElementById('editRealisasiModal'));
      modal.show();
    };

    document.getElementById('er_nilai').addEventListener('input', function () {
      const sisaPagu = parseFloat(document.getElementById('er_sisa_pagu').value) || 0;
      const nilai = parseRupiahStr(this.value);
      if (nilai > sisaPagu) {
        document.getElementById('er_warning').style.display = 'block';
      } else {
        document.getElementById('er_warning').style.display = 'none';
      }
    });

    const formEditRealisasi = document.getElementById('formEditRealisasi');
    formEditRealisasi.addEventListener('submit', async (e) => {
      e.preventDefault();

      const id = document.getElementById('er_realisasi_id').value;
      const sisaPagu = parseFloat(document.getElementById('er_sisa_pagu').value) || 0;
      const nilai = parseRupiahStr(document.getElementById('er_nilai').value);

      if (nilai <= 0 || nilai > sisaPagu) {
        showToast('Nilai realisasi tidak valid atau melebihi sisa pagu', 'error');
        return;
      }

      const formData = {
        nilai_realisasi_keuangan: nilai,
        keterangan: document.getElementById('er_keterangan').value
      };

      try {
        const response = await fetch(`/dashboard/rfk/realisasi/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('editRealisasiModal'));
          modal.hide();
          formEditRealisasi.reset();
          showToast(result.message, 'success');
          loadDashboardData();
        } else {
          showToast(result.message, 'error');
        }
      } catch (error) {
        showToast('Terjadi kesalahan saat mengajukan ulang realisasi', 'error');
      }
    });

    // ============ INIT ============
    document.addEventListener('DOMContentLoaded', () => {
      loadDashboardData();
    });

    // Dark mode toggle
    const toggleBtn = document.getElementById('toggleMode');
    toggleBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      toggleBtn.innerHTML = document.body.classList.contains('dark-mode') ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    });
  </script>
</body>

</html>