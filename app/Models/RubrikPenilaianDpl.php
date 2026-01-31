<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RubrikPenilaianDpl extends Model
{
    use HasFactory;
    protected $table = "rubrik_penilaian_dpl";
    protected $guarded = [];

    public function poskoDpl()
    {
        return $this->belongsTo(PoskoDpl::class);
    }
}
