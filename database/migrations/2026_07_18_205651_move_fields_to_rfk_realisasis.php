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
        Schema::table('table_input_rfk', function (Blueprint $table) {
            $table->dropColumn([
                'kode_program',
                'nama_program',
                'sub_kategori_program',
                'kategori_anggaran',
                'sub_kategori_anggaran',
                'sumber_dana_detail'
            ]);
        });

        Schema::table('rfk_realisasis', function (Blueprint $table) {
            $table->string('kode_program')->nullable()->after('status');
            $table->text('nama_program')->nullable()->after('kode_program');
            $table->string('sub_kategori_program')->nullable()->after('nama_program');
            $table->string('kategori_anggaran')->nullable()->after('sub_kategori_program');
            $table->string('sub_kategori_anggaran')->nullable()->after('kategori_anggaran');
            $table->string('sumber_dana_detail')->nullable()->after('sub_kategori_anggaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfk_realisasis', function (Blueprint $table) {
            $table->dropColumn([
                'kode_program',
                'nama_program',
                'sub_kategori_program',
                'kategori_anggaran',
                'sub_kategori_anggaran',
                'sumber_dana_detail'
            ]);
        });

        Schema::table('table_input_rfk', function (Blueprint $table) {
            $table->string('kode_program')->nullable();
            $table->text('nama_program')->nullable();
            $table->string('sub_kategori_program')->nullable();
            $table->string('kategori_anggaran')->nullable();
            $table->string('sub_kategori_anggaran')->nullable();
            $table->string('sumber_dana_detail')->nullable();
        });
    }
};
