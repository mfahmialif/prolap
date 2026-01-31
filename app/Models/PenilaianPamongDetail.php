<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPamongDetail extends Model
{
    use HasFactory;
    protected $table = "penilaian_pamong_detail";
    protected $guard = [];

    public function penilaianPamong()
    {
        return $this->belongsTo(PenilaianPamong::class, 'penilaian_pamong_id', 'id');
    }

    public function komponenNilai(){
        return $this->belongsTo(KomponenNilai::class, 'komponen_nilai_id', 'id');
    }
}
