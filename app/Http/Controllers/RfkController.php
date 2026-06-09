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

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    public function generateLaporanPdf(Request $request)
    {
        try {
            $query = InputRfk::with(['opd', 'user', 'realisasis' => function($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->whereIn('status', ['APPROVE', 'PENDING']);

            $user = Auth::user();
            if (in_array($user->role, ['staff', 'kepala_opd']) && $user->opd_id) {
                $query->where('opd_id', $user->opd_id);
            }

            // Apply Filters from Frontend
            if ($request->filled('program')) {
                $query->where(function($q) use ($request) {
                    $q->where('nama_program', 'LIKE', '%' . $request->program . '%')
                      ->orWhere('kode_program', 'LIKE', '%' . $request->program . '%');
                });
            }

            if ($request->filled('opd')) {
                $query->whereHas('opd', function($q) use ($request) {
                    $q->where('nama_opd', 'LIKE', '%' . $request->opd . '%');
                });
            }

            if ($request->filled('tahun')) {
                $query->where('tahun_anggaran', $request->tahun);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.pdf.laporan_rfk', compact('data'))
                      ->setPaper('a4', 'landscape');

            $fileName = 'Laporan_RFK_' . time() . '.pdf';
            
            // Simpan file secara lokal di public/storage/pdfs
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

        // Data for Diagram (Group by Status or by Time)
        $rfkStatusCount = [
            'APPROVE' => (clone $query)->where('status', 'APPROVE')->count(),
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

        return response()->json([
            'success' => true,
            'data' => [
                'total_program' => $totalProgram,
                'total_pagu' => $totalPagu,
                'total_realisasi' => $totalRealisasi,
                'total_sisa_pagu' => $totalSisaPagu,
                'avg_fisik' => round($avgFisik, 2),
                'opd_belum_input' => $opdBelumInput,
                'opd_belum_list' => $opdBelumList,
                'diagram_status' => $rfkStatusCount,
                'diagram_opd' => $opdStats
            ]
        ]);
    }

    public function getSuperadminData(Request $request)
    {
        $query = InputRfk::query()->with('opd');

        if ($request->filled('tahun')) {
            $query->where('tahun_anggaran', $request->tahun);
        }
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        $allPrograms = $query->get();

        $totalProgram = $allPrograms->count();
        $totalPagu = $allPrograms->sum('pagu');
        $totalRealisasi = $allPrograms->sum('realisasi_keuangan');
        $totalSisaPagu = $allPrograms->sum('sisa_pagu');
        $avgFisik = $totalProgram > 0 ? $allPrograms->avg('realisasi_fisik') : 0;

        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('Y-m');
        $thisYear = now()->format('Y');

        $realisasiHarian = $allPrograms->filter(function($p) use ($today) {
            return \Carbon\Carbon::parse($p->tanggal_input)->format('Y-m-d') === $today;
        })->sum('realisasi_keuangan');

        $realisasiBulanan = $allPrograms->filter(function($p) use ($thisMonth) {
            return \Carbon\Carbon::parse($p->tanggal_input)->format('Y-m') === $thisMonth;
        })->sum('realisasi_keuangan');

        $realisasiTahunan = $allPrograms->filter(function($p) use ($thisYear) {
            return \Carbon\Carbon::parse($p->tanggal_input)->format('Y') === $thisYear;
        })->sum('realisasi_keuangan');

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
                return [
                    'id' => $p->id,
                    'kode' => $p->kode_program,
                    'nama' => $p->nama_program,
                    'pagu' => (float) $p->pagu,
                    'realisasi' => (float) $p->realisasi_keuangan,
                    'sisa' => (float) $p->sisa_pagu,
                    'fisik' => (float) $p->realisasi_fisik,
                    'status' => $p->status,
                    'sub_kategori_program' => $p->sub_kategori_program,
                    'sumber_dana' => $p->sumber_dana,
                    'kategori_anggaran' => $p->kategori_anggaran,
                    'sub_kategori_anggaran' => $p->sub_kategori_anggaran,
                    'sumber_dana_detail' => $p->sumber_dana_detail,
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

        $lastUpdated = $allPrograms->max('updated_at');
        $lastUpdatedAtFormatted = $lastUpdated ? \Carbon\Carbon::parse($lastUpdated)->isoFormat('D MMMM YYYY, HH:mm') . ' WIB' : 'Belum ada data';

        $rfkStatusCount = [
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
            $p->opd_name = $p->opd ? $p->opd->nama_opd : 'Lainnya';
            return $p;
        })->values();

        // 2. Program Serapan Tertinggi & Terendah
        $programsWithPersen = $allPrograms->filter(function($p) { return $p->pagu > 0; })->map(function($p) {
            $p->persentase = round(($p->realisasi_keuangan / $p->pagu) * 100, 2);
            $p->opd_name = $p->opd ? $p->opd->nama_opd : 'Lainnya';
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

        return response()->json([
            'success' => true,
            'data' => [
                'last_updated_at' => $lastUpdatedAtFormatted,
                'total_program' => $totalProgram,
                'total_pagu' => $totalPagu,
                'total_realisasi' => $totalRealisasi,
                'total_sisa_pagu' => $totalSisaPagu,
                'avg_fisik' => round($avgFisik, 2),
                'realisasi_harian' => $realisasiHarian,
                'realisasi_bulanan' => $realisasiBulanan,
                'realisasi_tahunan' => $realisasiTahunan,
                'jumlah_opd_tercatat' => $jumlahOpdTercatat,
                'diagram_status' => $rfkStatusCount,
                'diagram_sumber_dana' => $sumberDanaData,
                'top_reject_opds' => $topRejectOpds,
                'top_10_paket' => $top10Paket,
                'serapan_tertinggi' => $serapanTertinggi,
                'serapan_terendah' => $serapanTerendah,
                'traffic_light' => [
                    'hijau' => count($trafficLight['hijau']),
                    'kuning' => count($trafficLight['kuning']),
                    'merah' => count($trafficLight['merah']),
                    'detail' => $trafficLight
                ],
                'ranking_opd' => $rankingOpd,
                'opds' => $opdsData
            ]
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'kode_program' => 'required|string|max:100',
                'nama_program' => 'required|string|max:255',
                'sub_kategori_program' => 'nullable|string|max:255',
                'sumber_dana' => 'required|in:APBD,APBN',
                'kategori_anggaran' => 'nullable|string',
                'sub_kategori_anggaran' => 'nullable|string',
                'sumber_dana_detail' => 'nullable|string',
                'tahun_anggaran' => 'required|integer|min:2020|max:2030',
                'pagu' => 'required|numeric|min:0',
                'realisasi_keuangan' => 'required|numeric|min:0',
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
            $inputRealisasiKeuangan = $request->realisasi_keuangan;

            // Buat Master Program (Akumulasi Awal = 0)
            $rfk = InputRfk::create([
                'kode_program' => $request->kode_program,
                'nama_program' => $request->nama_program,
                'sub_kategori_program' => $request->sub_kategori_program,
                'sumber_dana' => $request->sumber_dana,
                'kategori_anggaran' => $request->kategori_anggaran,
                'sub_kategori_anggaran' => $request->sub_kategori_anggaran,
                'sumber_dana_detail' => $request->sumber_dana_detail,
                'tahun_anggaran' => $request->tahun_anggaran,
                'pagu' => $pagu,
                'realisasi_keuangan' => 0,
                'realisasi_fisik' => 0,
                'sisa_pagu' => $pagu,
                'opd_id' => $opd ? $opd->id : null,
                'status' => 'PENDING',
                'keterangan' => 'Pembuatan Program',
                'user_id' => $user->id,
                'tanggal_input' => now()
            ]);

            // Jika ada input realisasi awal, masukkan ke tabel detail sebagai PENDING
            if ($inputRealisasiKeuangan > 0) {
                $realisasiFisik = ($inputRealisasiKeuangan / $pagu) * 100;
                $realisasiFisik = min(100, $realisasiFisik);

                $realisasi = $rfk->realisasis()->create([
                    'nilai_realisasi_keuangan' => $inputRealisasiKeuangan,
                    'nilai_realisasi_fisik' => $realisasiFisik,
                    'status' => 'PENDING',
                    'keterangan' => $request->keterangan ?? 'Realisasi Awal',
                    'user_id' => $user->id,
                    'tanggal_input' => now()
                ]);

                \App\Models\RfkRealisasiHistory::create([
                    'rfk_realisasi_id' => $realisasi->id,
                    'status_sebelumnya' => null,
                    'status_baru' => 'PENDING',
                    'keterangan' => 'Pengajuan awal oleh Staff: ' . ($request->keterangan ?? 'Realisasi Awal'),
                    'user_id' => $user->id
                ]);
            }

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

            $rfk = InputRfk::findOrFail($id);

            // Validasi status Master: Hanya bisa tambah jika status APPROVE
            if ($rfk->status !== 'APPROVE') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status program saat ini adalah ' . $rfk->status . '. Tambahan realisasi hanya dapat dilakukan jika status sudah APPROVE.'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'nilai_realisasi_keuangan' => 'required|numeric|min:1',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            $nilaiInput = $request->nilai_realisasi_keuangan;

            if ($nilaiInput > $rfk->sisa_pagu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai realisasi melebihi sisa pagu saat ini.'
                ], 422);
            }

            $realisasiFisik = ($nilaiInput / $rfk->pagu) * 100;
            
            $realisasiBaru = $rfk->realisasis()->create([
                'nilai_realisasi_keuangan' => $nilaiInput,
                'nilai_realisasi_fisik' => $realisasiFisik,
                'status' => 'PENDING',
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
            $master = $realisasi->inputRfk;

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
            $master = $realisasi->inputRfk;

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

            $realisasi = \App\Models\RfkRealisasi::findOrFail($id);
            $master = $realisasi->inputRfk;

            if ($realisasi->status !== 'REJECT') {
                return response()->json(['success' => false, 'message' => 'Hanya data yang ditolak (REJECT) yang bisa diperbaiki.'], 422);
            }

            $validator = Validator::make($request->all(), [
                'nilai_realisasi_keuangan' => 'required|numeric|min:1',
                'keterangan' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            $nilaiInput = $request->nilai_realisasi_keuangan;

            if ($nilaiInput > $master->sisa_pagu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai realisasi melebihi sisa pagu saat ini.'
                ], 422);
            }

            $realisasiFisik = ($nilaiInput / $master->pagu) * 100;
            
            $realisasi->update([
                'nilai_realisasi_keuangan' => $nilaiInput,
                'nilai_realisasi_fisik' => $realisasiFisik,
                'status' => 'PENDING',
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

            $pendingData = $query->orderBy('created_at', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $pendingData
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

            $rfkData = $query->get();

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
                'data' => $rfkData,
                'statistics' => [
                    'total_program' => $totalProgram,
                    'total_pagu' => $totalPagu,
                    'total_realisasi_keuangan' => $totalRealisasiKeuangan,
                    'total_sisa_pagu' => $totalSisaPagu,
                    'avg_fisik' => round($avgFisik, 1),
                    'progress_berjalan' => $progressBerjalan,
                    'terlambat' => $terlambat,
                    'avg_keuangan_persen' => $totalPagu > 0 ? round(($totalRealisasiKeuangan / $totalPagu) * 100, 1) : 0
                ]
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

            $rfk = InputRfk::findOrFail($id);
            $user = Auth::user();

            if ($user->role !== 'kepala_opd' && $user->role !== 'administrator' && $user->role !== 'superadmin') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
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
            $programs = InputRfk::with('opd')->get();

            foreach ($programs as $prog) {
                $pagu = (float) $prog->pagu;
                $realisasiKeu = (float) $prog->realisasi_keuangan;
                $realisasiFis = (float) $prog->realisasi_fisik;
                
                // 1. Over-Pagu Check
                if ($pagu > 0 && $realisasiKeu > $pagu) {
                    $mismatches[] = [
                        'type' => 'OVER_PAGU',
                        'program' => $prog->nama_program,
                        'opd' => $prog->opd ? $prog->opd->nama_opd : '-',
                        'detail' => 'Realisasi Keuangan melampaui Pagu (Pagu: Rp '.number_format($pagu, 0, ',', '.').', Realisasi: Rp '.number_format($realisasiKeu, 0, ',', '.').')'
                    ];
                }

                if ($realisasiFis > 100) {
                    $mismatches[] = [
                        'type' => 'OVER_FISIK',
                        'program' => $prog->nama_program,
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
                            'program' => $prog->nama_program,
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
                    GROUP_CONCAT(DISTINCT table_input_rfk.nama_program SEPARATOR " | ") as daftar_program
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

            $histories = $query->get();

            return response()->json([
                'success' => true,
                'data' => $histories
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
