<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPengawas extends Model
{
    use HasFactory;

    protected $table = "penilaian_pengawas";
    protected $guard = [];

    public function poskoPengawas()
    {
        return $this->belongsTo(PoskoPengawas::class, 'posko_pengawas_id', 'id');
    }

    public function poskoPeserta()
    {
        return $this->belongsTo(PoskoPeserta::class, 'posko_peserta_id', 'id');
    }

    public function penilaianPengawasDetail()
    {
        return $this->hasMany(PenilaianPengawasDetail::class, 'penilaian_pengawas_id', 'id');
    }
}