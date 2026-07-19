@extends('dashboard.layout.app', ['title' => 'Dashboard Super Administrator'])

@section('content')
<!-- Google Fonts: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif !important;
        background-color: #F8FAFC !important;
    }
    .super-card {
        background: #FFFFFF;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(226, 232, 240, 0.6);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .super-card:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
    }
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #F1F5F9; border-radius: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 8px; }
</style>

<div class="main-content min-h-screen pb-12 pt-6 px-4 md:px-8 max-w-screen-2xl mx-auto">

    <!-- Header Row -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Executive Insight</h1>
            <p class="text-slate-500 mt-1 font-medium">Pemantauan Serapan Anggaran & Realisasi Kinerja</p>
            <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-100 shadow-sm">
                <i class="fas fa-clock"></i> <span id="last-updated-text">Memuat data terakhir...</span>
            </div>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-200">
            <select id="filterTahun" class="px-4 py-2 bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 min-w-[120px]" onchange="loadSuperadminData()">
                <option value="">Tahun</option>
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
            </select>
            <div class="w-px bg-slate-200 my-2"></div>
            <select id="filterOPD" class="px-4 py-2 bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500 min-w-[200px]" onchange="loadSuperadminData()">
                <option value="">Seluruh Organisasi (OPD)</option>
                @foreach($opds as $opd)
                    <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                @endforeach
            </select>
            <button onclick="loadSuperadminData()" id="btn-refresh" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-colors shadow-md flex items-center justify-center">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <!-- Top Row: Realisasi Harian, Bulanan, Tahunan, Jumlah OPD -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <!-- Harian -->
        <div class="super-card p-6 flex flex-col justify-between relative overflow-hidden group cursor-default border-t-4 border-t-blue-500">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50/50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-blue-500 uppercase tracking-wider">Realisasi SI-RAFIKA (Harian)</p>
                <div class="stat-icon-wrapper bg-blue-50 text-blue-500 shadow-sm border border-blue-100 group-hover:-translate-y-1 transition-transform">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2" id="realisasi-harian">Rp 0</h3>
            <span class="text-left text-xs font-semibold text-slate-400">Total serapan hari ini</span>
        </div>

        <!-- Bulanan -->
        <div class="super-card p-6 flex flex-col justify-between relative overflow-hidden group cursor-default border-t-4 border-t-indigo-500">
            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50/50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-indigo-500 uppercase tracking-wider">Realisasi SI-RAFIKA (Bulanan)</p>
                <div class="stat-icon-wrapper bg-indigo-50 text-indigo-500 shadow-sm border border-indigo-100 group-hover:-translate-y-1 transition-transform">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2" id="realisasi-bulanan">Rp 0</h3>
            <span class="text-left text-xs font-semibold text-slate-400">Total serapan bulan ini</span>
        </div>

        <!-- Tahunan -->
        <div class="super-card p-6 flex flex-col justify-between relative overflow-hidden group cursor-default border-t-4 border-t-purple-500">
            <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50/50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-purple-500 uppercase tracking-wider">Realisasi SI-RAFIKA (Tahunan)</p>
                <div class="stat-icon-wrapper bg-purple-50 text-purple-500 shadow-sm border border-purple-100 group-hover:-translate-y-1 transition-transform">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2" id="realisasi-tahunan">Rp 0</h3>
            <span class="text-left text-xs font-semibold text-slate-400">Total serapan tahun ini</span>
        </div>

        <!-- Jumlah OPD -->
        <div class="super-card p-6 flex flex-col justify-between relative overflow-hidden group cursor-default border-t-4 border-t-emerald-500">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50/50 rounded-bl-full -z-10 group-hover:scale-110 transition-transform duration-500"></div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider">OPD Tercatat</p>
                <div class="stat-icon-wrapper bg-emerald-50 text-emerald-500 shadow-sm border border-emerald-100 group-hover:-translate-y-1 transition-transform">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2" id="jumlah-opd-tercatat">0 Instansi</h3>
            <span class="text-left text-xs font-semibold text-slate-400">Seluruh entitas pelapor aktif</span>
        </div>
    </div>

    <!-- 4 Main Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <!-- Pagu -->
        <div class="super-card p-6 flex flex-col justify-between group cursor-pointer" onclick="openGlobalSummaryModal()">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Pagu</p>
                <div class="stat-icon-wrapper bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2" id="global-pagu">Rp 0</h3>
            <span class="text-left text-xs font-semibold text-indigo-600 group-hover:text-indigo-800 flex items-center gap-1 transition-colors">
                Lihat Rekap Seluruh OPD <i class="fas fa-arrow-right"></i>
            </span>
        </div>

        <!-- Realisasi -->
        <div class="super-card p-6 flex flex-col justify-between group cursor-pointer" onclick="openGlobalSummaryModal()">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Realisasi Keuangan</p>
                <div class="stat-icon-wrapper bg-emerald-50 text-emerald-500 group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2" id="global-realisasi">Rp 0</h3>
            <span class="text-left text-xs font-semibold text-emerald-600 group-hover:text-emerald-800 flex items-center gap-1 transition-colors">
                Lihat Rekap Seluruh OPD <i class="fas fa-arrow-right"></i>
            </span>
        </div>

        <!-- Sisa -->
        <div class="super-card p-6 flex flex-col justify-between group cursor-pointer" onclick="openGlobalSummaryModal()">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-rose-500 uppercase tracking-wider">Sisa Anggaran</p>
                <div class="stat-icon-wrapper bg-rose-50 text-rose-500 group-hover:scale-110 transition-transform">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2" id="global-sisa">Rp 0</h3>
            <span class="text-left text-xs font-semibold text-rose-600 group-hover:text-rose-800 flex items-center gap-1 transition-colors">
                Lihat Rekap Seluruh OPD <i class="fas fa-arrow-right"></i>
            </span>
        </div>

        <!-- Fisik -->
        <div class="super-card p-6 flex flex-col justify-between group cursor-pointer" onclick="openGlobalSummaryModal()">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-indigo-500 uppercase tracking-wider">Rata-rata Fisik</p>
                <div class="stat-icon-wrapper bg-indigo-50 text-indigo-500 group-hover:scale-110 transition-transform">
                    <i class="fas fa-hammer"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2" id="global-fisik">0%</h3>
            <span class="text-left text-xs font-semibold text-indigo-600 group-hover:text-indigo-800 flex items-center gap-1 transition-colors">
                Lihat Rekap Seluruh OPD <i class="fas fa-arrow-right"></i>
            </span>
        </div>
    </div>



    <!-- Analytics Charts Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

        <!-- Status Validasi RFK (Pie Chart) -->
        <div class="super-card p-6 xl:col-span-1 flex flex-col justify-center relative">
            <h3 class="text-lg font-bold text-slate-800 mb-1">Status Validasi SI-RAFIKA</h3>
            <p class="text-xs text-slate-500 mb-6 font-medium">Berdasarkan rincian data program OPD</p>

            <div class="relative h-48 w-full mb-6">
                <canvas id="status-chart"></canvas>
            </div>

            <div class="flex flex-col gap-3 mb-6">
                <div class="flex justify-between items-center p-2.5 rounded-lg bg-blue-50 border border-blue-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500 shadow-sm"></span>
                        <span class="text-sm font-bold text-blue-700">Selesai (Tuntas)</span>
                    </div>
                    <span class="text-sm font-black text-blue-700 bg-blue-100 px-2 py-0.5 rounded" id="stat-selesai">0</span>
                </div>
                <div class="flex justify-between items-center p-2.5 rounded-lg bg-emerald-50 border border-emerald-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm"></span>
                        <span class="text-sm font-bold text-emerald-700">Telah Disetujui (Approve)</span>
                    </div>
                    <span class="text-sm font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded" id="stat-approve">0</span>
                </div>
                <div class="flex justify-between items-center p-2.5 rounded-lg bg-amber-50 border border-amber-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm"></span>
                        <span class="text-sm font-bold text-amber-700">Masih Menunggu (Pending)</span>
                    </div>
                    <span class="text-sm font-black text-amber-700 bg-amber-100 px-2 py-0.5 rounded" id="stat-pending">0</span>
                </div>
                <div class="flex justify-between items-center p-2.5 rounded-lg bg-rose-50 border border-rose-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500 shadow-sm"></span>
                        <span class="text-sm font-bold text-rose-700">Ditolak / Perbaikan (Reject)</span>
                    </div>
                    <span class="text-sm font-black text-rose-700 bg-rose-100 px-2 py-0.5 rounded" id="stat-reject">0</span>
                </div>
            </div>

            <button onclick="openStatusBreakdownModal()" class="w-full py-2.5 bg-slate-800 text-white font-semibold text-sm rounded-xl hover:bg-slate-700 transition-colors shadow-md flex justify-center items-center gap-2">
                <i class="fas fa-list-alt"></i> Lihat Detail Status RFK
            </button>
        </div>

        <!-- OPD Comparison Stacked Bar -->
        <div class="super-card p-6 xl:col-span-2 flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Progress Fisik vs Keuangan per OPD</h3>
                    <p class="text-sm text-slate-500 mt-1">Korelasi antara pencairan uang (Bar) dengan rata-rata progres fisik di lapangan (Line).</p>
                </div>
                <button onclick="openGlobalSummaryModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition-colors border border-slate-200">
                    <i class="fas fa-table mr-1"></i> Data Seluruh OPD
                </button>
            </div>
            <div class="chart-container flex-grow">
                <canvas id="bar-chart-opd"></canvas>
            </div>
        </div>

    </div>

    <!-- Advanced Analytics Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

        <!-- Sumber Dana Chart (Col-span-2) -->
        <div class="super-card p-6 xl:col-span-2 relative overflow-hidden flex flex-col">
            <div class="absolute top-0 left-0 w-2 h-full bg-blue-500"></div>
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 pl-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Alokasi & Penyerapan Anggaran per Sumber Dana</h3>
                    <p class="text-sm text-slate-500 mt-1">Analisis distribusi pagu dan realisasi dari APBN, APBD, dsb.</p>
                </div>
                <button onclick="openMatrixModal()" class="mt-4 lg:mt-0 px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 text-xs font-bold rounded-xl transition-colors shadow-sm flex items-center gap-2">
                    <i class="fas fa-th"></i> Buka Matrix per OPD
                </button>
            </div>
            <div class="chart-container flex-grow" style="min-height: 300px;">
                <canvas id="sumber-dana-chart"></canvas>
            </div>
        </div>

        <!-- Radar Kepatuhan (Col-span-1) -->
        <div class="super-card p-6 xl:col-span-1 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-radar text-rose-500 mr-2"></i>Radar Kepatuhan</h3>
                <p class="text-xs text-slate-500 mt-1">Top 5 OPD dengan frekuensi penolakan (REJECT) terbanyak berdasarkan riwayat verifikasi.</p>

                <div class="mt-5 space-y-3" id="reject-list-container">
                    <div class="text-center py-6 text-slate-400 text-sm"><i class="fas fa-circle-notch fa-spin"></i> Memuat...</div>
                </div>
            </div>

            <div class="mt-6 p-5 bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-800 rounded-2xl flex items-center justify-between shadow-2xl border border-indigo-500/30 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-10 -mt-10 blur-2xl"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mb-1">Skor Efektivitas Keuangan</p>
                    <h4 class="text-3xl font-black" id="efektivitas-score">0%</h4>
                </div>
                <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center text-3xl backdrop-blur-md border border-white/20 relative z-10 shadow-inner">
                    <i class="fas fa-tachometer-alt text-indigo-300"></i>
                </div>
            </div>
        </div>
    </div> <!-- Close Advanced Analytics Row -->

    <!-- Row: TOP 10 OPD Pagu Terbesar -->
    <div class="grid grid-cols-1 mb-8">
        <div class="super-card p-6 relative overflow-hidden flex flex-col">
            <div class="absolute top-0 left-0 w-2 h-full bg-cyan-500"></div>
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 pl-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-chart-bar text-cyan-500 mr-2"></i>TOP 10 OPD Dengan Pagu Terbesar</h3>
                    <p class="text-sm text-slate-500 mt-1">Daftar 10 Instansi dengan alokasi anggaran (Pagu) tertinggi.</p>
                </div>
            </div>
            <div class="chart-container flex-grow" style="min-height: 350px;">
                <canvas id="top10-opd-pagu-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Row: Top 10 Paket & Traffic Light -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <!-- Top 10 Paket (Col-span-2) -->
        <div class="super-card p-6 xl:col-span-2 flex flex-col relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full -z-10"></div>
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-crown text-amber-500 mr-2"></i>Top 10 Paket Anggaran Terbesar</h3>
                    <p class="text-sm text-slate-500 mt-1">Daftar program "Sultan" dengan pagu alokasi tertinggi di seluruh instansi.</p>
                </div>
            </div>
            <div class="flex-grow overflow-y-auto custom-scrollbar pr-2 mt-4" style="max-height: 400px;">
                <div id="top10-paket-list" class="space-y-3">
                    <div class="text-center py-4 text-slate-400">Memuat data...</div>
                </div>
            </div>
        </div>

        <!-- Traffic Light OPD (Col-span-1) -->
        <div class="super-card p-6 xl:col-span-1 flex flex-col justify-between items-center text-center">
            <div class="w-full">
                <h3 class="text-lg font-bold text-slate-800">Traffic Light Penyerapan</h3>
                <p class="text-xs text-slate-500 mt-1">Status Kecepatan Realisasi Anggaran OPD</p>
            </div>
            <div class="flex flex-row justify-center gap-4 mt-6 w-full max-w-sm mx-auto bg-slate-800 p-5 rounded-3xl shadow-inner border-[4px] border-slate-700">
                <!-- Hijau -->
                <div class="relative group cursor-pointer flex-1 flex flex-col items-center" onclick="filterOpdGridByTraffic('hijau')">
                    <div class="w-16 h-16 rounded-full bg-emerald-500 border-[3px] border-slate-600 shadow-[0_0_15px_#10B981] flex items-center justify-center transition-transform transform group-hover:scale-110">
                        <span class="text-white font-black text-xl" id="tl-hijau">0</span>
                    </div>
                    <p class="text-[9px] text-emerald-400 font-bold uppercase tracking-wider mt-3 text-center leading-tight">Optimal<br>(≥ 90%)</p>
                </div>
                <!-- Kuning -->
                <div class="relative group cursor-pointer flex-1 flex flex-col items-center" onclick="filterOpdGridByTraffic('kuning')">
                    <div class="w-16 h-16 rounded-full bg-amber-500 border-[3px] border-slate-600 shadow-[0_0_15px_#F59E0B] flex items-center justify-center transition-transform transform group-hover:scale-110">
                        <span class="text-white font-black text-xl" id="tl-kuning">0</span>
                    </div>
                    <p class="text-[9px] text-amber-400 font-bold uppercase tracking-wider mt-3 text-center leading-tight">Waspada<br>(70-89%)</p>
                </div>
                <!-- Merah -->
                <div class="relative group cursor-pointer flex-1 flex flex-col items-center" onclick="filterOpdGridByTraffic('merah')">
                    <div class="w-16 h-16 rounded-full bg-rose-500 border-[3px] border-slate-600 shadow-[0_0_15px_#EF4444] flex items-center justify-center transition-transform transform group-hover:scale-110">
                        <span class="text-white font-black text-xl" id="tl-merah">0</span>
                    </div>
                    <p class="text-[9px] text-rose-400 font-bold uppercase tracking-wider mt-3 text-center leading-tight">Kritis<br>(< 70%)</p>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-4 italic">*Klik lampu untuk filter OPD di bawah</p>
        </div>
    </div>

    <!-- Row: Ranking OPD & Program Ekstrem -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">

        <!-- Ranking OPD -->
        <div class="super-card p-0 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-800 to-slate-900">
                <h3 class="text-lg font-bold text-white"><i class="fas fa-trophy text-yellow-400 mr-2"></i>Leaderboard Klasemen OPD</h3>
                <p class="text-sm text-slate-300 mt-1">Peringkat performa serapan anggaran instansi dari tertinggi hingga terendah.</p>
            </div>
            <div class="overflow-y-auto custom-scrollbar p-3 sm:p-5 bg-slate-50/50" style="height: 400px;">
                <div id="ranking-opd-list" class="space-y-3">
                    <div class="text-center py-4 text-slate-400">Memuat data...</div>
                </div>
            </div>
        </div>

        <!-- Serapan Per Program -->
        <div class="super-card p-6 flex flex-col relative">
            <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-balance-scale text-indigo-500 mr-2"></i>Sorotan Ekstrem Program</h3>
                    <p class="text-sm text-slate-500 mt-1">5 Program Serapan Tertinggi & Terendah.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-[400px] overflow-y-auto custom-scrollbar pr-2">
                <!-- Kiri: Tertinggi -->
                <div>
                    <h4 class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-4 bg-emerald-50 py-2 px-3 rounded text-center">Top 5 Tertinggi</h4>
                    <div class="space-y-3" id="program-tertinggi-list"></div>
                </div>
                <!-- Kanan: Terendah -->
                <div>
                    <h4 class="text-xs font-black text-rose-600 uppercase tracking-widest mb-4 bg-rose-50 py-2 px-3 rounded text-center">Top 5 Terendah</h4>
                    <div class="space-y-3" id="program-terendah-list"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- OPD Detail Grid Title -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
            <i class="fas fa-chart-pie text-indigo-500"></i> Analisis Kinerja per Instansi (OPD)
        </h2>
        <span id="opd-count-badge" class="px-3 py-1 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-full shadow-sm">0 OPD</span>
    </div>

    <!-- OPD Grid -->
    <div id="opd-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- Loading -->
        <div class="col-span-full text-center py-12">
            <i class="fas fa-circle-notch fa-spin text-4xl text-indigo-400 mb-4"></i>
            <p class="text-slate-500 font-medium">Menganalisis data mendalam...</p>
        </div>
    </div>
