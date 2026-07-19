<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = App\Models\User::where('role', 'staff')->first();
    Auth::loginUsingId($user->id);

    $req = Illuminate\Http\Request::create('/dashboard/rfk', 'POST', [
        'sumber_dana' => 'APBD',
        'tahun_anggaran' => 2026,
        'pagu' => 1000,
        'keterangan' => 'tes'
    ]);

    $c = new App\Http\Controllers\RfkController();
    $res = $c->store($req);
    echo $res->getContent();

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
