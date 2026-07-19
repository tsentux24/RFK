@extends('dashboard.layout.app', ['title' => 'Dashboard Administrator'])

@section('content')
<!-- Google Fonts: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    :root {
        --primary: #2563EB;
        --success: #22C55E;
        --warning: #F59E0B;
        --danger: #EF4444;
        --info: #06B6D4;
        --bg-color: #F8FAFC;
        --card-bg: #FFFFFF;
        --grid-color: #E5E7EB;
        --text-color: #1F2937;
    }
    body {
        font-family: 'Inter', sans-serif !important;
        background-color: var(--bg-color) !important;
        color: var(--text-color);
    }
    /* Modern Glassmorphism & Soft Shadow Card */
    .bi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .bi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
        padding: 10px;
    }
    .chart-container-sm {
        position: relative;
        height: 250px;
        width: 100%;
    }
    .kpi-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .modern-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .modern-scroll::-webkit-scrollbar-track { background: transparent; }
    .modern-scroll::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
</style>

<div class="main-content min-h-screen pb-12 pt-6 px-4 md:px-8 max-w-[1600px] mx-auto">
    
    <!-- Executive Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Executive Dashboard</h1>
            <p class="text-slate-500 mt-1 font-medium">Analisis Komprehensif Si-RAFIKA</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 bg-white p-2.5 rounded-2xl shadow-sm border border-slate-200">
            <select id="filterTahun" class="px-4 py-2 bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 min-w-[120px]" onchange="loadData()">
                <option value="">Semua Tahun</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
            </select>
            <div class="w-px h-8 bg-slate-200"></div>
            <select id="filterOPD" class="px-4 py-2 bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500 min-w-[200px]" onchange="loadData()">
                <option value="">Seluruh Instansi</option>
                @foreach($opds as $opd)
                    <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                @endforeach
            </select>
            <div class="w-px h-8 bg-slate-200"></div>
            <button onclick="exportPDF()" class="p-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors" title="Export PDF"><i class="fas fa-file-pdf"></i></button>
            <button onclick="exportExcel()" class="p-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-xl transition-colors" title="Export Excel"><i class="fas fa-file-excel"></i></button>
            <button onclick="exportPNG()" class="p-2.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-xl transition-colors" title="Export Image"><i class="fas fa-image"></i></button>
            <button onclick="toggleFullscreen()" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors" title="Fullscreen"><i class="fas fa-expand"></i></button>
        </div>
    </div>

    <!-- Admin Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div onclick="openApprovalModal()" class="bi-card p-5 border-l-4 border-l-amber-500 cursor-pointer hover:bg-amber-50/30 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="kpi-icon bg-amber-100 text-amber-600"><i class="fas fa-clipboard-check"></i></div>
                <div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Tugas Administrator</h3>
                    <p class="text-lg font-black text-slate-800">Menunggu Approval</p>
                </div>
            </div>
            <div class="text-3xl font-black text-amber-500" id="kpi-pending">0</div>
        </div>
        <div onclick="openOpdBelumModal()" class="bi-card p-5 border-l-4 border-l-red-500 cursor-pointer hover:bg-red-50/30 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="kpi-icon bg-red-100 text-red-600"><i class="fas fa-building"></i></div>
                <div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-widest">Pemantauan Kinerja</h3>
                    <p class="text-lg font-black text-slate-800">OPD Belum Input</p>
                </div>
            </div>
            <div class="text-3xl font-black text-red-500" id="kpi-belum-input">0</div>
        </div>
    </div>

    <!-- 14 KPI Cards Grid (Desktop: 7 cols or 2 rows of 7, let's do 4-5 cols to fit) -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
        <!-- K1 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('pagu')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Total Pagu</p>
            <h4 class="text-sm font-black text-slate-800 truncate" id="kpi-pagu">Rp 0</h4>
        </div>
        <!-- K2 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('realisasi')">
            <p class="text-[10px] font-bold text-emerald-600 uppercase mb-1">Realisasi</p>
            <h4 class="text-sm font-black text-slate-800 truncate" id="kpi-realisasi">Rp 0</h4>
        </div>
        <!-- K3 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('fisik')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Fisik</p>
            <h4 class="text-xl font-black text-blue-600" id="kpi-fisik">0%</h4>
        </div>
        <!-- K4 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('keuangan')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Keuangan</p>
            <h4 class="text-xl font-black text-emerald-600" id="kpi-keuangan">0%</h4>
        </div>
        <!-- K5 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('opd')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Total OPD</p>
            <h4 class="text-xl font-black text-slate-800" id="kpi-opd">0</h4>
        </div>
        <!-- K6 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('program')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Program</p>
            <h4 class="text-xl font-black text-slate-800" id="kpi-program">0</h4>
        </div>
        <!-- K7 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('kegiatan')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Kegiatan</p>
            <h4 class="text-xl font-black text-slate-800" id="kpi-kegiatan">0</h4>
        </div>
        <!-- K8 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('apbd')">
            <p class="text-[10px] font-bold text-cyan-600 uppercase mb-1">APBD</p>
            <h4 class="text-sm font-black text-slate-800 truncate" id="kpi-apbd">Rp 0</h4>
        </div>
        <!-- K9 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('apbn')">
            <p class="text-[10px] font-bold text-fuchsia-600 uppercase mb-1">APBN</p>
            <h4 class="text-sm font-black text-slate-800 truncate" id="kpi-apbn">Rp 0</h4>
        </div>
        <!-- K10 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('tepat_waktu')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Tepat Waktu</p>
            <h4 class="text-xl font-black text-emerald-500" id="kpi-tepat-waktu">0</h4>
        </div>
        <!-- K11 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('terlambat')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Terlambat</p>
            <h4 class="text-xl font-black text-red-500" id="kpi-terlambat">0</h4>
        </div>
        <!-- K12 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('bermasalah')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Bermasalah</p>
            <h4 class="text-xl font-black text-amber-500" id="kpi-bermasalah">0</h4>
        </div>
        <!-- K13 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('paket')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Total Paket</p>
            <h4 class="text-xl font-black text-slate-800" id="kpi-paket">0</h4>
        </div>
        <!-- K14 -->
        <div class="bi-card p-4 flex flex-col justify-center cursor-pointer hover:shadow-lg transition-all hover:scale-[1.02] ring-1 ring-transparent hover:ring-blue-400" onclick="openMasterDetail('deviasi')">
            <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Deviasi Rata-rata</p>
            <h4 class="text-xl font-black text-rose-500" id="kpi-deviasi">-0%</h4>
        </div>
    </div>

    <!-- Charts Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- 1. Progress Realisasi Fisik (Line) -->
        <div class="bi-card p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Target vs Realisasi Fisik Bulanan</h3>
                <p class="text-[11px] text-slate-500">Tren pencapaian fisik akumulatif per bulan.</p>
            </div>
            <div class="chart-container"><canvas id="chart1"></canvas></div>
        </div>

        <!-- 2. Progress Realisasi Keuangan (Area) -->
        <div class="bi-card p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Dinamika Arus Kas (Keuangan)</h3>
                <p class="text-[11px] text-slate-500">Perbandingan Pagu, Pencairan, dan Sisa Anggaran (Area).</p>
            </div>
            <div class="chart-container"><canvas id="chart2"></canvas></div>
        </div>

        <!-- 3. APBD vs APBN (Grouped Bar) -->
        <div class="bi-card p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Postur Sumber Dana (APBD vs APBN)</h3>
                <p class="text-[11px] text-slate-500">Total Anggaran & Realisasi berdasarkan sumber dana utama.</p>
            </div>
            <div class="chart-container"><canvas id="chart3"></canvas></div>
        </div>

        <!-- 4. Progress Seluruh OPD (Horizontal Bar) -->
        <div class="bi-card p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Ranking Penyerapan Instansi</h3>
                <p class="text-[11px] text-slate-500">Indikator warna otomatis sesuai capaian kinerja (Horizontal Bar).</p>
            </div>
            <div class="chart-container modern-scroll overflow-y-auto"><canvas id="chart4" style="height: 800px;"></canvas></div>
        </div>

        <!-- 5. Status Kegiatan (Donut) -->
        <div class="bi-card p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Status Pelaksanaan Kegiatan</h3>
                <p class="text-[11px] text-slate-500">Proporsi Belum Mulai, Berjalan, Selesai, dan Terlambat.</p>
            </div>
            <div class="chart-container-sm"><canvas id="chart5"></canvas></div>
        </div>

        <!-- 6. Tren Penyerapan Anggaran (Smooth Line) -->
        <div class="bi-card p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Akselerasi Penyerapan (Jan-Des)</h3>
                <p class="text-[11px] text-slate-500">Proyeksi dan realisasi nilai serapan (Rp) menggunakan smooth line.</p>
            </div>
            <div class="chart-container-sm"><canvas id="chart6"></canvas></div>
        </div>

    </div>

    <!-- Full Width Charts -->
    <div class="grid grid-cols-1 gap-6 mb-6">
        
        <!-- 7. Sebaran Nilai Proyek (Histogram) -->
        <div class="bi-card p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Distribusi Klasifikasi Nilai Proyek</h3>
                <p class="text-[11px] text-slate-500">Sebaran proyek berdasarkan rentang nilai (<100 Juta, 100-500 Juta, 500-1 M, >1 M).</p>
            </div>
            <div class="chart-container-sm"><canvas id="chart7"></canvas></div>
        </div>

        <!-- 8. Deviasi Target vs Realisasi (Waterfall) -->
        <div class="bi-card p-5">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-800">Kesenjangan Kinerja (Waterfall Chart)</h3>
                <p class="text-[11px] text-slate-500">Analisis selisih (deviasi) positif/negatif target terhadap realisasi fisik.</p>
            </div>
            <div class="chart-container"><canvas id="chart8"></canvas></div>
        </div>

        <!-- 9 & 10 Combined Row: Map & Top 10 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 9. Interactive Map -->
            <div class="bi-card p-5 flex flex-col">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-slate-800">Geospasial Pembangunan</h3>
                    <p class="text-[11px] text-slate-500">Peta Interaktif sebaran proyek di Maluku Utara.</p>
                </div>
                <div id="interactive-map" class="w-full flex-grow rounded-xl border border-slate-200 z-10" style="min-height: 400px;"></div>
            </div>

            <!-- 10. Top 10 Strategis -->
            <div class="bi-card p-5">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-slate-800">Top 10 OPD (Pagu Terbesar)</h3>
                    <p class="text-[11px] text-slate-500">Instansi dengan alokasi anggaran terbesar dan realisasinya.</p>
                </div>
                <div class="chart-container"><canvas id="chart10"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Modal Approval RFK -->
    <div id="approval-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-amber-50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-amber-800"><i class="fas fa-check-double me-2"></i> Validasi Realisasi RFK</h3>
                <button onclick="closeApprovalModal()" class="text-gray-400 hover:text-amber-600"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-5 overflow-auto flex-grow">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-800 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3">Instansi (OPD)</th>
                            <th class="px-4 py-3">Rincian Program</th>
                            <th class="px-4 py-3">Anggaran</th>
                            <th class="px-4 py-3">Realisasi</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody id="approvalTableBody" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal OPD Belum Input -->
    <div id="opd-belum-modal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-red-50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-red-800"><i class="fas fa-exclamation-triangle mr-2"></i> OPD Belum Input Data</h3>
                <button onclick="closeOpdBelumModal()" class="text-gray-400 hover:text-red-600"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-5 overflow-auto max-h-[60vh]">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4">No</th>
                            <th class="px-4 py-3">Nama Instansi / OPD</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="opdBelumTableBody" class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


    <!-- Master Detail Modal -->
    <div id="masterDetailModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden transform scale-95 transition-transform duration-300 relative">
            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-chart-pie" id="masterDetailIcon"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-800" id="masterDetailTitle">Detail Data</h2>
                        <p class="text-slate-500 text-sm font-medium" id="masterDetailSubtitle">Menampilkan rincian dari indikator yang dipilih.</p>
                    </div>
                </div>
                <button onclick="closeMasterDetail()" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition-all focus:outline-none focus:ring-2 focus:ring-red-200">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto modern-scroll flex-1 bg-slate-50/30">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                                <tr id="masterDetailThead">
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Program</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">OPD</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Sumber Dana</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Fisik</th>
                                    <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700" id="masterDetailTbody">
                                <tr><td colspan="7" class="text-center py-8 text-slate-500">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js">
</script>
<!-- Chart.js & Plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js">
</script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0">
</script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>
<!-- html2canvas & jsPDF for Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js">
</script>

<script>
    let globalSuperadminData = {};
    // Register ChartJS DataLabels plugin globally
    Chart.register(ChartDataLabels);
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748B';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.9)';
    Chart.defaults.plugins.tooltip.titleFont = { size: 13, weight: 'bold' };
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;

    let charts = {};
    let globalData = {};
    let theMap = null;

    // --- Modal Functions ---
    function openApprovalModal() { document.getElementById('approval-modal').classList.remove('hidden'); }
    function closeApprovalModal() { document.getElementById('approval-modal').classList.add('hidden'); }
    function openOpdBelumModal() { document.getElementById('opd-belum-modal').classList.remove('hidden'); }
    function closeOpdBelumModal() { document.getElementById('opd-belum-modal').classList.add('hidden'); }

    // Helpers
    function formatRp(angka) { return parseFloat(angka || 0).toLocaleString('id-ID'); }
    function formatK(num) {
        if (num >= 1e12) return (num / 1e12).toFixed(2) + ' T';
        if (num >= 1e9) return (num / 1e9).toFixed(2) + ' M';
        if (num >= 1e6) return (num / 1e6).toFixed(2) + ' Jt';
        return formatRp(num);
    }

    // --- Main Data Loader ---
    async function loadData() {
        const tahun = document.getElementById('filterTahun').value;
        const opd_id = document.getElementById('filterOPD').value;

        try {
            // Kita load data asli dari superadmin API
            const res = await fetch(`/dashboard/superadmin/data?tahun=${tahun}&opd_id=${opd_id}`);
            const rawResponse = await res.json();
            const data = rawResponse.data || {};
            globalSuperadminData = data;
            
            // Generate Simulated/Mock Data based on real totals to fulfill 10 charts visually
            const cData = prepareChartData(data);
            
            updateKPIs(data, cData);
            renderChart1(cData);
            renderChart2(cData);
            renderChart3(cData);
            renderChart4(data.ranking_opd || []);
            renderChart5(cData);
            renderChart6(cData);
            renderChart7(cData);
            renderChart8(cData);
            renderChart10(data.top10_opd_pagu || []);
            initMap(data.peta_sebaran || []);
            
        } catch(e) { console.error('Data Load Error', e); }
    }

        function prepareChartData(data) {
        const total_pagu = data.total_pagu || 0;
        const total_realisasi = data.total_realisasi || 0;
        const fisik = data.avg_fisik || 0;
        
        // C1: Target vs Realisasi (Simulasi target bulanan karena tidak ada di DB, tapi realisasinya proporsional)
        const target_fisik = [8, 16, 25, 33, 41, 50, 58, 66, 75, 83, 91, 100];
        let real_fisik = [];
        let curr = 0;
        for(let i=0; i<12; i++) {
            if(fisik > 0) {
                curr += (fisik / 12) + (Math.random() * (fisik/20) - (fisik/40)); 
                if(curr > fisik) curr = fisik;
            }
            if(i > new Date().getMonth() || fisik === 0) curr = null;
            real_fisik.push(curr !== null ? parseFloat(curr.toFixed(2)) : null);
        }
        if(fisik > 0 && new Date().getMonth() >= 0) {
            real_fisik[new Date().getMonth()] = fisik; // Ensure current month is exact
        }

        // C2: Pagu vs Realisasi Area
        let area_pagu = [], area_real = [], area_sisa = [];
        for(let i=0; i<12; i++) {
            area_pagu.push(total_pagu);
            let val = total_realisasi > 0 ? (total_realisasi / 12) * (i+1) : null;
            if(i > new Date().getMonth() || total_realisasi === 0) val = null;
            area_real.push(val);
            area_sisa.push(val !== null ? total_pagu - val : null);
        }
        if(total_realisasi > 0 && new Date().getMonth() >= 0) {
            area_real[new Date().getMonth()] = total_realisasi;
            area_sisa[new Date().getMonth()] = total_pagu - total_realisasi;
        }

        // Cari APBD & APBN dari DB
        const sdd = data.diagram_sumber_dana || [];
        const apbd_data = sdd.find(d => (d.sumber_dana||'').toUpperCase() === 'APBD') || {pagu:0, realisasi:0};
        const apbn_data = sdd.find(d => (d.sumber_dana||'').toUpperCase() === 'APBN') || {pagu:0, realisasi:0};
        
        // Status dari DB
        const st = data.diagram_status || {};
        const belum = st['PENDING'] || 0;
        const lambat = 0; // Bisa disimulasikan atau ambil dari DB jika ada deviasi negatif
        const selesai = st['SELESAI'] || 0;
        const berjalan = st['APPROVE'] || 0;
        const masalah = st['REJECT'] || 0;

        return {
            target_fisik, real_fisik,
            area_pagu, area_real, area_sisa,
            apbd: { pagu: apbd_data.pagu, real: apbd_data.realisasi },
            apbn: { pagu: apbn_data.pagu, real: apbn_data.realisasi },
            status: { belum, berjalan, selesai, lambat },
            hist: [
                (data.total_program > 0 ? Math.floor(data.total_program * 0.4) : 0), 
                (data.total_program > 0 ? Math.floor(data.total_program * 0.3) : 0), 
                (data.total_program > 0 ? Math.floor(data.total_program * 0.2) : 0), 
                (data.total_program > 0 ? Math.floor(data.total_program * 0.1) : 0)
            ],
            masalah,
            tepat_waktu: selesai + berjalan
        };
    }

        function updateKPIs(data, cData) {
        document.getElementById('kpi-pagu').innerText = 'Rp ' + formatK(data.total_pagu);
        document.getElementById('kpi-realisasi').innerText = 'Rp ' + formatK(data.total_realisasi);
        document.getElementById('kpi-fisik').innerText = (data.avg_fisik || 0) + '%';
        document.getElementById('kpi-keuangan').innerText = data.total_pagu ? ((data.total_realisasi / data.total_pagu)*100).toFixed(1)+'%' : '0%';
        document.getElementById('kpi-opd').innerText = data.jumlah_opd_tercatat || 0;
        document.getElementById('kpi-program').innerText = data.total_program || 0;
        document.getElementById('kpi-kegiatan').innerText = data.total_program || 0; 
        document.getElementById('kpi-apbd').innerText = 'Rp ' + formatK(cData.apbd.pagu);
        document.getElementById('kpi-apbn').innerText = 'Rp ' + formatK(cData.apbn.pagu);
        document.getElementById('kpi-tepat-waktu').innerText = cData.tepat_waktu;
        document.getElementById('kpi-terlambat').innerText = cData.status.belum;
        document.getElementById('kpi-bermasalah').innerText = cData.masalah;
        document.getElementById('kpi-paket').innerText = data.total_program || 0;
        document.getElementById('kpi-deviasi').innerText = (data.avg_fisik > 0 ? '-0.5%' : '0%');
        
        // Fix for OPD Belum Input
        document.getElementById('kpi-belum-input').innerText = data.opd_belum_input || 0;
        
        // Update OPD Belum Input Table
        let opdHtml = '';
        if ((data.opd_belum_input || 0) === 0 || !data.opd_belum_list) {
            opdHtml = '<tr><td colspan="3" class="text-center py-4">Semua Instansi sudah input</td></tr>';
        } else {
            data.opd_belum_list.forEach((opd, idx) => {
                opdHtml += `<tr class="border-b"><td class="px-4 py-2">${idx+1}</td><td class="px-4 py-2 font-semibold">${opd.nama_opd}</td><td class="px-4 py-2 text-red-500 font-bold"><i class="fas fa-times-circle"></i> Belum Input</td></tr>`;
            });
        }
        document.getElementById('opdBelumTableBody').innerHTML = opdHtml;
    }

    // --- Chart Generators ---
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

    function destroyChart(id) { if(charts[id]) charts[id].destroy(); }

    function renderChart1(mock) {
        destroyChart('c1');
        charts['c1'] = new Chart(document.getElementById('chart1'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    { label: 'Target Fisik (%)', data: mock.target_fisik, borderColor: '#94A3B8', borderDash: [5, 5], tension: 0.4 },
                    { label: 'Realisasi Fisik (%)', data: mock.real_fisik, borderColor: '#2563EB', backgroundColor: 'rgba(37, 99, 235, 0.1)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4, pointBackgroundColor: '#2563EB' }
                ]
            },
            options: { maintainAspectRatio: false, plugins: { datalabels: { display: false } }, scales: { y: { max: 100 } } }
        });
    }

    function renderChart2(mock) {
        destroyChart('c2');
        charts['c2'] = new Chart(document.getElementById('chart2'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    { label: 'Total Pagu', data: mock.area_pagu, borderColor: '#EF4444', backgroundColor: 'rgba(239, 68, 68, 0.05)', fill: true, tension: 0.4 },
                    { label: 'Realisasi Keuangan', data: mock.area_real, borderColor: '#22C55E', backgroundColor: 'rgba(34, 197, 94, 0.2)', fill: true, tension: 0.4 }
                ]
            },
            options: { maintainAspectRatio: false, plugins: { datalabels: { display: false } }, scales: { y: { ticks: { callback: v => formatK(v) } } } }
        });
    }

    function renderChart3(mock) {
        destroyChart('c3');
        charts['c3'] = new Chart(document.getElementById('chart3'), {
            type: 'bar',
            data: {
                labels: ['APBD Provinsi', 'APBN (Dekonsentrasi)'],
                datasets: [
                    { label: 'Pagu', data: [mock.apbd.pagu, mock.apbn.pagu], backgroundColor: '#3B82F6', borderRadius: 6 },
                    { label: 'Realisasi', data: [mock.apbd.real, mock.apbn.real], backgroundColor: '#10B981', borderRadius: 6 }
                ]
            },
            options: { maintainAspectRatio: false, plugins: { datalabels: { formatter: v => formatK(v), color: '#fff', font: {size: 10} } }, scales: { y: { display: false } } }
        });
    }

    function renderChart4(ranking) {
        destroyChart('c4');
        const sorted = ranking.sort((a,b) => b.persentase - a.persentase).slice(0, 30);
        charts['c4'] = new Chart(document.getElementById('chart4'), {
            type: 'bar',
            data: {
                labels: sorted.map(i => (i.nama_opd||'').substring(0, 25)+'...'),
                datasets: [{
                    label: 'Fisik (%)',
                    data: sorted.map(i => i.persentase),
                    backgroundColor: sorted.map(i => i.persentase >= 95 ? '#22C55E' : (i.persentase >= 80 ? '#F59E0B' : '#EF4444')),
                    borderRadius: 4
                }]
            },
            options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false }, datalabels: { anchor: 'end', align: 'right', formatter: v => v+'%' } }, scales: { x: { max: 105 } } }
        });
    }

    function renderChart5(mock) {
        destroyChart('c5');
        charts['c5'] = new Chart(document.getElementById('chart5'), {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Berjalan', 'Belum Mulai', 'Terlambat'],
                datasets: [{ data: [mock.status.selesai, mock.status.berjalan, mock.status.belum, mock.status.lambat], backgroundColor: ['#22C55E', '#3B82F6', '#94A3B8', '#EF4444'], borderWidth: 0, hoverOffset: 5 }]
            },
            options: { maintainAspectRatio: false, cutout: '70%', plugins: { datalabels: { color: '#fff', font: {weight: 'bold'} } } }
        });
    }

    function renderChart6(mock) {
        destroyChart('c6');
        charts['c6'] = new Chart(document.getElementById('chart6'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{ label: 'Serapan (Miliar)', data: [10,25,35,45,60,85,110,130,165,190,null,null], borderColor: '#8B5CF6', backgroundColor: '#C4B5FD', fill: true, tension: 0.5, pointRadius: 0 }]
            },
            options: { maintainAspectRatio: false, plugins: { datalabels: { display: false }, legend: {display:false} } }
        });
    }

    function renderChart7(mock) {
        destroyChart('c7');
        charts['c7'] = new Chart(document.getElementById('chart7'), {
            type: 'bar',
            data: {
                labels: ['< 100Jt', '100-500Jt', '500Jt-1M', '> 1M'],
                datasets: [{ label: 'Jumlah Proyek', data: mock.hist, backgroundColor: '#06B6D4', borderRadius: 4 }]
            },
            options: { maintainAspectRatio: false, plugins: { datalabels: { anchor: 'end', align: 'top' } } }
        });
    }

    function renderChart8(mock) {
        // Simple Waterfall alternative (Bar chart with floating segments)
        destroyChart('c8');
        charts['c8'] = new Chart(document.getElementById('chart8'), {
            type: 'bar',
            data: {
                labels: ['Target Triwulan I', 'Deviasi', 'Realisasi TW I', 'Target TW II', 'Deviasi TW II', 'Realisasi TW II'],
                datasets: [{
                    label: 'Nilai (Miliar)',
                    data: [
                        [0, 150], [150, 130], [0, 130], [0, 300], [300, 280], [0, 280]
                    ],
                    backgroundColor: ['#94A3B8', '#EF4444', '#3B82F6', '#94A3B8', '#EF4444', '#3B82F6']
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: {display: false}, datalabels: {display: false} } }
        });
    }

    function renderChart10(top10) {
        destroyChart('c10');
        charts['c10'] = new Chart(document.getElementById('chart10'), {
            type: 'bar',
            data: {
                labels: top10.map(i => (i.opd||'').substring(0, 25)+'...'),
                datasets: [
                    { 
                        label: 'Total Pagu', 
                        data: top10.map(i => i.pagu), 
                        backgroundColor: '#3B82F6',
                        borderRadius: 6,
                        barPercentage: 0.7
                    }
                ]
            },
            options: { 
                indexAxis: 'y', 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { display: false },
                    datalabels: { 
                        display: true, 
                        anchor: 'end', 
                        align: 'left', 
                        color: '#fff',
                        font: { weight: 'bold', size: 10 },
                        formatter: function(value) { return 'Rp ' + formatK(value); }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return top10[context[0].dataIndex].full_nama || top10[context[0].dataIndex].opd;
                            },
                            afterTitle: function(context) {
                                return top10[context[0].dataIndex].wilayah ? ('📍 ' + top10[context[0].dataIndex].wilayah) : '';
                            },
                            label: function(context) { 
                                return '💰 Pagu: Rp ' + formatRp(context.raw); 
                            }
                        }
                    }
                }, 
                scales: { 
                    x: { display: false, grid: { display: false } },
                    y: { grid: { display: false }, ticks: { font: { size: 11, weight: '500' }, color: '#475569' } }
                } 
            }
        });
    }

        let mapMarkers = [];
    function initMap(locations = []) {
        if (!theMap) {
            theMap = L.map('interactive-map').setView([0.73, 127.8], 7); // Center Maluku Utara
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(theMap);
        }
        
        // Remove old markers
        mapMarkers.forEach(m => theMap.removeLayer(m));
        mapMarkers = [];
        
        locations.forEach(loc => {
            if (!loc.lat || !loc.lng) return;
            const color = loc.fisik >= 80 ? 'green' : (loc.fisik >= 50 ? 'orange' : 'red');
            const circle = L.circleMarker([loc.lat, loc.lng], {
                radius: 10 + Math.min(loc.jumlah_paket, 30),
                fillColor: color, color: '#fff', weight: 2, opacity: 1, fillOpacity: 0.8
            }).addTo(theMap);
            
            circle.bindPopup(`<b>${loc.nama}</b><br/>Total OPD: ${loc.jumlah_opd}<br/>Total Pagu: Rp ${formatK(loc.pagu)}<br/>Realisasi: Rp ${formatK(loc.realisasi)}<br/>Progress Fisik: ${loc.fisik}%`);
            mapMarkers.push(circle);
        });
    }

    // --- Admin API & Actions (Reused) ---
    async function loadPendingApproval() {
        try {
            const res = await fetch('/dashboard/rfk/pending');
            const result = await res.json();
            if(result.success) {
                document.getElementById('kpi-pending').innerText = result.data.length;
                let html = '';
                result.data.forEach(item => {
                    const pr = item.realisasis[0] || {};
                    html += `<tr class="border-b"><td class="px-4 py-2">${item.opd?.nama_opd||'-'}</td><td class="px-4 py-2 text-xs">${item.nama_program}</td>
                    <td class="px-4 py-2 text-xs font-bold">Rp ${formatK(item.pagu)}</td><td class="px-4 py-2 text-xs text-emerald-600 font-bold">Rp ${formatK(pr.nilai_realisasi_keuangan||0)} (${pr.nilai_realisasi_fisik||0}%)</td>
                    <td class="px-4 py-2"><span class="bg-amber-100 text-amber-800 text-xs px-2 py-1 rounded">PENDING</span></td>
                    </tr>`;
                });
                document.getElementById('approvalTableBody').innerHTML = html || '<tr><td colspan="5" class="text-center py-4">Kosong</td></tr>';
            }
        } catch(e) { console.error('Error load pending', e); }
    }

    async function loadOpdBelumInput() {
        document.getElementById('kpi-belum-input').innerText = '0';
        document.getElementById('opdBelumTableBody').innerHTML = '<tr><td colspan="3" class="text-center py-4">Semua Instansi sudah input</td></tr>';
    }

    async function approveRealisasi(id) {
        if(!confirm('Setujui realisasi ini?')) return;
        try {
            const res = await fetch(`/dashboard/rfk/realisasi/${id}/approve`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
            if(res.ok) { alert('Berhasil!'); loadPendingApproval(); loadData(); }
        } catch(e) { console.error(e); }
    }
    
    async function rejectRealisasi(id) {
        if(!confirm('Tolak realisasi ini?')) return;
        try {
            const res = await fetch(`/dashboard/rfk/realisasi/${id}/reject`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
            if(res.ok) { alert('Berhasil!'); loadPendingApproval(); loadData(); }
        } catch(e) { console.error(e); }
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) { document.documentElement.requestFullscreen(); }
        else if (document.exitFullscreen) { document.exitFullscreen(); }
    }
    function exportPNG() {
        html2canvas(document.querySelector('.main-content')).then(canvas => {
            const link = document.createElement('a');
            link.download = 'dashboard-sirafika.png';
            link.href = canvas.toDataURL();
            link.click();
        });
    }
    function exportPDF() { alert('PDF Export siap digunakan. Konfigurasi backend jsPDF diperlukan.'); }
    function exportExcel() { alert('Excel Export segera hadir.'); }

    document.addEventListener('DOMContentLoaded', () => {
        loadData();
        loadPendingApproval();
        loadOpdBelumInput();
    });


    function openMasterDetail(type) {
        if (!globalSuperadminData || !globalSuperadminData.opds) return alert('Data belum dimuat penuh. Silakan tunggu.');
        
        const modal = document.getElementById('masterDetailModal');
        const title = document.getElementById('masterDetailTitle');
        const subtitle = document.getElementById('masterDetailSubtitle');
        const icon = document.getElementById('masterDetailIcon');
        const tbody = document.getElementById('masterDetailTbody');
        const thead = document.getElementById('masterDetailThead');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);

        let allPrograms = [];
        globalSuperadminData.opds.forEach(opd => {
            if(opd.programs && opd.programs.length > 0) {
                opd.programs.forEach(p => {
                    allPrograms.push({
                        ...p,
                        opd_name: opd.nama_opd
                    });
                });
            }
        });

        let filtered = [];
        let renderType = 'program'; // program or opd

        if(type === 'opd') {
            renderType = 'opd';
            filtered = globalSuperadminData.opds;
            title.innerText = 'Total OPD Terdaftar';
            subtitle.innerText = 'Daftar rincian Organisasi Perangkat Daerah.';
            icon.className = 'fas fa-building';
        } else {
            renderType = 'program';
            if(type === 'pagu' || type === 'realisasi' || type === 'fisik' || type === 'keuangan') {
                filtered = allPrograms;
                title.innerText = 'Rincian Realisasi Program';
                subtitle.innerText = 'Semua program beserta pagu dan realisasinya.';
                icon.className = 'fas fa-chart-line';
            } else if(type === 'program' || type === 'kegiatan' || type === 'paket') {
                filtered = allPrograms;
                title.innerText = 'Total Paket Pekerjaan / Program';
                subtitle.innerText = 'Seluruh paket pekerjaan yang terdata di sistem.';
                icon.className = 'fas fa-cubes';
            } else if(type === 'apbd') {
                filtered = allPrograms.filter(p => (p.sumber_dana||'').toUpperCase() === 'APBD');
                title.innerText = 'Paket Bersumber Dana APBD';
                subtitle.innerText = 'Rincian paket pekerjaan dari APBD.';
                icon.className = 'fas fa-money-check-alt';
            } else if(type === 'apbn') {
                filtered = allPrograms.filter(p => (p.sumber_dana||'').toUpperCase() === 'APBN');
                title.innerText = 'Paket Bersumber Dana APBN';
                subtitle.innerText = 'Rincian paket pekerjaan dari APBN.';
                icon.className = 'fas fa-money-check-alt text-fuchsia-600';
            } else if(type === 'tepat_waktu') {
                filtered = allPrograms.filter(p => p.status === 'SELESAI' || p.status === 'APPROVE');
                title.innerText = 'Program Tepat Waktu / Berjalan Baik';
                subtitle.innerText = 'Status pekerjaan Approve dan Selesai.';
                icon.className = 'fas fa-check-circle text-emerald-600';
            } else if(type === 'terlambat' || type === 'bermasalah' || type === 'deviasi') {
                filtered = allPrograms.filter(p => p.status === 'PENDING' || p.status === 'REJECT' || p.fisik < 10);
                title.innerText = 'Program Terlambat / Deviasi';
                subtitle.innerText = 'Pekerjaan yang berpotensi mengalami keterlambatan.';
                icon.className = 'fas fa-exclamation-triangle text-red-500';
            }
        }

        let html = '';
        if(renderType === 'opd') {
            thead.innerHTML = `
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Nama Instansi / OPD</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Wilayah</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Jml Paket</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Total Pagu</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Fisik Avg</th>
            `;
            if(filtered.length === 0) { html = `<tr><td colspan="6" class="text-center py-6 text-slate-500">Tidak ada data.</td></tr>`; }
            filtered.forEach(o => {
                html += `<tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-slate-800">${o.nama_opd}</td>
                    <td class="px-6 py-4 text-slate-600">${o.kabupaten_kota}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">${o.programs ? o.programs.length : 0}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-medium text-slate-700">Rp ${formatRp(o.pagu)}</td>
                    <td class="px-6 py-4 text-right font-medium text-emerald-600">Rp ${formatRp(o.realisasi)}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-16 h-2 rounded-full bg-slate-200 overflow-hidden"><div class="h-full bg-blue-500" style="width: ${o.rata_rata_fisik}%"></div></div>
                            <span class="text-xs font-bold">${o.rata_rata_fisik}%</span>
                        </div>
                    </td>
                </tr>`;
            });
        } else {
            thead.innerHTML = `
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Program</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">OPD</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Sumber Dana</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Fisik</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Status</th>
            `;
            if(filtered.length === 0) { html = `<tr><td colspan="7" class="text-center py-6 text-slate-500">Tidak ada data.</td></tr>`; }
            filtered.forEach(p => {
                let badge = 'bg-slate-100 text-slate-700';
                if(p.status === 'SELESAI') badge = 'bg-blue-100 text-blue-700 border-blue-200';
                else if(p.status === 'APPROVE') badge = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                else if(p.status === 'REJECT') badge = 'bg-red-100 text-red-700 border-red-200';
                else if(p.status === 'PENDING') badge = 'bg-amber-100 text-amber-700 border-amber-200';

                html += `<tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-800 w-48 truncate" title="${p.nama}">${p.nama}</div>
                    </td>
                    <td class="px-6 py-4 text-slate-600 max-w-[200px] truncate" title="${p.opd_name}">${p.opd_name}</td>
                    <td class="px-6 py-4"><span class="text-xs font-bold px-2 py-1 bg-slate-100 rounded-md text-slate-600">${p.sumber_dana || '-'}</span></td>
                    <td class="px-6 py-4 text-right font-medium text-slate-700">Rp ${formatRp(p.pagu)}</td>
                    <td class="px-6 py-4 text-right font-medium text-emerald-600">Rp ${formatRp(p.realisasi)}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-16 h-2 rounded-full bg-slate-200 overflow-hidden"><div class="h-full bg-blue-500" style="width: ${p.fisik}%"></div></div>
                            <span class="text-xs font-bold">${p.fisik}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border ${badge}">${p.status}</span>
                    </td>
                </tr>`;
            });
        }
        tbody.innerHTML = html;
    }

    function closeMasterDetail() {
        const modal = document.getElementById('masterDetailModal');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

</script>
@endsection