</div>


<!-- ============================================== -->
<!-- MODALS (Using Inline Styles for 100% Reliability) -->
<!-- ============================================== -->

<!-- 1. MODAL DETAIL PROGRAM PER OPD -->
<div id="program-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; justify-content: center; align-items: center; padding: 1rem;">
    <!-- Backdrop -->
    <div id="program-modal-backdrop" onclick="closeModal('program-modal')" style="position: absolute; inset: 0; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s ease;"></div>

    <!-- Content -->
    <div id="program-modal-content" class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[85vh] flex flex-col relative" style="opacity: 0; transform: scale(0.95); transition: all 0.3s ease;">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-start bg-slate-50 rounded-t-2xl">
            <div>
                <h3 class="text-xl font-bold text-slate-800 leading-tight" id="modal-opd-name">Nama OPD</h3>
                <p class="text-sm text-slate-500 mt-1 font-medium"><i class="fas fa-stream mr-1"></i> Data Seluruh Program & Status RFK</p>
            </div>
            <button onclick="closeModal('program-modal')" class="h-10 w-10 bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-full flex items-center justify-center transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-0 overflow-y-auto flex-grow bg-white custom-scrollbar rounded-b-2xl">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 sticky top-0 shadow-sm z-10">
                    <tr>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Program & Kode</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Kategori & Sub Program</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Sumber Dana</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Kategori Anggaran</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu Anggaran</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi Keuangan</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Fisik</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Status Validasi</th>
                    </tr>
                </thead>
                <tbody id="modal-program-body" class="divide-y divide-slate-100">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 2. MODAL GLOBAL SUMMARY (REKAP SELURUH OPD) -->
