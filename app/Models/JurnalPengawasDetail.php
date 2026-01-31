<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalPengawasDetail extends Model
{
    use HasFactory;
    protected $table   = "jurnal_pengawas_detail";
    protected $guarded = [];

    public function jurnalPengawas()
    {
        return $this->belongsTo(JurnalPengawas::class, 'jurnal_pengawas_id');
    }

    public function poskoPeserta()
    {
        return $this->belongsTo(PoskoPeserta::class, 'posko_peserta_id', 'id');
    }
}
