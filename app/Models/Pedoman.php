<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedoman extends Model
{
    use HasFactory;

    protected $table = 'pedoman';
    protected $guarded = [];

    public function prodi(){
        return $this->belongsTo(Prodi::class);
    }
}