<div id="global-summary-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; justify-content: center; align-items: center; padding: 1rem;">
    <!-- Backdrop -->
    <div id="global-summary-modal-backdrop" onclick="closeModal('global-summary-modal')" style="position: absolute; inset: 0; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s ease;"></div>

    <!-- Content -->
    <div id="global-summary-modal-content" class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[85vh] flex flex-col relative" style="opacity: 0; transform: scale(0.95); transition: all 0.3s ease;">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-start bg-indigo-50 rounded-t-2xl">
            <div>
                <h3 class="text-xl font-bold text-indigo-900 leading-tight">Rekapitulasi Anggaran Seluruh OPD</h3>
                <p class="text-sm text-indigo-600 mt-1 font-medium"><i class="fas fa-globe mr-1"></i> Total Kumulatif Pagu, Realisasi, Sisa, & Progres Fisik</p>
            </div>
            <button onclick="closeModal('global-summary-modal')" class="h-10 w-10 bg-white border border-indigo-200 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-100 rounded-full flex items-center justify-center transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-0 overflow-y-auto flex-grow bg-white custom-scrollbar rounded-b-2xl">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-600 sticky top-0 shadow-sm z-10">
                    <tr>
                        <th class="px-8 py-4 font-bold uppercase tracking-wider text-xs">Nama Organisasi (OPD)</th>
                        <th class="px-8 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu Anggaran</th>
                        <th class="px-8 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi Keuangan</th>
                        <th class="px-8 py-4 font-bold uppercase tracking-wider text-xs text-right">Sisa Anggaran</th>
                        <th class="px-8 py-4 font-bold uppercase tracking-wider text-xs text-center">Rata-rata Fisik</th>
                    </tr>
                </thead>
                <tbody id="global-summary-body" class="divide-y divide-slate-100">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 3. MODAL STATUS BREAKDOWN (RINCIAN VALIDASI) -->
