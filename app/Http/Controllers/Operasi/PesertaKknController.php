<?php

namespace App\Http\Controllers\Operasi;

use App\Models\Peserta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PesertaKknController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = (string) $request->term;
        $peserta = Peserta::where(function ($q) use ($query) {
            $q->orWhere('nama', 'like', "%$query%");
            $q->orWhere('nim', 'like', "%$query%");
        })
            ->limit(10)
            ->with('user', 'prodi')
            ->get()
            ->toArray();

        $pesertaMap = array_map(function ($item) {
            $item["label"] = $item["nim"] . ' - ' . $item["nama"] . ' - ' . $item['prodi']['alias'];
            $item["value"] = $item["id"];
            return $item;
        }, $peserta);
        return $pesertaMap;
    }

    public function getData(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required'
            ]);

            $peserta = Peserta::where('id', $request->id)->with('user', 'prodi')->first();

            if (!$peserta) {
                return abort(500, 'Tidak ada data');
            }

            return response()->json([
                'status' => true,
                'data' => $peserta
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
