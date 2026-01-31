<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanMahasiswa extends Model
{
    use HasFactory;
    protected $table   = "kegiatan_mahasiswa";
    protected $guarded = [];
    protected $appends = ['formated_date'];

    public function posko()
    {
        return $this->belongsTo(Posko::class, 'posko_id', 'id');
    }

    public function bukti()
    {
        return $this->hasMany(KegiatanMahasiswaBukti::class, 'kegiatan_mahasiswa_id', 'id');
    }

    public function getFormatedDateAttribute()
    {
        return Carbon::parse($this->tanggal)->format('d-m-Y H:i');
    }
}
