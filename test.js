
    let superadminData = [];
    let allProgramsFlat = [];
    let sumberDanaData = [];
    let doughnutInstances = {};
    let mainBarChart = null;
    let statusChart = null;
    let sumberDanaChart = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadSuperadminData();
    });

    const formatRp = (angka) => {
        return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(angka || 0);
    };

    const formatK = (angka) => {
        let val = angka || 0;
        if(val >= 1000000000) return (val / 1000000000).toFixed(1) + 'M';
        if(val >= 1000000) return (val / 1000000).toFixed(1) + 'Jt';
        return formatRp(val);
    };

    function getBadgeClass(status) {
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
                document.getElementById('opd-count-badge').innerText = superadminData.length + ' OPD';
                
                // Efektivitas Score
                const efektivitas = data.total_pagu > 0 ? ((data.total_realisasi / data.total_pagu) * 100).toFixed(1) : 0;
                document.getElementById('efektivitas-score').innerText = efektivitas + '%';

                if (data.diagram_status) {
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

    window.openProgramModal = function(index) {
        try {
            const opd = superadminData[index];
            if (!opd) return;

            document.getElementById('modal-opd-name').innerText = opd.nama_opd || 'Detail OPD';
            const tbody = document.getElementById('modal-program-body');
            
            if (!opd.programs || opd.programs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-slate-500 font-medium">Belum ada data RFK untuk OPD ini.</td></tr>';
            } else {
                let rows = opd.programs.map(p => `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-8 py-5 whitespace-normal min-w-[280px]">
                            <p class="font-bold text-slate-800 text-sm mb-1">${p.nama || '-'}</p>
                            <p class="text-xs font-mono text-indigo-500 bg-indigo-50 inline-block px-2 py-0.5 rounded font-medium">${p.kode || '-'}</p>
                        </td>
                        <td class="px-8 py-5 text-right font-semibold text-slate-600">Rp ${formatRp(p.pagu)}</td>
                        <td class="px-8 py-5 text-right font-bold text-emerald-600">Rp ${formatRp(p.realisasi)}</td>
                        <td class="px-8 py-5 text-center font-bold ${p.fisik > 50 ? 'text-indigo-600' : 'text-orange-500'}">${p.fisik || 0}%</td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1 rounded-full text-[11px] font-black tracking-wider uppercase border ${getBadgeClass(p.status)}">${p.status || 'PENDING'}</span>
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
            ['ALL', 'PENDING', 'APPROVE', 'REJECT'].forEach(s => {
                const btn = document.getElementById('tab-status-' + s);
                if(btn) {
                    if(s === status) {
                        btn.className = `px-5 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors ${s==='ALL' ? 'bg-slate-800 text-white' : (s==='PENDING' ? 'bg-amber-500 text-white' : (s==='APPROVE' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'))}`;
                    } else {
                        btn.className = `px-5 py-2 bg-white border rounded-lg text-sm font-bold transition-colors ${s==='ALL' ? 'text-slate-600 border-slate-200 hover:bg-slate-50' : (s==='PENDING' ? 'text-amber-600 border-amber-200 hover:bg-amber-50' : (s==='APPROVE' ? 'text-emerald-600 border-emerald-200 hover:bg-emerald-50' : 'text-rose-600 border-rose-200 hover:bg-rose-50'))}`;
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
                        <td class="px-8 py-5 whitespace-normal min-w-[200px]">
                            <p class="font-semibold text-slate-700 text-sm">${p.nama_opd || '-'}</p>
                        </td>
                        <td class="px-8 py-5 whitespace-normal min-w-[250px]">
                            <p class="font-bold text-slate-800 text-sm mb-1">${p.nama || '-'}</p>
                            <p class="text-xs font-mono text-slate-500">${p.kode || '-'}</p>
                        </td>
                        <td class="px-8 py-5 text-right font-medium text-slate-600">Rp ${formatRp(p.pagu)}</td>
                        <td class="px-8 py-5 text-right">
                            <p class="font-bold text-emerald-600">Rp ${formatRp(p.realisasi)}</p>
                            <p class="text-xs font-bold text-indigo-500 mt-0.5">${p.fisik || 0}% Fisik</p>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1 rounded-full text-[11px] font-black tracking-wider uppercase border ${getBadgeClass(p.status)}">${p.status || 'PENDING'}</span>
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

    function renderStatusChart(statusData) {
        try {
            if(statusChart) statusChart.destroy();
            const ctx = document.getElementById('status-chart').getContext('2d');
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Approve', 'Pending', 'Reject'],
                    datasets: [{
                        data: [statusData.APPROVE || 0, statusData.PENDING || 0, statusData.REJECT || 0],
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
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
            const tbody = document.getElementById('ranking-opd-body');
            if(!rankingData || rankingData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-slate-400">Tidak ada data.</td></tr>';
                return;
            }

            let html = '';
            rankingData.forEach((opd, index) => {
                let rankBadge = '';
                let bgClass = 'hover:bg-slate-50 transition-colors';
                
                if (index === 0) rankBadge = '<i class="fas fa-medal text-xl text-yellow-400 drop-shadow-md"></i>';
                else if (index === 1) rankBadge = '<i class="fas fa-medal text-xl text-slate-300 drop-shadow-md"></i>';
                else if (index === 2) rankBadge = '<i class="fas fa-medal text-xl text-amber-600 drop-shadow-md"></i>';
                else rankBadge = `<span class="text-sm font-black text-slate-400 w-6 text-center inline-block">${index+1}</span>`;

                // highlight bottom 3
                if(index >= rankingData.length - 3 && rankingData.length > 3) {
                    bgClass = 'bg-rose-50 hover:bg-rose-100 transition-colors';
                }

                let progressColor = 'bg-emerald-500';
                if(opd.persentase < 70) progressColor = 'bg-rose-500';
                else if(opd.persentase < 90) progressColor = 'bg-amber-500';

                html += `
                    <tr class="${bgClass} border-b border-slate-100">
                        <td class="px-6 py-4 w-12 text-center">${rankBadge}</td>
                        <td class="px-2 py-4">
                            <p class="text-sm font-bold text-slate-800 line-clamp-1 truncate max-w-[200px]" title="${opd.nama_opd}">${opd.nama_opd}</p>
                            <p class="text-[10px] text-slate-500 font-medium">Realisasi: Rp ${formatRp(opd.realisasi)}</p>
                        </td>
                        <td class="px-6 py-4 w-1/3">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="${progressColor} h-2 rounded-full" style="width: ${opd.persentase}%"></div>
                                </div>
                                <span class="text-xs font-black text-slate-700 w-12 text-right">${opd.persentase}%</span>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch(e) {
            console.error("Error renderRankingOpd", e);
        }
    }

    function renderTop10Paket(paketData) {
        try {
            const tbody = document.getElementById('top10-paket-body');
            if(!paketData || paketData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-slate-400">Tidak ada data paket.</td></tr>';
                return;
            }

            let html = '';
            paketData.forEach((paket, index) => {
                let progress = paket.pagu > 0 ? ((paket.realisasi_keuangan / paket.pagu) * 100).toFixed(1) : 0;
                html += `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex gap-3 items-center">
                                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] font-black">${index+1}</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 line-clamp-1 w-[200px]" title="${paket.nama_program}">${paket.nama_program}</p>
                                    <p class="text-[10px] text-slate-500 line-clamp-1 w-[200px]" title="${paket.opd_name}">${paket.opd_name}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <p class="text-xs font-bold text-slate-800">Rp ${formatRp(paket.pagu)}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <p class="text-xs font-bold text-emerald-600">Rp ${formatRp(paket.realisasi_keuangan)}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 text-[10px] font-black rounded">${progress}%</span>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch(e) {
            console.error("Error renderTop10Paket", e);
        }
    }

    function renderProgramEkstrem(tertinggi, terendah) {
        try {
            const containerTinggi = document.getElementById('program-tertinggi-list');
            const containerRendah = document.getElementById('program-terendah-list');
            
            const renderItem = (item, colorClass) => `
                <div class="p-3 border border-slate-100 rounded-xl hover:shadow-sm transition-shadow">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-[11px] font-bold text-slate-800 line-clamp-2 w-3/4" title="${item.nama_program}">${item.nama_program}</p>
                        <span class="text-xs font-black ${colorClass} bg-white border border-slate-100 px-1.5 py-0.5 rounded shadow-sm">${item.persentase}%</span>
                    </div>
                    <p class="text-[10px] text-slate-500 truncate" title="${item.opd_name}"><i class="fas fa-building mr-1"></i>${item.opd_name}</p>
                    <div class="flex justify-between mt-2">
                        <p class="text-[10px] font-bold text-slate-600">Pagu: Rp ${formatK(item.pagu)}</p>
                        <p class="text-[10px] font-bold ${colorClass}">Cair: Rp ${formatK(item.realisasi_keuangan)}</p>
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
                    <div class="super-card p-6 flex flex-col justify-between group">
                        <div class="flex justify-between items-start mb-6 border-b border-slate-100 pb-4">
                            <h3 class="text-sm font-bold text-slate-800 leading-snug pr-4 line-clamp-2" title="${opd.nama_opd || ''}">${opd.nama_opd || '-'}</h3>
                            <div class="bg-indigo-50 text-indigo-600 text-[10px] px-2 py-1 rounded-md font-black whitespace-nowrap uppercase tracking-wider">
                                ${opd.programs ? opd.programs.length : 0} Prog
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-24 h-24 relative flex-shrink-0">
                                <canvas id="${chartId}"></canvas>
                                <div class="absolute inset-0 flex items-center justify-center flex-col">
                                    <span class="text-xs font-black text-slate-700">${opd.persentase || 0}%</span>
                                </div>
                            </div>
                            <div class="flex-grow space-y-3">
                                <div class="flex justify-between items-center">
                                    <p class="text-xs font-semibold text-slate-400">Pagu</p>
                                    <p class="text-sm font-bold text-slate-700">${formatK(opd.pagu)}</p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p class="text-xs font-semibold text-emerald-500">Realisasi</p>
                                    <p class="text-sm font-bold text-emerald-600">${formatK(opd.realisasi)}</p>
                                </div>
                            </div>
                        </div>

                        <button onclick="openProgramModal(${index})" class="w-full py-2.5 bg-slate-50 text-slate-600 font-semibold text-sm rounded-xl hover:bg-indigo-600 hover:text-white transition-colors border border-slate-200 flex justify-center items-center gap-2">
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
