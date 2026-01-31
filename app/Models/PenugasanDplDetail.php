<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanDplDetail extends Model
{
    use HasFactory;
    protected $table = "penugasan_dpl_detail";
    protected $guarded = [];

    public function penugasanDpl()
    {
        return $this->belongsTo(PenugasanDpl::class, 'penugasan_dpl_id');
    }

    public function poskoPeserta()
    {
        return $this->belongsTo(PoskoPeserta::class, 'posko_peserta_id', 'id');
    }
}
