<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanPamongDetail extends Model
{
    use HasFactory;
    protected $table = "penugasan_pamong_detail";
    protected $guarded = [];

    public function penugasanPamong()
    {
        return $this->belongsTo(PenugasanPamong::class, 'penugasan_pamong_id');
    }

    public function pamongPeserta()
    {
        return $this->belongsTo(PamongPeserta::class, 'pamong_peserta_id', 'id');
    }
}