<div id="status-breakdown-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; justify-content: center; align-items: center; padding: 1rem;">
    <!-- Backdrop -->
    <div id="status-breakdown-modal-backdrop" onclick="closeModal('status-breakdown-modal')" style="position: absolute; inset: 0; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s ease;"></div>

    <!-- Content -->
    <div id="status-breakdown-modal-content" class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl max-h-[90vh] flex flex-col relative" style="opacity: 0; transform: scale(0.95); transition: all 0.3s ease;">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-start bg-slate-800 rounded-t-2xl">
            <div>
                <h3 class="text-xl font-bold text-white leading-tight">Database Status SI-RAFIKA</h3>
                <p class="text-sm text-slate-300 mt-1 font-medium"><i class="fas fa-search-dollar mr-1"></i> Analisis Detil Program Berdasarkan Status Validasi</p>
            </div>
            <button onclick="closeModal('status-breakdown-modal')" class="h-10 w-10 bg-slate-700 border border-slate-600 text-slate-300 hover:text-white hover:bg-rose-500 hover:border-rose-500 rounded-full flex items-center justify-center transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Filter Tabs -->
        <div class="px-8 py-4 bg-slate-50 border-b border-slate-200 flex gap-4">
            <button onclick="filterStatusModal('ALL')" id="tab-status-ALL" class="px-5 py-2 bg-slate-800 text-white rounded-lg text-sm font-bold shadow-sm transition-colors">Semua Status</button>
            <button onclick="filterStatusModal('SELESAI')" id="tab-status-SELESAI" class="px-5 py-2 bg-white text-blue-600 border border-blue-200 hover:bg-blue-50 rounded-lg text-sm font-bold transition-colors">Selesai</button>
            <button onclick="filterStatusModal('APPROVE')" id="tab-status-APPROVE" class="px-5 py-2 bg-white text-emerald-600 border border-emerald-200 hover:bg-emerald-50 rounded-lg text-sm font-bold transition-colors">Approve</button>
            <button onclick="filterStatusModal('PENDING')" id="tab-status-PENDING" class="px-5 py-2 bg-white text-amber-600 border border-amber-200 hover:bg-amber-50 rounded-lg text-sm font-bold transition-colors">Pending</button>
            <button onclick="filterStatusModal('REJECT')" id="tab-status-REJECT" class="px-5 py-2 bg-white text-rose-600 border border-rose-200 hover:bg-rose-50 rounded-lg text-sm font-bold transition-colors">Reject</button>
        </div>

        <div class="p-0 overflow-y-auto flex-grow bg-white custom-scrollbar rounded-b-2xl">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-100 text-slate-600 sticky top-0 shadow-sm z-10">
                    <tr>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Instansi (OPD)</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Program & Kode</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Kategori & Sub Program</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Sumber Dana</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Kategori Anggaran</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu Anggaran</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi & Fisik</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Status Validasi</th>
                    </tr>
                </thead>
                <tbody id="status-breakdown-body" class="divide-y divide-slate-100">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 4. MODAL MATRIX SUMBER DANA PER OPD -->
