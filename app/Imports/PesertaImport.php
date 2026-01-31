<?php
namespace App\Imports;

use App\Models\User;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\Status;
use App\Models\Peserta;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesertaImport implements ToCollection, WithHeadingRow
{
    private $newData = 0;
    private $total   = 0;

    public function __construct()
    {
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (! $row['nim']) {
                continue;
            }
            $this->total++;

            $user = User::where('username', $row['nim'])->first();
            if ($user) {
                continue;
            }

            $user                = new User();
            $user->username      = $row['nim'];
            $user->nama          = $row['nama'];
            $user->role_id       = 2;
            $user->password      = \Hash::make($row['nim']);
            $user->jenis_kelamin = $row['jenis_kelamin'];
            $user->no_unik       = $row['nim'];
            $user->save();

            $kolomPeserta = $row->except(['keterangan', 'status_nama', 'jenis_kelamin', 'tahun_nama', 'prodi_nama', 'posko_nama', 'posko_lokasi', 'created_at', 'updated_at']);
            $status       = Status::where('nama', $row['status_nama'])->first();
            $tahun        = Tahun::where('nama', 'LIKE', '%' . $row['tahun_nama'] . '%')->first();
            $prodi        = Prodi::where('nama', $row['prodi_nama'])->first();
            $kolomPeserta = $kolomPeserta->merge([
                'status_id' => $status->id,
                'tahun_id'  => $tahun->id,
                'prodi_id'  => $prodi->id,
                'user_id'   => $user->id,
            ]);

            Peserta::create($kolomPeserta->toArray());
            $this->newData++;
        }
    }

    public function getResponse()
    {
        return "$this->newData data baru dari $this->total total data";
    }
}
