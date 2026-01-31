<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaAcaraDpl extends Model
{
    use HasFactory;
    protected $table = "berita_acara_dpl";
    protected $guarded = [];

    public function poskoDpl()
    {
        return $this->belongsTo(PoskoDpl::class);
    }
}
