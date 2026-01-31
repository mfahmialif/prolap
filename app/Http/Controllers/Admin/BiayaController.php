<?php

namespace App\Http\Controllers\Admin;

use App\Models\Biaya;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Services\Summernote;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;


class BiayaController extends Controller
{
    public function index()
    {
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        return view('admin.biaya.index', compact('tahun'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Biaya::join('tahun', 'biaya.tahun_id', '=', 'tahun.id')
            ->select('biaya.*', 'tahun.nama as tahun_nama');
        return DataTables::of($data)
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('biaya.jenjang', 'LIKE', "%$search%");
                    $query->orWhere('tahun.nama', 'LIKE', "%$search%");
                });
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Klik
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button type="button" class="dropdown-item"
                            data-toggle="modal" data-target="#modal_edit"
                            data-id="' . $row->id . '"
                            data-tahun_id="' . $row->tahun_id . '"
                            data-jenjang="' . $row->jenjang . '"
                            data-jumlah="' . $row->jumlah . '"
                        >Edit</button>
                        <form action="" onsubmit="deleteData(event)" method="POST">
                        ' . method_field('delete') . csrf_field() . '
                            <input type="hidden" name="id" value="' . $row->id . '">
                            <input type="hidden" name="nama" value="' . $row->jenjang . '">
                            <button type="submit" class="dropdown-item text-danger">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function add(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'tahun_id' => 'required',
                'jenjang' => 'required',
                'jumlah' => 'required',
            ]);

            $check = Biaya::where([
                ['tahun_id', $request->tahun_id],
                ['jenjang', $request->jenjang],
            ])->first();

            if ($check) {
                $data = [
                    "message" => 500,
                    "data" => 'Data sudah ada',
                    "req" => $request->all(),
                ];
                return $data;
            }

            $new = new Biaya();
            $new->tahun_id = $request->tahun_id;
            $new->jenjang = $request->jenjang;
            $new->jumlah = $request->jumlah;
            $new->save();

            $data = [
                "message" => 200,
                "data" => 'Berhasil menambahkan Biaya',
                "req" => $request->all(),
            ];
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            $data = [
                "message" => 500,
                "data" => $th->getMessage(),
                "req" => $request->all(),
            ];
        }
        return $data;
    }

    public function edit(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id' => 'required',
                'tahun_id' => 'required',
                'jenjang' => 'required',
                'jumlah' => 'required',
            ]);

            $editData = Biaya::where([
                ['tahun_id', $request->tahun_id],
                ['jenjang', $request->jenjang],
                ['id', '!=', $request->id]
            ])->first();

            if ($editData) {
                $data = [
                    "message" => 500,
                    "data" => 'Data sudah ada',
                    "req" => $request->all(),
                ];
                return $data;
            }

            $editData = Biaya::findOrFail($request->id);
            $editData->tahun_id = $request->tahun_id;
            $editData->jenjang = $request->jenjang;
            $editData->jumlah = $request->jumlah;
            $editData->save();

            $data = [
                'message' => 200,
                'data' => 'Berhasil mengedit Biaya',
                'req' => $request->all(),
            ];
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            $data = [
                'message' => 500,
                'data' => $th->getMessage(),
                'req' => $request->all(),
            ];
        }

        return $data;
    }

    public function delete(Request $request)
    {
        try {
            $dataValidated = $request->validate([
                'id' => 'required',
            ]);

            $delete = Biaya::where('id', $dataValidated['id'])->first();

            $delete->destroy($dataValidated['id']);
            $data = [
                "message" => 200,
                "data" => "Berhasil menghapus data",
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "message" => 500,
                "data" => 'Gagal menghapus data, seluruh data peserta dengan tipe yang sama harus dihapus dulu',
            ];
            return $data;
        }
    }
}