

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

    function closeModalSafe(modalId) {
      const el = document.getElementById(modalId);
      if (!el) return;
      let modal = bootstrap.Modal.getInstance(el);
      if (!modal) {
        modal = new bootstrap.Modal(el);
      }
      modal.hide();
      // Remove backdrop just in case
      document.querySelectorAll('.modal-backdrop').forEach(bd => bd.remove());
      document.body.classList.remove('modal-open');
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    }

    // ============ DATA CONSTANTS UNTUK DROPDOWNS ============
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

    const apbnSumberDana = [
      { value: 'DAU', label: 'DAU (Dana Alokasi Umum)' },
      { value: 'DAK', label: 'DAK (Dana Alokasi Khusus)' },
      { value: 'DBH', label: 'DBH (Dana Bagi Hasil)' },
      { value: 'DEKOM', label: 'DEKOM (Dana Dekonsentrasi)' }
    ];

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

    function formatRupiah(angka) {
      return formatRupiahManual(angka);
    }



    // ============ SUBMIT FORM ============
    const formRFK = document.getElementById('rfkForm');
    formRFK.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Kumpulkan data
      const formData = {
        sumber_dana: document.getElementById('sumberDana').value,
        tahun_anggaran: document.getElementById('tahunAnggaran').value,
        pagu: parseRupiahStr(document.getElementById('pagu').value),
        keterangan: document.getElementById('keterangan').value
      };

      // Validasi
      if (!formData.sumber_dana) {
        showToast('Pilih Sumber Dana!', 'error');
        return;
      }

      if (!formData.pagu || formData.pagu <= 0) {
        showToast('PAGU harus diisi dan lebih dari 0!', 'error');
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
          closeModalSafe('inputRFKModal');
          formRFK.reset();
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
          window.rfkListData = {};
          let tableRows = '';
          result.data.forEach(item => {
            window.rfkListData[item.id] = item;
            const statusBadge = getStatusBadge(item.status);

            let actionBtn = '';
            let deleteBtn = '';

            const rRealisasiId = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0].id : null;
            const rRealisasiStatus = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0].status : null;
            const hasApproved = item.realisasis && item.realisasis.some(r => r.status === 'APPROVE');

            if (item.status === 'PENDING') {
              actionBtn = `<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu Approval</span>`;

              if (rRealisasiId && rRealisasiStatus !== 'APPROVE') {
                deleteBtn = `<button class="btn btn-sm btn-outline-danger ms-1" onclick="hapusRealisasi(${rRealisasiId})" title="Hapus Pengajuan Realisasi ini"><i class="fas fa-trash"></i></button>`;
              }
              if (!hasApproved && !rRealisasiId) {
                deleteBtn = `<button class="btn btn-sm btn-outline-danger ms-1" onclick="hapusProgram(${item.id})" title="Hapus Program Keseluruhan"><i class="fas fa-trash"></i></button>`;
              }
            } else if (item.status === 'REJECT') {
              const rNilai = (item.realisasis && item.realisasis.length > 0) ? item.realisasis[0].nilai_realisasi_keuangan : '';
              const rKet = (item.realisasis && item.realisasis.length > 0) ? (item.realisasis[0].keterangan || '') : '';
              const rKegiatan = (item.realisasis && item.realisasis.length > 0) ? (item.realisasis[0].kegiatan || '') : '';
              const rSubKegiatan = (item.realisasis && item.realisasis.length > 0) ? (item.realisasis[0].sub_kegiatan || '') : '';

              actionBtn = `<button class="btn btn-sm btn-outline-danger" onclick="bukaModalEditRealisasiData(${item.id})"><i class="fas fa-edit"></i> Perbaiki</button>`;

              if (rRealisasiId && rRealisasiStatus !== 'APPROVE') {
                deleteBtn = `<button class="btn btn-sm btn-outline-danger ms-1" onclick="hapusRealisasi(${rRealisasiId})" title="Hapus Pengajuan Realisasi ini"><i class="fas fa-trash"></i></button>`;
              }
            } else if (item.sisa_pagu <= 0) {
              actionBtn = `<span class="badge bg-success"><i class="fas fa-check-double"></i> Pagu Habis</span>`;
            } else {
              actionBtn = `<button class="btn btn-sm btn-outline-primary" onclick="bukaModalRealisasiData(${item.id})"><i class="fas fa-plus"></i> Tambah Realisasi</button>`;
              if (!hasApproved) {
                deleteBtn = `<button class="btn btn-sm btn-outline-danger ms-1" onclick="hapusProgram(${item.id})" title="Hapus Program Keseluruhan"><i class="fas fa-trash"></i></button>`;
              }
            }

            tableRows += `
          <tr>
            <td class="small">${item.kode_program}</td>
            <td class="small fw-semibold">
                ${item.nama_program.substring(0, 40)}
            </td>
            <td class="small">${item.sumber_dana}</td>
            <td class="small">${item.realisasi_fisik}%</td>
            <td class="small">Rp ${formatRupiah(item.realisasi_keuangan)}</td>
            <td class="small">Rp ${formatRupiah(item.sisa_pagu)}</td>
            <td class="small">${statusBadge}</td>
            <td class="small text-nowrap">${actionBtn} ${deleteBtn}</td>
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

    // ============ FUNGSI HAPUS PROGRAM & REALISASI ============
    async function hapusProgram(id) {
      Swal.fire({
        title: 'Hapus Master Program?',
        text: 'Apakah Anda yakin ingin menghapus keseluruhan Master Program RFK ini? Tindakan ini tidak dapat dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const response = await fetch('/dashboard/rfk/' + id, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              }
            });
            const res = await response.json();
            if (res.success) {
              Swal.fire('Berhasil!', res.message, 'success').then(() => window.location.reload());
            } else {
              Swal.fire('Gagal!', res.message, 'error');
            }
          } catch (error) {
            Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
          }
        }
      });
    }

    async function hapusRealisasi(id) {
      Swal.fire({
        title: 'Hapus Pengajuan Realisasi?',
        text: 'Apakah Anda yakin ingin menghapus data pengajuan Laporan Realisasi ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const response = await fetch('/dashboard/rfk/realisasi/' + id, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
              }
            });
            const res = await response.json();
            if (res.success) {
              Swal.fire('Berhasil!', res.message, 'success').then(() => window.location.reload());
            } else {
              Swal.fire('Gagal!', res.message, 'error');
            }
          } catch (error) {
            Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
          }
        }
      });
    }

    function getStatusBadge(status) {
      const statusMap = {
        'PENDING': '<span class="status-badge-pending"><i class="fas fa-clock me-1"></i>PENDING</span>',
        'APPROVE': '<span class="status-badge-approve"><i class="fas fa-check me-1"></i>APPROVE</span>',
        'REJECT': '<span class="status-badge-reject"><i class="fas fa-times me-1"></i>REJECT</span>',
        'SELESAI': '<span class="status-badge-selesai"><i class="fas fa-check-double me-1"></i>SELESAI</span>'
      };
      return statusMap[status] || statusMap['PENDING'];
    }

    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });

    function showToast(msg, type = 'success') {
      Toast.fire({
        icon: type,
        title: msg
      });
    }

    // ============ TAMBAH REALISASI (BERTAHAP) ============

    function populateDropdowns(sumberDana, prefix, selectedKat = '', selectedSub = '', selectedDetail = '') {
      const apbdKategori = [
        { value: 'BELANJA_OPERASI', label: 'Belanja Operasi' },
        { value: 'BELANJA_MODAL', label: 'Belanja Modal' }
      ];
      const apbdSubOperasi = [
        { value: 'BELANJA_PEGAWAI', label: 'Belanja Pegawai' },
        { value: 'BELANJA_BARANG_JASA', label: 'Belanja Barang Dan Jasa' }
      ];
      const apbdSubModal = [
        { value: 'BELANJA_MODAL', label: 'Belanja Modal' },
        { value: 'BELANJA_MODAL_PERALATAN_MESIN', label: 'Belanja Modal Peralatan Dan Mesin' },
        { value: 'BELANJA_MODAL_JALAN_IRIGASI', label: 'Belanja Modal Jalan, Irigasi' },
        { value: 'BELANJA_MODAL_BANGUNAN_GEDUNG', label: 'Belanja Modal Bangunan Gedung' }
      ];
      const apbnSumberDana = [
        { value: 'DAU', label: 'Dana Alokasi Umum (DAU)' },
        { value: 'DAK_FISIK', label: 'DAK Fisik' },
        { value: 'DAK_NON_FISIK', label: 'DAK Non Fisik' },
        { value: 'DBH', label: 'Dana Bagi Hasil (DBH)' },
        { value: 'DEKONSENTRASI', label: 'Dekonsentrasi' },
        { value: 'TUGAS_PEMBANTUAN', label: 'Tugas Pembantuan' }
      ];

      const apbdContainer = document.getElementById(prefix + 'apbd_container');
      const apbnContainer = document.getElementById(prefix + 'apbn_container');
      const selKat = document.getElementById(prefix + 'kategori_anggaran');
      const selSub = document.getElementById(prefix + 'sub_kategori_anggaran');
      const selDetail = document.getElementById(prefix + 'sumber_dana_detail');

      apbdContainer.style.display = 'none';
      apbnContainer.style.display = 'none';

      if (sumberDana === 'APBD') {
        apbdContainer.style.display = 'flex';
        selKat.innerHTML = '<option value="">Pilih Kategori</option>';
        apbdKategori.forEach(k => {
          selKat.innerHTML += `<option value="${k.value}" ${k.value === selectedKat ? 'selected' : ''}>${k.label}</option>`;
        });

        const renderSub = (val, selected) => {
          selSub.innerHTML = '<option value="">Pilih Sub Kategori</option>';
          if (val === 'BELANJA_OPERASI') {
            apbdSubOperasi.forEach(s => selSub.innerHTML += `<option value="${s.value}" ${s.value === selected ? 'selected' : ''}>${s.label}</option>`);
          } else if (val === 'BELANJA_MODAL') {
            apbdSubModal.forEach(s => selSub.innerHTML += `<option value="${s.value}" ${s.value === selected ? 'selected' : ''}>${s.label}</option>`);
          }
        };

        renderSub(selectedKat, selectedSub);

        selKat.onchange = function () {
          renderSub(this.value, '');
        };

      } else if (sumberDana === 'APBN') {
        apbnContainer.style.display = 'block';
        selDetail.innerHTML = '<option value="">Pilih Sumber Dana Detail</option>';
        apbnSumberDana.forEach(s => {
          selDetail.innerHTML += `<option value="${s.value}" ${s.value === selectedDetail ? 'selected' : ''}>${s.label}</option>`;
        });
      }
    }

    window.bukaModalRealisasiData = function (itemId) {
      const item = window.rfkListData[itemId];
      if (!item) return;
      
      const id = item.id;
      const sumberDana = item.sumber_dana;
      const kodeProgram = item.kode_program;
      const namaProgram = item.nama_program;
      const subKategoriProgram = item.sub_kategori_program;
      const sisaPagu = item.sisa_pagu;
      const katAnggaran = item.kategori_anggaran;
      const subKatAnggaran = item.sub_kategori_anggaran;
      const sumberDanaDetail = item.sumber_dana_detail;

      // Tutup modal laporan jika ada
      const laporanModal = bootstrap.Modal.getInstance(document.getElementById('laporanModalInstance'));
      if (laporanModal) laporanModal.hide();

      document.getElementById('tr_rfk_id').value = id;
      document.getElementById('tr_sumber_dana').value = sumberDana || '';
      document.getElementById('tr_kode_program').value = (kodeProgram === '-' || !kodeProgram) ? '' : kodeProgram;
      document.getElementById('tr_nama_program').value = (namaProgram === 'Belum Ada Realisasi' || !namaProgram) ? '' : namaProgram;
      document.getElementById('tr_sub_kategori_program').value = subKategoriProgram || '';
      document.getElementById('tr_sisa_pagu_display').value = 'Rp ' + formatRupiah(sisaPagu);
      document.getElementById('tr_sisa_pagu').value = sisaPagu;
      document.getElementById('tr_nilai').value = '';
      
      const sisaBaruDisplay = document.getElementById('tr_sisa_pagu_baru_display');
      sisaBaruDisplay.value = 'Rp ' + formatRupiah(sisaPagu);
      sisaBaruDisplay.classList.remove('text-danger');
      
      document.getElementById('tr_kegiatan').value = '';
      document.getElementById('tr_sub_kegiatan').value = '';
      document.getElementById('tr_keterangan').value = '';
      document.getElementById('tr_warning').style.display = 'none';

      populateDropdowns(sumberDana, 'tr_', katAnggaran, subKatAnggaran, sumberDanaDetail);

      const modal = new bootstrap.Modal(document.getElementById('tambahRealisasiModal'));
      modal.show();
    };

    document.getElementById('tr_nilai').addEventListener('input', function () {
      const sisaPagu = parseFloat(document.getElementById('tr_sisa_pagu').value) || 0;
      const nilai = parseRupiahStr(this.value);

      const sisaBaru = sisaPagu - nilai;
      const sisaBaruDisplay = document.getElementById('tr_sisa_pagu_baru_display');

      if (sisaBaru < 0) {
        sisaBaruDisplay.value = 'Rp 0 (Melebihi Pagu!)';
        sisaBaruDisplay.classList.add('text-danger');
      } else {
        sisaBaruDisplay.value = 'Rp ' + formatRupiah(sisaBaru);
        sisaBaruDisplay.classList.remove('text-danger');
      }

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
        kode_program: document.getElementById('tr_kode_program').value,
        nama_program: document.getElementById('tr_nama_program').value,
        sub_kategori_program: document.getElementById('tr_sub_kategori_program').value,
        kategori_anggaran: document.getElementById('tr_kategori_anggaran').value,
        sub_kategori_anggaran: document.getElementById('tr_sub_kategori_anggaran').value,
        sumber_dana_detail: document.getElementById('tr_sumber_dana_detail').value,
        nilai_realisasi_keuangan: nilai,
        kegiatan: document.getElementById('tr_kegiatan').value,
        sub_kegiatan: document.getElementById('tr_sub_kegiatan').value,
        keterangan: document.getElementById('tr_keterangan').value
      };

      try {
        const response = await fetch(`/dashboard/rfk/${id}/realisasi`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
          closeModalSafe('tambahRealisasiModal');
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
    window.bukaModalEditRealisasiData = function (itemId) {
      const item = window.rfkListData[itemId];
      if (!item) {
        showToast('Data master tidak ditemukan.', 'error');
        return;
      }
      
      if (!item.realisasis || item.realisasis.length === 0) {
        showToast('Data realisasi tidak ditemukan.', 'error');
        return;
      }

      const r = item.realisasis[0];
      const realisasiId = r.id;
      const sumberDana = item.sumber_dana;
      const kodeProgram = item.kode_program;
      const namaProgram = item.nama_program;
      const subKategoriProgram = item.sub_kategori_program;
      const sisaPagu = item.sisa_pagu;
      
      const nilaiLama = r.nilai_realisasi_keuangan;
      const ketLama = r.keterangan;
      const kegLama = r.kegiatan;
      const subKegLama = r.sub_kegiatan;
      
      const katAnggaran = item.kategori_anggaran;
      const subKatAnggaran = item.sub_kategori_anggaran;
      const sumberDanaDetail = item.sumber_dana_detail;

      if (!realisasiId) {
        showToast('Data realisasi tidak valid.', 'error');
        return;
      }
      
      const laporanModal = bootstrap.Modal.getInstance(document.getElementById('laporanModalInstance'));
      if (laporanModal) laporanModal.hide();

      document.getElementById('er_realisasi_id').value = realisasiId;
      document.getElementById('er_sumber_dana').value = sumberDana || '';
      document.getElementById('er_kode_program').value = kodeProgram || '';
      document.getElementById('er_nama_program').value = namaProgram || '';
      document.getElementById('er_sub_kategori_program').value = subKategoriProgram || '';
      document.getElementById('er_sisa_pagu_display').value = 'Rp ' + formatRupiah(sisaPagu);
      document.getElementById('er_sisa_pagu').value = sisaPagu;
      document.getElementById('er_nilai').value = nilaiLama ? formatRupiah(nilaiLama) : '';

      const initialSisaBaru = sisaPagu - (parseFloat(nilaiLama) || 0);
      const erSisaBaruDisplay = document.getElementById('er_sisa_pagu_baru_display');
      erSisaBaruDisplay.value = 'Rp ' + formatRupiah(initialSisaBaru >= 0 ? initialSisaBaru : 0);
      erSisaBaruDisplay.classList.remove('text-danger');
      if (initialSisaBaru < 0) {
        erSisaBaruDisplay.value = 'Rp 0 (Melebihi Pagu!)';
        erSisaBaruDisplay.classList.add('text-danger');
      }

      document.getElementById('er_kegiatan').value = kegLama || '';
      document.getElementById('er_sub_kegiatan').value = subKegLama || '';
      document.getElementById('er_keterangan').value = ketLama || '';
      document.getElementById('er_warning').style.display = 'none';

      populateDropdowns(sumberDana, 'er_', katAnggaran, subKatAnggaran, sumberDanaDetail);

      const modal = new bootstrap.Modal(document.getElementById('editRealisasiModal'));
      modal.show();
    };

    document.getElementById('er_nilai').addEventListener('input', function () {
      const sisaPagu = parseFloat(document.getElementById('er_sisa_pagu').value) || 0;
      const nilai = parseRupiahStr(this.value);

      const sisaBaru = sisaPagu - nilai;
      const sisaBaruDisplay = document.getElementById('er_sisa_pagu_baru_display');

      if (sisaBaru < 0) {
        sisaBaruDisplay.value = 'Rp 0 (Melebihi Pagu!)';
        sisaBaruDisplay.classList.add('text-danger');
      } else {
        sisaBaruDisplay.value = 'Rp ' + formatRupiah(sisaBaru);
        sisaBaruDisplay.classList.remove('text-danger');
      }

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
        kode_program: document.getElementById('er_kode_program').value,
        nama_program: document.getElementById('er_nama_program').value,
        sub_kategori_program: document.getElementById('er_sub_kategori_program').value,
        kategori_anggaran: document.getElementById('er_kategori_anggaran').value,
        sub_kategori_anggaran: document.getElementById('er_sub_kategori_anggaran').value,
        sumber_dana_detail: document.getElementById('er_sumber_dana_detail').value,
        nilai_realisasi_keuangan: nilai,
        kegiatan: document.getElementById('er_kegiatan').value,
        sub_kegiatan: document.getElementById('er_sub_kegiatan').value,
        keterangan: document.getElementById('er_keterangan').value
      };

      try {
        const response = await fetch(`/dashboard/rfk/realisasi/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
          closeModalSafe('editRealisasiModal');
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
  