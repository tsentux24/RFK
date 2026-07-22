<?php

namespace App\Http\Controllers;

use App\Models\InputRfk;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RfkController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $opd = $user->opd;

        // Ambil data RFK berdasarkan OPD user
        $rfkData = InputRfk::with(['opd', 'user', 'realisasis' => function($q) {
            $q->orderBy('created_at', 'desc');
        }])
            ->where('opd_id', $opd->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.staff', compact('rfkData', 'opd'));
    }

    public function auditPage()
    {
        return view('dashboard.audit_rfk');
    }

    public function laporanPage()
    {
        $opds = Opd::orderBy('nama_opd', 'asc')->get();
        return view('dashboard.laporan', compact('opds'));
    }

    private function applyTriwulanFilter($data, $triwulan)
    {
        $startMonth = ($triwulan - 1) * 3 + 1;
        $endMonth = $startMonth + 2;

        foreach ($data as $item) {
            // Only count approved realisasis within the selected triwulan
            $realisasisInTriwulan = $item->realisasis->where('status', 'APPROVE')->filter(function($r) use ($startMonth, $endMonth) {
                $month = \Carbon\Carbon::parse($r->tanggal_input ?? $r->created_at)->month;
                return $month >= $startMonth && $month <= $endMonth;
            });

            $item->realisasi_keuangan = $realisasisInTriwulan->sum('nilai_realisasi_keuangan');
            $item->realisasi_fisik = $realisasisInTriwulan->sum('nilai_realisasi_fisik');
            $item->sisa_pagu = $item->pagu - $item->realisasi_keuangan;
            
            // Adjust status based on new filtered amounts
            if ($item->pagu > 0 && $item->realisasi_keuangan >= $item->pagu && $item->realisasi_fisik >= 100 && $item->status === 'APPROVE') {
                $item->status = 'SELESAI';
            } elseif ($item->status === 'SELESAI' && ($item->realisasi_keuangan < $item->pagu || $item->realisasi_fisik < 100)) {
                $item->status = 'APPROVE';
            }
        }

        return $data;
    }

    public function getLaporanData(Request $request)
    {
        $query = InputRfk::with(['opd', 'user', 'realisasis' => function($q) {
            $q->orderBy('created_at', 'asc'); // Ascending to show chronological progression
        }])
            ->whereIn('status', ['APPROVE', 'PENDING'])
            ->orderBy('created_at', 'desc');

        // Jika user staff/kepala_opd, batasi hanya OPD mereka
        $user = Auth::user();
        if (in_array($user->role, ['staff', 'kepala_opd']) && $user->opd_id) {
            $query->where('opd_id', $user->opd_id);
        }

        // Eager load realisasis to get latest program info
        $query->with(['opd', 'realisasis' => function($q) {
            $q->orderBy('created_at', 'desc');
        }]);

        $rawData = $query->get();
        
        if ($request->filled('triwulan')) {
            $rawData = $this->applyTriwulanFilter($rawData, $request->triwulan);
        }

        $data = $rawData->map(function($item) {
            $latestRealisasi = $item->realisasis->first();
            $item->nama_program = $latestRealisasi ? $latestRealisasi->nama_program : $item->keterangan;
            $item->kode_program = $latestRealisasi ? $latestRealisasi->kode_program : '-';
            $item->sub_kategori_program = $latestRealisasi ? $latestRealisasi->sub_kategori_program : '-';
            $item->kategori_anggaran = $latestRealisasi ? $latestRealisasi->kategori_anggaran : '-';
            $item->sub_kategori_anggaran = $latestRealisasi ? $latestRealisasi->sub_kategori_anggaran : '-';
            $item->sumber_dana_detail = $latestRealisasi ? $latestRealisasi->sumber_dana_detail : '-';
            $item->kegiatan = $latestRealisasi ? $latestRealisasi->kegiatan : '-';
            $item->sub_kegiatan = $latestRealisasi ? $latestRealisasi->sub_kegiatan : '-';
            $item->keterangan = $latestRealisasi ? $latestRealisasi->keterangan : '-';
            
            if ($item->pagu > 0 && $item->realisasi_keuangan >= $item->pagu && $item->realisasi_fisik >= 100 && $item->status === 'APPROVE') {
                $item->status = 'SELESAI';
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

        private function applyExportFilters($request)
    {
        $query = InputRfk::with(['opd', 'user', 'realisasis' => function($q) {
            $q->orderBy('created_at', 'asc');
        }]);
        
        $user = \Illuminate\Support\Facades\Auth::user();
        if (in_array($user->role, ['staff', 'kepala_opd']) && $user->opd_id) {
            $query->where('opd_id', $user->opd_id);
        }

        // Program Filter
        $programSearch = $request->program ?? $request->search;
        if (!empty($programSearch)) {
            $query->whereHas('realisasis', function($q) use ($programSearch) {
                $q->where('nama_program', 'LIKE', '%' . $programSearch . '%')
                  ->orWhere('kode_program', 'LIKE', '%' . $programSearch . '%');
            });
        }

        // OPD Filter (Hanya untuk Superadmin & Administrator)
        $opdSearch = $request->opd ?? $request->opd_id;
        if (!empty($opdSearch) && in_array($user->role, ['superadmin', 'administrator'])) {
            if (is_numeric($opdSearch)) {
                $query->where('opd_id', $opdSearch);
            } else {
                $query->whereHas('opd', function($q) use ($opdSearch) {
                    $q->where('nama_opd', 'LIKE', '%' . $opdSearch . '%');
                });
            }
        }

        // Tahun Filter
        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }

        // Status Filter
        if ($request->filled('status')) {
            if ($request->status === 'SELESAI') {
                $query->where('status', 'APPROVE')
                      ->whereColumn('realisasi_keuangan', '>=', 'pagu')
                      ->where('realisasi_fisik', '>=', 100)
                      ->where('pagu', '>', 0);
            } elseif ($request->status === 'APPROVE') {
                $query->where('status', 'APPROVE')
                      ->where(function($q) {
                          $q->whereColumn('realisasi_keuangan', '<', 'pagu')
                            ->orWhere('realisasi_fisik', '<', 100)
                            ->orWhere('pagu', '<=', 0);
                      });
            } else {
                $query->where('status', $request->status);
            }
        }

        $data = $query->orderBy('opd_id')->orderBy('created_at', 'desc')->get();

        // Triwulan Filter
        if ($request->filled('triwulan')) {
            $data = $this->applyTriwulanFilter($data, $request->triwulan);
        }

        // Fisik Filter
        if ($request->filled('fisik')) {
            $data = $data->filter(function($item) use ($request) {
                $fisik = (float) $item->realisasi_fisik;
                switch($request->fisik) {
                    case '0-25': return $fisik >= 0 && $fisik <= 25;
                    case '26-50': return $fisik > 25 && $fisik <= 50;
                    case '51-75': return $fisik > 50 && $fisik <= 75;
                    case '76-99': return $fisik > 75 && $fisik < 100;
                    case '100': return $fisik == 100;
                    default: return true;
                }
            })->values();
        }

        return $data;
    }

    public function generateLaporanPdf(Request $request)
    {
        try {
            $data = $this->applyExportFilters($request);

            // Calculate Grand Totals
            $grandPagu = $data->sum('pagu');
            $grandRealisasiKeuangan = $data->sum('realisasi_keuangan');
            $grandSisa = $data->sum('sisa_pagu');
            $averageFisik = $data->count() > 0 ? $data->avg('realisasi_fisik') : 0;
            
            // Group by OPD
            $groupedData = $data->groupBy(function($item) {
                return $item->opd ? $item->opd->nama_opd : 'Tanpa OPD';
            });

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.pdf.laporan_rfk', compact('groupedData', 'grandPagu', 'grandRealisasiKeuangan', 'grandSisa', 'averageFisik', 'request'))
                      ->setPaper('a4', 'landscape');

            $fileName = 'Laporan_RFK_' . time() . '.pdf';
            
            if (!\Storage::disk('public')->exists('pdfs')) {
                \Storage::disk('public')->makeDirectory('pdfs');
            }
            \Storage::disk('public')->put('pdfs/' . $fileName, $pdf->output());

            $url = asset('storage/pdfs/' . $fileName);

            return response()->json([
                'success' => true,
                'url' => $url
            ]);

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf(Request $request)
    {
        $data = $this->applyExportFilters($request);
        $opd_name = \Illuminate\Support\Facades\Auth::user()->opd ? \Illuminate\Support\Facades\Auth::user()->opd->nama_opd : 'Semua OPD';
        $fileName = 'Laporan_RFK_' . date('Y_m_d_H_i_s') . '.pdf';
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.laporan_rfk', compact('data', 'opd_name'))
                   ->setPaper('a4', 'landscape');
                   
        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->applyExportFilters($request);

        $fileName = 'Laporan_RFK_' . date('Y_m_d_H_i_s') . '.xls';
        
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta charset="UTF-8">';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan RFK</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>
            table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
            th, td { border: 1px solid #000; padding: 5px; }
            th { background-color: #4F81BD; color: #FFFFFF; font-weight: bold; text-align: center; }
            .num { mso-number-format: "\#\,\#\#0\.00"; }
            .text { mso-number-format: "\@"; }
        </style>';
        $html .= '</head><body>';
        
        $html .= '<table style="border: none; margin-bottom: 20px;"><tr>';
        $html .= '<td colspan="2" style="border: none; text-align: center;">';
        $logo_path = public_path('images/malut.png');
        if(file_exists($logo_path)) {
            $html .= '<img src="data:image/png;base64,' . base64_encode(file_get_contents($logo_path)) . '" width="80" height="80">';
        }
        $html .= '</td>';
        $html .= '<td colspan="14" style="border: none; text-align: center; vertical-align: middle;">';
        $html .= '<h2 style="margin: 0;">PEMERINTAH PROVINSI MALUKU UTARA</h2>';
        $html .= '<h3 style="margin: 0;">Laporan Rekapitulasi Realisasi Fisik dan Keuangan (SI-RAFIKA)</h3>';
        $html .= '</td></tr></table>';
        $html .= '<table>';
    
        $html .= '<thead><tr>';
        
        $columns = [
            'No', 'Kode Program', 'Nama Program Utama', 'Sub Kategori Program', 
            'Kategori Anggaran', 'Sub Kategori Anggaran', 'Rincian Sumber Dana', 
            'Nama Kegiatan', 'Sub Kegiatan', 'Keterangan/Catatan Realisasi',
            'Instansi / OPD', 'Tahun Anggaran', 'Update Terakhir',
            'Pagu Total (Rp)', 'Realisasi Keuangan (Rp)', 'Realisasi Fisik (%)', 
            'Sisa Pagu (Rp)', 'Status Program'
        ];
        
        foreach($columns as $col) {
            $html .= "<th>{$col}</th>";
        }
        $html .= '</tr></thead><tbody>';

        foreach ($data as $index => $item) {
            $latestRealisasi = $item->realisasis->sortByDesc('created_at')->first();
            
            $statusVal = $item->status;
            if ($item->pagu > 0 && $item->realisasi_keuangan >= $item->pagu && $item->realisasi_fisik >= 100 && $item->status === 'APPROVE') {
                $statusVal = 'SELESAI';
            }

            $html .= '<tr>';
            $html .= "<td>" . ($index + 1) . "</td>";
            $html .= "<td class='text'>" . ($latestRealisasi ? $latestRealisasi->kode_program : '-') . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->nama_program : $item->keterangan) . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->sub_kategori_program : '-') . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->kategori_anggaran : '-') . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->sub_kategori_anggaran : '-') . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->sumber_dana_detail : '-') . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->kegiatan : '-') . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->sub_kegiatan : '-') . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->keterangan : '-') . "</td>";
            $html .= "<td>" . ($item->opd ? $item->opd->nama_opd : '-') . "</td>";
            $html .= "<td class='text'>" . $item->tahun_anggaran . "</td>";
            $html .= "<td>" . ($latestRealisasi ? $latestRealisasi->created_at->format('d/m/Y H:i') : '-') . "</td>";
            $html .= "<td class='num'>" . $item->pagu . "</td>";
            $html .= "<td class='num'>" . $item->realisasi_keuangan . "</td>";
            $html .= "<td class='num'>" . $item->realisasi_fisik . "</td>";
            $html .= "<td class='num'>" . $item->sisa_pagu . "</td>";
            
            $color = $statusVal === 'SELESAI' ? '#C6EFCE' : ($statusVal === 'APPROVE' ? '#C6EFCE' : ($statusVal === 'PENDING' ? '#FFEB9C' : ''));
            $textColor = $statusVal === 'SELESAI' ? '#006100' : ($statusVal === 'APPROVE' ? '#006100' : ($statusVal === 'PENDING' ? '#9C5700' : ''));
            $html .= "<td style='background-color: {$color}; color: {$textColor}; font-weight: bold; text-align:center;'>" . $statusVal . "</td>";
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function getDashboardStats(Request $request)
    {
        $query = InputRfk::query();

        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        $totalProgram = (clone $query)->count();
        $totalPagu = (clone $query)->sum('pagu');
        $totalRealisasi = (clone $query)->sum('realisasi_keuangan');
        $totalSisaPagu = (clone $query)->sum('sisa_pagu');
        $avgFisik = $totalProgram > 0 ? (clone $query)->avg('realisasi_fisik') : 0;
        
        $totalOpd = Opd::count();
        $opdInputIds = InputRfk::distinct('opd_id')->pluck('opd_id')->toArray();
        $opdInputCount = count($opdInputIds);
        $opdBelumInput = max(0, $totalOpd - $opdInputCount);

        // List of OPDs not yet inputted
        $opdBelumList = Opd::whereNotIn('id', $opdInputIds)->orderBy('nama_opd', 'asc')->get();

        $selesaiCount = (clone $query)->where('status', 'APPROVE')
            ->where('pagu', '>', 0)
            ->whereRaw('realisasi_keuangan >= pagu')
            ->where('realisasi_fisik', '>=', 100)
            ->count();

        $approveCount = (clone $query)->where('status', 'APPROVE')->count() - $selesaiCount;

        // Data for Diagram (Group by Status or by Time)
        $rfkStatusCount = [
            'SELESAI' => $selesaiCount,
            'APPROVE' => max(0, $approveCount),
            'PENDING' => (clone $query)->where('status', 'PENDING')->count(),
            'REJECT' => (clone $query)->where('status', 'REJECT')->count(),
        ];

        // Data for Modern Chart (Realisasi per OPD)
        $opdStats = (clone $query)->with('opd')
            ->selectRaw('opd_id, SUM(pagu) as total_pagu, SUM(realisasi_keuangan) as total_realisasi, SUM(sisa_pagu) as total_sisa')
            ->groupBy('opd_id')
            ->having('total_pagu', '>', 0)
            ->get()
            ->map(function ($item) {
                return [
                    'opd' => $item->opd ? substr($item->opd->nama_opd, 0, 15) . '...' : 'Lainnya',
                    'pagu' => (float) $item->total_pagu,
                    'realisasi' => (float) $item->total_realisasi,
                    'sisa' => (float) $item->total_sisa,
                ];
            });

        // TOP 10 OPD Dengan PAGU terbesar
        $top10OpdPagu = (clone $query)->with('opd')
            ->selectRaw('opd_id, SUM(pagu) as total_pagu')
            ->groupBy('opd_id')
            ->orderByDesc('total_pagu')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'opd' => $item->opd ? substr($item->opd->nama_opd, 0, 25) . (strlen($item->opd->nama_opd) > 25 ? '...' : '') : 'Tanpa OPD',
                    'full_nama' => $item->opd ? $item->opd->nama_opd : 'Tanpa OPD',
                    'wilayah' => $item->opd && $item->opd->kabupaten_kota ? $item->opd->kabupaten_kota : 'Provinsi Maluku Utara',
                    'pagu' => (float) $item->total_pagu,
                ];
            });

        // Data for OPD Terbaru (Top 3 active OPDs by recent program update)
        $opdTerbaruIds = (clone $query)->orderBy('updated_at', 'desc')->pluck('opd_id')->unique()->take(3);
        $opdTerbaru = Opd::whereIn('id', $opdTerbaruIds)->get()->map(function($opd) {
            return [
                'nama' => $opd->nama_opd,
                'kode' => 'OPD-' . str_pad($opd->id, 3, '0', STR_PAD_LEFT)
            ];
        });

        // Data for Arsip Terbaru (Top 3 recent realisasis)
        $arsipTerbaru = \App\Models\RfkRealisasi::with(['inputRfk.opd'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function($realisasi) {
                $programName = 'Program Realisasi';
                if ($realisasi->inputRfk && !empty($realisasi->inputRfk->nama_program)) {
                    $programName = $realisasi->inputRfk->nama_program;
                } elseif (!empty($realisasi->nama_program)) {
                    $programName = $realisasi->nama_program;
                }

                $opdName = 'Tanpa OPD';
                if ($realisasi->inputRfk && $realisasi->inputRfk->opd && !empty($realisasi->inputRfk->opd->nama_opd)) {
                    $opdName = $realisasi->inputRfk->opd->nama_opd;
                } elseif (!empty($realisasi->opd_name)) { // Just in case there's another relation
                    $opdName = $realisasi->opd_name;
                }

                return [
                    'program' => $programName,
                    'opd' => $opdName,
                    'waktu' => $realisasi->created_at->diffForHumans(),
                    'jenis' => 'Data Realisasi'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $this->sanitizeArray([
                'total_program' => $totalProgram,
                'total_pagu' => $totalPagu,
                'total_realisasi' => $totalRealisasi,
                'total_sisa_pagu' => $totalSisaPagu,
                'avg_fisik' => round($avgFisik, 2),
                'opd_belum_input' => $opdBelumInput,
                'opd_belum_list' => $opdBelumList,
                'diagram_status' => $rfkStatusCount,
                'diagram_opd' => $opdStats,
                'top10_opd_pagu' => $top10OpdPagu,
                'opd_terbaru' => $opdTerbaru,
                'arsip_terbaru' => $arsipTerbaru
            ])
        ]);
    }

    public function getSuperadminData(Request $request)
    {
        $query = InputRfk::with(['opd', 'realisasis']);

        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        $allPrograms = $query->get()->map(function($p) {
            if ($p->pagu > 0 && $p->realisasi_keuangan >= $p->pagu && $p->realisasi_fisik >= 100 && $p->status === 'APPROVE') {
                $p->status = 'SELESAI';
            }
            return $p;
        });

        $totalProgram = $allPrograms->count();
        $totalPagu = $allPrograms->sum('pagu');
        $totalRealisasi = $allPrograms->sum('realisasi_keuangan');
        $totalSisaPagu = $allPrograms->sum('sisa_pagu');
        $avgFisik = $totalProgram > 0 ? $allPrograms->avg('realisasi_fisik') : 0;

        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('m');
        $thisYear = now()->format('Y');

        $totalOpd = Opd::count();
        $opdInputIds = InputRfk::distinct('opd_id')->pluck('opd_id')->toArray();
        $opdInputCount = count($opdInputIds);
        $opdBelumInput = max(0, $totalOpd - $opdInputCount);
        $opdBelumList = Opd::whereNotIn('id', $opdInputIds)->orderBy('nama_opd', 'asc')->get();

        $realisasiHarian = (clone $query)->whereDate('tanggal_input', $today)->sum('realisasi_keuangan');
        $realisasiBulanan = (clone $query)->whereMonth('tanggal_input', $thisMonth)
                                         ->whereYear('tanggal_input', $thisYear)->sum('realisasi_keuangan');
        $realisasiTahunan = (clone $query)->whereYear('tanggal_input', $thisYear)->sum('realisasi_keuangan');

        // Group by OPD
        $grouped = $allPrograms->groupBy('opd_id');
        $jumlahOpdTercatat = $grouped->count();

        $opdsData = [];
        foreach ($grouped as $opdId => $programs) {
            $opdModel = $programs->first()->opd;
            
            $opdPagu = $programs->sum('pagu');
            $opdRealisasi = $programs->sum('realisasi_keuangan');
            $opdSisa = $programs->sum('sisa_pagu');
            $opdPersen = $opdPagu > 0 ? round(($opdRealisasi / $opdPagu) * 100, 2) : 0;
            $opdFisikAvg = $programs->count() > 0 ? round($programs->avg('realisasi_fisik'), 2) : 0;

            $programList = $programs->map(function($p) {
                $latestRealisasi = $p->realisasis->sortByDesc('created_at')->first();
                $statusVal = $p->status; // Already set to SELESAI above if applicable
                return [
                    'id' => $p->id,
                    'kode' => $latestRealisasi ? $latestRealisasi->kode_program : '-',
                    'nama' => $latestRealisasi ? $latestRealisasi->nama_program : $p->keterangan,
                    'pagu' => (float) $p->pagu,
                    'realisasi' => (float) $p->realisasi_keuangan,
                    'sisa' => (float) $p->sisa_pagu,
                    'fisik' => (float) $p->realisasi_fisik,
                    'status' => $statusVal,
                    'sub_kategori_program' => $latestRealisasi ? $latestRealisasi->sub_kategori_program : '-',
                    'sumber_dana' => $p->sumber_dana,
                    'kategori_anggaran' => $latestRealisasi ? $latestRealisasi->kategori_anggaran : '-',
                    'sub_kategori_anggaran' => $latestRealisasi ? $latestRealisasi->sub_kategori_anggaran : '-',
                    'sumber_dana_detail' => $latestRealisasi ? $latestRealisasi->sumber_dana_detail : '-',
                    'kegiatan' => $latestRealisasi ? $latestRealisasi->kegiatan : '-',
                    'sub_kegiatan' => $latestRealisasi ? $latestRealisasi->sub_kegiatan : '-',
                    'keterangan' => $latestRealisasi ? $latestRealisasi->keterangan : '-',
                    'tahun_anggaran' => $p->tahun_anggaran
                ];
            })->values()->toArray();

            $sdGroup = $programs->groupBy('sumber_dana');
            $sdMatrix = [];
            foreach ($sdGroup as $sdName => $sdProgs) {
                $mPagu = $sdProgs->sum('pagu');
                $mRealisasi = $sdProgs->sum('realisasi_keuangan');
                $mSisa = $sdProgs->sum('sisa_pagu');
                $sdMatrix[] = [
                    'sumber_dana' => $sdName ?: 'Lainnya',
                    'pagu' => (float) $mPagu,
                    'realisasi' => (float) $mRealisasi,
                    'sisa' => (float) $mSisa,
                    'persentase' => $mPagu > 0 ? round(($mRealisasi / $mPagu) * 100, 2) : 0,
                ];
            }

            $opdsData[] = [
                'id' => $opdId,
                'nama_opd' => $opdModel ? $opdModel->nama_opd : 'Lainnya',
                'kabupaten_kota' => $opdModel ? $opdModel->kabupaten_kota : 'Provinsi Maluku Utara',
                'pagu' => (float) $opdPagu,
                'realisasi' => (float) $opdRealisasi,
                'sisa' => (float) $opdSisa,
                'persentase' => (float) $opdPersen,
                'rata_rata_fisik' => (float) $opdFisikAvg,
                'programs' => $programList,
                'sumber_dana_matrix' => $sdMatrix
            ];
        }

        // Sort by nama_opd
        usort($opdsData, function($a, $b) {
            return strcmp($a['nama_opd'], $b['nama_opd']);
        });

        
        $hist = [0, 0, 0, 0];
        foreach ($allPrograms as $p) {
            if ($p->pagu < 100000000) {
                $hist[0]++;
            } elseif ($p->pagu <= 500000000) {
                $hist[1]++;
            } elseif ($p->pagu <= 1000000000) {
                $hist[2]++;
            } else {
                $hist[3]++;
            }
        }
        
        // --- 16 Kategori Rincian Alokasi Dana ---
        $rincianAlokasi = [
            'apbd_operasi' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'apbd_modal' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'belanja_pegawai' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'belanja_barang_jasa' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'belanja_hibah' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'modal_peralatan' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'modal_jalan' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'modal_gedung' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            
            'apbn_dau' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'apbn_modal' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'apbn_dak' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'apbn_dak_fisik' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'apbn_dak_non_fisik' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'apbn_dbh' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'apbn_dekom' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
            'apbn_tp' => ['pagu' => 0, 'realisasi' => 0, 'programs' => []],
        ];

        foreach ($allPrograms as $p) {
            $sd = strtoupper($p->sumber_dana ?? '');
            $kat = ''; $sub = ''; $detail = '';
            if ($p->realisasis && $p->realisasis->count() > 0) {
                $lastReal = $p->realisasis->sortByDesc('id')->first();
                $kat = strtoupper($lastReal->kategori_anggaran ?? '');
                $sub = strtoupper($lastReal->sub_kategori_anggaran ?? '');
                $detail = strtoupper($lastReal->sumber_dana_detail ?? '');
            }

            $progData = [
                'opd' => $p->opd->nama_opd ?? '-',
                'nama' => $p->nama_program ?? 'Program Tanpa Nama',
                'pagu' => (float)($p->pagu ?? 0),
                'realisasi' => (float)($p->realisasi_keuangan ?? 0),
                'persen' => ($p->pagu > 0) ? round(((float)($p->realisasi_keuangan ?? 0) / $p->pagu) * 100, 2) : 0
            ];

            if ($sd === 'APBD') {
                if ($kat === 'BELANJA_OPERASI' || strpos($kat, 'OPERASI') !== false) {
                    $rincianAlokasi['apbd_operasi']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbd_operasi']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbd_operasi']['programs'][] = $progData;
                    
                    if ($sub === 'BELANJA_PEGAWAI' || strpos($sub, 'PEGAWAI') !== false) {
                        $rincianAlokasi['belanja_pegawai']['pagu'] += $progData['pagu'];
                        $rincianAlokasi['belanja_pegawai']['realisasi'] += $progData['realisasi'];
                        $rincianAlokasi['belanja_pegawai']['programs'][] = $progData;
                    } elseif ($sub === 'BELANJA_BARANG_JASA' || strpos($sub, 'BARANG') !== false || strpos($sub, 'JASA') !== false) {
                        $rincianAlokasi['belanja_barang_jasa']['pagu'] += $progData['pagu'];
                        $rincianAlokasi['belanja_barang_jasa']['realisasi'] += $progData['realisasi'];
                        $rincianAlokasi['belanja_barang_jasa']['programs'][] = $progData;
                    } elseif ($sub === 'BELANJA_HIBAH' || strpos($sub, 'HIBAH') !== false || strpos($kat, 'HIBAH') !== false) {
                        $rincianAlokasi['belanja_hibah']['pagu'] += $progData['pagu'];
                        $rincianAlokasi['belanja_hibah']['realisasi'] += $progData['realisasi'];
                        $rincianAlokasi['belanja_hibah']['programs'][] = $progData;
                    }
                } elseif ($kat === 'BELANJA_MODAL' || strpos($kat, 'MODAL') !== false) {
                    $rincianAlokasi['apbd_modal']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbd_modal']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbd_modal']['programs'][] = $progData;
                    
                    if ($sub === 'BELANJA_MODAL_PERALATAN_MESIN' || strpos($sub, 'MESIN') !== false) {
                        $rincianAlokasi['modal_peralatan']['pagu'] += $progData['pagu'];
                        $rincianAlokasi['modal_peralatan']['realisasi'] += $progData['realisasi'];
                        $rincianAlokasi['modal_peralatan']['programs'][] = $progData;
                    } elseif ($sub === 'BELANJA_MODAL_JALAN_IRIGASI' || strpos($sub, 'JALAN') !== false || strpos($sub, 'IRIGASI') !== false) {
                        $rincianAlokasi['modal_jalan']['pagu'] += $progData['pagu'];
                        $rincianAlokasi['modal_jalan']['realisasi'] += $progData['realisasi'];
                        $rincianAlokasi['modal_jalan']['programs'][] = $progData;
                    } elseif ($sub === 'BELANJA_MODAL_BANGUNAN_GEDUNG' || strpos($sub, 'GEDUNG') !== false) {
                        $rincianAlokasi['modal_gedung']['pagu'] += $progData['pagu'];
                        $rincianAlokasi['modal_gedung']['realisasi'] += $progData['realisasi'];
                        $rincianAlokasi['modal_gedung']['programs'][] = $progData;
                    }
                }
            } elseif ($sd === 'APBN') {
                if ($detail === 'DAU' || strpos($detail, 'UMUM') !== false) {
                    $rincianAlokasi['apbn_dau']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_dau']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_dau']['programs'][] = $progData;
                } elseif ($detail === 'DAK_FISIK' || (strpos($detail, 'DAK') !== false && strpos($detail, 'NON') === false)) {
                    $rincianAlokasi['apbn_dak_fisik']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_dak_fisik']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_dak_fisik']['programs'][] = $progData;
                    
                    $rincianAlokasi['apbn_dak']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_dak']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_dak']['programs'][] = $progData;
                } elseif ($detail === 'DAK_NON_FISIK' || (strpos($detail, 'DAK') !== false && strpos($detail, 'NON') !== false)) {
                    $rincianAlokasi['apbn_dak_non_fisik']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_dak_non_fisik']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_dak_non_fisik']['programs'][] = $progData;
                    
                    $rincianAlokasi['apbn_dak']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_dak']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_dak']['programs'][] = $progData;
                } elseif ($detail === 'DBH' || strpos($detail, 'HASIL') !== false) {
                    $rincianAlokasi['apbn_dbh']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_dbh']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_dbh']['programs'][] = $progData;
                } elseif ($detail === 'DEKONSENTRASI' || strpos($detail, 'DEKOM') !== false) {
                    $rincianAlokasi['apbn_dekom']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_dekom']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_dekom']['programs'][] = $progData;
                } elseif ($detail === 'TUGAS_PEMBANTUAN' || strpos($detail, 'TUGAS') !== false) {
                    $rincianAlokasi['apbn_tp']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_tp']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_tp']['programs'][] = $progData;
                }
                
                if (strpos($kat, 'MODAL') !== false || strpos($detail, 'MODAL') !== false) {
                    $rincianAlokasi['apbn_modal']['pagu'] += $progData['pagu'];
                    $rincianAlokasi['apbn_modal']['realisasi'] += $progData['realisasi'];
                    $rincianAlokasi['apbn_modal']['programs'][] = $progData;
                }
            }
            
            if ($sd === 'APBD' && (strpos($kat, 'HIBAH') !== false || strpos($sub, 'HIBAH') !== false) && !in_array($progData, $rincianAlokasi['belanja_hibah']['programs'])) {
                 $rincianAlokasi['belanja_hibah']['pagu'] += $progData['pagu'];
                 $rincianAlokasi['belanja_hibah']['realisasi'] += $progData['realisasi'];
                 $rincianAlokasi['belanja_hibah']['programs'][] = $progData;
            }
        }

        $lastUpdated = $allPrograms->max('updated_at');
        $lastUpdatedAtFormatted = $lastUpdated ? \Carbon\Carbon::parse($lastUpdated)->isoFormat('D MMMM YYYY, HH:mm') . ' WIB' : 'Belum ada data';

        $rfkStatusCount = [
            'SELESAI' => $allPrograms->where('status', 'SELESAI')->count(),
            'APPROVE' => $allPrograms->where('status', 'APPROVE')->count(),
            'PENDING' => $allPrograms->where('status', 'PENDING')->count(),
            'REJECT' => $allPrograms->where('status', 'REJECT')->count(),
        ];

        // Group by Sumber Dana
        $sumberDanaGroup = $allPrograms->groupBy('sumber_dana');
        $sumberDanaData = [];
        foreach ($sumberDanaGroup as $sumber => $progs) {
            $sdPagu = $progs->sum('pagu');
            $sdRealisasi = $progs->sum('realisasi_keuangan');
            $sdSisa = $progs->sum('sisa_pagu');
            $sdPersen = $sdPagu > 0 ? round(($sdRealisasi / $sdPagu) * 100, 2) : 0;
            
            $sumberDanaData[] = [
                'sumber_dana' => $sumber ?: 'Lainnya',
                'pagu' => (float) $sdPagu,
                'realisasi' => (float) $sdRealisasi,
                'sisa' => (float) $sdSisa,
                'persentase' => (float) $sdPersen,
                'jumlah_program' => $progs->count()
            ];
        }

        // Sort Sumber Dana by Pagu (Highest first)
        usort($sumberDanaData, function($a, $b) {
            return $b['pagu'] <=> $a['pagu'];
        });

        // Analytics: Top 5 OPD dengan REJECT terbanyak
        $topRejectOpds = \DB::table('rfk_realisasi_histories')
            ->join('rfk_realisasis', 'rfk_realisasi_histories.rfk_realisasi_id', '=', 'rfk_realisasis.id')
            ->join('table_input_rfk', 'rfk_realisasis.input_rfk_id', '=', 'table_input_rfk.id')
            ->join('opds', 'table_input_rfk.opd_id', '=', 'opds.id')
            ->where('rfk_realisasi_histories.status_baru', 'REJECT')
            ->select('opds.nama_opd', \DB::raw('count(rfk_realisasi_histories.id) as total_reject'))
            ->groupBy('opds.nama_opd')
            ->orderByDesc('total_reject')
            ->limit(5)
            ->get();

        // 1. Top 10 Paket Terbesar (Pagu Terbesar)
        $top10Paket = $allPrograms->sortByDesc('pagu')->take(10)->map(function($p) {
            $latestRealisasi = $p->realisasis->sortByDesc('created_at')->first();
            $p->nama_program = $latestRealisasi ? $latestRealisasi->nama_program : $p->keterangan;
            $p->opd_name = $p->opd ? $p->opd->nama_opd : 'Lainnya';
            if ($p->pagu > 0 && $p->realisasi_keuangan >= $p->pagu && $p->realisasi_fisik >= 100 && $p->status === 'APPROVE') {
                $p->status = 'SELESAI';
            }
            return $p;
        })->values();

        // 2. Program Serapan Tertinggi & Terendah
        $programsWithPersen = $allPrograms->filter(function($p) { return $p->pagu > 0; })->map(function($p) {
            $latestRealisasi = $p->realisasis->sortByDesc('created_at')->first();
            $p->nama_program = $latestRealisasi ? $latestRealisasi->nama_program : $p->keterangan;
            $p->persentase = round(($p->realisasi_keuangan / $p->pagu) * 100, 2);
            $p->opd_name = $p->opd ? $p->opd->nama_opd : 'Lainnya';
            // $p->status is already updated
            return $p;
        });
        $serapanTertinggi = $programsWithPersen->sortByDesc('persentase')->take(5)->values();
        $serapanTerendah = $programsWithPersen->sortBy('persentase')->take(5)->values();

        // 3. Traffic Light OPD
        $trafficLight = [
            'hijau' => [],
            'kuning' => [],
            'merah' => []
        ];
        
        $rankingOpd = collect($opdsData)->sortByDesc('persentase')->values();
        
        foreach ($rankingOpd as $opd) {
            if ($opd['persentase'] >= 90) {
                $trafficLight['hijau'][] = $opd;
            } elseif ($opd['persentase'] >= 70) {
                $trafficLight['kuning'][] = $opd;
            } else {
                $trafficLight['merah'][] = $opd;
            }
        }

        $top10OpdPagu = collect($opdsData)->sortByDesc('pagu')->take(10)->map(function($opd) {
            return [
                'opd' => substr($opd['nama_opd'], 0, 25) . (strlen($opd['nama_opd']) > 25 ? '...' : ''),
                'full_nama' => $opd['nama_opd'],
                'wilayah' => $opd['kabupaten_kota'],
                'pagu' => $opd['pagu'],
                'realisasi' => $opd['realisasi']
            ];
        })->values();

        // Data Peta Sebaran by Kabupaten/Kota
        $mapData = collect($opdsData)->groupBy(function($item) {
            return $item['kabupaten_kota'] ?: 'Provinsi Maluku Utara';
        })->map(function($items, $kabupaten) {
            // Preset koordinat berdasarkan nama kabupaten
            $coords = [
                'Ternate' => ['lat' => 0.7933, 'lng' => 127.3828],
                'Tidore' => ['lat' => 0.6868, 'lng' => 127.4294],
                'Halmahera Utara' => ['lat' => 1.6366, 'lng' => 127.9405],
                'Halmahera Selatan' => ['lat' => -0.6394, 'lng' => 127.8931],
                'Halmahera Barat' => ['lat' => 1.1578, 'lng' => 127.5028],
                'Halmahera Timur' => ['lat' => 1.1524, 'lng' => 128.3243],
                'Halmahera Tengah' => ['lat' => 0.2831, 'lng' => 127.9405],
                'Pulau Morotai' => ['lat' => 2.1158, 'lng' => 128.3122],
                'Kepulauan Sula' => ['lat' => -1.9723, 'lng' => 125.9926],
                'Pulau Taliabu' => ['lat' => -1.8211, 'lng' => 124.6391],
                'Provinsi Maluku Utara' => ['lat' => 0.7300, 'lng' => 127.8000] // Pusat provinsi
            ];
            
            $latlng = ['lat' => 0.7300, 'lng' => 127.8000]; // Default
            foreach ($coords as $k => $c) {
                if (stripos($kabupaten, $k) !== false) {
                    $latlng = $c;
                    break;
                }
            }

            $totalPagu = $items->sum('pagu');
            $totalReal = $items->sum('realisasi');
            $jumlahOpd = $items->count();
            $jumlahProgram = $items->sum(function($opd) { return count($opd['programs']); });
            
            $avgFisik = 0;
            if ($items->count() > 0) {
                $avgFisik = $items->avg('rata_rata_fisik');
            }

            return [
                'nama' => $kabupaten,
                'lat' => $latlng['lat'],
                'lng' => $latlng['lng'],
                'pagu' => $totalPagu,
                'realisasi' => $totalReal,
                'fisik' => round($avgFisik, 2),
                'jumlah_opd' => $jumlahOpd,
                'jumlah_paket' => $jumlahProgram
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $this->sanitizeArray([
                'last_updated_at' => $lastUpdatedAtFormatted,
                'total_program' => $totalProgram,
                'total_pagu' => $totalPagu,
                'total_realisasi' => $totalRealisasi,
                'total_sisa_pagu' => $totalSisaPagu,
                'avg_fisik' => round($avgFisik, 2),
                'opd_belum_input' => $opdBelumInput,
                'opd_belum_list' => $opdBelumList,
                'realisasi_harian' => $realisasiHarian,
                'realisasi_bulanan' => $realisasiBulanan,
                'realisasi_tahunan' => $realisasiTahunan,
                'jumlah_opd_tercatat' => $jumlahOpdTercatat,
                'diagram_status' => $rfkStatusCount,
                'diagram_sumber_dana' => $sumberDanaData,
                'top_reject_opds' => $topRejectOpds->toArray(),
                'top_10_paket' => $top10Paket->toArray(),
                'top10_opd_pagu' => $top10OpdPagu->toArray(),
                'hist' => $hist,
                'peta_sebaran' => $mapData->toArray(),
                'serapan_tertinggi' => $serapanTertinggi->toArray(),
                'serapan_terendah' => $serapanTerendah->toArray(),
                'traffic_light' => [
                    'hijau' => count($trafficLight['hijau']),
                    'kuning' => count($trafficLight['kuning']),
                    'merah' => count($trafficLight['merah']),
                    'detail' => $trafficLight
                ],
                'ranking_opd' => $rankingOpd,
                'rincian_alokasi' => $rincianAlokasi,
                'opds' => $opdsData
            ])
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'sumber_dana' => 'required|in:APBD,APBN',
                'tahun_anggaran' => 'required|integer|min:2020|max:2030',
                'pagu' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = Auth::user();
            $opd = $user->opd;
            $pagu = $request->pagu;

            // Buat Master Program (Akumulasi Awal = 0)
            $rfk = InputRfk::create([
                'sumber_dana' => $request->sumber_dana,
                'tahun_anggaran' => $request->tahun_anggaran,
                'pagu' => $pagu,
                'realisasi_keuangan' => 0,
                'realisasi_fisik' => 0,
                'sisa_pagu' => $pagu,
                'opd_id' => $opd ? $opd->id : null,
                'status' => 'PENDING',
                'keterangan' => $request->keterangan ?? 'Pembuatan Program',
                'user_id' => $user->id,
                'tanggal_input' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Program dan pengajuan realisasi berhasil disimpan. Menunggu approval.',
                'data' => $rfk
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error store RFK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeRealisasi(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $cutoff = \App\Models\Setting::where('key', 'cutoff_realisasi')->first();
            if ($cutoff && $cutoff->value) {
                $cutoffDate = \Carbon\Carbon::parse($cutoff->value);
                if (now()->greaterThan($cutoffDate)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Masa pelaporan realisasi telah ditutup pada ' . $cutoffDate->format('d M Y H:i') . '. Hubungi Superadmin.'
                    ], 422);
                }
            }

            $rfk = InputRfk::findOrFail($id);

            if (Auth::user()->role === 'staff' && $rfk->opd_id !== Auth::user()->opd_id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Program ini bukan milik OPD Anda.'], 403);
            }

            // Validasi status Master: Hanya bisa tambah jika status APPROVE atau REJECT (perbaikan)
            if (!in_array($rfk->status, ['APPROVE', 'REJECT'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status program saat ini adalah ' . $rfk->status . '. Tambahan realisasi hanya dapat dilakukan jika status sudah APPROVE atau REJECT.'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'kode_program' => 'required|string|max:100',
                'nama_program' => 'required|string|max:255',
                'sub_kategori_program' => 'nullable|string|max:255',
                'kategori_anggaran' => 'nullable|string',
                'sub_kategori_anggaran' => 'nullable|string',
                'sumber_dana_detail' => 'nullable|string',
                'nilai_realisasi_keuangan' => 'required|numeric|min:1',
                'kegiatan' => 'required|string|max:255',
                'sub_kegiatan' => 'required|string|max:255',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            if (trim($request->nama_program) === 'Belum Ada Realisasi' || trim(strtoupper($request->nama_program)) === 'SKPD' || trim(strtoupper($request->nama_program)) === 'UNDEFINED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama Program tidak boleh menggunakan teks default (seperti "SKPD", "Undefined", atau "Belum Ada Realisasi"). Silakan ganti dengan nama program yang valid sesuai DPA Anda.'
                ], 422);
            }

            $nilaiInput = $request->nilai_realisasi_keuangan;

            if ($nilaiInput > $rfk->sisa_pagu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai realisasi melebihi sisa pagu saat ini.'
                ], 422);
            }

            $realisasiFisik = $rfk->pagu > 0 ? ($nilaiInput / $rfk->pagu) * 100 : 0;
            
            $realisasiBaru = $rfk->realisasis()->create([
                'kode_program' => $request->kode_program,
                'nama_program' => $request->nama_program,
                'sub_kategori_program' => $request->sub_kategori_program,
                'kategori_anggaran' => $request->kategori_anggaran,
                'sub_kategori_anggaran' => $request->sub_kategori_anggaran,
                'sumber_dana_detail' => $request->sumber_dana_detail,
                'nilai_realisasi_keuangan' => $nilaiInput,
                'nilai_realisasi_fisik' => $realisasiFisik,
                'status' => 'PENDING',
                'kegiatan' => $request->kegiatan,
                'sub_kegiatan' => $request->sub_kegiatan,
                'keterangan' => $request->keterangan,
                'user_id' => Auth::id(),
                'tanggal_input' => now()
            ]);

            // Update status Master menjadi PENDING karena ada pengajuan baru yang berjalan
            $rfk->update(['status' => 'PENDING']);

            \App\Models\RfkRealisasiHistory::create([
                'rfk_realisasi_id' => $realisasiBaru->id,
                'status_sebelumnya' => null,
                'status_baru' => 'PENDING',
                'keterangan' => 'Pengajuan awal oleh Staff',
                'user_id' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Realisasi berhasil diajukan dan menunggu approval Kepala OPD.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function approveRealisasi($id)
    {
        try {
            DB::beginTransaction();
            $realisasi = \App\Models\RfkRealisasi::findOrFail($id);
            $master = \App\Models\InputRfk::lockForUpdate()->findOrFail($realisasi->input_rfk_id);

            if (Auth::user()->role === 'kepala_opd' && $master->opd_id !== Auth::user()->opd_id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Program ini bukan milik OPD Anda.'], 403);
            }

            if ($realisasi->status != 'PENDING') {
                return response()->json(['success' => false, 'message' => 'Status bukan PENDING'], 422);
            }

            // Update status detail
            $realisasi->update([
                'status' => 'APPROVE',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Update Master Akumulasi
            $newRealisasiKeu = $master->realisasi_keuangan + $realisasi->nilai_realisasi_keuangan;
            $newRealisasiFisik = $master->realisasi_fisik + $realisasi->nilai_realisasi_fisik;
            $newSisaPagu = $master->pagu - $newRealisasiKeu;

            if ($newSisaPagu < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Persetujuan gagal. Nilai pengajuan ini akan mengakibatkan sisa pagu menjadi minus (Over Budget).'
                ], 422);
            }

            $master->update([
                'realisasi_keuangan' => $newRealisasiKeu,
                'realisasi_fisik' => min(100, $newRealisasiFisik),
                'sisa_pagu' => $newSisaPagu,
                'status' => 'APPROVE' // Tandai program berjalan dan siap untuk realisasi berikutnya
            ]);

            \App\Models\RfkRealisasiHistory::create([
                'rfk_realisasi_id' => $realisasi->id,
                'status_sebelumnya' => 'PENDING',
                'status_baru' => 'APPROVE',
                'keterangan' => 'Disetujui oleh ' . Auth::user()->name,
                'user_id' => Auth::id()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Realisasi Berhasil Disetujui']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function rejectRealisasi(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $realisasi = \App\Models\RfkRealisasi::findOrFail($id);
            $master = \App\Models\InputRfk::lockForUpdate()->findOrFail($realisasi->input_rfk_id);

            if (Auth::user()->role === 'kepala_opd' && $master->opd_id !== Auth::user()->opd_id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Program ini bukan milik OPD Anda.'], 403);
            }

            if ($realisasi->status != 'PENDING') {
                return response()->json(['success' => false, 'message' => 'Status bukan PENDING'], 422);
            }

            $realisasi->update([
                'status' => 'REJECT',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

            // Update status Master menjadi REJECT
            $master->update(['status' => 'REJECT']);

            \App\Models\RfkRealisasiHistory::create([
                'rfk_realisasi_id' => $realisasi->id,
                'status_sebelumnya' => 'PENDING',
                'status_baru' => 'REJECT',
                'keterangan' => $request->keterangan ?? 'Ditolak',
                'user_id' => Auth::id()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Realisasi Ditolak']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function updateRealisasi(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $cutoff = \App\Models\Setting::where('key', 'cutoff_realisasi')->first();
            if ($cutoff && $cutoff->value) {
                $cutoffDate = \Carbon\Carbon::parse($cutoff->value);
                if (now()->greaterThan($cutoffDate)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Masa pelaporan realisasi telah ditutup pada ' . $cutoffDate->format('d M Y H:i') . '. Hubungi Superadmin.'
                    ], 422);
                }
            }

            $realisasi = \App\Models\RfkRealisasi::findOrFail($id);
            $master = \App\Models\InputRfk::lockForUpdate()->findOrFail($realisasi->input_rfk_id);

            if (Auth::user()->role === 'staff' && $master->opd_id !== Auth::user()->opd_id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Program ini bukan milik OPD Anda.'], 403);
            }

            if ($realisasi->status !== 'REJECT') {
                return response()->json(['success' => false, 'message' => 'Hanya data yang ditolak (REJECT) yang bisa diperbaiki.'], 422);
            }

            $validator = Validator::make($request->all(), [
                'kode_program' => 'required|string|max:100',
                'nama_program' => 'required|string|max:255',
                'sub_kategori_program' => 'nullable|string|max:255',
                'kategori_anggaran' => 'nullable|string',
                'sub_kategori_anggaran' => 'nullable|string',
                'sumber_dana_detail' => 'nullable|string',
                'nilai_realisasi_keuangan' => 'required|numeric|min:1',
                'kegiatan' => 'required|string|max:255',
                'sub_kegiatan' => 'required|string|max:255',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            if (trim($request->nama_program) === 'Belum Ada Realisasi' || trim(strtoupper($request->nama_program)) === 'SKPD' || trim(strtoupper($request->nama_program)) === 'UNDEFINED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama Program tidak boleh menggunakan teks default (seperti "SKPD", "Undefined", atau "Belum Ada Realisasi"). Silakan ganti dengan nama program yang valid sesuai DPA Anda.'
                ], 422);
            }

            $nilaiInput = $request->nilai_realisasi_keuangan;

            if ($nilaiInput > $master->sisa_pagu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai realisasi melebihi sisa pagu saat ini.'
                ], 422);
            }

            $realisasiFisik = $master->pagu > 0 ? ($nilaiInput / $master->pagu) * 100 : 0;
            
            $realisasi->update([
                'kode_program' => $request->kode_program,
                'nama_program' => $request->nama_program,
                'sub_kategori_program' => $request->sub_kategori_program,
                'kategori_anggaran' => $request->kategori_anggaran,
                'sub_kategori_anggaran' => $request->sub_kategori_anggaran,
                'sumber_dana_detail' => $request->sumber_dana_detail,
                'nilai_realisasi_keuangan' => $nilaiInput,
                'nilai_realisasi_fisik' => $realisasiFisik,
                'status' => 'PENDING',
                'kegiatan' => $request->kegiatan,
                'sub_kegiatan' => $request->sub_kegiatan,
                'keterangan' => $request->keterangan,
                'tanggal_input' => now()
            ]);

            // Update status Master kembali ke PENDING
            $master->update(['status' => 'PENDING']);

            \App\Models\RfkRealisasiHistory::create([
                'rfk_realisasi_id' => $realisasi->id,
                'status_sebelumnya' => 'REJECT',
                'status_baru' => 'PENDING',
                'keterangan' => 'Diperbaiki oleh Staff: ' . $request->keterangan,
                'user_id' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Realisasi berhasil diperbaiki dan menunggu approval ulang.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function destroyRealisasi($id)
    {
        try {
            DB::beginTransaction();

            $realisasi = \App\Models\RfkRealisasi::findOrFail($id);
            $master = \App\Models\InputRfk::lockForUpdate()->findOrFail($realisasi->input_rfk_id);

            if (Auth::user()->role === 'staff' && $master->opd_id !== Auth::user()->opd_id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Program ini bukan milik OPD Anda.'], 403);
            }
            
            if ($realisasi->status === 'APPROVE') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Data realisasi yang sudah disetujui tidak dapat dihapus'
                ], 403);
            }
            
            $realisasi->delete(); // Akan cascade ke history

            // Revert status master ke status realisasi terakhir, atau PENDING jika habis
            $latestRealisasi = $master->realisasis()->orderBy('created_at', 'desc')->first();
            if ($latestRealisasi) {
                $master->update(['status' => $latestRealisasi->status]);
            } else {
                $master->update(['status' => 'PENDING']);
            }

            DB::commit();

            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'DELETE_REALISASI',
                'target_type' => 'RfkRealisasi',
                'target_id' => $id,
                'details' => 'Menghapus Realisasi Keuangan: ' . $realisasi->nilai_realisasi_keuangan,
                'ip_address' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Laporan Realisasi RFK berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error delete Realisasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPendingApproval()
    {
        try {
            $user = Auth::user();

            $query = InputRfk::with(['user', 'opd', 'realisasis' => function($q) {
                $q->where('status', 'PENDING')->latest();
            }])->where('status', 'PENDING');

            // Jika role adalah staff atau kepala_opd, filter berdasarkan OPD-nya
            if (in_array($user->role, ['staff', 'kepala_opd'])) {
                if ($user->opd_id) {
                    $query->where('opd_id', $user->opd_id);
                }
            }

            $pendingData = $query->orderBy('created_at', 'asc')->get()->map(function($item) {
                $latestRealisasi = $item->realisasis->first();
                $item->nama_program = $latestRealisasi ? $latestRealisasi->nama_program : $item->keterangan;
                $item->kode_program = $latestRealisasi ? $latestRealisasi->kode_program : '-';
                $item->sub_kategori_program = $latestRealisasi ? $latestRealisasi->sub_kategori_program : '-';
                $item->kategori_anggaran = $latestRealisasi ? $latestRealisasi->kategori_anggaran : '-';
                $item->sub_kategori_anggaran = $latestRealisasi ? $latestRealisasi->sub_kategori_anggaran : '-';
                $item->sumber_dana_detail = $latestRealisasi ? $latestRealisasi->sumber_dana_detail : '-';
                $item->kegiatan = $latestRealisasi ? $latestRealisasi->kegiatan : '-';
                $item->sub_kegiatan = $latestRealisasi ? $latestRealisasi->sub_kegiatan : '-';
                $item->keterangan_realisasi = $latestRealisasi ? $latestRealisasi->keterangan : $item->keterangan;
                if ($item->pagu > 0 && $item->realisasi_keuangan >= $item->pagu && $item->realisasi_fisik >= 100 && $item->status === 'APPROVE') {
                    $item->status = 'SELESAI';
                }
                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $this->sanitizeArray($pendingData->toArray())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getData()
    {
        try {
            $user = Auth::user();
            $opd = $user->opd;

            $query = InputRfk::with(['opd', 'user', 'realisasis' => function($q) {
                $q->orderBy('created_at', 'desc');
            }])->orderBy('created_at', 'desc');

            if (in_array($user->role, ['staff', 'kepala_opd'])) {
                if ($user->opd_id) {
                    $query->where('opd_id', $user->opd_id);
                }
            }

            $rfkData = $query->get()->map(function($item) {
                $latestRealisasi = $item->realisasis->first();
                $item->nama_program = $latestRealisasi ? $latestRealisasi->nama_program : $item->keterangan;
                $item->kode_program = $latestRealisasi ? $latestRealisasi->kode_program : '-';
                $item->sub_kategori_program = $latestRealisasi ? $latestRealisasi->sub_kategori_program : '-';
                $item->kategori_anggaran = $latestRealisasi ? $latestRealisasi->kategori_anggaran : '-';
                $item->sub_kategori_anggaran = $latestRealisasi ? $latestRealisasi->sub_kategori_anggaran : '-';
                $item->sumber_dana_detail = $latestRealisasi ? $latestRealisasi->sumber_dana_detail : '-';
                $item->kegiatan = $latestRealisasi ? $latestRealisasi->kegiatan : '-';
                $item->sub_kegiatan = $latestRealisasi ? $latestRealisasi->sub_kegiatan : '-';
                $item->keterangan_realisasi = $latestRealisasi ? $latestRealisasi->keterangan : '-';
                if ($item->pagu > 0 && $item->realisasi_keuangan >= $item->pagu && $item->realisasi_fisik >= 100 && $item->status === 'APPROVE') {
                    $item->status = 'SELESAI';
                }
                return $item;
            });

            // Hitung statistik
            $totalProgram = $rfkData->count();
            $totalPagu = $rfkData->sum('pagu');
            $totalRealisasiKeuangan = $rfkData->sum('realisasi_keuangan');
            $totalSisaPagu = $rfkData->sum('sisa_pagu');
            $avgFisik = $totalProgram > 0 ? $rfkData->avg('realisasi_fisik') : 0;

            $progressBerjalan = $rfkData->where('realisasi_fisik', '<', 50)->count();

            $tahunIni = date('Y');
            $terlambat = $rfkData->filter(function($item) use ($tahunIni) {
                return $item->realisasi_fisik < 30 && $item->tahun_anggaran == $tahunIni;
            })->count();

            return response()->json([
                'success' => true,
                'data' => $this->sanitizeArray($rfkData->toArray()),
                'statistics' => $this->sanitizeArray([
                    'total_program' => $totalProgram,
                    'total_pagu' => $totalPagu,
                    'total_realisasi_keuangan' => $totalRealisasiKeuangan,
                    'total_sisa_pagu' => $totalSisaPagu,
                    'avg_fisik' => round($avgFisik, 2),
                    'progress_berjalan' => $progressBerjalan,
                    'terlambat' => $terlambat,
                    'avg_keuangan_persen' => $totalPagu > 0 ? round(($totalRealisasiKeuangan / $totalPagu) * 100, 1) : 0
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Error get RFK data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Fitur update master ditiadakan untuk menjaga integritas, update dilakukan via storeRealisasi
        return response()->json(['success' => false, 'message' => 'Untuk update realisasi, gunakan fitur Tambah Realisasi.'], 403);
    }

    public function destroy($id)
    {
        try {
            $rfk = InputRfk::findOrFail($id);

            // Cek apakah sudah ada realisasi yang di-approve
            $hasApproved = $rfk->realisasis()->where('status', 'APPROVE')->exists();
            
            if ($hasApproved) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program ini sudah memiliki realisasi yang disetujui, tidak dapat dihapus'
                ], 403);
            }

            $rfk->delete(); // Akan cascade ke realisasis

            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'DELETE_PROGRAM_RFK',
                'target_type' => 'InputRfk',
                'target_id' => $id,
                'details' => 'Menghapus Program Master ID: ' . $id,
                'ip_address' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Program RFK berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error delete RFK: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeStatusMaster(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $rfk = InputRfk::lockForUpdate()->findOrFail($id);
            $user = Auth::user();

            if (!in_array($user->role, ['kepala_opd', 'administrator', 'superadmin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($user->role === 'kepala_opd' && $rfk->opd_id !== $user->opd_id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Program ini bukan milik OPD Anda.'], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:PENDING,APPROVE,REJECT',
                'keterangan' => 'required_if:status,REJECT|string|nullable'
            ], [
                'keterangan.required_if' => 'Keterangan wajib diisi jika status ditolak (REJECT).'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            $oldStatus = $rfk->status;
            $newStatus = $request->status;

            // Jika ada realisasi PENDING terbaru, kita proses akumulasinya jika APPROVE
            $latestRealisasi = $rfk->realisasis()->where('status', 'PENDING')->latest()->first();

            if ($latestRealisasi) {
                $latestRealisasi->update([
                    'status' => $newStatus,
                    'keterangan' => $request->keterangan ?? $latestRealisasi->keterangan,
                    'approved_by' => in_array($newStatus, ['APPROVE', 'REJECT']) ? Auth::id() : null,
                    'approved_at' => in_array($newStatus, ['APPROVE', 'REJECT']) ? now() : null,
                ]);

                \App\Models\RfkRealisasiHistory::create([
                    'rfk_realisasi_id' => $latestRealisasi->id,
                    'status_sebelumnya' => 'PENDING',
                    'status_baru' => $newStatus,
                    'keterangan' => 'Perubahan status oleh ' . $user->name . ': ' . ($request->keterangan ?? '-'),
                    'user_id' => Auth::id()
                ]);

                // Jika di APPROVE, akumulasikan nilai realisasi ke Master
                if ($newStatus === 'APPROVE') {
                    $newRealisasiKeu = $rfk->realisasi_keuangan + $latestRealisasi->nilai_realisasi_keuangan;
                    $newRealisasiFisik = $rfk->realisasi_fisik + $latestRealisasi->nilai_realisasi_fisik;
                    $newSisaPagu = $rfk->pagu - $newRealisasiKeu;

                    if ($newSisaPagu < 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Perubahan status gagal. Nilai pengajuan yang menunggu ini akan mengakibatkan sisa pagu menjadi minus (Over Budget).'
                        ], 422);
                    }

                    $rfk->update([
                        'realisasi_keuangan' => $newRealisasiKeu,
                        'realisasi_fisik' => min(100, $newRealisasiFisik),
                        'sisa_pagu' => $newSisaPagu,
                        'status' => 'APPROVE',
                        'keterangan' => $request->keterangan ?? $rfk->keterangan
                    ]);
                } else {
                    $rfk->update([
                        'status' => $newStatus,
                        'keterangan' => $request->keterangan ?? $rfk->keterangan
                    ]);
                }
            } else {
                // Jika tidak ada realisasi yang PENDING, hanya ubah status master
                $rfk->update([
                    'status' => $newStatus,
                    'keterangan' => $request->keterangan ?? $rfk->keterangan
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status program berhasil diubah menjadi ' . $newStatus
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }

    public function validationEnginePage()
    {
        return view('dashboard.validation_engine');
    }

    public function runValidationEngine()
    {
        try {
            $mismatches = [];
            $suspicious = [];

            // ==========================================
            // PILAR 1: Data Mismatch
            // ==========================================
            $rfks = InputRfk::with(['opd', 'realisasis'])->get();

        foreach ($rfks as $prog) {
            $pagu = (float) $prog->pagu;
            $realisasiKeu = (float) $prog->realisasi_keuangan;
            $realisasiFis = (float) $prog->realisasi_fisik;
                
                // 1. Over-Pagu Check
                $latestRealisasi = $prog->realisasis->sortByDesc('created_at')->first();
                $namaProgram = $latestRealisasi ? $latestRealisasi->nama_program : $prog->keterangan;
                
                if ($pagu > 0 && $realisasiKeu > $pagu) {
                    $mismatches[] = [
                        'type' => 'OVER_PAGU',
                        'program' => $namaProgram,
                        'opd' => $prog->opd ? $prog->opd->nama_opd : '-',
                        'detail' => 'Realisasi Keuangan melampaui Pagu (Pagu: Rp '.number_format($pagu, 0, ',', '.').', Realisasi: Rp '.number_format($realisasiKeu, 0, ',', '.').')'
                    ];
                }

                if ($realisasiFis > 100) {
                    $mismatches[] = [
                        'type' => 'OVER_FISIK',
                        'program' => $namaProgram,
                        'opd' => $prog->opd ? $prog->opd->nama_opd : '-',
                        'detail' => 'Realisasi Fisik melampaui 100% (Saat ini: '.$realisasiFis.'%)'
                    ];
                }

                // 2. Deviasi Ekstrem Check
                if ($pagu > 0) {
                    $persentaseKeu = ($realisasiKeu / $pagu) * 100;
                    $selisih = abs($persentaseKeu - $realisasiFis);

                    // Threshold 30% deviasi
                    if ($selisih > 30) {
                        $mismatches[] = [
                            'type' => 'EXTREME_DEVIATION',
                            'program' => $namaProgram,
                            'opd' => $prog->opd ? $prog->opd->nama_opd : '-',
                            'detail' => 'Deviasi Ekstrem (Keuangan: '.number_format($persentaseKeu, 1).'% vs Fisik: '.$realisasiFis.'%)'
                        ];
                    }
                }
            }

            // ==========================================
            // PILAR 2: Suspicious Similarity
            // ==========================================
            
            // Mencari duplikasi nominal keuangan + persentase fisik + OPD yang diinput bersamaan tapi beda program
            $suspiciousGroups = DB::table('rfk_realisasis')
                ->join('table_input_rfk', 'rfk_realisasis.input_rfk_id', '=', 'table_input_rfk.id')
                ->join('opds', 'table_input_rfk.opd_id', '=', 'opds.id')
                ->selectRaw('
                    opds.nama_opd,
                    rfk_realisasis.nilai_realisasi_keuangan,
                    rfk_realisasis.nilai_realisasi_fisik,
                    DATE(rfk_realisasis.created_at) as tanggal_input,
                    COUNT(DISTINCT rfk_realisasis.input_rfk_id) as jumlah_program,
                    GROUP_CONCAT(DISTINCT rfk_realisasis.nama_program SEPARATOR " | ") as daftar_program
                ')
                ->groupBy('opds.nama_opd', 'rfk_realisasis.nilai_realisasi_keuangan', 'rfk_realisasis.nilai_realisasi_fisik', DB::raw('DATE(rfk_realisasis.created_at)'))
                ->having('jumlah_program', '>', 1)
                ->having('rfk_realisasis.nilai_realisasi_keuangan', '>', 0)
                ->get();

            foreach ($suspiciousGroups as $group) {
                $suspicious[] = [
                    'type' => 'COPY_PASTE_INPUT',
                    'opd' => $group->nama_opd,
                    'tanggal' => $group->tanggal_input,
                    'detail' => 'Ditemukan '.$group->jumlah_program.' program berbeda diinput dengan nilai persis sama (Rp '.number_format($group->nilai_realisasi_keuangan, 0, ',', '.').' & '.$group->nilai_realisasi_fisik.'%) pada hari yang sama.',
                    'programs' => $group->daftar_program
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'mismatches' => $mismatches,
                    'suspicious' => $suspicious,
                    'total_anomalies' => count($mismatches) + count($suspicious)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Engine gagal dijalankan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getHistory()
    {
        try {
            $query = \App\Models\RfkRealisasiHistory::with(['realisasi.inputRfk.opd', 'user'])
                ->orderBy('created_at', 'desc');

            // Jika role adalah staff atau kepala_opd, hanya tampilkan histori untuk OPD-nya
            $user = Auth::user();
            if (in_array($user->role, ['staff', 'kepala_opd'])) {
                if ($user->opd_id) {
                    $query->whereHas('realisasi.inputRfk', function($q) use ($user) {
                        $q->where('opd_id', $user->opd_id);
                    });
                }
            }

            $historyData = $query->orderBy('created_at', 'desc')->get()->map(function($item) {
                $item->nama_program = $item->realisasi ? $item->realisasi->nama_program : '-';
                $item->kode_program = $item->realisasi ? $item->realisasi->kode_program : '-';
                $item->kegiatan = $item->realisasi ? $item->realisasi->kegiatan : '-';
                $item->sub_kegiatan = $item->realisasi ? $item->realisasi->sub_kegiatan : '-';
                $item->keterangan_realisasi = $item->realisasi ? $item->realisasi->keterangan : '-';
                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $this->sanitizeArray($historyData->toArray())
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function globalSearch(Request $request)
    {
        try {
            $q = $request->query('q', '');
            if (strlen($q) < 3) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $user = Auth::user();
            $allowedRoles = ['superadmin', 'administrator'];
            if (!in_array($user->role, $allowedRoles)) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Search Master Data (InputRfk)
            $masterData = InputRfk::with(['opd', 'realisasis'])
                ->where('keterangan', 'LIKE', "%{$q}%")
                ->orWhereHas('realisasis', function ($query) use ($q) {
                    $query->where('nama_program', 'LIKE', "%{$q}%")
                          ->orWhere('kegiatan', 'LIKE', "%{$q}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $latestRealisasi = $item->realisasis->sortByDesc('created_at')->first();
                    $title = $latestRealisasi ? $latestRealisasi->nama_program : $item->keterangan;
                    return [
                        'type' => 'master',
                        'id' => $item->id,
                        'title' => $title ?: 'Program Master',
                        'subtitle' => $item->opd ? $item->opd->nama_opd : 'Tanpa OPD',
                        'pagu' => (float)$item->pagu,
                        'realisasi' => (float)$item->realisasi_keuangan,
                        'status' => $item->status,
                        'badge_color' => 'indigo' // Color for master type
                    ];
                });

            // Search Arsip Realisasi
            $arsipData = \App\Models\RfkRealisasi::with('inputRfk.opd')
                ->where('nama_program', 'LIKE', "%{$q}%")
                ->orWhere('kegiatan', 'LIKE', "%{$q}%")
                ->orWhere('sub_kegiatan', 'LIKE', "%{$q}%")
                ->orWhere('kode_program', 'LIKE', "%{$q}%")
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $opdName = 'Tanpa OPD';
                    if ($item->inputRfk && $item->inputRfk->opd) {
                        $opdName = $item->inputRfk->opd->nama_opd;
                    }
                    return [
                        'type' => 'realisasi',
                        'id' => $item->id,
                        'title' => $item->nama_program ?: 'Realisasi Program',
                        'subtitle' => $opdName . ' - ' . ($item->kegiatan ?: 'Tanpa Kegiatan'),
                        'pagu' => (float)($item->inputRfk ? $item->inputRfk->pagu : 0),
                        'realisasi' => (float)$item->nilai_realisasi_keuangan,
                        'status' => $item->status,
                        'badge_color' => 'emerald' // Color for realisasi type
                    ];
                });

            // Combine and sort
            $results = collect($masterData)->concat($arsipData)->sortByDesc('pagu')->take(10)->values();

            return response()->json([
                'success' => true,
                'data' => $this->sanitizeArray($results->toArray())
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    protected function sanitizeArray($data)
    {
        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeArray($value);
            }
        }
        return $data;
    }
}
