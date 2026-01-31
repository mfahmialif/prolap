<?php

namespace App\Http\Controllers\Operasi;

use App\Models\Peserta;
use Illuminate\Http\Request;
use App\Http\Services\Mahasiswa;
use App\Http\Controllers\Controller;

class PesertaController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = (string) $request->term;
        $mahasiswa = Mahasiswa::kkn(null, 30, $query, 'mst_mhs.id', 'desc', [
            ["mst_prodi.jenjang", "S1"]
        ]);
        $mahasiswaMap = array_map(function ($item) {
            $kkn = $item->kkn == true ? "Oke" : "Tidak Oke";
            $prodi = $item->prodi->alias;
            return "$item->nim - $item->nama - $prodi - (KKN: $kkn)";
        }, $mahasiswa);
        return $mahasiswaMap;
    }

    public function getData(Request $request)
    {
        try {
            $request->validate([
                'search' => 'required'
            ]);

            $siswa = Mahasiswa::kkn(null, 1, null, null, null, [["mst_mhs.nim", $request->search]]);

            if (count($siswa) < 1) {
                return response()->json([
                    'status' => false,
                    'data' => "Tidak ada data"
                ]);
            }

            $siswa = $siswa[0];
            $peserta = Peserta::where('nim', $siswa->nim)->with('status', 'user', 'prodi', 'tahun')->first();
            $siswa->nama = @$peserta->nama ? $peserta->nama : $siswa->nama;
            $siswa->peserta = @$peserta;
            return response()->json([
                'status' => true,
                'data' => $siswa
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'data' => 'Tidak ada data',
                'err' => $th->getMessage()
            ]);
        }
    }
}
