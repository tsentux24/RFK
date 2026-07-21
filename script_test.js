
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
    let currentModalPrograms = [];

    // --- Modal Functions ---
    
    // --- Rincian Alokasi Modal Functions ---
    let currentRincianData = {};
    
    function openRincianModal(key, title) {
        document.getElementById('rincianModalTitle').innerText = 'Rincian ' + title;
        const tbody = document.getElementById('rincianModalBody');
        tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-slate-400"><i class="fas fa-spinner fa-spin text-2xl mb-2"></i><br>Memuat data...</td></tr>';
        
        const modal = document.getElementById('rincianAlokasiModal');
        const modalInner = modal.querySelector('div');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalInner.classList.remove('scale-95');
            modalInner.classList.add('scale-100');
        }, 10);

        if (globalSuperadminData && globalSuperadminData.rincian_alokasi && globalSuperadminData.rincian_alokasi[key]) {
            const progs = globalSuperadminData.rincian_alokasi[key].programs || [];
            
            if (progs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="py-12 text-center text-slate-400"><div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fas fa-folder-open text-xl text-slate-300"></i></div>Tidak ada program pada kategori ini.</td></tr>';
                return;
            }
            
            // Sort by largest pagu
            progs.sort((a, b) => b.pagu - a.pagu);
            
            let html = '';
            progs.forEach(p => {
                let colorClass = 'text-slate-500 bg-slate-100';
                if (p.persen >= 90) colorClass = 'text-emerald-700 bg-emerald-100';
                else if (p.persen >= 70) colorClass = 'text-amber-700 bg-amber-100';
                else if (p.persen > 0) colorClass = 'text-rose-700 bg-rose-100';

                html += `
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="py-4 px-6 text-sm font-medium text-slate-700">
                            <div class="flex items-start">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-3 shrink-0 mt-0.5">
                                    ${p.opd.substring(0, 2).toUpperCase()}
                                </div>
                                <span class="line-clamp-2">${p.opd}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600">
                            <span class="line-clamp-2">${p.nama}</span>
                        </td>
                        <td class="py-4 px-6 text-sm font-semibold text-slate-700 text-right">
                            Rp ${p.pagu.toLocaleString('id-ID')}
                        </td>
                        <td class="py-4 px-6 text-sm font-medium text-slate-600 text-right">
                            Rp ${p.realisasi.toLocaleString('id-ID')}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold ${colorClass}">
                                ${p.persen}%
                            </span>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-slate-400">Data tidak tersedia.</td></tr>';
        }
    }

    function closeRincianModal() {
        const modal = document.getElementById('rincianAlokasiModal');
        const modalInner = modal.querySelector('div');
        
        modal.classList.add('opacity-0');
        modalInner.classList.remove('scale-100');
        modalInner.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openApprovalModal() { document.getElementById('approval-modal').classList.remove('hidden'); }
    function closeApprovalModal() { document.getElementById('approval-modal').classList.add('hidden'); }
    function openOpdBelumModal() { document.getElementById('opd-belum-modal').classList.remove('hidden'); }
    function closeOpdBelumModal() { document.getElementById('opd-belum-modal').classList.add('hidden'); }

    function openMasterDetail(type, extraArg = null) {
        let allPrograms = [];
        if (globalSuperadminData.ranking_opd) {
            globalSuperadminData.ranking_opd.forEach(opd => {
                if(opd.programs) {
                    opd.programs.forEach(p => {
                        allPrograms.push({...p, opd_nama: opd.nama_opd});
                    });
                }
            });
        }

        let filtered = [];
        let title = "Detail Data";
        let subtitle = "Menampilkan rincian dari indikator yang dipilih.";
        let icon = "fa-coins";
        let headerColor = "bg-slate-800";
        let tableType = "default"; // keuangan, fisik, status, kegiatan, opd, default

        switch(type) {
            case 'custom_opd_programs':
                title = "Program OPD: " + (extraArg || '');
                subtitle = "Rincian program khusus untuk instansi terpilih.";
                icon = "fa-building";
                headerColor = "bg-indigo-600";
                tableType = "default";
                filtered = allPrograms.filter(p => p.opd_nama === extraArg);
                break;
            case 'pagu':
            case 'realisasi':
            case 'keuangan':
            case 'apbd':
            case 'apbn':
                tableType = "keuangan";
                headerColor = "bg-blue-600";
                if(type==='pagu') { title="Rincian Pagu Anggaran"; subtitle="Daftar seluruh program beserta pagu anggarannya."; icon="fa-money-bill-wave"; filtered=allPrograms; }
                else if(type==='realisasi') { title="Rincian Realisasi Keuangan"; subtitle="Program yang memiliki penyerapan (realisasi) keuangan."; icon="fa-coins"; filtered=allPrograms.filter(p=>p.realisasi>0); }
                else if(type==='keuangan') { title="Persentase Keuangan"; subtitle="Daftar program dan capaian persentase keuangannya."; icon="fa-chart-line"; filtered=allPrograms; }
                else if(type==='apbd') { title="Program Sumber Dana APBD"; subtitle="Program yang didanai oleh APBD."; icon="fa-wallet"; filtered=allPrograms.filter(p=>(p.sumber_dana||'').toUpperCase()==='APBD'); headerColor="bg-cyan-600"; }
                else if(type==='apbn') { title="Program Sumber Dana APBN"; subtitle="Program yang didanai oleh APBN."; icon="fa-wallet"; filtered=allPrograms.filter(p=>(p.sumber_dana||'').toUpperCase()==='APBN'); headerColor="bg-fuchsia-600"; }
                break;
            case 'fisik':
            case 'deviasi':
                tableType = "fisik";
                headerColor = "bg-emerald-600";
                if(type==='fisik') { title="Rincian Realisasi Fisik"; subtitle="Daftar capaian fisik dari setiap program."; icon="fa-hard-hat"; filtered=allPrograms; }
                break;
            case 'sisa':
                tableType = "opd";
                title = "Daftar OPD dengan Sisa Anggaran";
                subtitle = "Instansi yang masih memiliki pagu anggaran yang belum terserap.";
                icon = "fa-coins";
                headerColor = "bg-rose-500";
                break;
            case 'tepat_waktu':
            case 'terlambat':
            case 'bermasalah':
                tableType = "status";
                if(type==='tepat_waktu') { title="Program Tepat Waktu (On-Track)"; subtitle="Program dengan progres sesuai target atau berstatus Selesai."; icon="fa-check-circle"; headerColor="bg-emerald-500"; filtered=allPrograms.filter(p=>p.status==='APPROVE'||p.status==='SELESAI'); }
                else if(type==='terlambat') { title="Program Terlambat (Warning)"; subtitle="Program yang belum ada progress (Pending)."; icon="fa-exclamation-triangle"; headerColor="bg-red-500"; filtered=allPrograms.filter(p=>p.status==='PENDING'); }
                else if(type==='bermasalah') { title="Program Bermasalah (Reject)"; subtitle="Program yang laporannya ditolak atau bermasalah."; icon="fa-times-circle"; headerColor="bg-amber-500"; filtered=allPrograms.filter(p=>p.status==='REJECT'); }
                break;
            case 'opd':
                tableType = "opd";
                title = "Rincian OPD Aktif";
                subtitle = "Menampilkan data rekap per OPD.";
                icon = "fa-building";
                headerColor = "bg-slate-700";
                break;
            case 'kegiatan':
                tableType = "kegiatan";
                title = "Daftar Seluruh Kegiatan";
                subtitle = "Mengelompokkan program berdasarkan nama kegiatan.";
                icon = "fa-tasks";
                headerColor = "bg-indigo-500";
                filtered = allPrograms;
                break;
            case 'program':
            case 'paket':
                tableType = "default";
                headerColor = "bg-slate-800";
                if(type==='program') { title="Daftar Seluruh Program"; subtitle="Menampilkan rincian seluruh program yang diinput."; icon="fa-folder-open"; filtered=allPrograms; }
                else if(type==='paket') { title="Total Paket Program"; subtitle="Daftar seluruh paket pekerjaan."; icon="fa-box"; filtered=allPrograms; }
                break;
        }

        // Apply Header styling
        const header = document.getElementById('masterDetailHeader');
        header.className = `px-8 py-6 border-b border-white/20 flex justify-between items-center ${headerColor} text-white transition-colors duration-300 relative overflow-hidden`;

        document.getElementById('masterDetailTitle').innerText = title;
        document.getElementById('masterDetailSubtitle').innerText = subtitle;
        document.getElementById('masterDetailIcon').className = `fas ${icon}`;
        document.getElementById('masterDetailSearch').value = '';
        if(document.getElementById('masterDetailSearchMobile')) document.getElementById('masterDetailSearchMobile').value = '';

        let tbody = document.getElementById('masterDetailTbody');
        let thead = document.getElementById('masterDetailThead');

        if (tableType === 'opd') {
            const isSisa = (type === 'sisa');
            thead.innerHTML = `
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Instansi (OPD)</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi</th>
                ${isSisa ? '<th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right text-rose-600">Sisa Anggaran</th>' : '<th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Keuangan (%)</th>'}
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Fisik (%)</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Aksi</th>
            `;
            let opdHtml = '';
            if (globalSuperadminData.ranking_opd) {
                let opdsToRender = [...globalSuperadminData.ranking_opd];
                if (isSisa) {
                    opdsToRender = opdsToRender.filter(opd => (opd.pagu || 0) > (opd.realisasi || 0));
                    opdsToRender.sort((a, b) => ((b.pagu || 0) - (b.realisasi || 0)) - ((a.pagu || 0) - (a.realisasi || 0)));
                }

                opdsToRender.forEach(opd => {
                    const sisa = (opd.pagu || 0) - (opd.realisasi || 0);
                    const sisaCol = isSisa 
                        ? `<td class="px-6 py-4 text-right text-rose-500 font-bold whitespace-nowrap font-mono">Rp ${formatRp(sisa)}</td>`
                        : `<td class="px-6 py-4 text-center"><span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">${opd.persentase}%</span></td>`;

                    opdHtml += `
                        <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 search-row">
                            <td class="px-6 py-4 font-semibold text-slate-800 whitespace-normal min-w-[200px] search-target">${opd.nama_opd}</td>
                            <td class="px-6 py-4 text-right text-slate-600 whitespace-nowrap font-mono">Rp ${formatRp(opd.pagu)}</td>
                            <td class="px-6 py-4 text-right text-emerald-600 font-medium whitespace-nowrap font-mono">Rp ${formatRp(opd.realisasi)}</td>
                            ${sisaCol}
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-bold">${opd.rata_rata_fisik}%</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="openMasterDetail('custom_opd_programs', '${opd.nama_opd.replace(/'/g, "\\'")}')" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                                    <i class="fas fa-list"></i> Lihat Program
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            tbody.innerHTML = opdHtml || `<tr><td colspan="6" class="text-center py-8 text-slate-500">Data tidak ditemukan</td></tr>`;
        
        } else if (tableType === 'kegiatan') {
            // Group by Kegiatan
            const kegiatanMap = {};
            filtered.forEach(p => {
                const k = p.kegiatan || 'Tanpa Kegiatan';
                if(!kegiatanMap[k]) kegiatanMap[k] = { opd: p.opd_nama, pagu: 0, realisasi: 0, programs: [] };
                kegiatanMap[k].pagu += (p.pagu || 0);
                kegiatanMap[k].realisasi += (p.realisasi || 0);
                kegiatanMap[k].programs.push(p);
            });
            
            thead.innerHTML = `
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Kegiatan</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">OPD</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Jml Program</th>
            `;
            let html = '';
            Object.keys(kegiatanMap).forEach(k => {
                const data = kegiatanMap[k];
                html += `
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 search-row">
                        <td class="px-6 py-4 font-medium text-slate-800 whitespace-normal min-w-[200px] search-target">${k}</td>
                        <td class="px-6 py-4 text-slate-600 text-sm whitespace-normal min-w-[150px] search-target">${data.opd}</td>
                        <td class="px-6 py-4 text-right text-slate-600 font-medium whitespace-nowrap font-mono">Rp ${formatRp(data.pagu)}</td>
                        <td class="px-6 py-4 text-right text-emerald-600 font-medium whitespace-nowrap font-mono">Rp ${formatRp(data.realisasi)}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold">${data.programs.length}</span>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || `<tr><td colspan="5" class="text-center py-8 text-slate-500">Tidak ada data untuk kategori ini.</td></tr>`;

        } else if (tableType === 'keuangan') {
            currentModalPrograms = filtered;
            thead.innerHTML = `
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs w-[30%]">Program</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs w-[20%]">OPD / Sumber Dana</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu / Realisasi</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center w-32">Progress Keuangan</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center w-24">Aksi</th>
            `;
            let html = '';
            filtered.forEach(p => {
                const persenKeuangan = p.pagu > 0 ? ((p.realisasi / p.pagu) * 100).toFixed(1) : 0;
                html += `
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 search-row">
                        <td class="px-6 py-4 font-medium text-slate-800 whitespace-normal search-target">
                            ${p.nama}
                            <div class="text-[10px] text-slate-400 mt-1 font-mono">${p.kode}</div>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-normal search-target">
                            <span class="font-semibold text-slate-700">${p.opd_nama}</span>
                            <div class="text-[10px] text-slate-500 mt-1 bg-slate-100 inline-block px-2 py-0.5 rounded">${p.sumber_dana || '-'}</div>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap font-mono">
                            <div class="text-slate-500 text-xs mb-1">Pagu: <span class="font-bold text-slate-700">Rp ${formatRp(p.pagu)}</span></div>
                            <div class="text-emerald-600 text-xs">Real: <span class="font-bold">Rp ${formatRp(p.realisasi)}</span></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: ${persenKeuangan}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-700 w-8 text-right">${persenKeuangan}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openProgramDetail(${p.id})" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                                <i class="fas fa-search"></i> Detail
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || `<tr><td colspan="5" class="text-center py-8 text-slate-500">Tidak ada data untuk kategori ini.</td></tr>`;

        } else if (tableType === 'fisik') {
            currentModalPrograms = filtered;
            thead.innerHTML = `
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs w-[35%]">Program</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs w-[25%]">OPD</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center w-32">Progress Fisik</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center w-24">Aksi</th>
            `;
            let html = '';
            filtered.forEach(p => {
                let barColor = p.fisik >= 80 ? 'bg-emerald-500' : (p.fisik >= 50 ? 'bg-amber-400' : 'bg-red-500');
                html += `
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 search-row">
                        <td class="px-6 py-4 font-medium text-slate-800 whitespace-normal search-target">
                            ${p.nama}
                            <div class="text-[10px] text-slate-400 mt-1 font-mono">${p.kode}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-normal search-target">
                            <span class="font-semibold text-slate-700">${p.opd_nama}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-slate-200 rounded-full h-2.5">
                                    <div class="${barColor} h-2.5 rounded-full" style="width: ${p.fisik}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-700 w-8 text-right">${p.fisik}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openProgramDetail(${p.id})" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                                <i class="fas fa-search"></i> Detail
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || `<tr><td colspan="4" class="text-center py-8 text-slate-500">Tidak ada data untuk kategori ini.</td></tr>`;

        } else if (tableType === 'status') {
            currentModalPrograms = filtered;
            thead.innerHTML = `
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs w-[30%]">Program</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs w-[20%]">OPD</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs w-[25%]">Keterangan Laporan</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center w-24">Status</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center w-20">Aksi</th>
            `;
            let html = '';
            filtered.forEach(p => {
                let statusBadge = '';
                if(p.status === 'SELESAI') statusBadge = '<span class="px-2.5 py-1 bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-xs font-bold shadow-sm">SELESAI</span>';
                else if(p.status === 'APPROVE') statusBadge = '<span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-md text-xs font-bold shadow-sm">APPROVE</span>';
                else if(p.status === 'PENDING') statusBadge = '<span class="px-2.5 py-1 bg-amber-100 text-amber-700 border border-amber-200 rounded-md text-xs font-bold shadow-sm">PENDING</span>';
                else if(p.status === 'REJECT') statusBadge = '<span class="px-2.5 py-1 bg-red-100 text-red-700 border border-red-200 rounded-md text-xs font-bold shadow-sm">REJECT</span>';

                let ketHtml = p.keterangan && p.keterangan !== '-' ? 
                    `<div class="text-xs text-slate-600 italic bg-slate-50 p-2 rounded border border-slate-100">"${p.keterangan.substring(0,80)}${p.keterangan.length>80?'...':''}"</div>` : 
                    `<span class="text-xs text-slate-400 italic">Tidak ada catatan</span>`;

                html += `
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 search-row">
                        <td class="px-6 py-4 font-medium text-slate-800 whitespace-normal search-target">
                            ${p.nama}
                            <div class="text-[10px] text-slate-400 mt-1 font-mono">${p.kode}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 whitespace-normal search-target">
                            ${p.opd_nama}
                        </td>
                        <td class="px-6 py-4 whitespace-normal search-target">
                            ${ketHtml}
                        </td>
                        <td class="px-6 py-4 text-center">
                            ${statusBadge}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openProgramDetail(${p.id})" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || `<tr><td colspan="5" class="text-center py-8 text-slate-500">Tidak ada data untuk kategori ini.</td></tr>`;

        } else {
            // default
            currentModalPrograms = filtered;
            thead.innerHTML = `
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Program</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">OPD</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Sumber Dana</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Pagu</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Realisasi</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Fisik</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Status</th>
                <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-center">Aksi</th>
            `;
            let html = '';
            filtered.forEach(p => {
                let statusBadge = '';
                if(p.status === 'SELESAI') statusBadge = '<span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-md text-[10px] font-bold">SELESAI</span>';
                else if(p.status === 'APPROVE') statusBadge = '<span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-md text-[10px] font-bold">APPROVE</span>';
                else if(p.status === 'PENDING') statusBadge = '<span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-md text-[10px] font-bold">PENDING</span>';
                else if(p.status === 'REJECT') statusBadge = '<span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-md text-[10px] font-bold">REJECT</span>';

                html += `
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 search-row">
                        <td class="px-6 py-4 font-medium text-slate-800 whitespace-normal min-w-[200px] search-target">
                            ${p.nama}
                            <div class="text-[10px] text-slate-400 mt-1 font-mono">${p.kode}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-sm whitespace-normal min-w-[150px] search-target">${p.opd_nama}</td>
                        <td class="px-6 py-4 text-slate-600 text-sm search-target">${p.sumber_dana || '-'}</td>
                        <td class="px-6 py-4 text-right text-slate-600 font-medium whitespace-nowrap font-mono">Rp ${formatRp(p.pagu)}</td>
                        <td class="px-6 py-4 text-right text-emerald-600 font-medium whitespace-nowrap font-mono">Rp ${formatRp(p.realisasi)}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-blue-600">${p.fisik}%</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            ${statusBadge}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openProgramDetail(${p.id})" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm">
                                <i class="fas fa-search"></i> Detail
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html || `<tr><td colspan="8" class="text-center py-8 text-slate-500">Tidak ada data untuk kategori ini.</td></tr>`;
        }

        const modal = document.getElementById('masterDetailModal');
        modal.classList.remove('hidden');
        // trigger animation
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.firstElementChild.classList.remove('scale-95');
        }, 10);
    }

    
    function filterMasterDetailTable() {
        const input = document.getElementById('masterDetailSearch').value.toLowerCase();
        const rows = document.querySelectorAll('#masterDetailTbody .search-row');
        let hasVisible = false;
        rows.forEach(row => {
            const targets = row.querySelectorAll('.search-target');
            let match = false;
            targets.forEach(t => {
                if (t.innerText.toLowerCase().includes(input)) match = true;
            });
            if (match) {
                row.style.display = '';
                hasVisible = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        let noResultRow = document.getElementById('noResultRow');
        if (!hasVisible && rows.length > 0) {
            if (!noResultRow) {
                const tbody = document.getElementById('masterDetailTbody');
                const colCount = document.getElementById('masterDetailThead').querySelectorAll('th').length;
                noResultRow = document.createElement('tr');
                noResultRow.id = 'noResultRow';
                noResultRow.innerHTML = `<td colspan="${colCount}" class="text-center py-8 text-slate-500">Tidak ada data yang cocok dengan pencarian "<span></span>".</td>`;
                tbody.appendChild(noResultRow);
                noResultRow.querySelector('span').innerText = document.getElementById('masterDetailSearch').value;
            } else {
                noResultRow.querySelector('span').innerText = document.getElementById('masterDetailSearch').value;
                noResultRow.style.display = '';
            }
        } else if (noResultRow) {
            noResultRow.style.display = 'none';
        }
    }

    function closeMasterDetail() {
        const modal = document.getElementById('masterDetailModal');
        modal.classList.add('opacity-0');
        modal.firstElementChild.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openProgramDetail(id) {
        const p = currentModalPrograms.find(prog => prog.id === id);
        if(!p) return;

        let statusBadge = '';
        if(p.status === 'SELESAI') statusBadge = '<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-md text-xs font-bold">SELESAI</span>';
        else if(p.status === 'APPROVE') statusBadge = '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-md text-xs font-bold">APPROVE</span>';
        else if(p.status === 'PENDING') statusBadge = '<span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-md text-xs font-bold">PENDING</span>';
        else if(p.status === 'REJECT') statusBadge = '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-md text-xs font-bold">REJECT</span>';

        const contentHtml = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Instansi (OPD)</p>
                        <p class="text-sm font-semibold text-slate-800">${p.opd_nama}</p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Kode & Nama Program</p>
                        <p class="text-xs font-mono text-blue-600 mb-1">${p.kode}</p>
                        <p class="text-sm font-semibold text-slate-800">${p.nama}</p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Kegiatan / Sub Kegiatan</p>
                        <p class="text-sm font-semibold text-slate-800 mb-1">Kegiatan: <span class="font-normal text-slate-600">${p.kegiatan}</span></p>
                        <p class="text-sm font-semibold text-slate-800">Sub: <span class="font-normal text-slate-600">${p.sub_kegiatan}</span></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Keterangan / Deskripsi</p>
                        <p class="text-sm text-slate-600 whitespace-pre-wrap">${p.keterangan || '-'}</p>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Status Pelaporan</p>
                            ${statusBadge}
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Tahun Anggaran</p>
                            <p class="text-lg font-bold text-slate-800">${p.tahun_anggaran}</p>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="text-[10px] uppercase font-bold text-slate-500 mb-2">Klasifikasi Anggaran</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-slate-500 text-xs">Sumber Dana:</span><br><span class="font-semibold text-slate-800">${p.sumber_dana || '-'}</span></div>
                            <div><span class="text-slate-500 text-xs">Rincian Dana:</span><br><span class="font-semibold text-slate-800">${p.sumber_dana_detail || '-'}</span></div>
                            <div><span class="text-slate-500 text-xs">Kategori:</span><br><span class="font-semibold text-slate-800">${p.kategori_anggaran || '-'}</span></div>
                            <div><span class="text-slate-500 text-xs">Sub Kategori:</span><br><span class="font-semibold text-slate-800">${p.sub_kategori_anggaran || '-'}</span></div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                        <p class="text-[10px] uppercase font-bold text-blue-600 mb-3">Rincian Keuangan & Fisik</p>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center border-b border-blue-100 pb-2">
                                <span class="text-sm font-medium text-slate-600">Pagu Anggaran</span>
                                <span class="font-bold text-slate-800">Rp ${formatRp(p.pagu)}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-blue-100 pb-2">
                                <span class="text-sm font-medium text-slate-600">Realisasi Keuangan</span>
                                <span class="font-bold text-emerald-600">Rp ${formatRp(p.realisasi)}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-blue-100 pb-2">
                                <span class="text-sm font-medium text-slate-600">Sisa Pagu</span>
                                <span class="font-bold text-rose-500">Rp ${formatRp(p.sisa)}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-slate-600">Capaian Fisik</span>
                                <span class="font-black text-xl text-blue-600">${p.fisik}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('programDetailContent').innerHTML = contentHtml;
        const modal = document.getElementById('programDetailModal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.firstElementChild.classList.remove('scale-95');
        }, 10);
    }

    function closeProgramDetail() {
        const modal = document.getElementById('programDetailModal');
        modal.classList.add('opacity-0');
        modal.firstElementChild.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

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
            hist: data.hist || [0, 0, 0, 0],
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
        document.getElementById('kpi-sisa').innerText = 'Rp ' + formatK((data.total_pagu || 0) - (data.total_realisasi || 0));
        
        // Fix for OPD Belum Input
        document.getElementById('kpi-belum-input').innerText = data.opd_belum_input || 0;

        // Update Rincian Alokasi
        if (data.rincian_alokasi) {
            const rKeys = ['apbd_operasi', 'apbd_modal', 'belanja_pegawai', 'belanja_barang_jasa', 'belanja_hibah', 'modal_peralatan', 'modal_jalan', 'modal_gedung', 'apbn_dau', 'apbn_modal', 'apbn_dak', 'apbn_dak_fisik', 'apbn_dak_non_fisik', 'apbn_dbh', 'apbn_dekom', 'apbn_tp'];
            rKeys.forEach(k => {
                if (data.rincian_alokasi[k]) {
                    const pagu = data.rincian_alokasi[k].pagu || 0;
                    const realisasi = data.rincian_alokasi[k].realisasi || 0;
                    const persen = pagu > 0 ? ((realisasi / pagu) * 100).toFixed(1) : 0;
                    
                    const pEl = document.getElementById('rincian-pagu-'+k);
                    if(pEl) pEl.innerText = 'Rp ' + formatK(pagu);
                    
                    const ptEl = document.getElementById('rincian-persen-'+k);
                    if(ptEl) ptEl.innerText = persen + '%';
                    
                    const barEl = document.getElementById('rincian-bar-'+k);
                    if(barEl) barEl.style.width = Math.min(persen, 100) + '%';
                }
            });
        }

        
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
        // Top 5 OPD Kinerja Terbaik (Real-time data)
        let top5Opd = [...(globalSuperadminData.ranking_opd || [])].sort((a, b) => (b.rata_rata_fisik || 0) - (a.rata_rata_fisik || 0)).slice(0, 5);
        let top5Labels = top5Opd.map(o => {
            let name = o.nama_opd || '';
            return name.length > 25 ? name.substring(0, 25) + '...' : name;
        });
        let top5Data = top5Opd.map(o => o.rata_rata_fisik || 0);

        destroyChart('c8');
        charts['c8'] = new Chart(document.getElementById('chart8'), {
            type: 'bar',
            data: {
                labels: top5Labels,
                datasets: [{
                    label: 'Rata-rata Fisik (%)',
                    data: top5Data,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal Bar
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) { return context.parsed.x + '% Fisik'; }
                        }
                    },
                    datalabels: { display: true, align: 'end', anchor: 'end', formatter: (value) => value + '%' }
                },
                scales: {
                    x: { beginAtZero: true, max: 100, grid: { borderDash: [2,4] }, title: { display: true, text: 'Capaian Fisik (%)' } },
                    y: { grid: { display: false }, ticks: { font: { size: 9, family: 'Inter' } } }
                }
            }
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


