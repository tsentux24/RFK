<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi RFK</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 12pt;
            font-weight: bold;
            color: #34495e;
            margin: 5px 0;
        }
        .date {
            font-size: 9pt;
            color: #7f8c8d;
            margin: 0;
            font-style: italic;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #34495e;
            padding: 6px;
        }
        table.data-table th {
            background-color: #ecf0f1;
            font-weight: bold;
            text-align: center;
            color: #2c3e50;
            font-size: 9pt;
        }
        table.data-table td {
            font-size: 8pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-green { color: #27ae60; }
        .text-red { color: #c0392b; }
        .text-bold { font-weight: bold; }
        
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 30px;
            text-align: right;
            font-size: 9pt;
            font-style: italic;
            color: #7f8c8d;
            border-top: 1px dashed #bdc3c7;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <!-- Using absolute path for dompdf -->
            <td class="logo-container">
                <img src="{{ public_path('assets/images/malut.png') }}" class="logo" alt="Logo" onerror="this.src='https://e-rekrutmen.malutprov.go.id/assets/images/malut.png'">
            </td>
            <td class="title-container">
                <h1 class="title">PEMERINTAH PROVINSI MALUKU UTARA</h1>
                <h2 class="subtitle">LAPORAN REKAPITULASI REALISASI FISIK DAN KEUANGAN (RFK)</h2>
                <p class="date">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</p>
            </td>
            <td style="width: 80px;"></td> <!-- Spacer to center title properly -->
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 8%;">Tahun</th>
                <th style="width: 15%;">Instansi OPD</th>
                <th style="width: 22%;">Program & Kode</th>
                <th style="width: 15%;">Pagu Total</th>
                <th style="width: 13%;">Realisasi Keuangan</th>
                <th style="width: 10%;">Sisa Pagu</th>
                <th style="width: 5%;">Fisik</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandPagu = 0;
                $grandReal = 0;
                $grandSisa = 0;
            @endphp
            @forelse($data as $index => $item)
                @php
                    $pagu = $item->pagu;
                    $realisasi = $item->realisasi_keuangan;
                    $sisa = $item->sisa_pagu;
                    
                    $grandPagu += $pagu;
                    $grandReal += $realisasi;
                    $grandSisa += $sisa;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->tahun_anggaran }}</td>
                    <td>{{ $item->opd ? $item->opd->nama_opd : '-' }}</td>
                    <td>
                        <div class="text-bold">{{ $item->nama_program }}</div>
                        <div style="font-size: 7pt; color: #7f8c8d;">{{ $item->kode_program }}</div>
                    </td>
                    <td class="text-right">Rp {{ number_format($pagu, 0, ',', '.') }}</td>
                    <td class="text-right text-green text-bold">Rp {{ number_format($realisasi, 0, ',', '.') }}</td>
                    <td class="text-right text-red text-bold">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                    <td class="text-center text-bold">{{ $item->realisasi_fisik }}%</td>
                    <td class="text-center">{{ $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada data Realisasi / Program pada filter ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right text-bold" style="background-color: #ecf0f1;">TOTAL KESELURUHAN:</td>
                <td class="text-right text-bold">Rp {{ number_format($grandPagu, 0, ',', '.') }}</td>
                <td class="text-right text-green text-bold">Rp {{ number_format($grandReal, 0, ',', '.') }}</td>
                <td class="text-right text-red text-bold">Rp {{ number_format($grandSisa, 0, ',', '.') }}</td>
                <td colspan="2" style="background-color: #ecf0f1;"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Data Di cetak Dengan Sistem RFK Provinsi Maluku Utara
    </div>

</body>
</html>
