@extends('dashboard.layout.app',['title'=>'Audit Global RFK'])
@section('content')
<div class="p-6 audit-print-container">
    <style>
        @media print {
            .sidebar, header, .mb-6 { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            body { background-color: white !important; }
            .audit-print-container { padding: 0 !important; }
            .bg-white { box-shadow: none !important; border: none !important; }
            table { width: 100% !important; border-collapse: collapse !important; }
            th, td { border: 1px solid #e5e7eb !important; padding: 12px !important; }
            /* Make text fully black and remove backgrounds for cleaner print */
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Audit Global RFK</h2>
            <p class="text-sm text-gray-500">Rekam jejak seluruh aktivitas dan perubahan status program RFK dari semua OPD</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <button onclick="loadAuditData()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> Refresh Data
            </button>
            <button onclick="window.print()" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Filter & Search (Visual Only for now, but ready to be hooked) -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Cari program, nama OPD, atau user..." onkeyup="filterTable()">
            </div>
        </div>
        <div class="w-full md:w-64">
            <select id="statusFilter" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg" onchange="filterTable()">
                <option value="">Semua Status</option>
                <option value="APPROVE">APPROVE</option>
                <option value="PENDING">PENDING</option>
                <option value="REJECT">REJECT</option>
            </select>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="auditTable">
                <thead class="bg-indigo-50 text-indigo-900 border-b border-indigo-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Waktu / Tanggal</th>
                        <th class="px-5 py-4 font-semibold">Nama OPD</th>
                        <th class="px-5 py-4 font-semibold">Program & Kode</th>
                        <th class="px-5 py-4 font-semibold">Detail Pengajuan</th>
                        <th class="px-5 py-4 font-semibold">Transaksi Status</th>
                        <th class="px-5 py-4 font-semibold">Keterangan</th>
                        <th class="px-5 py-4 font-semibold">Diinput Oleh</th>
                    </tr>
                </thead>
                <tbody id="auditTableBody" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="7" class="text-center py-8">
                            <i class="fas fa-spinner fa-spin text-indigo-600 text-2xl mb-2"></i>
                            <p class="text-gray-500">Memuat data audit realtime...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadAuditData();
    });

    async function loadAuditData() {
        try {
            document.getElementById('auditTableBody').innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-indigo-600 text-2xl mb-2"></i>
                        <p class="text-gray-500">Memuat data audit realtime...</p>
                    </td>
                </tr>
            `;

            const response = await fetch('{{ route("rfk.history") }}');
            const result = await response.json();

            if (result.success) {
                let rows = '';
                result.data.forEach(item => {
                    // Extract data safely
                    const dateObj = new Date(item.created_at);
                    const dateFormatted = dateObj.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
                    const timeFormatted = dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute:'2-digit' });
                    
                    const inputRfk = item.realisasi && item.realisasi.input_rfk ? item.realisasi.input_rfk : null;
                    const programName = inputRfk ? inputRfk.nama_program : '-';
                    const programCode = inputRfk ? inputRfk.kode_program : '-';
                    const opdName = inputRfk && inputRfk.opd ? inputRfk.opd.nama_opd : '-';
                    
                    const userRole = item.user ? item.user.role : '';
                    const userName = item.user ? item.user.name : '-';
                    let roleBadge = '';
                    if(userRole === 'staff') roleBadge = '<span class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 ml-1">Staff</span>';
                    else if(userRole === 'kepala_opd') roleBadge = '<span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-100 ml-1">Kepala OPD</span>';
                    else if(userRole === 'administrator' || userRole === 'superadmin') roleBadge = '<span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-100 ml-1">Admin</span>';
                    
                    let statusBadge = '';
                    if (item.status_baru === 'APPROVE') statusBadge = '<span class="bg-green-100 text-green-800 px-2.5 py-1 rounded-md font-medium text-xs shadow-sm"><i class="fas fa-check-circle mr-1"></i>APPROVE</span>';
                    else if (item.status_baru === 'REJECT') statusBadge = '<span class="bg-red-100 text-red-800 px-2.5 py-1 rounded-md font-medium text-xs shadow-sm"><i class="fas fa-times-circle mr-1"></i>REJECT</span>';
                    else statusBadge = '<span class="bg-yellow-100 text-yellow-800 px-2.5 py-1 rounded-md font-medium text-xs shadow-sm"><i class="fas fa-clock mr-1"></i>PENDING</span>';

                    const prevStatus = item.status_sebelumnya ? item.status_sebelumnya : 'Baru';

                    const nilaiDiajukan = item.realisasi ? item.realisasi.nilai_realisasi_keuangan : 0;
                    const fisikDiajukan = item.realisasi ? item.realisasi.nilai_realisasi_fisik : 0;
                    const sisaPagu = inputRfk ? inputRfk.sisa_pagu : 0;

                    rows += `
                        <tr class="hover:bg-indigo-50/50 transition-colors duration-150">
                            <td class="px-5 py-4">
                                <div class="text-gray-900 font-medium">${dateFormatted}</div>
                                <div class="text-gray-500 text-xs mt-1"><i class="far fa-clock mr-1"></i>${timeFormatted}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                        <i class="fas fa-building text-xs"></i>
                                    </div>
                                    <span class="font-medium text-gray-700 opd-text">${opdName}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 max-w-xs">
                                <div class="text-gray-900 font-medium truncate program-text" title="${programName}">${programName}</div>
                                <div class="text-indigo-600 text-xs font-mono mt-1">${programCode}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs">
                                    <span class="text-green-600 font-medium">Ajukan: Rp ${new Intl.NumberFormat('id-ID').format(nilaiDiajukan)} (${fisikDiajukan}%)</span><br>
                                    <span class="text-red-500 font-medium">Sisa Pagu: Rp ${new Intl.NumberFormat('id-ID').format(sisaPagu)}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500 text-xs font-medium bg-gray-100 px-2 py-1 rounded">${prevStatus}</span>
                                    <i class="fas fa-arrow-right text-gray-300 text-xs"></i>
                                    <span class="status-cell">${statusBadge}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600 text-sm max-w-xs truncate" title="${item.keterangan || '-'}">
                                ${item.keterangan || '-'}
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-gray-900 font-medium user-text">${userName}</div>
                                <div class="mt-1">${roleBadge}</div>
                            </td>
                        </tr>
                    `;
                });

                if(result.data.length === 0) {
                    rows = '<tr><td colspan="7" class="text-center py-8 text-gray-500"><i class="fas fa-inbox text-4xl mb-3 text-gray-300 block"></i>Belum ada rekam jejak audit RFK</td></tr>';
                }

                document.getElementById('auditTableBody').innerHTML = rows;
            }
        } catch (error) {
            console.error("Error fetching history", error);
            document.getElementById('auditTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>Gagal memuat data. Silakan coba lagi.</td></tr>';
        }
    }

    function filterTable() {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('#auditTableBody tr');

        rows.forEach(row => {
            // Jika row loading atau kosong, skip
            if(row.cells.length < 6) return;

            const opd = row.querySelector('.opd-text').innerText.toLowerCase();
            const program = row.querySelector('.program-text').innerText.toLowerCase();
            const user = row.querySelector('.user-text').innerText.toLowerCase();
            const statusHtml = row.querySelector('.status-cell').innerHTML;
            
            let statusMatch = true;
            if (statusFilter) {
                statusMatch = statusHtml.includes(statusFilter);
            }

            let textMatch = true;
            if (searchText) {
                textMatch = opd.includes(searchText) || program.includes(searchText) || user.includes(searchText);
            }

            if (statusMatch && textMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
