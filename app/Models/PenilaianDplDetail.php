<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianDplDetail extends Model
{
    use HasFactory;

    protected $table = "penilaian_dpl_detail";
    protected $guard = [];

    public function penilaianDpl(){
        return $this->belongsTo(PenilaianDpl::class, 'penilaian_dpl_id', 'id');
    }

    public function komponenNilai(){
        return $this->belongsTo(KomponenNilai::class, 'komponen_nilai_id', 'id');
    }
}
