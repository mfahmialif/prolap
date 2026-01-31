<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoskoPeserta extends Model
{
    use HasFactory;
    protected $table = "posko_peserta";
    protected $guarded = [];

    public function posko()
    {
        return $this->belongsTo(Posko::class);
    }
    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
    public function penilaianDpl()
    {
        return $this->hasMany(PenilaianDpl::class);
    }
    public function penilaianPengawas()
    {
        return $this->hasMany(PenilaianPengawas::class);
    }
    public function penilaianPamong()
    {
        return $this->hasMany(PenilaianPamong::class);
    }
    public function absensiPsDplDetail(){
        return $this->hasMany(AbsensiPsDplDetail::class);
    }
    public function absensiPsPengawasDetail(){
        return $this->hasMany(AbsensiPsPengawasDetail::class);
    }

}