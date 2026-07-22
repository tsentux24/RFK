<!DOCTYPE html>
<html>
<head>
    

    <meta charset="utf-8">
    <title>Laporan Realisasi RFK Detail</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }
        .header h3 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 10pt;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #bdc3c7;
            padding: 6px;
            vertical-align: top;
        }
        th {
            background-color: #ecf0f1;
            font-weight: bold;
            text-align: center;
            color: #2c3e50;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        .badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 8pt;
            color: white;
            display: inline-block;
            font-weight: bold;
        }
        .badge-success { background-color: #27ae60; }
        .badge-warning { background-color: #f1c40f; color: #2c3e50; }
        .badge-danger { background-color: #e74c3c; }
        .badge-info { background-color: #2980b9; }
        
        .detail-row td {
            background-color: #f9fbfd;
            border-top: none;
            padding: 6px 10px;
            font-size: 8.5pt;
        }
        .detail-grid {
            width: 100%;
            display: table;
        }
        .detail-col {
            display: table-cell;
            width: 33.33%;
            padding-right: 10px;
        }
        .detail-label {
            font-weight: bold;
            color: #7f8c8d;
            display: inline-block;
            width: 130px;
        }
        .detail-value {
            color: #2c3e50;
        }
    </style>
</head>
<body>
<?php 
$logo_path = public_path('images/malut.png');
$logo_base64 = '';
if(file_exists($logo_path)) {
    $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
}
?>
    <table width="100%" style="border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 20px;">
        <tr>
            <td width="15%" style="text-align: left; vertical-align: middle;">
                <img src="{{ $logo_base64 }}" alt="Logo Maluku Utara" style="width: 80px; height: auto;">
            </td>
            <td width="70%" style="text-align: center; vertical-align: middle;">
                <h3 style="margin: 0; font-size: 16pt; color: #2c3e50;">PEMERINTAH PROVINSI MALUKU UTARA</h3>
                <h3 style="margin: 5px 0 0 0; font-size: 14pt; color: #2c3e50;">LAPORAN REALISASI RFK DETAIL</h3>
            </td>
            <td width="15%"></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="10%">Kode</th>
                <th width="25%">Program Utama</th>
                <th width="8%">Sumber Dana</th>
                <th width="13%">Pagu (Rp)</th>
                <th width="13%">Realisasi (Rp)</th>
                <th width="8%">Fisik (%)</th>
                <th width="12%">Sisa Pagu (Rp)</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    $latestRealisasi = $item->realisasis->sortByDesc('created_at')->first();
                    $kode = $latestRealisasi ? $latestRealisasi->kode_program : '-';
                    $program = $latestRealisasi ? $latestRealisasi->nama_program : $item->keterangan;
                    $kegiatan = $latestRealisasi ? $latestRealisasi->kegiatan : '-';
                    $subKegiatan = $latestRealisasi ? $latestRealisasi->sub_kegiatan : '-';
                    $subKategoriProgram = $latestRealisasi ? $latestRealisasi->sub_kategori_program : '-';
                    
                    $katAnggaran = $latestRealisasi ? $latestRealisasi->kategori_anggaran : '-';
                    $subKatAnggaran = $latestRealisasi ? $latestRealisasi->sub_kategori_anggaran : '-';
                    $sumberDanaDetail = $latestRealisasi ? $latestRealisasi->sumber_dana_detail : '-';
                    $keteranganRealisasi = $latestRealisasi ? $latestRealisasi->keterangan : '-';
                    
                    $statusVal = $item->status;
                    if ($item->pagu > 0 && $item->realisasi_keuangan >= $item->pagu && $item->realisasi_fisik >= 100 && $item->status === 'APPROVE') {
                        $statusVal = 'SELESAI';
                    }
                    
                    $badgeClass = 'badge-warning';
                    if($statusVal == 'APPROVE') $badgeClass = 'badge-success';
                    elseif($statusVal == 'REJECT') $badgeClass = 'badge-danger';
                    elseif($statusVal == 'SELESAI') $badgeClass = 'badge-info';
                @endphp
                
                <!-- Main Row -->
                <tr>
                    <td class="text-center fw-bold" style="border-bottom: none;">{{ $index + 1 }}</td>
                    <td class="text-center fw-bold" style="border-bottom: none;">{{ $kode }}</td>
                    <td class="fw-bold" style="border-bottom: none;">{{ $program }}</td>
                    <td class="text-center fw-bold" style="border-bottom: none;">{{ $item->sumber_dana }}</td>
                    <td class="text-right fw-bold" style="border-bottom: none;">{{ number_format($item->pagu, 0, ',', '.') }}</td>
                    <td class="text-right fw-bold" style="border-bottom: none;">{{ number_format($item->realisasi_keuangan, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold" style="border-bottom: none;">{{ number_format($item->realisasi_fisik, 2, ',', '.') }}%</td>
                    <td class="text-right fw-bold" style="border-bottom: none;">{{ number_format($item->sisa_pagu, 0, ',', '.') }}</td>
                    <td class="text-center" style="border-bottom: none;">
                        <span class="badge {{ $badgeClass }}">{{ $statusVal }}</span>
                    </td>
                </tr>
                
                <!-- Detailed Info Row -->
                <tr class="detail-row">
                    <td colspan="9">
                        <div class="detail-grid">
                            <div class="detail-col">
                                <div><span class="detail-label">Kegiatan:</span> <span class="detail-value">{{ $kegiatan }}</span></div>
                                <div><span class="detail-label">Sub Kegiatan:</span> <span class="detail-value">{{ $subKegiatan }}</span></div>
                                <div><span class="detail-label">Sub Kategori Program:</span> <span class="detail-value">{{ $subKategoriProgram }}</span></div>
                            </div>
                            <div class="detail-col">
                                <div><span class="detail-label">Kategori Anggaran:</span> <span class="detail-value">{{ $katAnggaran }}</span></div>
                                <div><span class="detail-label">Sub Kategori Anggaran:</span> <span class="detail-value">{{ $subKatAnggaran }}</span></div>
                                <div><span class="detail-label">Rincian Sumber Dana:</span> <span class="detail-value">{{ $sumberDanaDetail }}</span></div>
                            </div>
                            <div class="detail-col">
                                <div><span class="detail-label">Update Terakhir:</span> <span class="detail-value">{{ $latestRealisasi ? $latestRealisasi->created_at->format('d/m/Y H:i') : '-' }}</span></div>
                                <div><span class="detail-label">Keterangan/Catatan:</span> <span class="detail-value">{{ $keteranganRealisasi }}</span></div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Data tidak ditemukan atau belum ada realisasi yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
