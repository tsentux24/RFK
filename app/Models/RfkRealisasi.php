<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfkRealisasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rfk_realisasis';

    protected $fillable = [
        'input_rfk_id',
        'kode_program',
        'nama_program',
        'sub_kategori_program',
        'kategori_anggaran',
        'sub_kategori_anggaran',
        'sumber_dana_detail',
        'nilai_realisasi_keuangan',
        'nilai_realisasi_fisik',
        'status',
        'kegiatan',
        'sub_kegiatan',
        'keterangan',
        'user_id',
        'approved_by',
        'approved_at',
        'tanggal_input'
    ];

    protected $casts = [
        'nilai_realisasi_keuangan' => 'decimal:2',
        'nilai_realisasi_fisik' => 'decimal:2',
        'tanggal_input' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function inputRfk()
    {
        return $this->belongsTo(InputRfk::class, 'input_rfk_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
