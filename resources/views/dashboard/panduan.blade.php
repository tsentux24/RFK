@extends('dashboard.layout.app')

@section('content')
@php
    $role = Auth::user()->role ?? 'guest';
@endphp

<style>
    .hero-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        color: white;
        padding: 60px 0;
        position: relative;
        overflow: hidden;
        border-radius: 0 0 20px 20px;
        margin-bottom: -50px;
        margin-top: -20px; /* Offset the main content padding if any */
    }
    
    .hero-pattern {
        position: absolute;
        inset: 0;
        opacity: 0.1;
        background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
    }
    
    .content-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        position: relative;
        z-index: 10;
        min-height: 600px;
    }
    
    .nav-pills-custom .nav-link {
        color: #6c757d;
        border-radius: 8px;
        padding: 12px 20px;
        margin-bottom: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        text-align: left;
    }
    
    .nav-pills-custom .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .nav-pills-custom .nav-link:hover {
        background-color: #f8f9fa;
    }
    
    .nav-pills-custom .nav-link.active {
        color: var(--primary);
        background-color: rgba(49, 50, 111, 0.1);
        border-color: rgba(49, 50, 111, 0.2);
    }
    
    .tab-pane-fade {
        animation: fadeIn 0.4s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .icon-box {
        background: rgba(255,255,255,0.2);
        padding: 30px;
        border-radius: 20px;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.3);
        display: inline-flex;
    }
    
    .step-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .accordion-button {
        font-weight: 600;
        color: #495057;
    }

    .accordion-button:not(.collapsed) {
        color: var(--primary);
        background-color: rgba(49, 50, 111, 0.1);
        box-shadow: none;
    }
</style>

