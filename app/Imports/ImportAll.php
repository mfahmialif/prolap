<?php

namespace App\Imports;

use App\Http\Services\DosenService;
use App\Models\DPL;
use App\Models\Posko;
use App\Models\Peserta;
use App\Models\PoskoDpl;
use App\Models\PoskoPeserta;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportAll implements ToCollection, WithHeadingRow
{
    public $results = [];

    public function import(Collection $row)
    {
        try {
            if (empty($row['nama']) || empty($row['lokasi'])) {
                return null; // Jangan lanjutkan jika data tidak lengkap
            }

            $jumlah = Posko::count();
            $no = $jumlah + 1;
            $dataPosko = Posko::where('lokasi', $row['lokasi'])->first();
            $mahasiswa = Peserta::where('nim', $row['nim'])->first();
            if (!$mahasiswa) {
                abort(404, $row['nim'] . ' tidak ditemukan');
            }
            /*$dataDosen = DosenService::all(null, null, $row['niy'], null, null, null);*/
            $dataDosen = $row['dosen'];
            if (!$dataDosen) {
                abort(404, $row['dpl'] . ' tidak ditemukan');
            }

            $password = "123456";
            $userDosen = User::where('username', $dataDosen->kode)->first();
            if (!$userDosen) {
                $userDosen = new User();
            }
            $userDosen->username = $dataDosen->kode;
            $userDosen->nama = $dataDosen->nama;
            $userDosen->email = $dataDosen->email;
            $userDosen->password = Hash::make($password);
            $userDosen->no_unik = $password;
            $userDosen->jenis_kelamin = $dataDosen->jenis_kelamin;
            $userDosen->role_id = 4;
            $userDosen->save();

            /*$user = User::where('nama', $dataDosen->nama)->first();*/
            /*if (empty($user)) {*/
            /*    $user->username = $dataDosen->nidn;*/
            /*    $pass = $row["dpl"].$no;*/
            /*    $user = User::create([*/
            /*        'username' => $dataDosen->nidn,*/
            /*        'nama' => $dataDosen->nama,*/
            /*        'email' => $dataDosen->email,*/
            /*        'role_id' => 4,*/
            /*        'password' => Hash::make($pass),*/
            /*        'jenis_kelamin' => 'Laki-laki',*/
            /*        'status' => 'active',*/
            /*        'no_unik' => rand(1, 50)*/
            /*    ]);*/
            /*}*/

            $prodi = Prodi::where('nama', $dataDosen->nama_prodi)->first();
            if (!$prodi) {
                abort(404, 'Prodi ' . $dataDosen->nama_prodi . 'tidak ditemukan');
            }

            if (!$dataPosko) {
                $dataPosko = new Posko();
            }
            $dataPosko->tahun_id = 2;
            $dataPosko->nama = "Posko " . $no;
            $dataPosko->lokasi = $row['lokasi'];
            $dataPosko->keterangan = 'Aman';
            $dataPosko->save();

            $poskoPeserta = PoskoPeserta::where('posko_id', $dataPosko->id)
                ->where('peserta_id', $mahasiswa->id)
                ->first();
            if (!$poskoPeserta) {
                $poskoPeserta = new PoskoPeserta();
            }
            $poskoPeserta->posko_id = $dataPosko->id;
            $poskoPeserta->peserta_id = $mahasiswa->id;
            $poskoPeserta->save();

            /*$dpl = DPL::where('nama', $dataDosen->nama)->first();*/
            $dpl = $userDosen->dpl;

            if (!$dpl) {
                $dpl = new DPL();
            }
            $dpl->user_id = $userDosen->id;
            $dpl->prodi_id = $prodi->id;
            $dpl->dosen_id = $dataDosen->id;
            $dpl->nama = $dataDosen->nama;
            $dpl->status = 'Aktif';
            $dpl->save();

            $poskoDpl = PoskoDpl::where('posko_id', $dataPosko->id)
                ->where('dpl_id', $dpl->id)
                ->first();
            if (!$poskoDpl) {
                $poskoDpl = new PoskoDpl();
            }
            $poskoDpl->posko_id = $dataPosko->id;
            $poskoDpl->dpl_id = $dpl->id;
            $poskoDpl->save();

            return [
                'status' => true,
                'message' => 'Success',
                'data' => $row
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage(),
                'data' => $row
            ];
        }
    }

    public function deleteData()
    {
        // delete all data before import
        PoskoDpl::query()->delete();
        PoskoPeserta::query()->delete();
        Posko::query()->delete();
        DPL::query()->delete();
        User::whereIn('role_id', [4, 5, 6])->delete();
    }

    public function getDataDosen(Collection $rows)
    {
        // get dataDosen with whereIn NIY for effective API, one time request
        $niyDpl = $rows->pluck('niy')->filter()->unique()->values();
        $dataDosen = DosenService::all(null, null, null, null, null, null, [
            ['mst_dosen.kode', $niyDpl]
        ]);
        if (count($dataDosen) != count($niyDpl)) {
            abort(404, 'Data Dosen tidak ditemukan di siakad');
        }
        $dataDosen = collect($dataDosen);
        return $dataDosen;
    }

    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();

            $this->deleteData();
            $dataDosen = $this->getDataDosen($rows);

            // import
            foreach ($rows as $row) {
                $dosen = $dataDosen->firstWhere('kode', $row['niy']);
                $row['dosen'] = $dosen;

                $import = $this->import($row);

                if (!$import['status']) {
                    $this->results[] = $import['message'];
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new \Exception($th->getMessage());
        }
    }
}
