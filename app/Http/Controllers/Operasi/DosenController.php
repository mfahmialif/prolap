<?php

namespace App\Http\Controllers\Operasi;

use App\Models\DPL;
use App\Models\Peserta;
use Illuminate\Http\Request;
use App\Http\Services\Mahasiswa;
use App\Http\Services\DosenService;
use App\Http\Controllers\Controller;

class DosenController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = (string) $request->term;
        $mahasiswa = DosenService::all(null, 30, $query, null, null, null);
        $mahasiswaMap = array_map(function ($item) {
            $prodi = $item->nama_prodi;
            return "$item->id - $item->kode - $item->nama - $prodi";
        }, $mahasiswa);
        return $mahasiswaMap;
    }

    public function getData(Request $request)
    {
        try {
            $request->validate([
                'dosen_id' => 'required'
            ]);

            $dosen = DosenService::all(null, 1, null, null, null, [["mst_dosen.id", $request->dosen_id]]);

            if (count($dosen) < 1) {
                return response()->json([
                    'status' => false,
                    'data' => "Tidak ada data"
                ]);
            }

            $dosen = $dosen[0];
            $dpl = DPL::where('dosen_id', $dosen->id)->with('user', 'prodi')->first();
            $dosen->dpl = @$dpl;
            return response()->json([
                'status' => true,
                'data' => $dosen
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
