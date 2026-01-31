<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPsPengawas extends Model
{
    use HasFactory;

    public function poskoPengawas(){
        return $this->belongsTo(PoskoPengawas::class);
    }
}
