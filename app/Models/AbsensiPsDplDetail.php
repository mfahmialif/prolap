<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPsDplDetail extends Model
{
    use HasFactory;

    protected $table = "absensi_ps_dpl_detail";

    // protected $guarded = [];
    protected $fillable = [
        'absensi_ps_dpl_id',
        'posko_peserta_id',
        'status',
        'waktu_absen',
    ];

    public function absensi()
    {
        return $this->belongsTo(AbsensiPsDpl::class, 'absensi_ps_dpl_id', 'id');
    }

    public function poskoPeserta()
    {
        return $this->belongsTo(PoskoPeserta::class, 'posko_peserta_id', 'id');
    }
}