<div id="matrix-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; justify-content: center; align-items: center; padding: 1rem;">
    <!-- Backdrop -->
    <div id="matrix-modal-backdrop" onclick="closeModal('matrix-modal')" style="position: absolute; inset: 0; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); opacity: 0; transition: opacity 0.3s ease;"></div>

    <!-- Content -->
    <div id="matrix-modal-content" class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl max-h-[90vh] flex flex-col relative" style="opacity: 0; transform: scale(0.95); transition: all 0.3s ease;">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-start bg-slate-900 rounded-t-2xl">
            <div>
                <h3 class="text-xl font-bold text-white leading-tight">Matrix Realisasi per Sumber Dana</h3>
                <p class="text-sm text-slate-300 mt-1 font-medium"><i class="fas fa-th-large mr-1"></i> Analisa Mendalam Pencairan APBN, APBD, dan lainnya untuk tiap Instansi</p>
            </div>
            <button onclick="closeModal('matrix-modal')" class="h-10 w-10 bg-slate-800 border border-slate-700 text-slate-300 hover:text-white hover:bg-rose-500 hover:border-rose-500 rounded-full flex items-center justify-center transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-0 overflow-y-auto flex-grow bg-slate-50 custom-scrollbar rounded-b-2xl p-6">
            <div id="matrix-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Populated by JS -->
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    let superadminData = [];
    let allProgramsFlat = [];
    let sumberDanaData = [];
    let doughnutInstances = {};
    let mainBarChart = null;
    let statusChart = null;
    let sumberDanaChart = null;
    let top10OpdPaguChart = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadSuperadminData();
    });

    const formatRp = (angka) => {
        return formatRupiahManual(angka || 0);
    };

    const formatK = (angka) => {
        let val = angka || 0;
        if(val >= 1000000000) return (val / 1000000000).toFixed(1) + 'M';
        if(val >= 1000000) return (val / 1000000).toFixed(1) + 'Jt';
        return formatRp(val);
    };

    function getBadgeClass(status) {
        if(status === 'SELESAI') return 'bg-blue-100 text-blue-700 border-blue-200';
        if(status === 'APPROVE') return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        if(status === 'REJECT') return 'bg-rose-100 text-rose-700 border-rose-200';
        return 'bg-amber-100 text-amber-700 border-amber-200';
    }

    /* Modal Controller Functions (Foolproof Inline Styles) */
    function openModal(modalId) {
        try {
            const modal = document.getElementById(modalId);
            const backdrop = document.getElementById(modalId + '-backdrop');
            const content = document.getElementById(modalId + '-content');

            modal.style.display = 'flex';
            void modal.offsetWidth; // Force browser reflow

            backdrop.style.opacity = '1';
            content.style.opacity = '1';
            content.style.transform = 'scale(1)';

            document.body.style.overflow = 'hidden';
        } catch (error) {
            console.error("Error opening modal: ", error);
        }
    }

    function closeModal(modalId) {
        try {
            const modal = document.getElementById(modalId);
            const backdrop = document.getElementById(modalId + '-backdrop');
            const content = document.getElementById(modalId + '-content');

            backdrop.style.opacity = '0';
            content.style.opacity = '0';
            content.style.transform = 'scale(0.95)';

            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        } catch (error) {
            console.error("Error closing modal: ", error);
        }
    }


    async function loadSuperadminData() {
        const tahun = document.getElementById('filterTahun').value;
        const opdId = document.getElementById('filterOPD').value;
        const btn = document.getElementById('btn-refresh');

        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';

        try {
            const res = await fetch(`/dashboard/superadmin/data?tahun=${tahun}&opd_id=${opdId}`);
            const result = await res.json();

            if (result.success) {
                const data = result.data;
                superadminData = data.opds || [];
                sumberDanaData = data.diagram_sumber_dana || [];

                // Flatten programs
                allProgramsFlat = [];
                superadminData.forEach(opd => {
                    if (opd.programs && Array.isArray(opd.programs)) {
                        opd.programs.forEach(prog => {
                            allProgramsFlat.push({
                                ...prog,
                                nama_opd: opd.nama_opd || 'Tanpa Nama OPD'
                            });
                        });
                    }
                });

                document.getElementById('last-updated-text').innerText = 'Diperbarui: ' + (data.last_updated_at || '-');
                document.getElementById('global-pagu').innerText = 'Rp ' + formatK(data.total_pagu);
                document.getElementById('global-realisasi').innerText = 'Rp ' + formatK(data.total_realisasi);
                document.getElementById('global-sisa').innerText = 'Rp ' + formatK(data.total_sisa_pagu);
                document.getElementById('global-fisik').innerText = (data.avg_fisik || 0) + '%';

                // Top Row Realisasi & OPD
                document.getElementById('realisasi-harian').innerText = 'Rp ' + formatK(data.realisasi_harian || 0);
                document.getElementById('realisasi-bulanan').innerText = 'Rp ' + formatK(data.realisasi_bulanan || 0);
                document.getElementById('realisasi-tahunan').innerText = 'Rp ' + formatK(data.realisasi_tahunan || 0);
                document.getElementById('jumlah-opd-tercatat').innerText = (data.jumlah_opd_tercatat || 0) + ' Instansi';
                document.getElementById('opd-count-badge').innerText = superadminData.length + ' OPD';

                // Efektivitas Score
                const efektivitas = data.total_pagu > 0 ? ((data.total_realisasi / data.total_pagu) * 100).toFixed(1) : 0;
                document.getElementById('efektivitas-score').innerText = efektivitas + '%';

                if (data.diagram_status) {
                    document.getElementById('stat-selesai').innerText = data.diagram_status.SELESAI || 0;
                    document.getElementById('stat-approve').innerText = data.diagram_status.APPROVE || 0;
                    document.getElementById('stat-pending').innerText = data.diagram_status.PENDING || 0;
                    document.getElementById('stat-reject').innerText = data.diagram_status.REJECT || 0;
                    renderStatusChart(data.diagram_status);
                }

                // Traffic Light
                if (data.traffic_light) {
                    document.getElementById('tl-hijau').innerText = data.traffic_light.hijau || 0;
                    document.getElementById('tl-kuning').innerText = data.traffic_light.kuning || 0;
                    document.getElementById('tl-merah').innerText = data.traffic_light.merah || 0;
                }

                if(data.diagram_sumber_dana) {
                    renderSumberDanaChart();
                }

                if(data.top10_opd_pagu) {
                    renderTop10OpdPaguChart(data.top10_opd_pagu);
                }

                // Render Ranking
                renderRankingOpd(data.ranking_opd || []);

                // Render Top 10 Paket
                renderTop10Paket(data.top_10_paket || []);

                // Render Serapan Ekstrem
                renderProgramEkstrem(data.serapan_tertinggi || [], data.serapan_terendah || []);

                renderSumberDanaChart();
                renderMainComboChart();
                renderOpdGrid();
                renderRejectRadar(data.top_reject_opds || []);
            }
        } catch (e) {
            console.error("Failed to load Superadmin Data:", e);
        } finally {
            btn.innerHTML = '<i class="fas fa-sync-alt"></i>';
        }
    }

    // --- Modal Populate Functions ---

    window.openProgramModalByOpdId = function(opdId) {
        try {
            const opd = superadminData.find(o => o.id == opdId) || allProgramsFlat.find(p => p.opd_id == opdId)?.opd;
            // In case it's not found directly (should be in superadminData though).
            // Actually, superadminData has the full list since it's restored after filter.
            if (!opd) {
                const opdFromData = superadminData.find(o => o.id == opdId);
                if (!opdFromData) return;
            }
            const actualOpd = superadminData.find(o => o.id == opdId);

            document.getElementById('modal-opd-name').innerText = actualOpd.nama_opd || 'Detail OPD';
            const tbody = document.getElementById('modal-program-body');

            if (!opd.programs || opd.programs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-slate-500 font-medium">Belum ada data RFK untuk OPD ini.</td></tr>';
            } else {
                let rows = opd.programs.map(p => `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-5 whitespace-normal min-w-[250px]">
                            <p class="font-bold text-slate-800 text-[13px] mb-1 line-clamp-2" title="${p.nama || '-'}">${p.nama || '-'}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-[10px] font-mono text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded font-semibold border border-indigo-100">${p.kode || '-'}</span>
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">TA: ${p.tahun_anggaran || '-'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-normal min-w-[180px]">
                            <p class="text-[11px] font-bold text-slate-700 leading-tight mb-1" title="${p.sub_kategori_program || '-'}">${p.sub_kategori_program || '-'}</p>
                        </td>
                        <td class="px-6 py-5 whitespace-normal min-w-[150px]">
                            <p class="text-[11px] font-bold text-slate-800 mb-0.5">${p.sumber_dana || '-'}</p>
                            <p class="text-[10px] text-slate-500 leading-tight">${p.sumber_dana_detail || '-'}</p>
                        </td>
                        <td class="px-6 py-5 whitespace-normal min-w-[160px]">
                            <p class="text-[11px] font-bold text-slate-700 leading-tight mb-0.5">${p.kategori_anggaran || '-'}</p>
                            <p class="text-[10px] text-slate-500 leading-tight">${p.sub_kategori_anggaran || '-'}</p>
                        </td>
                        <td class="px-6 py-5 text-right font-bold text-slate-700">Rp ${formatRp(p.pagu)}</td>
                        <td class="px-6 py-5 text-right font-black text-emerald-600">Rp ${formatRp(p.realisasi)}</td>
                        <td class="px-6 py-5 text-center font-bold ${p.fisik > 50 ? 'text-indigo-600' : 'text-orange-500'}">${p.fisik || 0}%</td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1.5 rounded-md text-[10px] font-black tracking-widest uppercase border shadow-sm ${getBadgeClass(p.status)}">${p.status || 'PENDING'}</span>
                        </td>
                    </tr>
                `).join('');
                tbody.innerHTML = rows;
            }
            openModal('program-modal');
        } catch (e) {
            console.error("Error opening Program Modal:", e);
        }
    };

    window.openGlobalSummaryModal = function() {
        try {
            const tbody = document.getElementById('global-summary-body');

            if (superadminData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-slate-500 font-medium">Tidak ada data OPD yang tersedia.</td></tr>';
            } else {
                let rows = superadminData.map(opd => `
                    <tr class="hover:bg-indigo-50/30 transition-colors">
                        <td class="px-8 py-5 whitespace-normal">
                            <p class="font-bold text-slate-800 text-sm">${opd.nama_opd || '-'}</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-bold">${opd.programs ? opd.programs.length : 0} Program</p>
                        </td>
                        <td class="px-8 py-5 text-right font-semibold text-slate-600">Rp ${formatRp(opd.pagu)}</td>
                        <td class="px-8 py-5 text-right font-bold text-emerald-600">Rp ${formatRp(opd.realisasi)}</td>
                        <td class="px-8 py-5 text-right font-bold text-rose-500">Rp ${formatRp(opd.sisa)}</td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-16 bg-slate-200 rounded-full h-1.5"><div class="bg-indigo-500 h-1.5 rounded-full" style="width: ${opd.rata_rata_fisik || 0}%"></div></div>
                                <span class="font-bold text-indigo-700 text-sm w-12">${opd.rata_rata_fisik || 0}%</span>
                            </div>
                        </td>
                    </tr>
                `).join('');
                tbody.innerHTML = rows;
            }
            openModal('global-summary-modal');
        } catch (e) {
            console.error("Error opening Global Summary Modal:", e);
        }
    };

    window.openStatusBreakdownModal = function() {
        filterStatusModal('ALL');
        openModal('status-breakdown-modal');
    };

    window.filterStatusModal = function(status) {
        try {
            ['ALL', 'SELESAI', 'PENDING', 'APPROVE', 'REJECT'].forEach(s => {
                const btn = document.getElementById('tab-status-' + s);
                if(btn) {
                    if(s === status) {
                        btn.className = `px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors ${s==='ALL' ? 'bg-slate-800 text-white' : (s==='SELESAI' ? 'bg-blue-500 text-white' : (s==='PENDING' ? 'bg-amber-500 text-white' : (s==='APPROVE' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white')))}`;
                    } else {
                        btn.className = `px-5 py-2 bg-white border rounded-lg text-sm font-bold transition-colors ${s==='ALL' ? 'text-slate-600 border-slate-200 hover:bg-slate-50' : (s==='SELESAI' ? 'text-blue-600 border-blue-200 hover:bg-blue-50' : (s==='PENDING' ? 'text-amber-600 border-amber-200 hover:bg-amber-50' : (s==='APPROVE' ? 'text-emerald-600 border-emerald-200 hover:bg-emerald-50' : 'text-rose-600 border-rose-200 hover:bg-rose-50')))}`;
                    }
                }
            });

            const tbody = document.getElementById('status-breakdown-body');
            const filtered = status === 'ALL' ? allProgramsFlat : allProgramsFlat.filter(p => p.status === status);

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-slate-500 font-medium"><i class="fas fa-folder-open text-3xl mb-3 text-slate-300 block"></i> Tidak ada program dengan status ${status}.</td></tr>`;
            } else {
                let rows = filtered.map(p => `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-5 whitespace-normal min-w-[180px]">
                            <p class="font-bold text-slate-800 text-xs mb-1" title="${p.nama_opd || '-'}">${p.nama_opd || '-'}</p>
                        </td>
                        <td class="px-6 py-5 whitespace-normal min-w-[250px]">
                            <p class="font-bold text-slate-800 text-[13px] mb-1 line-clamp-2" title="${p.nama || '-'}">${p.nama || '-'}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-[10px] font-mono text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded font-semibold border border-indigo-100">${p.kode || '-'}</span>
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">TA: ${p.tahun_anggaran || '-'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-normal min-w-[180px]">
                            <p class="text-[11px] font-bold text-slate-700 leading-tight mb-1" title="${p.sub_kategori_program || '-'}">${p.sub_kategori_program || '-'}</p>
                            <p class="text-[10px] text-slate-500 leading-tight mb-0.5"><span class="font-bold">Kegiatan:</span> ${p.kegiatan || '-'}</p>
                            <p class="text-[10px] text-slate-500 leading-tight"><span class="font-bold">Sub Kegiatan:</span> ${p.sub_kegiatan || '-'}</p>
                            <p class="text-[10px] text-slate-500 leading-tight mt-1"><span class="font-bold">Ket:</span> ${p.keterangan || '-'}</p>
                        </td>
                        <td class="px-6 py-5 whitespace-normal min-w-[150px]">
                            <p class="text-[11px] font-bold text-slate-800 mb-0.5">${p.sumber_dana || '-'}</p>
                            <p class="text-[10px] text-slate-500 leading-tight">${p.sumber_dana_detail || '-'}</p>
                        </td>
                        <td class="px-6 py-5 whitespace-normal min-w-[160px]">
                            <p class="text-[11px] font-bold text-slate-700 leading-tight mb-0.5">${p.kategori_anggaran || '-'}</p>
                            <p class="text-[10px] text-slate-500 leading-tight">${p.sub_kategori_anggaran || '-'}</p>
                        </td>
                        <td class="px-6 py-5 text-right font-bold text-slate-600">Rp ${formatRp(p.pagu)}</td>
                        <td class="px-6 py-5 text-right">
                            <p class="font-bold text-emerald-600">Rp ${formatRp(p.realisasi)}</p>
                            <p class="text-[10px] font-black text-indigo-500 mt-1">${p.fisik || 0}% Fisik</p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1.5 rounded-md text-[10px] font-black tracking-widest uppercase border shadow-sm ${getBadgeClass(p.status)}">${p.status || 'PENDING'}</span>
                        </td>
                    </tr>
                `).join('');
                tbody.innerHTML = rows;
            }
        } catch (e) {
            console.error("Error filtering Status Modal:", e);
        }
    };

    window.filterOpdGridByTraffic = function(color) {
        if (!superadminData || superadminData.length === 0) return;

        const grid = document.getElementById('opd-grid');
        grid.innerHTML = '<div class="col-span-full text-center py-12"><i class="fas fa-circle-notch fa-spin text-4xl text-indigo-400 mb-4"></i><p>Memfilter data...</p></div>';

        setTimeout(() => {
            let filteredData = [];
            if (color === 'hijau') {
                filteredData = superadminData.filter(opd => opd.persentase >= 90);
            } else if (color === 'kuning') {
                filteredData = superadminData.filter(opd => opd.persentase >= 70 && opd.persentase < 90);
            } else if (color === 'merah') {
                filteredData = superadminData.filter(opd => opd.persentase < 70);
            }

            // Backup the full data and temporarily replace it to reuse renderOpdGrid logic
            const originalData = superadminData;
            superadminData = filteredData;
            renderOpdGrid();
            superadminData = originalData; // Restore

            // Update badge
            document.getElementById('opd-count-badge').innerText = `Filter ${color.toUpperCase()}: ${filteredData.length} OPD`;

            // Scroll to grid
            document.getElementById('opd-grid').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 300);
    };

    window.openMatrixModal = function() {
        try {
            const grid = document.getElementById('matrix-grid');
            if (superadminData.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center py-12 text-slate-500 font-medium">Tidak ada data OPD.</div>';
                openModal('matrix-modal');
                return;
            }

            let html = '';
            superadminData.forEach(opd => {
                let rows = '';
                if(opd.sumber_dana_matrix && opd.sumber_dana_matrix.length > 0) {
                    rows = opd.sumber_dana_matrix.map(sd => `
                        <div class="mb-4 last:mb-0 bg-white border border-slate-100 p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-bold text-slate-700 text-sm"><i class="fas fa-coins text-amber-500 mr-2"></i>${sd.sumber_dana}</span>
                                <span class="text-xs font-black bg-slate-100 text-slate-600 px-2 py-1 rounded">${sd.persentase}% Terserap</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 mb-3">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: ${sd.persentase}%"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-0.5">Pagu Alokasi</p>
                                    <p class="text-xs font-bold text-slate-800">Rp ${formatRp(sd.pagu)}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-emerald-500 uppercase font-bold tracking-wider mb-0.5">Realisasi (Cair)</p>
                                    <p class="text-xs font-bold text-emerald-600">Rp ${formatRp(sd.realisasi)}</p>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    rows = '<p class="text-xs text-slate-400 italic text-center py-4">Data sumber dana tidak tersedia</p>';
                }

                html += `
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-slate-800 px-5 py-4 flex justify-between items-center">
                            <h4 class="font-bold text-white text-sm line-clamp-1 w-3/4" title="${opd.nama_opd}">${opd.nama_opd}</h4>
                            <span class="text-xs font-bold text-slate-300 bg-slate-700 px-2 py-1 rounded">Total: ${opd.persentase}%</span>
                        </div>
                        <div class="p-4 bg-slate-50 flex-grow">
                            ${rows}
                        </div>
                    </div>
                `;
            });
            grid.innerHTML = html;
            openModal('matrix-modal');
        } catch (e) {
            console.error("Error opening Matrix Modal:", e);
        }
    };

    function renderRejectRadar(rejectData) {
        const container = document.getElementById('reject-list-container');
        if(!rejectData || rejectData.length === 0) {
            container.innerHTML = `
                <div class="text-center py-6 border border-dashed border-emerald-200 bg-emerald-50 rounded-xl">
                    <i class="fas fa-check-circle text-2xl text-emerald-400 mb-2 block"></i>
                    <p class="text-xs text-emerald-600 font-bold">Kepatuhan Sempurna. Tidak ada riwayat Reject.</p>
                </div>
            `;
            return;
        }

        let html = '';
        rejectData.forEach((item, index) => {
            const bgColor = index === 0 ? 'bg-rose-50 border-rose-200' : 'bg-white border-slate-100';
            const textColor = index === 0 ? 'text-rose-600' : 'text-slate-700';
            const iconColor = index === 0 ? 'text-rose-500' : 'text-slate-400';

            html += `
                <div class="flex items-center justify-between p-3 rounded-xl border ${bgColor} shadow-sm transition-colors hover:shadow-md">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center font-bold text-xs ${textColor} flex-shrink-0">
                            #${index + 1}
                        </div>
                        <p class="text-xs font-bold ${textColor} truncate" title="${item.nama_opd}">${item.nama_opd}</p>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                        <i class="fas fa-times-circle ${iconColor}"></i>
                        <span class="text-xs font-black ${textColor}">${item.total_reject}x</span>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    // --- Charts Rendering ---

    function renderSumberDanaChart() {
        try {
            if(sumberDanaChart) sumberDanaChart.destroy();

            const ctx = document.getElementById('sumber-dana-chart').getContext('2d');

            let labels = sumberDanaData.map(sd => sd.sumber_dana.toUpperCase());
            let realisasi = sumberDanaData.map(sd => sd.realisasi);
            let sisa = sumberDanaData.map(sd => (sd.sisa < 0 ? 0 : sd.sisa));

            sumberDanaChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Realisasi Keuangan',
                            data: realisasi,
                            backgroundColor: '#3B82F6', // Blue
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.5
                        },
                        {
                            label: 'Sisa Anggaran',
                            data: sisa,
                            backgroundColor: '#FB7185', // Rose
                            borderRadius: 6,
                            barPercentage: 0.8,
                            categoryPercentage: 0.5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Make it Horizontal Bar Chart for better reading
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(c) {
                                    return c.dataset.label + ': Rp ' + formatRp(c.raw);
                                },
                                afterBody: function(c) {
                                    const idx = c[0].dataIndex;
                                    const sd = sumberDanaData[idx];
                                    return `\nTotal Pagu: Rp ${formatRp(sd.pagu)}\nSerapan: ${sd.persentase}%\nProgram: ${sd.jumlah_program}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { color: '#F1F5F9' },
                            ticks: { callback: function(value) { return formatK(value); } }
                        },
                        y: {
                            stacked: true,
                            grid: { display: false },
                            ticks: { font: { weight: 'bold' } }
                        }
                    }
                }
            });
        } catch (e) {
            console.error("Error rendering Sumber Dana Chart:", e);
        }
    }

    function renderTop10OpdPaguChart(top10Data) {
        try {
            if(top10OpdPaguChart) top10OpdPaguChart.destroy();
            const ctx = document.getElementById('top10-opd-pagu-chart');
            if(!ctx) return;
            
            const labels = top10Data.map(item => item.opd);
            const paguValues = top10Data.map(item => item.pagu);
            
            top10OpdPaguChart = new Chart(ctx.getContext('2d'), {
                type: 'bar', // Horizontal bar chart for better label readability
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pagu Anggaran',
                        data: paguValues,
                        backgroundColor: 'rgba(6, 182, 212, 0.8)',
                        borderColor: 'rgb(8, 145, 178)',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    indexAxis: 'y', // Makes it horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 14 },
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return 'Pagu: Rp ' + formatRupiahManual(context.parsed.x);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: '#F1F5F9' },
                            ticks: { 
                                font: { size: 11 },
                                callback: function(value) { return formatK(value); } 
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { 
                                font: { weight: 'bold', size: 11 },
                                autoSkip: false 
                            }
                        }
                    }
                }
            });
        } catch (e) {
            console.error("Error rendering Top 10 OPD Pagu Chart:", e);
        }
    }

    function renderStatusChart(statusData) {
        try {
            if(statusChart) statusChart.destroy();
            const ctx = document.getElementById('status-chart').getContext('2d');
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Approve', 'Pending', 'Reject'],
                    datasets: [{
                        data: [statusData.SELESAI || 0, statusData.APPROVE || 0, statusData.PENDING || 0, statusData.REJECT || 0],
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(c) { return ' ' + c.label + ': ' + c.raw + ' Program'; }
                            }
                        }
                    }
                }
            });
        } catch (e) {
            console.error("Error rendering Status Chart:", e);
        }
    }

    function renderMainComboChart() {
        try {
            let sortedOpds = [...superadminData].sort((a, b) => b.pagu - a.pagu);
            let topOpds = sortedOpds.slice(0, 15);

            let labels = topOpds.map(opd => (opd.nama_opd && opd.nama_opd.length > 15) ? opd.nama_opd.substring(0, 15) + '...' : (opd.nama_opd || 'OPD'));
            let dataRealisasi = topOpds.map(opd => opd.realisasi || 0);
            let dataSisa = topOpds.map(opd => (opd.sisa < 0 ? 0 : (opd.sisa || 0)));
            let dataFisik = topOpds.map(opd => opd.rata_rata_fisik || 0);

            if(mainBarChart) mainBarChart.destroy();

            const ctx = document.getElementById('bar-chart-opd').getContext('2d');
            mainBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Rata-rata Fisik (%)',
                            data: dataFisik,
                            borderColor: '#3B82F6',
                            backgroundColor: '#3B82F6',
                            borderWidth: 3,
                            pointRadius: 4,
                            tension: 0.4,
                            yAxisID: 'y1'
                        },
                        {
                            type: 'bar',
                            label: 'Realisasi Keuangan',
                            data: dataRealisasi,
                            backgroundColor: '#10B981',
                            borderRadius: 0,
                            yAxisID: 'y'
                        },
                        {
                            type: 'bar',
                            label: 'Sisa Anggaran',
                            data: dataSisa,
                            backgroundColor: '#EF4444',
                            borderRadius: { topLeft: 4, topRight: 4 },
                            yAxisID: 'y'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: {
                            callbacks: {
                                label: function(c) {
                                    if(c.dataset.type === 'line') return c.dataset.label + ': ' + c.raw + '%';
                                    return c.dataset.label + ': Rp ' + formatRp(c.raw);
                                }
                            }
                        }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false } },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            stacked: true,
                            grid: { color: '#F1F5F9' },
                            ticks: { callback: function(value) { return formatK(value); } }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { display: false },
                            min: 0,
                            max: 100,
                            ticks: { callback: function(value) { return value + '%'; } }
                        }
                    }
                }
            });
        } catch (e) {
            console.error("Error rendering Main Combo Chart:", e);
        }
    }

    function renderRankingOpd(rankingData) {
        try {
            const container = document.getElementById('ranking-opd-list');
            if(!rankingData || rankingData.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-slate-400">Tidak ada data.</div>';
                return;
            }

            let html = '';
            rankingData.forEach((opd, index) => {
                let rankBadge = '';
                let bgClass = 'bg-white border-slate-100 hover:border-indigo-300';

                if (index === 0) rankBadge = '<i class="fas fa-medal text-2xl text-yellow-400 drop-shadow-md"></i>';
                else if (index === 1) rankBadge = '<i class="fas fa-medal text-2xl text-slate-300 drop-shadow-md"></i>';
                else if (index === 2) rankBadge = '<i class="fas fa-medal text-2xl text-amber-600 drop-shadow-md"></i>';
                else rankBadge = `<span class="text-sm font-black text-slate-400 w-8 h-8 flex items-center justify-center bg-slate-50 rounded-full border border-slate-200">${index+1}</span>`;

                if(index >= rankingData.length - 3 && rankingData.length > 3) {
                    bgClass = 'bg-rose-50 border-rose-100 hover:border-rose-300';
                }

                let progressColor = 'bg-emerald-500';
                if(opd.persentase < 70) progressColor = 'bg-rose-500';
                else if(opd.persentase < 90) progressColor = 'bg-amber-500';

                html += `
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-3 ${bgClass} border rounded-xl transition-all shadow-sm">
                        <div class="w-10 flex-shrink-0 flex justify-center">${rankBadge}</div>
                        <div class="flex-grow min-w-0 w-full sm:w-auto">
                            <p class="text-sm font-bold text-slate-800 truncate" title="${opd.nama_opd}">${opd.nama_opd}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Realisasi: Rp ${formatRp(opd.realisasi)}</p>
                        </div>
                        <div class="w-full sm:w-32 flex-shrink-0 flex items-center gap-3">
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="${progressColor} h-2 rounded-full" style="width: ${opd.persentase}%"></div>
                            </div>
                            <span class="text-xs font-black text-slate-700 w-10 text-right">${opd.persentase}%</span>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } catch(e) {
            console.error("Error renderRankingOpd", e);
        }
    }

    function renderTop10Paket(paketData) {
        try {
            const container = document.getElementById('top10-paket-list');
            if(!paketData || paketData.length === 0) {
                container.innerHTML = '<div class="text-center py-4 text-slate-400">Tidak ada data paket.</div>';
                return;
            }

            let html = '';
            paketData.forEach((p, index) => {
                let badge = index === 0 ? 'bg-amber-100 text-amber-700 border-amber-200 shadow-amber-200 shadow-sm' : 'bg-slate-100 text-slate-500 border-slate-200';

                html += `
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-4 bg-white border border-slate-100 rounded-xl hover:shadow-md transition-shadow gap-4 group">
                        <div class="flex items-start gap-3 sm:gap-4 flex-grow min-w-0 w-full sm:w-auto">
                            <div class="w-8 h-8 rounded-full ${badge} border flex items-center justify-center font-bold text-xs flex-shrink-0">
                                #${index + 1}
                            </div>
                            <div class="min-w-0 flex-grow">
                                <p class="text-sm font-bold text-slate-800 line-clamp-2 leading-snug group-hover:text-indigo-600 transition-colors" title="${p.nama_program}">${p.nama_program}</p>
                                <p class="text-[11px] text-slate-500 mt-1 truncate" title="${p.opd_name}"><i class="fas fa-building mr-1"></i>${p.opd_name}</p>
                            </div>
                        </div>
                        <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-2 sm:gap-1 flex-shrink-0 bg-slate-50 sm:bg-transparent p-3 sm:p-0 rounded-lg">
                            <div class="text-left sm:text-right">
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Pagu</p>
                                <p class="text-sm font-black text-slate-700">Rp ${formatK(p.pagu)}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-semibold text-emerald-500 uppercase tracking-wider">Realisasi</p>
                                <p class="text-sm font-black text-emerald-600">Rp ${formatK(p.realisasi_keuangan)}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } catch(e) {
            console.error("Error renderTop10Paket", e);
        }
    }

    function renderProgramEkstrem(tertinggi, terendah) {
        try {
            const containerTinggi = document.getElementById('program-tertinggi-list');
            const containerRendah = document.getElementById('program-terendah-list');

            const renderItem = (item, colorClass) => `
                <div class="p-4 bg-white border border-slate-100 rounded-xl hover:shadow-md transition-shadow group flex flex-col gap-2 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-1 h-full ${colorClass.replace('text-', 'bg-')} opacity-20"></div>
                    <div class="flex justify-between items-start min-w-0">
                        <p class="text-[11px] font-bold text-slate-800 line-clamp-2 flex-grow pr-2 leading-snug" title="${item.nama_program}">${item.nama_program}</p>
                        <span class="text-[10px] font-black ${colorClass} bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded shadow-sm flex-shrink-0">${item.persentase}%</span>
                    </div>
                    <p class="text-[10px] text-slate-500 truncate mt-1" title="${item.opd_name}"><i class="fas fa-building mr-1"></i>${item.opd_name}</p>
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-slate-50">
                        <p class="text-[10px] font-semibold text-slate-500">Pagu: <span class="font-bold text-slate-700">Rp ${formatK(item.pagu)}</span></p>
                        <p class="text-[10px] font-semibold ${colorClass}">Cair: <span class="font-bold">Rp ${formatK(item.realisasi_keuangan)}</span></p>
                    </div>
                </div>
            `;

            containerTinggi.innerHTML = tertinggi.length ? tertinggi.map(p => renderItem(p, 'text-emerald-600')).join('') : '<p class="text-xs text-center text-slate-400 py-4">Tidak ada data.</p>';
            containerRendah.innerHTML = terendah.length ? terendah.map(p => renderItem(p, 'text-rose-600')).join('') : '<p class="text-xs text-center text-slate-400 py-4">Tidak ada data.</p>';
        } catch(e) {
            console.error("Error renderProgramEkstrem", e);
        }
    }

    function renderOpdGrid() {
        try {
            const grid = document.getElementById('opd-grid');
            grid.innerHTML = '';

            Object.values(doughnutInstances).forEach(chart => { if(chart) chart.destroy(); });
            doughnutInstances = {};

            if (superadminData.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-dashed border-slate-300">
                        <i class="fas fa-folder-open text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-500 font-medium">Tidak ada data RFK untuk kriteria ini.</p>
                    </div>
                `;
                return;
            }

            superadminData.forEach((opd, index) => {
                const chartId = `doughnut-${index}`;
                const sisa = opd.sisa < 0 ? 0 : (opd.sisa || 0);

                const cardHtml = `
                    <div class="super-card p-5 lg:p-6 flex flex-col justify-between group h-full">
                        <div class="flex justify-between items-start mb-5 border-b border-slate-100 pb-4 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 leading-snug pr-3 truncate" title="${opd.nama_opd || ''}">${opd.nama_opd || '-'}</h3>
                            <div class="bg-indigo-50 text-indigo-600 text-[10px] px-2 py-1 rounded-md font-black whitespace-nowrap uppercase tracking-wider flex-shrink-0">
                                ${opd.programs ? opd.programs.length : 0} Prog
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4 mb-6 flex-grow">
                            <div class="w-20 h-20 relative flex-shrink-0 mx-auto sm:mx-0">
                                <canvas id="${chartId}"></canvas>
                                <div class="absolute inset-0 flex items-center justify-center flex-col">
                                    <span class="text-[11px] font-black text-slate-700">${opd.persentase || 0}%</span>
                                </div>
                            </div>
                            <div class="w-full sm:flex-grow space-y-2.5 min-w-0">
                                <div class="flex justify-between items-center bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 overflow-hidden">
                                    <p class="text-[11px] font-semibold text-slate-500">Pagu</p>
                                    <p class="text-sm font-bold text-slate-700 truncate ml-2">Rp ${formatK(opd.pagu)}</p>
                                </div>
                                <div class="flex justify-between items-center bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 overflow-hidden">
                                    <p class="text-[11px] font-semibold text-emerald-600">Cair</p>
                                    <p class="text-sm font-bold text-emerald-700 truncate ml-2">Rp ${formatK(opd.realisasi)}</p>
                                </div>
                            </div>
                        </div>

                        <button onclick="openProgramModalByOpdId(${opd.id})" class="w-full py-2.5 bg-white text-slate-600 font-semibold text-sm rounded-xl hover:bg-indigo-600 hover:text-white transition-all border border-slate-200 hover:border-indigo-600 flex justify-center items-center gap-2 shadow-sm mt-auto">
                            <i class="fas fa-search-plus"></i> Analisis Detail
                        </button>
                    </div>
                `;
                grid.insertAdjacentHTML('beforeend', cardHtml);

                const ctx = document.getElementById(chartId).getContext('2d');
                doughnutInstances[chartId] = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Realisasi', 'Sisa'],
                        datasets: [{
                            data: [opd.realisasi || 0, sisa],
                            backgroundColor: ['#10B981', '#E2E8F0'],
                            borderWidth: 0,
                            hoverOffset: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: { legend: { display: false }, tooltip: { enabled: false } }
                    }
                });
            });
        } catch (e) {
            console.error("Error rendering OPD Grid:", e);
        }
    }
</script>
@endsection
