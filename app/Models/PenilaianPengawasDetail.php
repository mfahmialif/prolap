<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPengawasDetail extends Model
{
    use HasFactory;

    protected $table = "penilaian_pengawas_detail";
    protected $guard = [];

    public function penilaianPengawas()
    {
        return $this->belongsTo(PenilaianPengawas::class, 'penilaian_pengawas_id', 'id');
    }

    public function komponenNilai()
    {
        return $this->belongsTo(KomponenNilai::class, 'komponen_nilai_id', 'id');
    }
}