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
        Schema::create('rfk_realisasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('input_rfk_id');
            $table->decimal('nilai_realisasi_keuangan', 15, 2)->default(0);
            $table->decimal('nilai_realisasi_fisik', 5, 2)->default(0);
            $table->enum('status', ['PENDING', 'APPROVE', 'REJECT'])->default('PENDING');
            $table->text('keterangan')->nullable();
            
            $table->unsignedBigInteger('user_id'); // Staff
            $table->unsignedBigInteger('approved_by')->nullable(); // Kepala OPD
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('tanggal_input')->useCurrent();
            
            $table->timestamps();

            $table->foreign('input_rfk_id')->references('id')->on('table_input_rfk')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfk_realisasis');
    }
};
