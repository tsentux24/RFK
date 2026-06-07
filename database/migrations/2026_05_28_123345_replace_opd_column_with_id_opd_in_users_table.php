<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom opd_id sudah ada
            if (!Schema::hasColumn('users', 'opd_id')) {
                // Tambahkan kolom opd_id setelah kolom role
                $table->unsignedBigInteger('opd_id')->nullable()->after('role');

                // Tambahkan foreign key constraint ke tabel opds
                $table->foreign('opd_id')
                      ->references('id')
                      ->on('opds')
                      ->onDelete('set null'); // Jika OPD dihapus, set opd_id menjadi null
            }

            // Cek apakah kolom status sudah ada (untuk status aktif/nonaktif user)
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(1)->after('opd_id');
            }

            // Cek apakah kolom last_login sudah ada
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['opd_id']);

            // Hapus kolom
            $table->dropColumn(['opd_id', 'status', 'last_login']);
        });
    }
};
