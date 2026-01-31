<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengawas extends Model
{
    use HasFactory;
    protected $table = "pengawas";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function poskoPengawas(){
        return $this->hasMany(PoskoPengawas::class, 'pengawas_id', 'id');
    }
}
