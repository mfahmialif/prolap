<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoskoDpl extends Model
{
    use HasFactory;
    protected $table      = "posko_dpl";
    protected $primaryKey = 'id';
    protected $guarded    = [];

    public function posko()
    {
        return $this->belongsTo(Posko::class);
    }

    public function dpl()
    {
        return $this->belongsTo(DPL::class);
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiPsDpl::class);
    }

    public function rubrikPenilaian()
    {
        return $this->hasOne(RubrikPenilaianDpl::class);
    }

    public function beritaAcara()
    {
        return $this->hasOne(BeritaAcaraDpl::class);
    }

    public function dokumentasi()
    {
        return $this->hasOne(DokumentasiDpl::class);
    }

    public function penugasanDpl()
    {
        return $this->hasMany(PenugasanDpl::class, 'posko_dpl_id', 'id');
    }
}
