<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPamong extends Model
{
    use HasFactory;
    protected $table = "penilaian_pamong";
    protected $guard = [];

    public function pamong()
    {
        return $this->belongsTo(Pamong::class, 'pamong_id', 'id');
    }

    public function pamongPeserta()
    {
        return $this->belongsTo(PamongPeserta::class, 'pamong_peserta_id', 'id');
    }

    public function penilaianPamongDetail()
    {
        return $this->hasMany(PenilaianPamongDetail::class, 'penilaian_pamong_detail_id', 'id');
    }
}
