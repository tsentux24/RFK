<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Panduan SI-RAFIKA - Staff OPD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
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

    .btn-primary-gradient {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border: none;
      border-radius: 40px;
      padding: 10px 20px;
      font-weight: 600;
      color: white;
    }

    /* Tabs Styling */
    .nav-pills .nav-link {
      color: var(--text-light);
      border-radius: 10px;
      margin-bottom: 10px;
      font-weight: 500;
      padding: 12px 15px;
      background: var(--card-light);
      border: 1px solid #eaeaea;
    }

    body.dark-mode .nav-pills .nav-link {
      background: var(--card-dark);
      color: var(--text-dark);
      border-color: #333;
    }

    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
      color: #fff;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-color: transparent;
    }

    .accordion-button {
      font-weight: 600;
      background-color: var(--card-light);
      color: var(--text-light);
    }

    body.dark-mode .accordion-button {
      background-color: var(--card-dark);
      color: var(--text-dark);
    }

    .accordion-button:not(.collapsed) {
      background-color: rgba(79, 183, 179, 0.1);
      color: var(--primary);
    }

    body.dark-mode .accordion-item {
      background-color: var(--card-dark);
      border-color: #333;
    }

    body.dark-mode .accordion-body {
      color: var(--text-dark);
    }
  </style>
</head>

