<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pamong extends Model
{
    use HasFactory;
    protected $table = "pamong";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function absensi(){
        return $this->hasMany(AbsensiPsPamong::class);
    }

    public function pamongPeserta(){
        return $this->hasMany(PamongPeserta::class);
    }

    public function penilaianPamong(){
        return $this->hasMany(PenilaianPamong::class);
    }
}
