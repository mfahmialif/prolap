<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;
    protected $table = "peserta";
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }
    public function poskoPeserta()
    {
        return $this->hasMany(PoskoPeserta::class);
    }
    public function pamongPeserta()
    {
        return $this->hasMany(PamongPeserta::class);
    }
    public function nilai(){
        return $this->hasOne(Nilai::class);
    }
}