<body>

  <div class="app-header">
    <div class="container">
      <div class="d-flex align-items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none">
          <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <div>
          <h3 class="fw-bold mb-0">Panduan Staff OPD</h3>
          <p class="mb-0 opacity-75">SI-RAFIKA</p>
        </div>
      </div>
    </div>
  </div>

  <div class="container mb-5">
    <div class="row">
      <!-- Nav Tabs Mobile/Desktop -->
      <div class="col-md-4 mb-4">
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
          <button class="nav-link active text-start" id="v-pills-beranda-tab" data-bs-toggle="pill"
            data-bs-target="#v-pills-beranda" type="button" role="tab" aria-selected="true"><i
              class="fas fa-home me-2"></i> Beranda</button>
          <button class="nav-link text-start" id="v-pills-alur-tab" data-bs-toggle="pill" data-bs-target="#v-pills-alur"
            type="button" role="tab" aria-selected="false"><i class="fas fa-route me-2"></i> Alur Kerja</button>
          <button class="nav-link text-start" id="v-pills-menu-tab" data-bs-toggle="pill" data-bs-target="#v-pills-menu"
            type="button" role="tab" aria-selected="false"><i class="fas fa-book-open me-2"></i> Panduan Fitur</button>
          <button class="nav-link text-start" id="v-pills-faq-tab" data-bs-toggle="pill" data-bs-target="#v-pills-faq"
            type="button" role="tab" aria-selected="false"><i class="fas fa-question-circle me-2"></i> FAQ &
            Bantuan</button>
        </div>
      </div>

      <!-- Content -->
      <div class="col-md-8">
        <div class="tab-content card-custom p-4" id="v-pills-tabContent">

          <!-- BERANDA -->
          <div class="tab-pane fade show active" id="v-pills-beranda" role="tabpanel">
            <h4 class="fw-bold mb-3">Selamat Datang di Panduan SI-RAFIKA</h4>
            <p>Halo <strong>Staff OPD</strong>, tugas utama Anda di dalam sistem ini adalah mencatat rencana anggaran
              program kerja dan melaporkan progres realisasinya secara berkala (bulanan).</p>

            <div class="alert alert-info border-0 rounded-4 mt-4" style="background: rgba(79, 183, 179, 0.1);">
              <h6 class="fw-bold text-primary mb-2"><i class="fas fa-bullseye me-2"></i>Fokus Anda:</h6>
              <ul class="mb-0 ps-3">
                <li>Membuat Master Program dan menentukan Pagu Anggaran.</li>
                <li>Menginput nilai realisasi keuangan & fisik secara berkala.</li>
                <li>Memperbaiki pengajuan realisasi jika ditolak (REJECT) oleh Kepala OPD.</li>
              </ul>
            </div>
          </div>

          <!-- ALUR KERJA -->
          <div class="tab-pane fade" id="v-pills-alur" role="tabpanel">
            <h4 class="fw-bold mb-4">Alur Kerja Staff</h4>

            <div class="d-flex mb-4">
              <div class="me-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                  style="width: 40px; height: 40px; font-weight: bold;">1</div>
              </div>
              <div>
                <h6 class="fw-bold text-primary">Input Master Program</h6>
                <p class="small text-muted mb-0">Masuk ke menu Dashboard, klik "Input RFK", buat program baru dengan
                  Data Pagu Anggaran. Data ini akan berstatus <span class="badge bg-warning text-dark">PENDING</span>.
                </p>
              </div>
            </div>

            <div class="d-flex mb-4">
              <div class="me-3">
                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center"
                  style="width: 40px; height: 40px; font-weight: bold;">2</div>
              </div>
              <div>
                <h6 class="fw-bold text-info">Menunggu Approval Kepala OPD</h6>
                <p class="small text-muted mb-0">Kepala OPD akan mereview. Jika disetujui, status menjadi <span
                    class="badge bg-success">APPROVE</span>. Jika ditolak, status menjadi <span
                    class="badge bg-danger">REJECT</span> dan wajib Anda perbaiki. Apabila program telah mencapai target 100% secara keseluruhan (Fisik dan Keuangan), status otomatis menjadi <span class="badge bg-primary">SELESAI (Tuntas)</span>.</p>
              </div>
            </div>

            <div class="d-flex mb-4">
              <div class="me-3">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                  style="width: 40px; height: 40px; font-weight: bold;">3</div>
              </div>
              <div>
                <h6 class="fw-bold text-success">Tambah Realisasi Termin Berikutnya</h6>
                <p class="small text-muted mb-0">Jika program sudah APPROVE, Anda bisa menambahkan realisasi pada bulan
                  berikutnya dengan klik ikon <i class="fas fa-plus-circle text-primary"></i> di baris program yang
                  sama.</p>
              </div>
            </div>
          </div>

          <!-- PANDUAN FITUR -->
          <div class="tab-pane fade" id="v-pills-menu" role="tabpanel">
            <h4 class="fw-bold mb-4">Panduan Penggunaan Fitur</h4>

            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-tasks me-2"></i> Input Program Baru (RFK)</h6>
            <img src="{{ asset('images/panduan/sirafika_input_rfk_1784204237817.png') }}"
              class="img-fluid rounded-3 mb-3 border shadow-sm">
            <ol class="small text-muted">
              <li>Di Beranda, klik kartu <strong>Input RFK</strong>.</li>
              <li>Isi form dengan detail yang benar (Kode, Nama, Pagu).</li>
              <li>Jika ada, masukkan realisasi awal di form tersebut. Klik Simpan.</li>
              <li>Data akan berstatus <span class="badge bg-warning text-dark">PENDING</span> menunggu approval Kepala
                OPD.</li>
            </ol>

            <hr class="my-4">

            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-plus-circle me-2"></i> Cara Menambah Realisasi Bulan
              Berikutnya</h6>
            <ol class="small text-muted">
              <li>Pastikan program utama sudah berstatus <span class="badge bg-success">APPROVE</span>.</li>
              <li>Scroll ke bawah di Dashboard, cari tabel program Anda.</li>
              <li>Klik tombol biru <strong>Tambah Realisasi</strong> (ikon plus <i class="fas fa-plus-circle"></i>) pada
                baris program tersebut.</li>
              <li>Masukkan nilai tambahan realisasi uang/fisik, lalu Submit.</li>
            </ol>

            <hr class="my-4">

            <h6 class="fw-bold text-danger mb-3"><i class="fas fa-edit me-2"></i> Cara Memperbaiki Pengajuan (Ditolak)
            </h6>
            <ol class="small text-muted">
              <li>Jika status program adalah <span class="badge bg-danger">REJECT</span>.</li>
              <li>Klik tombol merah <strong>Edit Realisasi</strong> (ikon pensil <i class="fas fa-edit"></i>).</li>
              <li>Ubah nominal sesuai instruksi/catatan dari Kepala OPD, lalu Submit ulang.</li>
            </ol>

          </div>

          <!-- FAQ & KONTAK -->
          <div class="tab-pane fade" id="v-pills-faq" role="tabpanel">
            <h4 class="fw-bold mb-4">Tanya Jawab & Bantuan</h4>

            <div class="accordion accordion-flush" id="faqAccordion">
              <div class="accordion-item rounded-3 mb-2 border">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#faq1">
                    Mengapa status program saya masih PENDING?
                  </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body small text-muted">
                    Pengajuan Anda sedang dalam antrean untuk direview oleh Kepala OPD (Approver). Silakan infokan ke
                    Kepala OPD Anda untuk melakukan Approve dari akun mereka.
                  </div>
                </div>
              </div>

              <div class="accordion-item rounded-3 mb-2 border">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#faq2">
                    Bagaimana jika saya salah input pagu master?
                  </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body small text-muted">
                    Jika status masih PENDING atau REJECT, Anda masih bisa mengeditnya. Namun jika sudah APPROVE, Anda
                    harus menghubungi Administrator Pusat untuk mereset atau menyesuaikan data.
                  </div>
                </div>
              </div>

              <div class="accordion-item rounded-3 mb-4 border">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#faq3">
                    Lupa kata sandi (Password) login?
                  </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body small text-muted">
                    Silakan hubungi Administrator OPD atau Admin Pusat via menu Kontak untuk mereset kata sandi akun
                    Anda.
                  </div>
                </div>
              </div>
            </div>

            <h5 class="fw-bold mb-3 mt-4">Hubungi Dukungan (IT)</h5>
            <div class="row g-3">
              <div class="col-sm-6">
                <div class="p-3 border rounded-3 text-center bg-light">
                  <i class="fab fa-whatsapp fs-1 text-success mb-2"></i>
                  <h6 class="fw-bold mb-1">WhatsApp</h6>
                  <p class="small text-muted mb-0">+62 821-8986-0629</p>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="p-3 border rounded-3 text-center bg-light">
                  <i class="far fa-envelope fs-1 text-primary mb-2"></i>
                  <h6 class="fw-bold mb-1">Email Support</h6>
                  <p class="small text-muted mb-0">support@malutprov.go.id</p>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-nav">
    <a href="{{ route('dashboard') }}"><i class="fas fa-home footer-icon"></i><span>Beranda</span></a>
    <a href="#"><i class="fas fa-user footer-icon"></i><span>Profil</span></a>
    <a href="#" class="active"><i class="fas fa-book footer-icon"></i><span>Panduan</span></a>
    <a href="#"><i class="fas fa-cog footer-icon"></i><span>Pengaturan</span></a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Cek preferensi dark mode (Bisa diaktifkan jika perlu)
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    if (isDarkMode) {
      document.body.classList.add('dark-mode');
    }
  </script>
</body>

</html>