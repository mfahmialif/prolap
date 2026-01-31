<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPsPamong extends Model
{
    use HasFactory; 
    
    protected $table = "absensi_ps_pamong";

    protected $guarded = [];

    public function pamong()
    {
        return $this->belongsTo(Pamong::class, 'pamong_id', 'id');
    }
}
