<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputRfk extends Model
{
    use HasFactory;

    protected $table = 'table_input_rfk';

    protected $fillable = [
        'kode_program',
        'nama_program',
        'sub_kategori_program',
        'sumber_dana',
        'kategori_anggaran',
        'sub_kategori_anggaran',
        'sumber_dana_detail',
        'tahun_anggaran',
        'pagu',
        'realisasi_keuangan',
        'realisasi_fisik',
        'sisa_pagu',
        'opd_id',
        'status',
        'keterangan',
        'user_id',
        'tanggal_input'
    ];

    protected $casts = [
        'pagu' => 'decimal:2',
        'realisasi_keuangan' => 'decimal:2',
        'realisasi_fisik' => 'decimal:2',
        'sisa_pagu' => 'decimal:2',
        'tanggal_input' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke OPD
    public function opd()
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope untuk filter status
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVE');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'REJECT');
    }

    // Relasi ke RfkRealisasi
    public function realisasis()
    {
        return $this->hasMany(RfkRealisasi::class, 'input_rfk_id');
    }
}
