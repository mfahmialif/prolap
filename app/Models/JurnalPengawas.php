<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalPengawas extends Model
{
    use HasFactory;
    protected $table   = "jurnal_pengawas";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function poskoPengawas()
    {
        return $this->belongsTo(PoskoPengawas::class);
    }

    public function jurnalPengawasDetail()
    {
        return $this->hasMany(JurnalPengawasDetail::class);
    }
}
