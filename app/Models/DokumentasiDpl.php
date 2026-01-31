<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumentasiDpl extends Model
{
    use HasFactory;
    protected $table = "dokumentasi_dpl";
    protected $guarded = [];

    public function poskoDpl()
    {
        return $this->belongsTo(PoskoDpl::class);
    }
}
