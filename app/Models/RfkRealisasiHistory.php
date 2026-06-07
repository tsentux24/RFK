<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfkRealisasiHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfk_realisasi_id',
        'status_sebelumnya',
        'status_baru',
        'keterangan',
        'user_id'
    ];

    public function realisasi()
    {
        return $this->belongsTo(RfkRealisasi::class, 'rfk_realisasi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
