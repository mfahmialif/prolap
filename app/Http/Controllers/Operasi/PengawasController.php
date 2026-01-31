<?php

namespace App\Http\Controllers\Operasi;

use App\Models\DPL;
use App\Models\Pengawas;
use App\Models\Peserta;
use Illuminate\Http\Request;
use App\Http\Services\Mahasiswa;
use App\Http\Services\DosenService;
use App\Http\Controllers\Controller;

class PengawasController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = (string) $request->term;
        $pengawas = Pengawas::join('users', 'users.id', '=', 'pengawas.user_id')
            ->where(function ($q) use ($query) {
                $q->orWhere('pengawas.nama', 'like', "%$query%");
                $q->orWhere('users.username', 'like', "%$query%");
            })
            ->limit(30)
            ->select('pengawas.*', 'users.username as username')
            ->get()
            ->toArray();

        $pengawasMap = array_map(function ($item) {
            $item["label"] = $item["username"] . ' - ' . $item["nama"];
            $item["value"] = $item["id"];
            return $item;
        }, $pengawas);
        return $pengawasMap;
    }

    public function getData(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required'
            ]);

            $pengawas = Pengawas::where('id', $request->id)->with('user')->first();

            if (!$pengawas) {
                return abort(500, 'Tidak ada data');
            }

            return response()->json([
                'status' => true,
                'data' => $pengawas
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'data' => 'Tidak ada data',
                'err' => $th->getMessage(),
                'req' => $request->all()
            ]);
        }
    }
}
