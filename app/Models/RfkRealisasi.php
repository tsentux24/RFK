<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfkRealisasi extends Model
{
    use HasFactory;

    protected $table = 'rfk_realisasis';

    protected $fillable = [
        'input_rfk_id',
        'nilai_realisasi_keuangan',
        'nilai_realisasi_fisik',
        'status',
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
