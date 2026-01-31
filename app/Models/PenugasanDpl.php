<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanDpl extends Model
{
    use HasFactory;
    protected $table = "penugasan_dpl";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function poskoDpl()
    {
        return $this->belongsTo(PoskoDpl::class);
    }

    public function penugasanDplDetail()
    {
        return $this->hasMany(PenugasanDplDetail::class);
    }
}
