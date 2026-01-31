<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianDpl extends Model
{
    use HasFactory;

    protected $table = "penilaian_dpl";
    protected $guard = [];

    public function poskoDpl()
    {
        return $this->belongsTo(PoskoDpl::class, 'posko_dpl_id', 'id');
    }

    public function poskoPeserta()
    {
        return $this->belongsTo(PoskoPeserta::class, 'posko_peserta_id', 'id');
    }

    public function penilaianDplDetail()
    {
        return $this->hasMany(PenilaianDplDetail::class, 'penilaian_dpl_id', 'id');
    }
}