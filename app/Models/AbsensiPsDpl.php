<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiPsDpl extends Model
{
    use HasFactory;
    protected $table = "absensi_ps_dpl";

    protected $guarded = [];

    public function poskoDpl()
    {
        return $this->belongsTo(PoskoDpl::class, 'posko_dpl_id', 'id');
    }
}
