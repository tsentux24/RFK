@extends('dashboard.layout.app',['title'=>'Laporan Rekapitulasi SI-RAFIKA'])
@section('content')

<div class="p-6 laporan-print-container">
    <style>
        @media print {
            /* Hide UI Elements */
            .sidebar, header, .mb-6:not(.print-header), .filter-section, button { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            body, html { background-color: white !important; font-family: 'Times New Roman', Times, serif !important; margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; zoom: 90%; }

            /* Print Container and General Styling */
            @page { size: landscape; margin: 5mm; }
            .laporan-print-container { padding: 0 !important; width: 100% !important; max-width: 100% !important; overflow: visible !important; }
            .bg-white { box-shadow: none !important; border: none !important; }

            /* Header Print Styling */
            .print-header { display: flex !important; flex-direction: row; align-items: center; justify-content: center; border-bottom: 3px solid black; padding-bottom: 15px; margin-bottom: 20px; }
            .print-header img { width: 80px; height: 80px; margin-right: 20px; }
            .print-header .text-center { text-align: center; }
            .print-header h1 { font-size: 18pt; font-weight: bold; margin: 0; padding: 0; letter-spacing: 1px; text-transform: uppercase; }
            .print-header h2 { font-size: 14pt; font-weight: bold; margin: 5px 0 0 0; padding: 0; }
            .print-header p { font-size: 10pt; margin: 5px 0 0 0; font-style: italic; }

            /* Table Styling */
            table { width: 100% !important; max-width: 100% !important; border-collapse: collapse !important; border: 1px solid black !important; font-size: 8pt !important; table-layout: fixed; word-wrap: break-word; page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            th, td { border: 1px solid black !important; padding: 4px !important; color: black !important; overflow: hidden; }
            th { background-color: #f3f4f6 !important; font-weight: bold !important; text-align: center !important; }

            /* Badges should be plain text for print */
            span.bg-green-100, span.bg-yellow-100 { background: none !important; color: black !important; border: none !important; padding: 0 !important; font-weight: bold !important; }

            /* Expandable rows must print */
            .print-expand { display: table-row !important; }
            .print-expand table { border: 1px solid #666 !important; margin-top: 5px !important; }
            .print-expand th { background-color: #e5e7eb !important; }

            /* Footer Print Styling */
            .print-footer { display: block !important; margin-top: 30px; font-size: 10pt; text-align: right; border-top: 1px dashed #ccc; padding-top: 10px; font-style: italic; }

            /* Ensures colors print if allowed by browser */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }

        /* Hide Print Elements in Screen View */
        @media screen {
            .print-header, .print-footer { display: none; }
        }
    </style>

    <!-- Print Header (Hidden on Screen) -->
    <div class="print-header">
        <img src="https://e-rekrutmen.malutprov.go.id/assets/images/malut.png" alt="Logo Maluku Utara">
        <div class="text-center text-black">
            <h1>PEMERINTAH PROVINSI MALUKU UTARA</h1>
            <h2>LAPORAN REKAPITULASI REALISASI FISIK DAN KEUANGAN (SI-RAFIKA)</h2>
            <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
        </div>
    </div>

    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Rekapitulasi SI-RAFIKA</h2>
            <p class="text-sm text-gray-500">Akumulasi Berjalan (Running Total) Realisasi Keuangan dan Fisik Program</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2 flex-wrap justify-end">
            <button onclick="loadLaporanData()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </button>
            <button onclick="window.print()" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
            <button onclick="exportCsv()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2 font-medium">
                <i class="fas fa-file-csv text-lg"></i> Export CSV
            </button>
            @if(Auth::user()->role === 'superadmin' || Auth::user()->role === 'administrator')
            <button onclick="exportPdfDownload()" id="btn-export-pdf" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2 font-medium">
                <i class="fas fa-file-pdf text-lg"></i> Export PDF
            </button>
            @endif
            <button onclick="openWaModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2 font-medium">
                <i class="fab fa-whatsapp text-lg"></i> Kirim via WhatsApp (PDF)
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 filter-section">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Kode / Nama Program</label>
            <input type="text" id="filterProgram" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Cari program..." onkeyup="filterLaporan()">
        </div>
        @if(Auth::user()->role === 'superadmin' || Auth::user()->role === 'administrator')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Dinas / OPD</label>
            <select id="filterOPD" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="filterLaporan()">
                <option value="">Semua OPD</option>
                @foreach($opds as $dinas)
                    <option value="{{ strtolower($dinas->nama_opd) }}">{{ $dinas->nama_opd }}</option>
                @endforeach
            </select>
        </div>
        @else
        <div class="hidden">
            <input type="hidden" id="filterOPD" value="{{ Auth::user()->opd ? Auth::user()->opd->nama_opd : '' }}">
        </div>
        @endif
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Tahun Anggaran</label>
            <input type="text" id="filterTahun" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Contoh: 2024" onkeyup="filterLaporan()">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Triwulan</label>
            <select id="filterTriwulan" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="loadLaporanData()">
                <option value="">Semua Triwulan (Tahunan)</option>
                <option value="1">Triwulan 1 (Jan - Mar)</option>
                <option value="2">Triwulan 2 (Apr - Jun)</option>
                <option value="3">Triwulan 3 (Jul - Sep)</option>
                <option value="4">Triwulan 4 (Okt - Des)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status Master Program</label>
            <select id="filterStatus" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="filterLaporan()">
                <option value="">Semua Status</option>
                @if(Auth::user()->role !== 'superadmin' && Auth::user()->role !== 'administrator')
                <option value="SELESAI">SELESAI (100%)</option>
                @endif
                <option value="APPROVE">APPROVE</option>
                <option value="PENDING">PENDING</option>
            </select>
        </div>
        @if(Auth::user()->role === 'superadmin' || Auth::user()->role === 'administrator')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Realisasi Fisik (%)</label>
            <select id="filterFisik" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="filterLaporan()">
                <option value="">Semua Persentase</option>
                <option value="0-25">0% - 25% (Awal)</option>
                <option value="26-50">26% - 50% (Sedang Berjalan)</option>
                <option value="51-75">51% - 75% (Lebih dari Setengah)</option>
                <option value="76-99">76% - 99% (Hampir Selesai)</option>
                <option value="100">100% (Selesai)</option>
            </select>
        </div>
        @else
        <div class="hidden">
            <input type="hidden" id="filterFisik" value="">
        </div>
        @endif
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="laporanTable">
                <thead class="bg-indigo-50 text-indigo-900 border-b border-indigo-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Tahun</th>
                        <th class="px-5 py-4 font-semibold">Instansi OPD</th>
                        <th class="px-5 py-4 font-semibold">Program & Kode</th>
                        <th class="px-5 py-4 font-semibold">Pagu Total</th>
                        <th class="px-5 py-4 font-semibold text-right">Realisasi Keuangan</th>
                        <th class="px-5 py-4 font-semibold text-right">Sisa Pagu</th>
                        <th class="px-5 py-4 font-semibold text-center">Fisik</th>
                        <th class="px-5 py-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="laporanTableBody" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-indigo-600 text-2xl mb-2"></i>
                            <p class="text-gray-500">Memuat rekapitulasi laporan realtime...</p>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-50 font-bold text-gray-800 border-t-2 border-gray-400">
                    <tr id="laporanTotalRow">
                        <td colspan="3" class="px-5 py-4 text-right border border-gray-300">TOTAL KESELURUHAN PADA FILTER:</td>
                        <td class="px-5 py-4 text-left text-indigo-700 border border-gray-300" id="grandTotalPagu">Rp 0</td>
                        <td class="px-5 py-4 text-right text-green-600 border border-gray-300" id="grandTotalRealisasi">Rp 0</td>
                        <td class="px-5 py-4 text-right text-red-500 border border-gray-300" id="grandTotalSisa">Rp 0</td>
                        <td colspan="2" class="px-5 py-4 border border-gray-300"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Print Footer (Hidden on Screen) -->
    <div class="print-footer">
        Data Di cetak Dengan Sistem SI-RAFIKA Biro Adminisistrasi Pembangunan Setda Provinsi Maluku Utara
    </div>

    <!-- WhatsApp Modal -->
    <div id="wa-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-100 transition-transform">
            <div class="bg-green-600 p-5 flex justify-between items-center text-white">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="fab fa-whatsapp text-2xl"></i> Kirim PDF Laporan
                </h3>
                <button onclick="closeWaModal()" class="text-green-200 hover:text-white transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="bg-green-50 text-green-800 text-sm p-4 rounded-xl mb-5 flex gap-3 items-start border border-green-100">
                    <i class="fas fa-info-circle mt-1"></i>
                    <p>Sistem akan membuat PDF Laporan berdasarkan filter yang sedang aktif, lalu mengarahkan Anda ke WhatsApp dengan tautan (link) PDF terlampir.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor WhatsApp Tujuan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fab fa-whatsapp text-gray-400"></i>
                        </div>
                        <input type="text" id="waNumber" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-gray-900" placeholder="Contoh: 628123456789">
                    </div>
                    <p class="text-xs text-gray-500 mt-2 ml-1">Gunakan format internasional tanpa tanda plus (+). Awali dengan kode negara (contoh: 62 untuk Indonesia).</p>
                </div>
            </div>
            <div class="bg-gray-50 p-5 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                <button onclick="closeWaModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">Batal</button>
                <button onclick="generateAndSendWa()" id="btn-wa-send" class="px-5 py-2 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-md shadow-green-200 transition-all flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Buat & Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<script>

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

    document.addEventListener('DOMContentLoaded', function() {
        loadLaporanData();
    });

    async function loadLaporanData() {
        try {
            document.getElementById('laporanTableBody').innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-indigo-600 text-2xl mb-2"></i>
                        <p class="text-gray-500">Memuat rekapitulasi laporan realtime...</p>
                    </td>
                </tr>
            `;

            const triwulanVal = document.getElementById('filterTriwulan').value;
            const fetchUrl = '{{ route("laporan.data") }}' + (triwulanVal ? '?triwulan=' + triwulanVal : '');

            const response = await fetch(fetchUrl);
            if(!response.ok) throw new Error("Gagal load data");
            const result = await response.json();

            if (result.success) {
                let rows = '';
                result.data.forEach(item => {
                    const opdName = item.opd ? item.opd.nama_opd : '-';
                    const pagu = parseFloat(item.pagu) || 0;
                    const realKeuangan = parseFloat(item.realisasi_keuangan) || 0;
                    const sisaPagu = parseFloat(item.sisa_pagu) || 0;
                    const realFisik = parseFloat(item.realisasi_fisik) || 0;

                    let statusBadge = '';
                    if (item.status === 'SELESAI') {
                        statusBadge = '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded font-medium text-xs"><i class="fas fa-check-double mr-1"></i>SELESAI</span>';
                    } else if(item.status === 'APPROVE') {
                        statusBadge = '<span class="bg-green-100 text-green-800 px-2 py-1 rounded font-medium text-xs">APPROVE</span>';
                    } else {
                        statusBadge = '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded font-medium text-xs">PENDING</span>';
                    }

                    let masterRowId = 'master-' + item.id;

                    rows += `
                        <tr class="hover:bg-indigo-50 transition-colors data-row cursor-pointer" onclick="toggleDetail('${masterRowId}')" title="Klik untuk melihat rincian realisasi">
                            <td class="px-5 py-4 font-medium text-gray-700 tahun-text">
                                <i class="fas fa-chevron-down text-indigo-400 mr-2 text-xs"></i>${item.tahun_anggaran || '-'}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-800 opd-text">${opdName}</td>
                            <td class="px-5 py-4 max-w-xs">
                                <div class="text-gray-900 font-bold truncate program-text" title="${item.nama_program}">${item.nama_program}</div>
                                <div class="text-indigo-600 text-xs font-mono mt-1 mb-1">${item.kode_program}</div>
                                <div class="text-gray-500 text-[10px] leading-tight"><span class="font-bold">Kegiatan:</span> ${item.kegiatan || '-'}</div>
                                <div class="text-gray-500 text-[10px] leading-tight"><span class="font-bold">Sub Kegiatan:</span> ${item.sub_kegiatan || '-'}</div>
                            </td>
                            <td class="px-5 py-4 font-semibold text-gray-800" data-val="${pagu}">
                                Rp ${formatRupiahManual(pagu)}
                            </td>
                            <td class="px-5 py-4 font-semibold text-right text-green-600" data-val="${realKeuangan}">
                                Rp ${formatRupiahManual(realKeuangan)}
                            </td>
                            <td class="px-5 py-4 font-semibold text-right text-red-500" data-val="${sisaPagu}">
                                Rp ${formatRupiahManual(sisaPagu)}
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-gray-700">
                                ${realFisik}%
                            </td>
                            <td class="px-5 py-4 text-center status-text">
                                ${statusBadge}
                            </td>
                        </tr>
                    `;

                    // Generate Detail Row
                    let detailHtml = '';
                    if (item.realisasis && item.realisasis.length > 0) {
                        detailHtml += `
                            <table class="w-full text-xs text-left bg-white border border-gray-200 shadow-sm mt-2">
                                <thead class="bg-gray-100 text-gray-600">
                                    <tr>
                                        <th class="p-2 border">Waktu Pengajuan</th>
                                        <th class="p-2 border">Kegiatan / Sub Kegiatan</th>
                                        <th class="p-2 border">Keterangan</th>
                                        <th class="p-2 border text-right">Nilai Diajukan (Rp)</th>
                                        <th class="p-2 border text-center">Fisik (%)</th>
                                        <th class="p-2 border text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        item.realisasis.forEach(real => {
                            const val = parseFloat(real.nilai_realisasi_keuangan) || 0;
                            const fis = parseFloat(real.nilai_realisasi_fisik) || 0;
                            const dateStr = new Date(real.created_at).toLocaleString('id-ID');
                            const stat = real.status;
                            let statBadge = stat === 'APPROVE' ? '<span class="text-green-600 font-bold">APPROVE</span>' :
                                            (stat === 'REJECT' ? '<span class="text-red-600 font-bold">REJECT</span>' : '<span class="text-yellow-600 font-bold">PENDING</span>');

                            detailHtml += `
                                <tr class="hover:bg-gray-50">
                                    <td class="p-2 border">${dateStr}</td>
                                    <td class="p-2 border">
                                        ${real.kegiatan ? `<strong>Keg:</strong> ${real.kegiatan}<br>` : ''}
                                        ${real.sub_kegiatan ? `<strong>Sub:</strong> ${real.sub_kegiatan}` : '-'}
                                    </td>
                                    <td class="p-2 border">${real.keterangan || '-'}</td>
                                    <td class="p-2 border text-right text-green-700 font-medium">Rp ${formatRupiahManual(val)}</td>
                                    <td class="p-2 border text-center font-medium">${fis}%</td>
                                    <td class="p-2 border text-center">${statBadge}</td>
                                </tr>
                            `;
                        });
                        detailHtml += `</tbody></table>`;
                    } else {
                        detailHtml = '<p class="text-xs text-gray-500 italic mt-2">Belum ada rincian tahapan realisasi. Data ini adalah program awal (Tahap 0%).</p>';
                    }

                    rows += `
                        <tr id="${masterRowId}" class="hidden print-expand">
                            <td colspan="8" class="p-4 bg-gray-50 border-b-2 border-indigo-200">
                                <div class="pl-4 border-l-4 border-indigo-500 py-1">
                                    <p class="font-bold text-indigo-900 text-xs uppercase tracking-wider"><i class="fas fa-list-ul mr-1"></i> Rincian Tahapan Realisasi Berjalan:</p>
                                    ${detailHtml}
                                </div>
                            </td>
                        </tr>
                    `;
                });

                if(result.data.length === 0) {
                    rows = '<tr><td colspan="8" class="text-center py-8 text-gray-500"><i class="fas fa-folder-open text-4xl mb-3 block text-gray-300"></i>Belum ada data Realisasi / Program</td></tr>';
                }

                document.getElementById('laporanTableBody').innerHTML = rows;
                calculateTotals();
            }
        } catch (error) {
            console.error("Error fetching laporan", error);
            document.getElementById('laporanTableBody').innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>Gagal memuat rekap laporan. Silakan refresh.</td></tr>';
        }
    }

    function filterLaporan() {
        const pSearch = document.getElementById('filterProgram').value.toLowerCase();
        const oSearch = document.getElementById('filterOPD') ? document.getElementById('filterOPD').value.toLowerCase() : '';
        const tSearch = document.getElementById('filterTahun').value.toLowerCase();
        const sSearch = document.getElementById('filterStatus').value;
        const fSearch = document.getElementById('filterFisik') ? document.getElementById('filterFisik').value : '';

        const rows = document.querySelectorAll('#laporanTableBody tr.data-row');

        rows.forEach(row => {
            const program = row.querySelector('.program-text').innerText.toLowerCase() + " " + row.querySelector('.font-mono').innerText.toLowerCase();
            const opd = row.querySelector('.opd-text').innerText.toLowerCase();
            const tahun = row.querySelector('.tahun-text').innerText.toLowerCase();
            const status = row.querySelector('.status-text').innerHTML;
            
            // Get numeric fisik
            const fisikText = row.children[6].innerText.replace('%', '').trim();
            const fisik = parseFloat(fisikText) || 0;

            let show = true;
            if(pSearch && !program.includes(pSearch)) show = false;
            if(oSearch && !opd.includes(oSearch)) show = false;
            if(tSearch && !tahun.includes(tSearch)) show = false;
            if(sSearch && !status.includes(sSearch)) show = false;
            
            if(fSearch) {
                if(fSearch === '0-25' && !(fisik >= 0 && fisik <= 25)) show = false;
                else if(fSearch === '26-50' && !(fisik > 25 && fisik <= 50)) show = false;
                else if(fSearch === '51-75' && !(fisik > 50 && fisik <= 75)) show = false;
                else if(fSearch === '76-99' && !(fisik > 75 && fisik < 100)) show = false;
                else if(fSearch === '100' && fisik !== 100) show = false;
            }

            row.style.display = show ? '' : 'none';
        });

        calculateTotals();
    }

    function calculateTotals() {
        const rows = document.querySelectorAll('#laporanTableBody tr.data-row');
        let totalPagu = 0, totalReal = 0, totalSisa = 0;

        rows.forEach(row => {
            if(row.style.display !== 'none') {
                const p = parseFloat(row.children[3].getAttribute('data-val')) || 0;
                const r = parseFloat(row.children[4].getAttribute('data-val')) || 0;
                const s = parseFloat(row.children[5].getAttribute('data-val')) || 0;

                totalPagu += p;
                totalReal += r;
                totalSisa += s;
            }
        });

        document.getElementById('grandTotalPagu').innerText = 'Rp ' + formatRupiahManual(totalPagu);
        document.getElementById('grandTotalRealisasi').innerText = 'Rp ' + formatRupiahManual(totalReal);
        document.getElementById('grandTotalSisa').innerText = 'Rp ' + formatRupiahManual(totalSisa);
    }

    function toggleDetail(rowId) {
        const row = document.getElementById(rowId);
        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    }

    // WA PDF Logic
    const waModal = document.getElementById('wa-modal');

    function openWaModal() {
        waModal.classList.remove('hidden');
        document.getElementById('waNumber').focus();
    }

    function closeWaModal() {
        waModal.classList.add('hidden');
    }

    async function generateAndSendWa() {
        const number = document.getElementById('waNumber').value.trim();
        if (!number) {
            Swal.fire('Perhatian', 'Silakan masukkan nomor WhatsApp tujuan!', 'warning');
            return;
        }

        const btn = document.getElementById('btn-wa-send');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses PDF...';

        // Get active filters
        const pSearch = document.getElementById('filterProgram').value;
        const oSearch = document.getElementById('filterOPD') ? document.getElementById('filterOPD').value : '';
        const tSearch = document.getElementById('filterTahun').value;
        const sSearch = document.getElementById('filterStatus').value;
        const twSearch = document.getElementById('filterTriwulan').value;
        const fSearch = document.getElementById('filterFisik') ? document.getElementById('filterFisik').value : '';

        try {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            if(pSearch) formData.append('program', pSearch);
            if(oSearch) formData.append('opd', oSearch);
            if(tSearch) formData.append('tahun', tSearch);
            if(sSearch) formData.append('status', sSearch);
            if(twSearch) formData.append('triwulan', twSearch);
            if(fSearch) formData.append('fisik', fSearch);

            const response = await fetch('{{ route("laporan.pdf") }}', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                const pdfUrl = result.url;
                const textMessage = encodeURIComponent(`Berikut adalah Laporan Rekapitulasi SI-RAFIKA:\n\n${pdfUrl}`);
                const waUrl = `https://wa.me/${number}?text=${textMessage}`;

                // Close modal & open WA
                closeWaModal();
                window.open(waUrl, '_blank');
            } else {
                Swal.fire('Gagal!', 'Gagal membuat PDF: ' + result.message, 'error');
            }

        } catch (e) {
            console.error(e);
            Swal.fire('Error!', 'Terjadi kesalahan saat memproses laporan.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Buat & Kirim';
        }
    }

    async function exportPdfDownload() {
        const btn = document.getElementById('btn-export-pdf');
        if(!btn) return;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-lg"></i> Proses...';

        const pSearch = document.getElementById('filterProgram').value;
        const oSearch = document.getElementById('filterOPD') ? document.getElementById('filterOPD').value : '';
        const tSearch = document.getElementById('filterTahun').value;
        const sSearch = document.getElementById('filterStatus').value;
        const twSearch = document.getElementById('filterTriwulan').value;
        const fSearch = document.getElementById('filterFisik') ? document.getElementById('filterFisik').value : '';

        try {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            if(pSearch) formData.append('program', pSearch);
            if(oSearch) formData.append('opd', oSearch);
            if(tSearch) formData.append('tahun', tSearch);
            if(sSearch) formData.append('status', sSearch);
            if(twSearch) formData.append('triwulan', twSearch);
            if(fSearch) formData.append('fisik', fSearch);

            const response = await fetch('{{ route("laporan.pdf") }}', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Open the PDF in a new tab to view or trigger download
                const link = document.createElement('a');
                link.href = result.url;
                link.target = '_blank'; // Open in new tab
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                Swal.fire('Gagal!', 'Gagal membuat PDF: ' + (result.message || 'Error internal'), 'error');
            }

        } catch (e) {
            console.error(e);
            Swal.fire('Error!', 'Terjadi kesalahan saat mengekspor laporan.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-file-pdf text-lg"></i> Export PDF';
        }
    }

    function exportCsv() {
        const pSearch = document.getElementById('filterProgram').value;
        const oSearch = document.getElementById('filterOPD') ? document.getElementById('filterOPD').value : '';
        const tSearch = document.getElementById('filterTahun').value;
        const sSearch = document.getElementById('filterStatus').value;
        const twSearch = document.getElementById('filterTriwulan').value;
        const fSearch = document.getElementById('filterFisik') ? document.getElementById('filterFisik').value : '';
        
        let url = '{{ route("laporan.csv") }}?';
        if(pSearch) url += '&program=' + encodeURIComponent(pSearch);
        if(oSearch) url += '&opd=' + encodeURIComponent(oSearch);
        if(tSearch) url += '&tahun=' + encodeURIComponent(tSearch);
        if(sSearch) url += '&status=' + encodeURIComponent(sSearch);
        if(twSearch) url += '&triwulan=' + encodeURIComponent(twSearch);
        if(fSearch) url += '&fisik=' + encodeURIComponent(fSearch);
        
        window.location.href = url;
    }
</script>
@endsection
