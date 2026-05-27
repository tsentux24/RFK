<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>RFK (Realisasi Fisik Dan Keuangan) - Dashboard OPD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
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

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 600'%3E%3Cpath fill='%2331326F' fill-opacity='0.05' d='M200,450 L250,400 L300,450 L350,400 L400,450 L450,400 L500,450 L550,400 L600,450 L650,400 L700,450 L750,400 L800,450 L800,600 L0,600 L0,450 L50,400 L100,450 L150,400 L200,450 Z'/%3E%3Cpath fill='%2331326F' fill-opacity='0.05' d='M250,350 L300,300 L350,350 L400,300 L450,350 L500,300 L550,350 L600,300 L650,350 L700,300 L750,350 L750,450 L250,450 L250,350 Z'/%3E%3Cpath fill='%2331326F' fill-opacity='0.05' d='M300,250 L350,200 L400,250 L450,200 L500,250 L550,200 L600,250 L600,350 L300,350 L300,250 Z'/%3E%3Cpath fill='%2331326F' fill-opacity='0.05' d='M350,150 L400,100 L450,150 L500,100 L550,150 L550,250 L350,250 L350,150 Z'/%3E%3Cpath fill='%2331326F' fill-opacity='0.05' d='M375,50 L425,0 L475,50 L475,150 L375,150 L375,50 Z'/%3E%3Crect x='425' y='100' width='50' height='50' fill='%2331326F' fill-opacity='0.08'/%3E%3Crect x='325' y='200' width='50' height='50' fill='%2331326F' fill-opacity='0.08'/%3E%3Crect x='525' y='200' width='50' height='50' fill='%2331326F' fill-opacity='0.08'/%3E%3Crect x='225' y='300' width='50' height='50' fill='%2331326F' fill-opacity='0.08'/%3E%3Crect x='625' y='300' width='50' height='50' fill='%2331326F' fill-opacity='0.08'/%3E%3C/svg%3E");
      background-size: cover;
      background-position: center bottom;
      background-repeat: no-repeat;
      opacity: 0.4;
      z-index: -1;
      pointer-events: none;
    }

    body.dark-mode::before {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 600'%3E%3Cpath fill='%234FB7B3' fill-opacity='0.08' d='M200,450 L250,400 L300,450 L350,400 L400,450 L450,400 L500,450 L550,400 L600,450 L650,400 L700,450 L750,400 L800,450 L800,600 L0,600 L0,450 L50,400 L100,450 L150,400 L200,450 Z'/%3E%3Cpath fill='%234FB7B3' fill-opacity='0.08' d='M250,350 L300,300 L350,350 L400,300 L450,350 L500,300 L550,350 L600,300 L650,350 L700,300 L750,350 L750,450 L250,450 L250,350 Z'/%3E%3Cpath fill='%234FB7B3' fill-opacity='0.08' d='M300,250 L350,200 L400,250 L450,200 L500,250 L550,200 L600,250 L600,350 L300,350 L300,250 Z'/%3E%3Cpath fill='%234FB7B3' fill-opacity='0.08' d='M350,150 L400,100 L450,150 L500,100 L550,150 L550,250 L350,250 L350,150 Z'/%3E%3Cpath fill='%234FB7B3' fill-opacity='0.08' d='M375,50 L425,0 L475,50 L475,150 L375,150 L375,50 Z'/%3E%3Crect x='425' y='100' width='50' height='50' fill='%234FB7B3' fill-opacity='0.1'/%3E%3Crect x='325' y='200' width='50' height='50' fill='%234FB7B3' fill-opacity='0.1'/%3E%3Crect x='525' y='200' width='50' height='50' fill='%234FB7B3' fill-opacity='0.1'/%3E%3Crect x='225' y='300' width='50' height='50' fill='%234FB7B3' fill-opacity='0.1'/%3E%3Crect x='625' y='300' width='50' height='50' fill='%234FB7B3' fill-opacity='0.1'/%3E%3C/svg%3E");
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
      position: relative;
      z-index: 1;
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
      opacity: 0;
      transform: translateY(30px);
      animation: fadeSlideUp 0.8s forwards;
      position: relative;
      z-index: 1;
      backdrop-filter: blur(5px);
      background-color: rgba(255, 255, 255, 0.9);
    }

    body.dark-mode .card-custom {
      background: rgba(30, 30, 30, 0.9);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    @keyframes fadeSlideUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .stagger-1 { animation-delay: 0.1s; }
    .stagger-2 { animation-delay: 0.2s; }
    .stagger-3 { animation-delay: 0.3s; }
    .stagger-4 { animation-delay: 0.4s; }
    .stagger-5 { animation-delay: 0.5s; }
    .stagger-6 { animation-delay: 0.6s; }
    .stagger-7 { animation-delay: 0.7s; }

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

    body.dark-mode .progress {
      background-color: #2d2d2d;
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
      animation: pulseIcon 2.5s infinite ease-in-out;
    }

    .menu-card:hover .menu-icon {
      transform: scale(1.1);
      box-shadow: 0 0 20px rgba(49, 50, 111, 0.5);
    }

    .menu-card p {
      font-weight: 500;
      margin-bottom: 0;
    }

    @keyframes pulseIcon {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
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
      box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.4s;
      z-index: 1000;
    }

    body.dark-mode .footer-nav {
      background: var(--card-dark);
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.2);
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

    .footer-nav a:hover, .footer-nav a.active {
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

    .mode-toggle:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: rotate(30deg);
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
      background: var(--card-light);
      border: none;
    }
    body.dark-mode .modal-content {
      background: var(--card-dark);
      color: var(--text-dark);
    }
    .form-control, .form-select {
      border-radius: 12px;
      border: 1px solid #dee2e6;
      padding: 10px 14px;
      transition: all 0.2s;
    }
    body.dark-mode .form-control, body.dark-mode .form-select {
      background-color: #2c2c2c;
      border-color: #444;
      color: #f1f1f1;
    }
    .form-control:focus, .form-select:focus {
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
      background: linear-gradient(135deg, #2a2b60, #3fa09c);
    }
    .input-group-text-custom {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      border: none;
      border-radius: 12px 0 0 12px;
    }
    @media (max-width: 576px) {
      .summary-card p { font-size: 1.3rem; }
      .menu-card { padding: 15px 5px; }
      .menu-icon { width: 45px; height: 45px; line-height: 45px; font-size: 20px; }
      .logo-img { height: 30px; }
      .app-header h2 { font-size: 1.3rem; }
    }
    .toast-notif {
      position: fixed;
      bottom: 80px;
      right: 16px;
      z-index: 1100;
      min-width: 240px;
      background: var(--card-light);
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      border-left: 5px solid var(--secondary);
    }
    .info-text {
      font-size: 0.7rem;
      color: #6c757d;
      margin-top: 4px;
    }
    .badge-auto {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      font-size: 0.7rem;
      padding: 4px 8px;
      border-radius: 20px;
    }
  </style>
</head>
<body>

<div class="app-header">
  <div class="container">
    <div class="logo-container">
      <img src="https://e-rekrutmen.malutprov.go.id/assets/images/malut.png" alt="Logo Pemerintah Provinsi Maluku Utara" class="logo-img">
      <div>
        <h2 class="fw-bold mb-0">RFK (Realisasi Fisik Dan Keuangan)</h2>
        <p class="mb-0 opacity-75">Biro Administrasi Pembangunan Setda Provinsi Maluku Utara</p>
      </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2">
      <p class="mb-0">Selamat datang, OPD!</p>
      <button id="toggleMode" class="mode-toggle"><i class="fas fa-moon"></i></button>
    </div>
  </div>
</div>

<div class="container">
  <h5 class="section-title">Ringkasan</h5>
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card-custom summary-card stagger-1">
        <i class="fas fa-folder-open summary-icon" style="color: var(--primary);"></i>
        <h6>Total Program</h6>
        <p style="color: var(--primary);" id="totalProgramCount">0</p>
        <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-custom summary-card stagger-2">
        <i class="fas fa-chart-line summary-icon" style="color: var(--secondary);"></i>
        <h6>TOTAL PAGU (Rp)</h6>
        <p style="color: var(--secondary);" id="totalKontrakDisplay">0</p>
        <div class="progress"><div class="progress-bar" style="width: 60%"></div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-custom summary-card stagger-3">
        <i class="fas fa-spinner summary-icon" style="color: var(--accent);"></i>
        <h6>Progress Berjalan</h6>
        <p style="color: var(--accent);" id="progressBerjalan">0</p>
        <div class="progress"><div class="progress-bar" style="width: 32%"></div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card-custom summary-card stagger-4">
        <i class="fas fa-exclamation-triangle summary-icon" style="color: var(--warning);"></i>
        <h6>Terlambat</h6>
        <p style="color: var(--warning);" id="terlambatCount">0</p>
        <div class="progress"><div class="progress-bar" style="width: 8%"></div></div>
      </div>
    </div>
  </div>

  <div class="card-custom p-3 mb-4 stagger-5">
    <div class="stats-highlight">
      <div class="row text-center">
        <div class="col-4"><h4 class="mb-0" style="color: var(--primary);" id="avgFisik">0%</h4><small>Rata-rata Fisik</small></div>
        <div class="col-4"><h4 class="mb-0" style="color: var(--secondary);" id="avgKeuanganPersen">0%</h4><small>Rata-rata Realisasi Keuangan</small></div>
        <div class="col-4"><h4 class="mb-0" style="color: var(--accent);" id="totalSisaPag">Rp 0</h4><small>Total Sisa Pagu</small></div>
      </div>
    </div>
  </div>

  <h5 class="section-title">Progress Fisik Program</h5>
  <div class="card-custom p-3 mb-4 stagger-5">
    <div class="mini-chart" id="dynamicChart"></div>
    <div class="d-flex justify-content-between mt-2"><small>Program terbaru</small><small>Realisasi Fisik %</small></div>
  </div>

  <h5 class="section-title">Menu Pilihan</h5>
  <div class="row g-3 mb-5">
    <div class="col-6 col-md-4">
      <div class="card-custom menu-card stagger-6" data-bs-toggle="modal" data-bs-target="#inputRFKModal">
        <div class="menu-icon pulse-2"><i class="fas fa-tasks"></i></div>
        <p>Input RFK</p>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="card-custom menu-card stagger-7" id="laporanSayaBtn">
        <div class="menu-icon pulse-3"><i class="fas fa-chart-bar"></i></div>
        <p>Laporan Saya</p>
      </div>
    </div>
  </div>
</div>

<div class="footer-nav">
  <a href="#" class="active"><i class="fas fa-home footer-icon"></i><span>Beranda</span></a>
  <a href="#"><i class="fas fa-user footer-icon"></i><span>Profil</span></a>
  <a href="#"><i class="fas fa-bell footer-icon"></i><span>Notifikasi</span></a>
  <a href="#"><i class="fas fa-cog footer-icon"></i><span>Pengaturan</span></a>
</div>

<!-- MODAL INPUT RFK - Realisasi Fisik Otomatis dari (Realisasi Keuangan / PAGU) * 100% -->
<div class="modal fade" id="inputRFKModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="inputRFKModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="inputRFKModalLabel"><i class="fas fa-pen-ruler me-2" style="color: var(--secondary);"></i>Form Input Realisasi RFK</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body pt-3">
        <form id="rfkForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i>Nama Program <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="namaProgram" placeholder="Contoh: Peningkatan Jalan Daerah" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-barcode me-1"></i>Kode Program <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="kodeProgram" placeholder="Contoh: PRG-001" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-coins me-1"></i>Sumber Dana</label>
              <select class="form-select" id="sumberDana">
                <option value="DAU">DAU (Dana Alokasi Umum)</option>
                <option value="DAK">DAK (Dana Alokasi Khusus)</option>
                <option value="DBH">DBH (Dana Bagi Hasil)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Tahun Anggaran</label>
              <input type="number" class="form-control" id="tahunAnggaran" placeholder="2025" value="2025" required>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-semibold"><i class="fas fa-money-bill-wave me-1"></i>PAGU (Rp) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text input-group-text-custom">Rp</span>
                <input type="number" class="form-control" id="pagu" placeholder="Total Pagu dalam Rupiah" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-chart-line me-1"></i>Realisasi Keuangan (Rp) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text input-group-text-custom">Rp</span>
                <input type="number" class="form-control" id="realKeuanganRupiah" placeholder="Sudah direalisasikan (Rp)" required>
              </div>
              <div class="info-text">Masukkan nominal realisasi keuangan dalam Rupiah</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold"><i class="fas fa-percent me-1"></i>Realisasi Fisik (%) <i class="fas fa-magic fa-xs text-muted ms-1" title="Dihitung otomatis dari Realisasi Keuangan / PAGU"></i></label>
              <div class="input-group">
                <span class="input-group-text input-group-text-custom"><i class="fas fa-calculator"></i></span>
                <input type="text" class="form-control bg-light" id="realFisikOtomatis" readonly placeholder="Terisi otomatis">
                <span class="input-group-text">%</span>
              </div>
              <p><div class="info-text"><span class="badge-auto">Otomatis</span> = (Realisasi Keuangan ÷ PAGU) × 100%</div></p>
            </div>
            <div class="col-md-12">
              <label class="form-label fw-semibold"><i class="fas fa-calculator me-1"></i>Sisa PAGU (Rp)</label>
              <div class="input-group">
                <span class="input-group-text input-group-text-custom">Rp</span>
                <input type="text" class="form-control bg-light" id="sisaPagu" readonly placeholder="Terisi otomatis">
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold"><i class="fas fa-sticky-note me-1"></i>Keterangan</label>
              <textarea class="form-control" id="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary-gradient px-4 text-white">Simpan RFK <i class="fas fa-save ms-1"></i></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="liveToast" class="toast-notif p-3" style="display: none;">
  <div class="d-flex align-items-center">
    <i class="fas fa-check-circle me-2 fs-4" style="color: var(--secondary);"></i>
    <div class="fw-semibold" id="toastMessage">Data tersimpan</div>
    <button type="button" class="btn-close ms-auto" onclick="closeToast()"></button>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let rfkDataList = [];

  const totalProgramSpan = document.getElementById('totalProgramCount');
  const progressBerjalanSpan = document.getElementById('progressBerjalan');
  const terlambatSpan = document.getElementById('terlambatCount');
  const avgFisikSpan = document.getElementById('avgFisik');
  const avgKeuanganPersenSpan = document.getElementById('avgKeuanganPersen');
  const totalSisaPagSpan = document.getElementById('totalSisaPag');
  const totalKontrakDisplay = document.getElementById('totalKontrakDisplay');
  const chartContainer = document.getElementById('dynamicChart');

  function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
  }

  function updateDashboard() {
    const totalProgram = rfkDataList.length;
    totalProgramSpan.innerText = totalProgram;

    let totalPag = 0;
    let totalRealisasiKeuRupiah = 0;
    let totalFisikPersen = 0;
    let totalSisa = 0;
    let progressBerjalan = 0;
    let terlambat = 0;

    rfkDataList.forEach(item => {
      totalPag += item.pagu;
      totalRealisasiKeuRupiah += item.realKeuanganRupiah;
      totalFisikPersen += item.realFisik;
      totalSisa += (item.pagu - item.realKeuanganRupiah);
      if (item.realFisik < 50) progressBerjalan++;
      if (item.realFisik < 30 && item.tahunAnggaran == new Date().getFullYear()) terlambat++;
    });

    const avgFisik = totalProgram ? (totalFisikPersen / totalProgram).toFixed(1) : 0;
    const avgKeuPersen = totalPag ? ((totalRealisasiKeuRupiah / totalPag) * 100).toFixed(1) : 0;

    avgFisikSpan.innerText = avgFisik + '%';
    avgKeuanganPersenSpan.innerText = avgKeuPersen + '%';
    totalSisaPagSpan.innerText = 'Rp ' + formatRupiah(totalSisa);
    totalKontrakDisplay.innerText = 'Rp ' + formatRupiah(totalPag);
    progressBerjalanSpan.innerText = progressBerjalan;
    terlambatSpan.innerText = terlambat;

    const lastPrograms = [...rfkDataList].slice(-7);
    chartContainer.innerHTML = '';
    if(lastPrograms.length === 0) {
      for(let i=0;i<7;i++) {
        const bar = document.createElement('div');
        bar.className = 'chart-bar';
        bar.style.height = '20%';
        chartContainer.appendChild(bar);
      }
    } else {
      lastPrograms.forEach(prog => {
        const bar = document.createElement('div');
        bar.className = 'chart-bar';
        let tinggi = Math.min(95, Math.max(5, prog.realFisik || 0));
        bar.style.height = tinggi + '%';
        bar.setAttribute('title', `${prog.namaProgram} : ${prog.realFisik}%`);
        chartContainer.appendChild(bar);
      });
    }
  }

  // Auto hitung Realisasi Fisik (%) dan Sisa Pagu (Rp)
  const paguInput = document.getElementById('pagu');
  const realKeuRupiahInput = document.getElementById('realKeuanganRupiah');
  const sisaPaguField = document.getElementById('sisaPagu');
  const realFisikOtomatisField = document.getElementById('realFisikOtomatis');

  function hitungOtomatis() {
    const pagu = parseFloat(paguInput.value) || 0;
    const realKeu = parseFloat(realKeuRupiahInput.value) || 0;

    // Hitung Sisa Pagu
    const sisa = pagu - realKeu;
    sisaPaguField.value = sisa >= 0 ? formatRupiah(sisa) : '0';
    if(sisa < 0) sisaPaguField.value = formatRupiah(0);

    // Hitung Realisasi Fisik (%) = (Realisasi Keuangan / PAGU) * 100
    let fisikPersen = 0;
    if(pagu > 0 && realKeu > 0) {
      fisikPersen = (realKeu / pagu) * 100;
      if(fisikPersen > 100) fisikPersen = 100;
      realFisikOtomatisField.value = fisikPersen.toFixed(2);
    } else if(pagu > 0 && realKeu === 0) {
      realFisikOtomatisField.value = '0';
    } else if(pagu === 0) {
      realFisikOtomatisField.value = '0';
    } else {
      realFisikOtomatisField.value = '0';
    }
  }

  paguInput.addEventListener('input', hitungOtomatis);
  realKeuRupiahInput.addEventListener('input', hitungOtomatis);

  const formRFK = document.getElementById('rfkForm');
  formRFK.addEventListener('submit', (e) => {
    e.preventDefault();
    const namaProgram = document.getElementById('namaProgram').value.trim();
    const kodeProgram = document.getElementById('kodeProgram').value.trim();
    const sumberDana = document.getElementById('sumberDana').value;
    const tahunAnggaran = parseInt(document.getElementById('tahunAnggaran').value);
    const pagu = parseFloat(document.getElementById('pagu').value);
    const realKeuanganRupiah = parseFloat(document.getElementById('realKeuanganRupiah').value);
    const realFisik = parseFloat(realFisikOtomatisField.value) || 0;
    const keterangan = document.getElementById('keterangan').value;

    if(!namaProgram || !kodeProgram || isNaN(pagu) || pagu <= 0) {
      showToast('Isi Nama Program, Kode Program, dan Pagu (minimal 1 Rupiah)!', 'warning');
      return;
    }
    if(isNaN(realKeuanganRupiah) || realKeuanganRupiah < 0) {
      showToast('Realisasi Keuangan harus diisi nominal Rupiah yang valid', 'warning');
      return;
    }
    if(realKeuanganRupiah > pagu) {
      showToast('Realisasi Keuangan tidak boleh melebihi Pagu!', 'error');
      return;
    }

    const newRFK = {
      id: Date.now(),
      namaProgram, kodeProgram, sumberDana, tahunAnggaran, pagu,
      realKeuanganRupiah, realFisik, keterangan,
      sisaPagu: pagu - realKeuanganRupiah
    };
    rfkDataList.push(newRFK);
    updateDashboard();
    formRFK.reset();
    sisaPaguField.value = '';
    realFisikOtomatisField.value = '';
    const modal = bootstrap.Modal.getInstance(document.getElementById('inputRFKModal'));
    modal.hide();
    showToast(`Program "${namaProgram}" berhasil ditambahkan (Fisik: ${realFisik}%)`, 'success');
  });

  function showToast(msg, type='success') {
    const toastEl = document.getElementById('liveToast');
    const toastMsg = document.getElementById('toastMessage');
    toastMsg.innerText = msg;
    toastEl.style.display = 'flex';
    setTimeout(() => { toastEl.style.display = 'none'; }, 2800);
  }
  window.closeToast = function() { document.getElementById('liveToast').style.display = 'none'; };

  const laporanBtn = document.getElementById('laporanSayaBtn');
  laporanBtn.addEventListener('click', () => {
    if(rfkDataList.length === 0) {
      showToast('Belum ada data RFK. Silakan input terlebih dahulu.', 'info');
      return;
    }
    let tableRows = '';
    rfkDataList.forEach(item => {
      const sisa = item.pagu - item.realKeuanganRupiah;
      tableRows += `<tr>
        <td class="small">${item.kodeProgram}</td>
        <td class="small fw-semibold">${item.namaProgram.substring(0,30)}</td>
        <td class="small">${item.sumberDana}</td>
        <td class="small">${item.realFisik}%</td>
        <td class="small">Rp ${formatRupiah(item.realKeuanganRupiah)}</td>
        <td class="small">Rp ${formatRupiah(sisa)}</td>
      </tr>`;
    });
    const modalLaporan = document.createElement('div');
    modalLaporan.className = 'modal fade';
    modalLaporan.innerHTML = `
      <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
            <h5 class="modal-title"><i class="fas fa-chart-line me-2"></i>Laporan Realisasi RFK</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-sm table-hover">
                <thead class="table-light"><tr><th>Kode</th><th>Program</th><th>Sumber Dana</th><th>Fisik %</th><th>Realisasi Keuangan (Rp)</th><th>Sisa Pagu (Rp)</th></tr></thead>
                <tbody>${tableRows}</tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
        </div>
      </div>
    `;
    document.body.appendChild(modalLaporan);
    const modalInstance = new bootstrap.Modal(modalLaporan);
    modalInstance.show();
    modalLaporan.addEventListener('hidden.bs.modal', () => modalLaporan.remove());
  });

  function seedDummyData() {
    if(rfkDataList.length === 0) {
      rfkDataList.push({
        id: 1, namaProgram: 'Peningkatan Infrastruktur Jalan', kodeProgram: 'PRG-101', sumberDana: 'DAU', tahunAnggaran: 2025,
        pagu: 5000000000, realKeuanganRupiah: 3425000000, realFisik: 68.5, keterangan: 'Progress baik', sisaPagu: 1575000000
      });
      rfkDataList.push({
        id: 2, namaProgram: 'Rehab Gedung Sekolah', kodeProgram: 'PRG-202', sumberDana: 'DAK', tahunAnggaran: 2025,
        pagu: 3200000000, realKeuanganRupiah: 1440000000, realFisik: 45, keterangan: 'Tahap konstruksi', sisaPagu: 1760000000
      });
      rfkDataList.push({
        id: 3, namaProgram: 'Pengadaan Alat Kesehatan', kodeProgram: 'PRG-303', sumberDana: 'DBH', tahunAnggaran: 2025,
        pagu: 1800000000, realKeuanganRupiah: 1620000000, realFisik: 90, keterangan: 'Hampir selesai', sisaPagu: 180000000
      });
    }
    updateDashboard();
  }
  seedDummyData();

  const toggleBtn = document.getElementById('toggleMode');
  toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    toggleBtn.innerHTML = document.body.classList.contains('dark-mode') ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
  });
</script>
</body>
</html>
