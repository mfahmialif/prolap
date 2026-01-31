<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoskoPengawas extends Model
{
    use HasFactory;

    protected $table = "posko_pengawas";
    protected $guarded = [];

    public function posko()
    {
        return $this->belongsTo(Posko::class);
    }

    public function pengawas()
    {
        return $this->belongsTo(Pengawas::class);
    }

    public function absensi(){
        return $this->hasMany(AbsensiPsPengawas::class);
    }
}
