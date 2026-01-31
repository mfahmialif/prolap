<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPsPamongDetail extends Model
{
    use HasFactory;

    protected $table = "absensi_ps_pamong_detail";

    protected $guarded = [];

    public function absensi(){
        return $this->belongsTo(AbsensiPsPamong::class, 'absensi_ps_pamong_id', 'id');
    }

    public function pamongPeserta(){
        return $this->belongsTo(PamongPeserta::class, 'pamong_peserta_id', 'id');
    }
}
