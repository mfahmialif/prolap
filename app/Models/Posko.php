<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posko extends Model
{
    use HasFactory;
    protected $table = "posko";
    protected $guarded = [];

    public function poskoPeserta(){
        return $this->hasMany(PoskoPeserta::class, 'posko_id', 'id');
    }

    public function poskoDpl(){
        return $this->hasMany(PoskoDpl::class, 'posko_id', 'id');
    }

    public function poskoPengawas(){
        return $this->hasMany(PoskoPengawas::class, 'posko_id', 'id');
    }

    public function tahun(){
        return $this->belongsTo(Tahun::class, 'tahun_id', 'id');
    }

    public function kegiatanMahasiswa(){
        return $this->hasMany(KegiatanMahasiswa::class, 'posko_id', 'id');
    }
}
