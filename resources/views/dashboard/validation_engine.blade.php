@extends('dashboard.layout.app',['title'=>'SI-RAFIKA Validation Engine'])
@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
            <i class="fas fa-microchip text-indigo-600"></i> SI-RAFIKA Validation Engine
        </h2>
        <p class="text-gray-600 mt-2">Mesin cerdas berbasis aturan (Rule-based Engine) untuk mengaudit dan mendeteksi anomali data serta indikasi manipulasi (Copy-Paste).</p>
    </div>

    <!-- Control Panel -->
    <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 p-6 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-indigo-50 rounded-full opacity-50 z-0"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Status Pemindaian</h3>
                <p class="text-sm text-gray-500" id="scan-status">Sistem siap. Klik tombol di samping untuk memulai audit menyeluruh.</p>
            </div>
            <button id="btn-run-engine" onclick="runValidation()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                <i class="fas fa-play-circle text-xl"></i> Jalankan Audit Otomatis
            </button>
        </div>
    </div>

    <!-- Loader Animation -->
    <div id="scanning-animation" class="hidden flex flex-col items-center justify-center py-12">
        <div class="relative w-24 h-24">
            <div class="absolute inset-0 border-4 border-indigo-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin"></div>
            <i class="fas fa-search absolute inset-0 flex items-center justify-center text-indigo-400 text-2xl animate-pulse"></i>
        </div>
        <h3 class="text-lg font-bold text-indigo-800 mt-4 animate-pulse">Memindai Ribuan Data...</h3>
        <p class="text-sm text-gray-500">Menganalisis Mismatch & Pola Mencurigakan</p>
    </div>

    <!-- Results Container -->
    <div id="results-container" class="hidden space-y-8">

        <!-- Summary Alert -->
        <div id="summary-alert" class="rounded-xl p-5 border-l-4 flex items-center gap-4 shadow-sm">
            <div class="text-3xl" id="summary-icon"></div>
            <div>
                <h3 class="font-bold text-lg" id="summary-title"></h3>
                <p class="text-sm opacity-90" id="summary-desc"></p>
            </div>
        </div>

        <!-- Pilar 1: Data Mismatch -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2">
                <i class="fas fa-exclamation-circle text-red-500"></i> Pilar I: Deteksi Ketidakcocokan Data (Data Mismatch)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="mismatch-cards">
                <!-- Cards Injected via JS -->
            </div>
            <div id="mismatch-empty" class="hidden bg-green-50 text-green-700 p-4 rounded-lg border border-green-100 flex items-center gap-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <p class="font-medium">Bersih! Tidak ditemukan Over-Pagu maupun Deviasi Ekstrem.</p>
            </div>
        </div>

        <!-- Pilar 2: Suspicious Similarity -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-2">
                <i class="fas fa-user-secret text-purple-600"></i> Pilar II: Deteksi Kesamaan Mencurigakan (Copy-Paste)
            </h3>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" id="suspicious-table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-purple-800 uppercase tracking-wider">Tanggal & Instansi</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-purple-800 uppercase tracking-wider">Pola Kemiripan (Uang & Fisik)</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-purple-800 uppercase tracking-wider">Program yang Terdeteksi Identik</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="suspicious-tbody">
                        <!-- Rows Injected via JS -->
                    </tbody>
                </table>
            </div>
            <div id="suspicious-empty" class="hidden bg-green-50 text-green-700 p-4 rounded-lg border border-green-100 flex items-center gap-3 mt-4">
                <i class="fas fa-check-circle text-2xl"></i>
                <p class="font-medium">Bersih! Tidak ditemukan indikasi inputan ganda / asal isi.</p>
            </div>
        </div>
    </div>
</div>

