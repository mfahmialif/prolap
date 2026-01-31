<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomponenNilai extends Model
{
    use HasFactory;

    protected $table = 'komponen_nilai';

    protected $guarded = [];

    public function penilaianDplDetail()
    {
        return $this->hasMany(PenilaianDplDetail::class, 'komponen_nilai_id', 'id');
    }
    
    public function penilaianPamongDetail()
    {
        return $this->hasMany(PenilaianPamongDetail::class, 'komponen_nilai_id', 'id');
    }
}