<div class="container-fluid px-0 mb-5">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-pattern"></div>
        <div class="container position-relative" style="z-index: 1;">
            <div class="row align-items-center">
                <div class="col-lg-8 text-center text-lg-start mb-4 mb-lg-0">
                    <h1 class="display-5 fw-bold mb-3 text-white">Panduan Digital Interaktif</h1>
                    <h3 class="h4 fw-normal text-white-50 mb-4">Sistem Informasi Rekapitulasi Fisik dan Keuangan</h3>
                    <p class="lead mb-4 text-white opacity-75">
                        Panduan khusus untuk hak akses <strong>{{ strtoupper(str_replace('_', ' ', $role)) }}</strong>. Pelajari fitur dan alur kerja yang dirancang khusus untuk peran Anda dalam aplikasi SI-RAFIKA.
                    </p>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-center">
                    <div class="icon-box shadow-lg">
                        @if($role == 'staff')
                            <i class="fas fa-keyboard fa-5x text-white opacity-75"></i>
                        @elseif($role == 'kepala_opd')
                            <i class="fas fa-check-double fa-5x text-white opacity-75"></i>
                        @else
                            <i class="fas fa-cogs fa-5x text-white opacity-75"></i>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="container">
        <div class="content-card p-0">
            <div class="row g-0">
                <!-- Sidebar Nav -->
                <div class="col-md-4 col-lg-3 bg-light border-end p-4" style="border-top-left-radius: 16px; border-bottom-left-radius: 16px;">
                    <div class="nav flex-column nav-pills-custom" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link w-100 active text-start" id="v-pills-beranda-tab" data-bs-toggle="pill" data-bs-target="#v-pills-beranda" type="button" role="tab" aria-selected="true">
                            <i class="fas fa-home"></i> Beranda
                        </button>
                        <button class="nav-link w-100 text-start" id="v-pills-pengenalan-tab" data-bs-toggle="pill" data-bs-target="#v-pills-pengenalan" type="button" role="tab" aria-selected="false">
                            <i class="fas fa-info-circle"></i> Pengenalan
                        </button>
                        <button class="nav-link w-100 text-start" id="v-pills-alur-tab" data-bs-toggle="pill" data-bs-target="#v-pills-alur" type="button" role="tab" aria-selected="false">
                            <i class="fas fa-route"></i> Alur Penggunaan
                        </button>
                        <button class="nav-link w-100 text-start" id="v-pills-menu-tab" data-bs-toggle="pill" data-bs-target="#v-pills-menu" type="button" role="tab" aria-selected="false">
                            <i class="fas fa-book-open"></i> Panduan per Menu
                        </button>
                        <button class="nav-link w-100 text-start" id="v-pills-faq-tab" data-bs-toggle="pill" data-bs-target="#v-pills-faq" type="button" role="tab" aria-selected="false">
                            <i class="fas fa-question-circle"></i> FAQ
                        </button>
                        <button class="nav-link w-100 text-start" id="v-pills-kontak-tab" data-bs-toggle="pill" data-bs-target="#v-pills-kontak" type="button" role="tab" aria-selected="false">
                            <i class="fas fa-headset"></i> Kontak Admin
                        </button>
                    </div>
                </div>
                
                <!-- Content Area -->
                <div class="col-md-8 col-lg-9 p-4 p-lg-5">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- BERANDA -->
                        <div class="tab-pane fade show active tab-pane-fade" id="v-pills-beranda" role="tabpanel">
                            <h3 class="fw-bold mb-4 border-bottom pb-3 text-dark">Selamat Datang di Panduan SI-RAFIKA</h3>
                            
                            @if($role == 'staff')
                                <p class="lead mb-4 text-secondary">
                                    Halo <strong>Staff OPD</strong>, tugas utama Anda di dalam sistem ini adalah mencatat rencana anggaran program kerja dan melaporkan progres realisasinya secara berkala (bulanan).
                                </p>
                                <div class="alert alert-info border-0 shadow-sm" style="background-color: #e8f4fd;">
                                    <h5 class="fw-bold text-primary mb-3"><i class="fas fa-bullseye me-2"></i> Fokus Anda:</h5>
                                    <ul class="mb-0 text-dark">
                                        <li class="mb-2">Membuat Master Program dan menentukan Pagu Anggaran.</li>
                                        <li class="mb-2">Menginput nilai realisasi keuangan & fisik secara berkala.</li>
                                        <li>Memperbaiki pengajuan realisasi jika ditolak (REJECT) oleh Kepala OPD.</li>
                                    </ul>
                                </div>
                            @elseif($role == 'kepala_opd')
                                <p class="lead mb-4 text-secondary">
                                    Halo <strong>Kepala OPD</strong>, tugas utama Anda adalah memantau serapan anggaran instansi dan meninjau keabsahan progres yang diajukan oleh Staff Anda.
                                </p>
                                <div class="alert alert-success border-0 shadow-sm" style="background-color: #e8f8f5;">
                                    <h5 class="fw-bold text-success mb-3"><i class="fas fa-search me-2"></i> Fokus Anda:</h5>
                                    <ul class="mb-0 text-dark">
                                        <li class="mb-2">Memonitor analitik Dashboard OPD (Traffic Light Status).</li>
                                        <li class="mb-2">Mengaudit laporan realisasi dari Staff (Approve atau Reject).</li>
                                        <li>Mencetak laporan pencapaian OPD.</li>
                                    </ul>
                                </div>
                            @else
                                <p class="lead mb-4 text-secondary">
                                    Halo <strong>Administrator</strong>, Anda memiliki hak penuh untuk mengelola master data sistem, mengontrol akses pengguna, dan memantau progres seluruh OPD.
                                </p>
                                <div class="alert alert-warning border-0 shadow-sm" style="background-color: #fff8e1;">
                                    <h5 class="fw-bold text-warning mb-3" style="color: #d35400 !important;"><i class="fas fa-cogs me-2"></i> Fokus Anda:</h5>
                                    <ul class="mb-0 text-dark">
                                        <li class="mb-2">Manajemen Data OPD & Manajemen Pengguna (Users).</li>
                                        <li class="mb-2">Monitoring Dashboard Global (Peringkat OPD, Kepatuhan).</li>
                                        <li>Analitik mendalam & Validasi data keseluruhan.</li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- PENGENALAN -->
                        <div class="tab-pane fade tab-pane-fade" id="v-pills-pengenalan" role="tabpanel">
                            <h3 class="fw-bold mb-4 border-bottom pb-3 text-dark">Pengenalan SI-RAFIKA</h3>
                            <p class="mb-4 text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
                                <strong>SI-RAFIKA</strong> (Sistem Informasi Rekapitulasi Fisik dan Keuangan) adalah platform digital terpadu yang dikembangkan untuk mempermudah Pemerintah Daerah dalam memonitor, mengevaluasi, dan merekapitulasi serapan anggaran (Keuangan) dan progres pembangunan (Fisik) di seluruh Organisasi Perangkat Daerah (OPD).
                            </p>
                            
                            <h5 class="fw-bold text-dark mt-5 mb-3">Tujuan Utama</h5>
                            <ul class="text-secondary mb-5" style="line-height: 1.8;">
                                <li class="mb-2">Meningkatkan transparansi dan akuntabilitas penggunaan anggaran daerah.</li>
                                <li class="mb-2">Mempercepat proses pelaporan progres dari level Staff hingga Pimpinan.</li>
                                <li>Menyediakan dashboard analitik <em>real-time</em> untuk pengambilan keputusan.</li>
                            </ul>
                            
                            <div class="card border-warning border-start border-5 shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="fas fa-lightbulb fa-2x text-warning me-4"></i>
                                    <p class="mb-0 text-dark">
                                        Tahukah Anda? Data yang dimasukkan ke SI-RAFIKA secara otomatis menghasilkan grafik dan peringatan <strong>Traffic Light</strong> (Merah < 70%, Kuning 70-89%, Hijau >= 90%) berdasarkan persentase serapan anggaran.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- ALUR PENGGUNAAN -->
                        <div class="tab-pane fade tab-pane-fade" id="v-pills-alur" role="tabpanel">
                            <h3 class="fw-bold mb-4 border-bottom pb-3 text-dark">Alur Kerja</h3>
                            
                            <div class="mt-5">
                                @if($role == 'staff')
                                    <div class="d-flex mb-4">
                                        <div class="step-circle bg-primary shadow">1</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-primary">Input Master Program</h5>
                                            <p class="text-secondary">Masuk ke menu Proyek, buat program baru dengan memasukkan Data Pagu Anggaran dan Informasi Program. Data ini akan berstatus <span class="badge bg-warning text-dark">PENDING</span>.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-4">
                                        <div class="step-circle bg-info shadow">2</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-info">Menunggu Approval</h5>
                                            <p class="text-secondary">Program diajukan masuk ke antrean Kepala OPD. Jika disetujui, status menjadi <span class="badge bg-success">APPROVE</span>. Jika ditolak, menjadi <span class="badge bg-danger">REJECT</span> dan wajib Anda perbaiki.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="step-circle bg-success shadow">3</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-success">Tambah Realisasi</h5>
                                            <p class="text-secondary">Jika program sudah APPROVE, Anda bisa menambahkan realisasi pada bulan berikutnya dengan klik "Tambah Realisasi" di baris program yang sama.</p>
                                        </div>
                                    </div>
                                @elseif($role == 'kepala_opd')
                                    <div class="d-flex mb-4">
                                        <div class="step-circle bg-primary shadow">1</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-primary">Terima Notifikasi Pengajuan</h5>
                                            <p class="text-secondary">Setiap kali Staff OPD Anda membuat laporan, data tersebut berstatus <span class="badge bg-warning text-dark">PENDING</span> dan masuk ke daftar Audit Anda.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-4">
                                        <div class="step-circle bg-info shadow">2</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-info">Audit RFK</h5>
                                            <p class="text-secondary">Buka menu <strong>Audit RFK</strong>. Pastikan nilai progres keuangan maupun persentase fisik sesuai dengan riil lapangan.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="step-circle bg-success shadow">3</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-success">Approve atau Reject</h5>
                                            <p class="text-secondary">Klik <span class="badge bg-success">Approve</span> jika data benar. Klik <span class="badge bg-danger">Reject</span> bila ada kesalahan, agar Staff merevisinya.</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex mb-4">
                                        <div class="step-circle bg-primary shadow">1</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-primary">Setup Master Data</h5>
                                            <p class="text-secondary">Di awal, Admin mendaftarkan data OPD (Menu Manajemen OPD) serta Akun Kepala OPD dan Staff (Menu Users).</p>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-4">
                                        <div class="step-circle bg-info shadow">2</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-info">Monitoring Global</h5>
                                            <p class="text-secondary">Biarkan Staff OPD dan Kepala OPD bertransaksi secara desentralisasi. Pantau pergerakan serapan keseluruhan melalui Dashboard.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="step-circle bg-success shadow">3</div>
                                        <div class="ms-4">
                                            <h5 class="fw-bold text-success">Maintenance & Validasi</h5>
                                            <p class="text-secondary">Gunakan fitur Laporan untuk mengekspor data keseluruhan, serta jalankan <em>Validation Engine</em> jika terdapat anomali data perhitungan matematis dari sistem.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- PANDUAN MENU -->
                        <div class="tab-pane fade tab-pane-fade" id="v-pills-menu" role="tabpanel">
                            <h3 class="fw-bold mb-4 border-bottom pb-3 text-dark">Panduan per Menu</h3>
                            
                            <div class="mb-5">
                                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-home me-2"></i> Dashboard & Analitik</h5>
                                <img src="{{ asset('images/panduan/sirafika_dashboard_1784204227842.png') }}" class="img-fluid rounded border shadow-sm mb-3">
                                <p class="text-secondary">
                                    Dashboard memberikan visualisasi data secara real-time. Anda dapat melihat ringkasan Total Pagu, serapan anggaran, dan status Traffic Light.
                                </p>
                            </div>
                            
                            @if(in_array($role, ['staff', 'superadmin', 'administrator']))
                            <hr class="my-5 text-muted">
                            <div class="mb-5">
                                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-plus-circle me-2"></i> Input Data RFK</h5>
                                <img src="{{ asset('images/panduan/sirafika_input_rfk_1784204237817.png') }}" class="img-fluid rounded border shadow-sm mb-3">
                                <ol class="text-secondary" style="line-height: 1.8;">
                                    <li>Masuk ke menu Proyek/RFK.</li>
                                    <li>Klik tombol <strong>+ Tambah Program</strong>.</li>
                                    <li>Isi form dengan detail (Kode, Nama, Pagu).</li>
                                    <li>Klik Simpan. Data akan berstatus <span class="badge bg-warning text-dark">PENDING</span>.</li>
                                </ol>
                            </div>
                            @endif

                            @if(in_array($role, ['kepala_opd', 'superadmin', 'administrator']))
                            <hr class="my-5 text-muted">
                            <div class="mb-5">
                                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-check-double me-2"></i> Audit & Approval</h5>
                                <img src="{{ asset('images/panduan/sirafika_approval_1784204247524.png') }}" class="img-fluid rounded border shadow-sm mb-3">
                                <ol class="text-secondary" style="line-height: 1.8;">
                                    <li>Masuk ke menu <strong>Audit RFK</strong>.</li>
                                    <li>Pilih pengajuan dari Staff.</li>
                                    <li>Klik <button class="btn btn-sm btn-success py-0">Approve</button> jika sesuai, atau <button class="btn btn-sm btn-danger py-0">Reject</button> jika perlu revisi.</li>
                                </ol>
                            </div>
                            @endif

                            <hr class="my-5 text-muted">
                            <div>
                                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-file-pdf me-2"></i> Cetak Laporan</h5>
                                <img src="{{ asset('images/panduan/sirafika_laporan_1784204257941.png') }}" class="img-fluid rounded border shadow-sm mb-3">
                                <p class="text-secondary">
                                    Pada menu Laporan, gunakan filter pencarian lalu klik <strong>Export to PDF</strong> untuk mencetak dokumen.
                                </p>
                            </div>
                        </div>

                        <!-- FAQ -->
                        <div class="tab-pane fade tab-pane-fade" id="v-pills-faq" role="tabpanel">
                            <h3 class="fw-bold mb-4 border-bottom pb-3 text-dark">FAQ & Bantuan</h3>
                            
                            <div class="accordion" id="faqAccordion">
                                @if($role == 'staff')
                                <div class="accordion-item mb-3 border rounded shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                            Mengapa status program masih PENDING?
                                        </button>
                                    </h2>
                                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-secondary">
                                            Sedang dalam antrean review Kepala OPD.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item mb-3 border rounded shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                            Bagaimana jika ditolak (REJECT)?
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-secondary">
                                            Klik tombol Edit pada baris program, sesuaikan nilainya, dan simpan ulang.
                                        </div>
                                    </div>
                                </div>
                                @elseif($role == 'kepala_opd')
                                <div class="accordion-item mb-3 border rounded shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                            Bisa membatalkan Approval?
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-secondary">
                                            Tidak bisa langsung. Harus melalui Administrator Pusat.
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="accordion-item mb-3 border rounded shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                            Fungsi Validation Engine?
                                        </button>
                                    </h2>
                                    <div id="faq4" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-secondary">
                                            Memindai anomali / selisih perhitungan matematis antara detail realisasi dan Pagu Master.
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="accordion-item border rounded shadow-sm">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                            Lupa Password?
                                        </button>
                                    </h2>
                                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-secondary">
                                            Hubungi Admin Pusat via menu Kontak.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KONTAK -->
                        <div class="tab-pane fade tab-pane-fade" id="v-pills-kontak" role="tabpanel">
                            <h3 class="fw-bold mb-4 border-bottom pb-3 text-dark">Kontak Admin Pusat</h3>
                            <p class="text-secondary mb-5">
                                Jika butuh bantuan teknis, hubungi:
                            </p>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card shadow-sm border-0 bg-light text-center p-4">
                                        <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                                        <h5 class="fw-bold text-dark">WhatsApp</h5>
                                        <p class="mb-0 text-success fw-bold">+62 812-3456-7890</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card shadow-sm border-0 bg-light text-center p-4">
                                        <i class="far fa-envelope fa-3x text-primary mb-3"></i>
                                        <h5 class="fw-bold text-dark">Email Support</h5>
                                        <p class="mb-0 text-primary fw-bold">support@malutprov.go.id</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ensure Bootstrap JS is loaded for Tabs/Accordion to work if not already loaded in layout -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
