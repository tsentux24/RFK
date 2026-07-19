<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi RFK</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-container {
            width: 80px;
            text-align: left;
        }
        .logo {
            width: 70px;
            height: auto;
        }
        .title-container {
            text-align: center;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 11pt;
            font-weight: bold;
            color: #34495e;
            margin: 5px 0;
        }
        .date {
            font-size: 8pt;
            color: #7f8c8d;
            margin: 0;
            font-style: italic;
        }
        
        /* Summary Section */
        .summary-container {
            width: 100%;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 5px;
            vertical-align: top;
        }
        .summary-title {
            font-size: 9pt;
            color: #6c757d;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #2c3e50;
        }
        .summary-value.green { color: #198754; }
        .summary-value.red { color: #dc3545; }
        .summary-value.blue { color: #0d6efd; }

        /* Group Title */
        .opd-title {
            font-size: 11pt;
            font-weight: bold;
            color: #fff;
            background-color: #34495e;
            padding: 6px 10px;
            margin-top: 15px;
            margin-bottom: 5px;
            border-radius: 3px 3px 0 0;
        }

        /* Data Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #bdc3c7;
            padding: 6px;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #ecf0f1;
            font-weight: bold;
            text-align: center;
            color: #2c3e50;
            font-size: 8pt;
            text-transform: uppercase;
        }
        table.data-table td {
            font-size: 8pt;
        }
        
        /* Utility */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-muted { color: #6c757d; }
        
        /* Program Hierarchy */
        .program-name {
            font-weight: bold;
            font-size: 8.5pt;
            color: #1a202c;
            margin-bottom: 4px;
        }
        .program-meta {
            font-size: 7pt;
            color: #4a5568;
            margin-bottom: 2px;
            background-color: #f7fafc;
            padding: 2px 4px;
            border: 1px solid #e2e8f0;
            display: inline-block;
            border-radius: 2px;
        }
        .hierarchy-keg {
            font-size: 7.5pt;
            font-weight: bold;
            color: #2b6cb0;
            margin-top: 4px;
        }
        .hierarchy-sub {
            font-size: 7pt;
            color: #4a5568;
            padding-left: 8px;
            border-left: 2px solid #cbd5e0;
            margin-top: 2px;
        }
        .master-ket {
            font-size: 7pt;
            color: #718096;
            font-style: italic;
            margin-top: 4px;
        }

        /* Progress Bar */
        .progress-container {
            width: 100%;
            background-color: #e9ecef;
            border-radius: 2px;
            height: 10px;
            margin-top: 4px;
            position: relative;
        }
        .progress-bar {
            height: 10px;
            border-radius: 2px;
        }
        .pb-green { background-color: #198754; }
        .pb-yellow { background-color: #ffc107; }
        .pb-red { background-color: #dc3545; }
        .pb-blue { background-color: #0d6efd; }
        .progress-text {
            font-size: 7pt;
            font-weight: bold;
            text-align: right;
            margin-top: 2px;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-approve { background-color: #d1e7dd; color: #0f5132; }
        .badge-selesai { background-color: #cfe2ff; color: #084298; }
        .badge-pending { background-color: #fff3cd; color: #664d03; }
        .badge-reject { background-color: #f8d7da; color: #842029; }

        /* Subtotal Row */
        .subtotal-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0px;
            right: 0px;
            height: 20px;
            text-align: right;
            font-size: 8pt;
            font-style: italic;
            color: #7f8c8d;
            border-top: 1px dashed #bdc3c7;
            padding-top: 5px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .filter-info {
            font-size: 8pt;
            color: #4a5568;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-container">
                <img src="{{ public_path('assets/images/malut.png') }}" class="logo" alt="Logo" onerror="this.src='https://e-rekrutmen.malutprov.go.id/assets/images/malut.png'">
            </td>
            <td class="title-container">
                <h1 class="title">PEMERINTAH PROVINSI MALUKU UTARA</h1>
                <h2 class="subtitle">LAPORAN REKAPITULASI REALISASI FISIK DAN KEUANGAN (RFK)</h2>
                <p class="date">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} | Ekspor oleh Administrator/Superadmin</p>
                @if($request->filled('tahun') || $request->filled('status') || $request->filled('opd') || $request->filled('triwulan'))
                <div class="filter-info">
                    Filter Aktif: 
                    {{ $request->filled('tahun') ? 'Tahun: ' . $request->tahun : '' }}
                    {{ $request->filled('status') ? '| Status: ' . $request->status : '' }}
                    {{ $request->filled('opd') ? '| OPD: ' . $request->opd : '' }}
                    @if($request->filled('triwulan'))
                        | Triwulan: {{ $request->triwulan }} 
                        @if($request->triwulan == 1) (Jan - Mar) @elseif($request->triwulan == 2) (Apr - Jun) @elseif($request->triwulan == 3) (Jul - Sep) @elseif($request->triwulan == 4) (Okt - Des) @endif
                    @endif
                </div>
                @endif
            </td>
            <td style="width: 80px;"></td> <!-- Spacer -->
        </tr>
    </table>

    <!-- Executive Summary -->
    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td style="width: 25%;">
                    <div class="summary-title">Total Pagu Provinsi</div>
                    <div class="summary-value">Rp {{ number_format($grandPagu, 0, ',', '.') }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-title">Total Serapan Keuangan</div>
                    <div class="summary-value green">Rp {{ number_format($grandRealisasiKeuangan, 0, ',', '.') }}</div>
                    @php $persenUang = $grandPagu > 0 ? round(($grandRealisasiKeuangan / $grandPagu) * 100, 2) : 0; @endphp
                    <div style="font-size: 7.5pt; color: #198754;">{{ $persenUang }}% terserap</div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-title">Total Sisa Pagu</div>
                    <div class="summary-value red">Rp {{ number_format($grandSisa, 0, ',', '.') }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-title">Rata-rata Fisik Provinsi</div>
                    <div class="summary-value blue">{{ number_format($averageFisik, 2) }}%</div>
                </td>
            </tr>
        </table>
    </div>

    @forelse($groupedData as $opdName => $programs)
        <div class="opd-title">
            {{ $opdName }} 
            <span style="font-weight: normal; font-size: 9pt; float: right; margin-top: 2px;">
                Total Program: {{ $programs->count() }}
            </span>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 33%;">Detail Program & Kegiatan</th>
                    <th style="width: 10%;">Sumber Dana</th>
                    <th style="width: 14%;">Pagu Anggaran</th>
                    <th style="width: 15%;">Serapan Keuangan</th>
                    <th style="width: 12%;">Progres Fisik</th>
                    <th style="width: 12%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $subTotalPagu = 0;
                    $subTotalReal = 0;
                    $subTotalSisa = 0;
                    $sumFisik = 0;
                @endphp

                @foreach($programs as $index => $item)
                    @php
                        $subTotalPagu += $item->pagu;
                        $subTotalReal += $item->realisasi_keuangan;
                        $subTotalSisa += $item->sisa_pagu;
                        $sumFisik += $item->realisasi_fisik;

                        $latestReal = $item->realisasis->first();
                        $kegiatan = $latestReal ? $latestReal->kegiatan : '-';
                        $subkeg = $latestReal ? $latestReal->sub_kegiatan : '-';
                        $ket = $latestReal ? $latestReal->keterangan : '-';
                        
                        $persenKeuangan = $item->pagu > 0 ? round(($item->realisasi_keuangan / $item->pagu) * 100, 2) : 0;
                        $uangColor = $persenKeuangan >= 90 ? 'pb-green' : ($persenKeuangan >= 70 ? 'pb-yellow' : 'pb-red');
                        $fisikColor = $item->realisasi_fisik >= 90 ? 'pb-green' : ($item->realisasi_fisik >= 70 ? 'pb-yellow' : 'pb-red');
                        
                        $badgeClass = '';
                        if($item->status === 'SELESAI') $badgeClass = 'badge-selesai';
                        elseif($item->status === 'APPROVE') $badgeClass = 'badge-approve';
                        elseif($item->status === 'REJECT') $badgeClass = 'badge-reject';
                        else $badgeClass = 'badge-pending';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="program-name">{{ $item->nama_program }}</div>
                            <span class="program-meta">Kode: {{ $item->kode_program }}</span>
                            <span class="program-meta">Tahun: {{ $item->tahun_anggaran }}</span>
                            
                            <div class="hierarchy-keg">{{ $kegiatan }}</div>
                            <div class="hierarchy-sub">{{ $subkeg }}</div>
                            <div class="master-ket">Keterangan: {{ $ket }}</div>
                        </td>
                        <td class="text-center">
                            <span class="text-bold">{{ $item->sumber_dana }}</span><br>
                            <span style="font-size: 7pt; color: #6c757d;">{{ $item->sumber_dana_detail }}</span>
                        </td>
                        <td class="text-right text-bold">Rp {{ number_format($item->pagu, 0, ',', '.') }}</td>
                        <td>
                            <div class="text-right text-bold" style="color: #198754;">Rp {{ number_format($item->realisasi_keuangan, 0, ',', '.') }}</div>
                            <div class="progress-container">
                                <div class="progress-bar {{ $uangColor }}" style="width: {{ min($persenKeuangan, 100) }}%;"></div>
                            </div>
                            <div class="progress-text" style="color: #6c757d;">{{ $persenKeuangan }}%</div>
                            <div class="text-right" style="font-size: 7pt; color: #dc3545; margin-top: 3px;">Sisa: Rp {{ number_format($item->sisa_pagu, 0, ',', '.') }}</div>
                        </td>
                        <td>
                            <div class="text-right text-bold" style="color: #0d6efd;">{{ $item->realisasi_fisik }}%</div>
                            <div class="progress-container">
                                <div class="progress-bar {{ $fisikColor }}" style="width: {{ min($item->realisasi_fisik, 100) }}%;"></div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $badgeClass }}">{{ $item->status }}</span>
                            @if($item->status == 'SELESAI')
                                <div style="font-size: 7pt; color: #0d6efd; margin-top: 4px; font-weight: bold;">TUNTAS 100%</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <td colspan="3" class="text-right text-bold">SUB TOTAL {{ strtoupper($opdName) }}:</td>
                    <td class="text-right text-bold">Rp {{ number_format($subTotalPagu, 0, ',', '.') }}</td>
                    <td class="text-right text-bold" style="color: #198754;">Rp {{ number_format($subTotalReal, 0, ',', '.') }}</td>
                    <td class="text-right text-bold" style="color: #0d6efd;">{{ $programs->count() > 0 ? number_format($sumFisik / $programs->count(), 2) : 0 }}% (Rata-rata)</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @empty
        <div style="text-align: center; padding: 30px; border: 1px dashed #bdc3c7; background-color: #f8f9fa; color: #6c757d;">
            <h3>Data Tidak Ditemukan</h3>
            <p>Tidak ada data realisasi / program yang sesuai dengan filter pencarian.</p>
        </div>
    @endforelse

    <div class="footer">
        Data Dicetak Melalui Sistem RFK Provinsi Maluku Utara
    </div>

</body>
</html>
