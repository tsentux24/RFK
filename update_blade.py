import re

with open('resources/views/dashboard/staff.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add populateDropdowns function before bukaModalRealisasi
populate_func = """
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
                if(val === 'BELANJA_OPERASI') {
                    apbdSubOperasi.forEach(s => selSub.innerHTML += `<option value="${s.value}" ${s.value === selected ? 'selected' : ''}>${s.label}</option>`);
                } else if(val === 'BELANJA_MODAL') {
                    apbdSubModal.forEach(s => selSub.innerHTML += `<option value="${s.value}" ${s.value === selected ? 'selected' : ''}>${s.label}</option>`);
                }
            };
            
            renderSub(selectedKat, selectedSub);
            
            selKat.onchange = function() {
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
"""

content = content.replace('window.bukaModalRealisasi = function (id, nama, sisaPagu) {', populate_func + '\n    window.bukaModalRealisasi = function (id, sumberDana, kodeProgram, namaProgram, subKategoriProgram, sisaPagu, katAnggaran, subKatAnggaran, sumberDanaDetail) {')

# 2. Update bukaModalRealisasi body
bukaModal_old = """      document.getElementById('tr_rfk_id').value = id;
      document.getElementById('tr_nama_program').value = nama;
      document.getElementById('tr_sisa_pagu_display').value = 'Rp ' + formatRupiah(sisaPagu);
      document.getElementById('tr_sisa_pagu').value = sisaPagu;
      document.getElementById('tr_nilai').value = '';
      document.getElementById('tr_keterangan').value = '';
      document.getElementById('tr_warning').style.display = 'none';"""

bukaModal_new = """      document.getElementById('tr_rfk_id').value = id;
      document.getElementById('tr_sumber_dana').value = sumberDana || '';
      document.getElementById('tr_kode_program').value = kodeProgram || '';
      document.getElementById('tr_nama_program').value = namaProgram || '';
      document.getElementById('tr_sub_kategori_program').value = subKategoriProgram || '';
      document.getElementById('tr_sisa_pagu_display').value = 'Rp ' + formatRupiah(sisaPagu);
      document.getElementById('tr_sisa_pagu').value = sisaPagu;
      document.getElementById('tr_nilai').value = '';
      document.getElementById('tr_keterangan').value = '';
      document.getElementById('tr_warning').style.display = 'none';
      
      populateDropdowns(sumberDana, 'tr_', katAnggaran, subKatAnggaran, sumberDanaDetail);"""

content = content.replace(bukaModal_old, bukaModal_new)

# 3. Update formTambahRealisasi submit handler
submitTR_old = """      const formData = {
        nilai_realisasi_keuangan: nilai,
        kegiatan: document.getElementById('tr_kegiatan').value,
        sub_kegiatan: document.getElementById('tr_sub_kegiatan').value,
        keterangan: document.getElementById('tr_keterangan').value
      };"""

submitTR_new = """      const formData = {
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
      };"""
      
content = content.replace(submitTR_old, submitTR_new)

# 4. Update bukaModalEditRealisasi signature and body
content = content.replace('window.bukaModalEditRealisasi = function (realisasiId, nama, sisaPagu, nilaiLama, ketLama, kegLama, subKegLama) {', 'window.bukaModalEditRealisasi = function (realisasiId, sumberDana, kodeProgram, namaProgram, subKategoriProgram, sisaPagu, nilaiLama, ketLama, kegLama, subKegLama, katAnggaran, subKatAnggaran, sumberDanaDetail) {')

bukaEdit_old = """      document.getElementById('er_realisasi_id').value = realisasiId;
      document.getElementById('er_nama_program').value = nama;
      document.getElementById('er_sisa_pagu_display').value = 'Rp ' + formatRupiah(sisaPagu);
      document.getElementById('er_sisa_pagu').value = sisaPagu;
      document.getElementById('er_nilai').value = nilaiLama ? formatRupiah(nilaiLama) : '';
      document.getElementById('er_kegiatan').value = kegLama || '';
      document.getElementById('er_sub_kegiatan').value = subKegLama || '';
      document.getElementById('er_keterangan').value = ketLama || '';
      document.getElementById('er_warning').style.display = 'none';"""
      
bukaEdit_new = """      document.getElementById('er_realisasi_id').value = realisasiId;
      document.getElementById('er_sumber_dana').value = sumberDana || '';
      document.getElementById('er_kode_program').value = kodeProgram || '';
      document.getElementById('er_nama_program').value = namaProgram || '';
      document.getElementById('er_sub_kategori_program').value = subKategoriProgram || '';
      document.getElementById('er_sisa_pagu_display').value = 'Rp ' + formatRupiah(sisaPagu);
      document.getElementById('er_sisa_pagu').value = sisaPagu;
      document.getElementById('er_nilai').value = nilaiLama ? formatRupiah(nilaiLama) : '';
      document.getElementById('er_kegiatan').value = kegLama || '';
      document.getElementById('er_sub_kegiatan').value = subKegLama || '';
      document.getElementById('er_keterangan').value = ketLama || '';
      document.getElementById('er_warning').style.display = 'none';
      
      populateDropdowns(sumberDana, 'er_', katAnggaran, subKatAnggaran, sumberDanaDetail);"""

content = content.replace(bukaEdit_old, bukaEdit_new)

# 5. Update formEditRealisasi submit handler
submitER_old = """      const formData = {
        nilai_realisasi_keuangan: nilai,
        kegiatan: document.getElementById('er_kegiatan').value,
        sub_kegiatan: document.getElementById('er_sub_kegiatan').value,
        keterangan: document.getElementById('er_keterangan').value
      };"""

submitER_new = """      const formData = {
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
      };"""

content = content.replace(submitER_old, submitER_new)

# 6. Update fetchData action buttons to pass the new parameters.
# For Tambah Realisasi
tambahBtn_old = """onclick="bukaModalRealisasi(${item.id}, '${item.nama_program}', ${item.sisa_pagu})\""""
tambahBtn_new = """onclick="bukaModalRealisasi(${item.id}, '${item.sumber_dana}', '${item.kode_program}', '${item.nama_program}', '${item.sub_kategori_program || ''}', ${item.sisa_pagu}, '${item.kategori_anggaran || ''}', '${item.sub_kategori_anggaran || ''}', '${item.sumber_dana_detail || ''}')\""""
content = content.replace(tambahBtn_old, tambahBtn_new)

# For Edit Realisasi
editBtn_old = """onclick="bukaModalEditRealisasi(${rRealisasiId}, '${item.nama_program}', ${item.sisa_pagu}, ${rNilai}, '${rKet}', '${rKegiatan}', '${rSubKegiatan}')\""""
editBtn_new = """onclick="bukaModalEditRealisasi(${rRealisasiId}, '${item.sumber_dana}', '${item.kode_program}', '${item.nama_program}', '${item.sub_kategori_program || ''}', ${item.sisa_pagu}, ${rNilai}, '${rKet}', '${rKegiatan}', '${rSubKegiatan}', '${item.kategori_anggaran || ''}', '${item.sub_kategori_anggaran || ''}', '${item.sumber_dana_detail || ''}')\""""
content = content.replace(editBtn_old, editBtn_new)

with open('resources/views/dashboard/staff.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Done updating staff.blade.php!')
