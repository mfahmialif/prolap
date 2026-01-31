<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PamongPeserta extends Model
{
    use HasFactory;
    protected $table = "pamong_peserta";
    protected $guarded = [];

    public function pamong()
    {
        return $this->belongsTo(Pamong::class);
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function absensiPsPamongDetail()
    {
        return $this->hasMany(AbsensiPsPamongDetail::class);
    }
}
