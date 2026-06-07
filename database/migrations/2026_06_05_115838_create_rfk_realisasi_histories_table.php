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
        Schema::create('rfk_realisasi_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rfk_realisasi_id');
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_baru');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('user_id'); // siapa yang mengubah (Staff/Kepala OPD/Admin)
            $table->timestamps();

            $table->foreign('rfk_realisasi_id')->references('id')->on('rfk_realisasis')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfk_realisasi_histories');
    }
};
