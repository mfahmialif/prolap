<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPsPengawasDetail extends Model
{
    use HasFactory;

    protected $table = "absensi_ps_pengawas_detail";

    protected $guarded = [];

    public function absensi()
    {
        return $this->belongsTo(AbsensiPsPengawas::class, 'absensi_ps_pengawas_id', 'id');
    }

    public function poskoPeserta()
    {
        return $this->belongsTo(PoskoPeserta::class, 'posko_peserta_id', 'id');
    }
}
