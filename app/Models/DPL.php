<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DPL extends Model
{
    use HasFactory;
    protected $table = "dpl";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function poskoDpl()
    {
        return $this->hasMany(PoskoDpl::class, 'dpl_id', 'id');
    }

    public function penilaianDpl()
    {
        return $this->hasMany(PenilaianDpl::class, 'dpl_id', 'id');
    }

}