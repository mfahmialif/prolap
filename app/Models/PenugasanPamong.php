<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanPamong extends Model
{
    use HasFactory;
    protected $table = "penugasan_pamong";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pamong()
    {
        return $this->belongsTo(Pamong::class);
    }

    public function penugasanPamongDetail()
    {
        return $this->hasMany(PenugasanPamongDetail::class);
    }
}
