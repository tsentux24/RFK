<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = App\Models\User::where('role', 'staff')->first();
    if (!$user) {
        die("No staff user found.");
    }

    $opd = App\Models\Opd::first();
    if (!$opd) {
        die("No OPD found.");
    }

    $rfk = App\Models\InputRfk::first();
    if(!$rfk) {
        $rfk = App\Models\InputRfk::create([
            'sumber_dana' => 'APBD',
            'tahun_anggaran' => 2026,
            'pagu' => 1000000,
            'realisasi_keuangan' => 0,
            'realisasi_fisik' => 0,
            'sisa_pagu' => 1000000,
            'opd_id' => $opd->id,
            'status' => 'APPROVE',
            'user_id' => $user->id,
            'tanggal_input' => now()
        ]);
        echo "Created mock InputRfk.\n";
    }

    $realisasiBaru = $rfk->realisasis()->create([
        'kode_program' => 'K01',
        'nama_program' => 'Test',
        'sub_kategori_program' => null,
        'kategori_anggaran' => null,
        'sub_kategori_anggaran' => null,
        'sumber_dana_detail' => null,
        'nilai_realisasi_keuangan' => 1000,
        'nilai_realisasi_fisik' => 10,
        'status' => 'PENDING',
        'kegiatan' => 'Kegiatan Test',
        'sub_kegiatan' => 'Sub Test',
        'keterangan' => 'Ket',
        'user_id' => $user->id,
        'tanggal_input' => now()
    ]);
    
    echo "SUCCESS: " . $realisasiBaru->id;

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
