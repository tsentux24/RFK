@extends('dashboard.layout.app',['title'=>'HomePage'])
@section('content')

        <!-- Content -->
        <div class="p-6">
            <!-- Statistik Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500">Total Program / Pengajuan</p>
                            <h3 class="text-2xl font-bold text-gray-800" id="stat-total-program">0</h3>
                        </div>
                        <div class="bg-indigo-100 p-3 rounded-lg">
                            <i class="fas fa-project-diagram text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-indigo-600 mt-2">Seluruh Pengajuan RFK</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500">Total Pagu Master</p>
                            <h3 class="text-xl font-bold text-gray-800" id="stat-total-pagu">Rp 0</h3>
                        </div>
                        <div class="bg-cyan-100 p-3 rounded-lg">
                            <i class="fas fa-money-bill-wave text-cyan-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-cyan-600 mt-2">Akumulasi Anggaran</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500">Total Realisasi</p>
                            <h3 class="text-xl font-bold text-gray-800" id="stat-total-realisasi">Rp 0</h3>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-green-600 mt-2">Telah direalisasikan</p>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover cursor-pointer hover:bg-red-50 transition-colors" onclick="openOpdBelumModal()">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500">OPD Belum Input</p>
                            <h3 class="text-2xl font-bold text-red-600" id="stat-opd-belum">0</h3>
                        </div>
                        <div class="bg-red-100 p-3 rounded-lg">
                            <i class="fas fa-building text-red-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-red-600 mt-2">Klik untuk melihat detail OPD</p>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 card-hover cursor-pointer" onclick="openApprovalModal()">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-500">Menunggu Approval</p>
                            <h3 class="text-2xl font-bold text-gray-800" id="pendingApprovalCount">0</h3>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <i class="fas fa-clipboard-check text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-xs text-yellow-600 mt-2">Klik untuk memproses</p>
                </div>
            </div>

            <!-- Modal Approval RFK -->
            <div id="approval-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
                <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl">
                    <div class="p-5 border-b border-gray-200 flex justify-between items-center bg-indigo-600 rounded-t-xl">
                        <h3 class="text-lg font-semibold text-white"><i class="fas fa-check-double me-2"></i> Persetujuan Realisasi RFK</h3>
                        <button onclick="closeApprovalModal()" class="text-white hover:text-gray-200"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-5 overflow-auto max-h-[70vh]">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Program</th>
                                    <th class="px-4 py-3">Nilai Realisasi</th>
                                    <th class="px-4 py-3">Fisik %</th>
                                    <th class="px-4 py-3">Staff Input</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody id="approvalTableBody">
                                <!-- Data PENDING akan dimuat di sini -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal OPD Belum Input -->
            <div id="opd-belum-modal" class="modal fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center p-4 z-50 hidden backdrop-blur-sm transition-all duration-300">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl transform scale-100 transition-transform">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-red-50 rounded-t-2xl">
                        <h3 class="text-lg font-bold text-red-800 flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-3 text-xl"></i> 
                            Daftar OPD Belum Memasukkan Data RFK
                        </h3>
                        <button onclick="closeOpdBelumModal()" class="text-gray-400 hover:text-red-600 hover:bg-white rounded-full p-1 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-6 overflow-auto max-h-[65vh]">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        OPD di bawah ini sama sekali belum mengajukan program RFK ke dalam sistem. Segera tindak lanjuti untuk memenuhi target pelaporan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">No</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Instansi / OPD</th>
                                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Status</th>
                                        <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="opdBelumTableBody" class="divide-y divide-gray-200 bg-white">
                                    <!-- Data dimuat via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-2xl flex justify-end">
                        <button onclick="closeOpdBelumModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            <!-- Chart & Proyek Terbaru -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Chart: Realisasi vs Pagu -->
                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-5 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-indigo-600"></i>
                        Grafik Perbandingan Pagu & Realisasi OPD
                    </h3>
                    <div class="chart-container" style="height: 350px;">
                        <canvas id="opdRealisasiChart"></canvas>
                    </div>
                </div>

                <!-- Proyek Terbaru -->
                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 lg:col-span-2">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-history text-indigo-600"></i>
                            Progres Realisasi Program Terbaru
                        </h3>
                    </div>

                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2" id="allDataContainer">
                        <div class="text-center py-4 text-gray-500">Memuat data...</div>
                    </div>
                </div>
            </div>

            <!-- OPD & Arsip Terbaru -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- OPD Terbaru -->
                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-building text-indigo-600"></i>
                            OPD Terbaru
                        </h3>
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-building text-indigo-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium">Dinas Kelautan dan Perikanan</h4>
                                    <p class="text-sm text-gray-600">Kode: OPD-042</p>
                                </div>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-medium">Aktif</span>
                        </div>

                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-cyan-100 flex items-center justify-center">
                                    <i class="fas fa-building text-cyan-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium">Dinas Pariwisata</h4>
                                    <p class="text-sm text-gray-600">Kode: OPD-041</p>
                                </div>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-medium">Aktif</span>
                        </div>

                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-building text-purple-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-medium">Dinas Koperasi dan UKM</h4>
                                    <p class="text-sm text-gray-600">Kode: OPD-040</p>
                                </div>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-medium">Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Arsip Terbaru -->
                <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-archive text-indigo-600"></i>
                            Arsip Terbaru
                        </h3>
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div>
                                <h4 class="font-medium text-indigo-700">Laporan Triwulan I 2025</h4>
                                <p class="text-sm text-gray-600">Dinas Pendidikan • 2 jam yang lalu</p>
                            </div>
                            <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-medium">PDF</span>
                        </div>

                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div>
                                <h4 class="font-medium text-indigo-700">Data Realisasi Anggaran</h4>
                                <p class="text-sm text-gray-600">Dinas Kesehatan • 1 hari yang lalu</p>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-medium">XLSX</span>
                        </div>

                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div>
                                <h4 class="font-medium text-indigo-700">Statistik Kependudukan</h4>
                                <p class="text-sm text-gray-600">Dinas Kependudukan • 2 hari yang lalu</p>
                            </div>
                            <span class="bg-purple-100 text-purple-800 text-xs px-2.5 py-1 rounded-full font-medium">DOC</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Trail RFK -->
            <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 mt-6">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-history text-indigo-600"></i>
                        Riwayat Keseluruhan RFK (Audit Trail)
                    </h3>
                    <button onclick="loadHistoryRFK()" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition-colors">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600">
                            <tr>
                                <th class="px-4 py-3 rounded-tl-lg">Waktu</th>
                                <th class="px-4 py-3">OPD</th>
                                <th class="px-4 py-3">Program</th>
                                <th class="px-4 py-3">Perubahan Status</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3 rounded-tr-lg">Oleh</th>
                            </tr>
                        </thead>
                        <tbody id="auditTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-4">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Filter Arsip -->
    <div id="arsip-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="p-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Filter Arsip</h3>
            </div>

            <div class="p-5">
                <form>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">OPD</label>
                        <select class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">Pilih OPD</option>
                            <option value="opd-1">Dinas Pendidikan</option>
                            <option value="opd-2">Dinas Kesehatan</option>
                            <option value="opd-3">Dinas Pekerjaan Umum</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Pilih Bulan</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Pilih Tahun</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex justify-end gap-3 p-5 border-t border-gray-200">
                <button id="close-modal" class="px-4 py-2 text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Terapkan Filter</button>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // User dropdown toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && e.target !== userMenuButton) {
                    userDropdown.style.display = 'none';
                }
            });
        }

        // Modal functionality
        const arsipModal = document.getElementById('arsip-modal');
        const closeModal = document.getElementById('close-modal');

        function openModal() {
            arsipModal.classList.remove('hidden');
        }

        function closeModalFunc() {
            arsipModal.classList.add('hidden');
        }

        closeModal.addEventListener('click', closeModalFunc);

        // Close modal when clicking outside
        arsipModal.addEventListener('click', function(e) {
            if (e.target === arsipModal) {
                closeModalFunc();
            }
        });

        // Modern Bar Chart (Pagu vs Realisasi)
        const ctxOpd = document.getElementById('opdRealisasiChart').getContext('2d');
        let opdRealisasiChart = new Chart(ctxOpd, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Total Pagu',
                        data: [],
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Realisasi Keuangan',
                        data: [],
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if(value >= 1000000000) return 'Rp ' + (value/1000000000).toFixed(1) + 'M';
                                if(value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'Jt';
                                return 'Rp ' + value;
                            }
                        }
                    }
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        let opdBelumListData = []; // Store globally for modal

        async function loadDashboardStats() {
            try {
                const response = await fetch('{{ route("dashboard.stats") }}');
                const result = await response.json();
                
                if (result.success) {
                    const d = result.data;
                    document.getElementById('stat-total-program').innerText = d.total_program;
                    document.getElementById('stat-total-pagu').innerText = 'Rp ' + formatRupiahStr(d.total_pagu || 0);
                    document.getElementById('stat-total-realisasi').innerText = 'Rp ' + formatRupiahStr(d.total_realisasi || 0);
                    document.getElementById('stat-opd-belum').innerText = d.opd_belum_input;

                    // Update Global Variable for Modal
                    if (d.opd_belum_list) {
                        opdBelumListData = d.opd_belum_list;
                        renderOpdBelumTable();
                    }

                    // Update Chart
                    if (d.diagram_opd && d.diagram_opd.length > 0) {
                        const labels = d.diagram_opd.map(item => item.opd);
                        const paguData = d.diagram_opd.map(item => item.pagu);
                        const realisasiData = d.diagram_opd.map(item => item.realisasi);

                        opdRealisasiChart.data.labels = labels;
                        opdRealisasiChart.data.datasets[0].data = paguData;
                        opdRealisasiChart.data.datasets[1].data = realisasiData;
                        opdRealisasiChart.update();
                    }
                }
            } catch(e) {
                console.error("Error fetching stats", e);
            }
        }

        // Realtime Polling
        setInterval(loadDashboardStats, 5000);

        // Animate progress bars
        // Modal Approval RFK
        // Modal OPD Belum Input
        const opdBelumModal = document.getElementById('opd-belum-modal');

        function openOpdBelumModal() {
            opdBelumModal.classList.remove('hidden');
            renderOpdBelumTable();
        }

        function closeOpdBelumModal() {
            opdBelumModal.classList.add('hidden');
        }

        function renderOpdBelumTable() {
            let html = '';
            if (opdBelumListData.length === 0) {
                html = '<tr><td colspan="4" class="py-6 text-center text-gray-500 font-medium">Bagus! Seluruh OPD telah memasukkan data.</td></tr>';
            } else {
                opdBelumListData.forEach((opd, index) => {
                    html += `
                        <tr class="hover:bg-red-50 transition-colors group">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">${index + 1}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700 font-medium flex items-center">
                                <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center mr-3 text-red-600">
                                    <i class="fas fa-building"></i>
                                </div>
                                ${opd.nama_opd}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 border border-red-200">Kosong</span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                <button class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md font-medium transition-colors border border-indigo-200 text-xs">
                                    <i class="fas fa-paper-plane mr-1"></i> Ingatkan
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            document.getElementById('opdBelumTableBody').innerHTML = html;
        }

        const approvalModal = document.getElementById('approval-modal');

        function openApprovalModal() {
            approvalModal.classList.remove('hidden');
            loadPendingApproval();
        }

        function closeApprovalModal() {
            approvalModal.classList.add('hidden');
        }

        function formatRupiahStr(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        async function loadPendingApproval() {
            try {
                const response = await fetch('{{ route("rfk.pending") }}');
                const result = await response.json();

                if (result.success) {
                    document.getElementById('pendingApprovalCount').innerText = result.data.length;
                    
                    let rows = '';
                    result.data.forEach(item => {
                        const programName = item.nama_program || '-';
                        const staffName = item.user ? item.user.name : '-';
                        
                        let pendingRealisasi = item.realisasis && item.realisasis.length > 0 ? item.realisasis[0] : null;
                        const valKeuangan = pendingRealisasi ? pendingRealisasi.nilai_realisasi_keuangan : 0;
                        const valFisik = pendingRealisasi ? pendingRealisasi.nilai_realisasi_fisik : 0;
                        
                        rows += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">${programName}</td>
                                <td class="px-4 py-3 text-green-600 font-medium">Rp ${formatRupiahStr(valKeuangan)}</td>
                                <td class="px-4 py-3 font-bold">${valFisik}%</td>
                                <td class="px-4 py-3"><i class="far fa-user mr-1"></i>${staffName}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-1 rounded-full font-bold shadow-sm">PENDING</span>
                                </td>
                            </tr>
                        `;
                    });

                    if(result.data.length === 0) {
                        rows = '<tr><td colspan="5" class="text-center py-4">Tidak ada data yang menunggu persetujuan</td></tr>';
                    }

                    document.getElementById('approvalTableBody').innerHTML = rows;
                }
            } catch (error) {
                console.error("Error fetching pending data", error);
            }
        }

        // Admin view only, approval process handled by Kepala OPD

        async function loadHistoryRFK() {
            try {
                const response = await fetch('{{ route("rfk.history") }}');
                const result = await response.json();

                if (result.success) {
                    let rows = '';
                    result.data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleString('id-ID');
                        const program = item.realisasi && item.realisasi.input_rfk ? item.realisasi.input_rfk.nama_program : '-';
                        const opd = item.realisasi && item.realisasi.input_rfk && item.realisasi.input_rfk.opd ? item.realisasi.input_rfk.opd.nama_opd : '-';
                        const user = item.user ? item.user.name : '-';
                        
                        let statusBadge = '';
                        if (item.status_baru === 'APPROVE') statusBadge = '<span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs">APPROVE</span>';
                        else if (item.status_baru === 'REJECT') statusBadge = '<span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-xs">REJECT</span>';
                        else statusBadge = '<span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs">PENDING</span>';

                        const prevStatus = item.status_sebelumnya ? item.status_sebelumnya : 'Baru';

                        rows += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-xs text-gray-500">${date}</td>
                                <td class="px-4 py-3 text-xs">${opd}</td>
                                <td class="px-4 py-3 text-xs font-medium">${program.substring(0, 40)}</td>
                                <td class="px-4 py-3 text-xs">${prevStatus} &rarr; ${statusBadge}</td>
                                <td class="px-4 py-3 text-xs">${item.keterangan || '-'}</td>
                                <td class="px-4 py-3 text-xs">${user}</td>
                            </tr>
                        `;
                    });

                    if(result.data.length === 0) {
                        rows = '<tr><td colspan="6" class="text-center py-4">Tidak ada data riwayat</td></tr>';
                    }

                    document.getElementById('auditTableBody').innerHTML = rows;
                }
            } catch (error) {
                console.error("Error fetching history", error);
            }
        }

        async function loadAllData() {
            try {
                const response = await fetch('{{ route("rfk.data") }}');
                const result = await response.json();

                if (result.success) {
                    let html = '';
                    result.data.forEach(item => {
                        const fisik = item.realisasi_fisik;
                        let statusColor = 'bg-blue-600';
                        if (fisik >= 100) statusColor = 'bg-green-600';
                        else if (fisik < 30) statusColor = 'bg-red-600';
                        
                        html += `
                        <div class="p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-indigo-700">${item.nama_program}</h4>
                                <span class="bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded-full font-medium">${item.status}</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">${item.opd ? item.opd.nama_opd : '-'}</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="${statusColor} h-2 rounded-full progress-bar" style="width: ${fisik}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Progress Fisik: ${fisik}%</span>
                                <span>Rp ${formatRupiahStr(item.realisasi_keuangan)} / Rp ${formatRupiahStr(item.pagu)}</span>
                            </div>
                        </div>`;
                    });

                    if(result.data.length === 0) {
                        html = '<div class="text-center py-4 text-gray-500">Tidak ada data</div>';
                    }
                    document.getElementById('allDataContainer').innerHTML = html;
                    
                    setTimeout(() => {
                        const progressBars = document.querySelectorAll('.progress-bar');
                        progressBars.forEach(bar => {
                            const width = bar.style.width;
                            bar.style.width = '0';
                            setTimeout(() => { bar.style.width = width; }, 100);
                        });
                    }, 100);
                }
            } catch(e) {
                console.error("Error fetching all data", e);
            }
        }

        // Load count initially
        document.addEventListener('DOMContentLoaded', function() {
            loadPendingApproval();
            loadHistoryRFK();
            loadAllData();
            loadDashboardStats();
        });
    </script>
</body>
</html>
@endsection
