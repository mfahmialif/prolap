<?php
namespace App\Models;

use App\Http\Services\GoogleDrive;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanMahasiswaBukti extends Model
{
    use HasFactory;
    protected $table   = "kegiatan_mahasiswa_bukti";
    protected $guarded = [];
    protected $appends = ['url']; // <-- kolom tambahan

    public function kegiatanMahasiswa()
    {
        return $this->belongsTo(KegiatanMahasiswa::class, 'kegiatan_mahasiswa_id', 'id');
    }

    public function getUrlAttribute()
    {
        $value = $this->path;
        return GoogleDrive::link($value);
    }

}
