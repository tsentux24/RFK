<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('table_input_rfk', function (Blueprint $table) {
            $table->id();

            // Data Program
            $table->string('kode_program', 100);
            $table->string('nama_program', 255);
            $table->string('sub_kategori_program', 255)->nullable();

            // Sumber Dana & Kategori
            $table->enum('sumber_dana', ['APBD', 'APBN'])->nullable();
            $table->string('kategori_anggaran', 100)->nullable(); // Belanja Operasi / Belanja Modal
            $table->string('sub_kategori_anggaran', 100)->nullable(); // Belanja Pegawai, DLL

            // Detail Anggaran
            $table->enum('sumber_dana_detail', ['DAU', 'DAK', 'DBH', 'DEKOM'])->nullable();
            $table->integer('tahun_anggaran');
            $table->decimal('pagu', 15, 2)->default(0);
            $table->decimal('realisasi_keuangan', 15, 2)->default(0);
            $table->decimal('realisasi_fisik', 5, 2)->default(0);
            $table->decimal('sisa_pagu', 15, 2)->default(0);

            // Status & OPD
            $table->unsignedBigInteger('opd_id')->nullable();
            $table->enum('status', ['PENDING', 'APPROVE', 'REJECT'])->default('PENDING');
            $table->text('keterangan')->nullable();

            // User yang input
            $table->unsignedBigInteger('user_id');
            $table->timestamp('tanggal_input')->useCurrent();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('opd_id')->references('id')->on('opds')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index('status');
            $table->index('sumber_dana');
            $table->index('tahun_anggaran');
        });
    }

    public function down()
    {
        Schema::dropIfExists('table_input_rfk');
    }
};