<script>
    async function runValidation() {
        const btn = document.getElementById('btn-run-engine');
        const loader = document.getElementById('scanning-animation');
        const results = document.getElementById('results-container');
        const status = document.getElementById('scan-status');

        // UI State: Scanning
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');

        results.classList.add('hidden');
        loader.classList.remove('hidden');
        status.innerText = 'Engine sedang bekerja...';

        try {
            // Fake delay for UX (Audit feels thorough)
            await new Promise(r => setTimeout(r, 1500));

            const response = await fetch('{{ route("validation.run") }}');
            const data = await response.json();

            if(data.success) {
                renderResults(data.data);
            } else {
                alert('Gagal menjalankan engine: ' + data.message);
            }
        } catch(e) {
            console.error(e);
            alert('Terjadi kesalahan jaringan.');
        } finally {
            // UI State: Done
            loader.classList.add('hidden');
            results.classList.remove('hidden');

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-redo text-xl"></i> Pindai Ulang';
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            status.innerText = 'Pemindaian selesai pada ' + new Date().toLocaleTimeString();
        }
    }

    function renderResults(data) {
        // Summary
        const alertBox = document.getElementById('summary-alert');
        const alertIcon = document.getElementById('summary-icon');
        const alertTitle = document.getElementById('summary-title');
        const alertDesc = document.getElementById('summary-desc');

        alertBox.className = 'rounded-xl p-5 border-l-4 flex items-center gap-4 shadow-sm';

        if (data.total_anomalies > 0) {
            alertBox.classList.add('bg-red-50', 'border-red-500', 'text-red-800');
            alertIcon.innerHTML = '<i class="fas fa-engine-warning"></i>'; // FontAwesome fallback
            alertIcon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
            alertTitle.innerText = `Ditemukan ${data.total_anomalies} Anomali!`;
            alertDesc.innerText = 'Engine mendeteksi ketidakwajaran data. Harap segera periksa rincian di bawah.';
        } else {
            alertBox.classList.add('bg-green-50', 'border-green-500', 'text-green-800');
            alertIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
            alertTitle.innerText = 'Data Sangat Sehat';
            alertDesc.innerText = 'Tidak ditemukan anomali, over-pagu, deviasi ekstrem, maupun indikasi copy-paste.';
        }

        // Render Mismatches
        const mismatchContainer = document.getElementById('mismatch-cards');
        const mismatchEmpty = document.getElementById('mismatch-empty');
        mismatchContainer.innerHTML = '';

        if (data.mismatches.length > 0) {
            mismatchContainer.classList.remove('hidden');
            mismatchEmpty.classList.add('hidden');

            data.mismatches.forEach(m => {
                let badgeClass = m.type === 'EXTREME_DEVIATION' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800';
                let iconClass = m.type === 'EXTREME_DEVIATION' ? 'fa-balance-scale-right text-orange-500' : 'fa-arrow-up text-red-500';

                mismatchContainer.innerHTML += `
                    <div class="bg-white rounded-xl border border-red-100 p-5 shadow-sm hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-2 py-1 rounded text-xs font-bold ${badgeClass}">${m.type}</span>
                            <i class="fas ${iconClass} text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-1">${m.program}</h4>
                        <p class="text-xs text-gray-500 mb-3"><i class="fas fa-building mr-1"></i> ${m.opd}</p>
                        <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700 font-medium border border-gray-100">
                            ${m.detail}
                        </div>
                    </div>
                `;
            });
        } else {
            mismatchContainer.classList.add('hidden');
            mismatchEmpty.classList.remove('hidden');
        }

        // Render Suspicious
        const suspiciousTbody = document.getElementById('suspicious-tbody');
        const suspiciousContainer = document.getElementById('suspicious-table-container');
        const suspiciousEmpty = document.getElementById('suspicious-empty');
        suspiciousTbody.innerHTML = '';

        if (data.suspicious.length > 0) {
            suspiciousContainer.classList.remove('hidden');
            suspiciousEmpty.classList.add('hidden');

            data.suspicious.forEach(s => {
                // Split programs string by |
                const progList = s.programs.split(' | ').map(p => `<li><i class="fas fa-caret-right text-purple-400 mr-1"></i> ${p}</li>`).join('');

                suspiciousTbody.innerHTML += `
                    <tr class="hover:bg-purple-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">${s.tanggal}</div>
                            <div class="text-xs text-gray-500">${s.opd}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-copy text-purple-500 text-lg"></i>
                                <span class="text-sm font-medium text-gray-800">${s.detail}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <ul class="text-xs text-gray-700 space-y-1">
                                ${progList}
                            </ul>
                        </td>
                    </tr>
                `;
            });
        } else {
            suspiciousContainer.classList.add('hidden');
            suspiciousEmpty.classList.remove('hidden');
        }
    }
</script>
@endsection
