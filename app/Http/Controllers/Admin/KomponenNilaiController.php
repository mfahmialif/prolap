<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tahun;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\KomponenNilai;
use Yajra\DataTables\DataTables;
use App\Http\Services\Summernote;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;


class KomponenNilaiController extends Controller
{
    public function index()
    {
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        $jenis = Role::whereNotIn('nama', [
            'admin', 'peserta', 'keuangan'
        ])->get()->pluck('nama')->toArray();
        return view('admin.komponen-nilai.index', compact('tahun', 'jenis'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = KomponenNilai::join('tahun', 'komponen_nilai.tahun_id', '=', 'tahun.id')
            ->select('komponen_nilai.*', 'tahun.nama as tahun_nama');
        return DataTables::of($data)
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('komponen_nilai.jenis', 'LIKE', "%$search%");
                    $query->orWhere('komponen_nilai.nama', 'LIKE', "%$search%");
                    $query->orWhere('komponen_nilai.bobot', 'LIKE', "%$search%");
                    $query->orWhere('tahun.kode', 'LIKE', "%$search%");
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
                            data-jenis="' . $row->jenis . '"
                            data-nama="' . $row->nama . '"
                            data-bobot="' . $row->bobot . '"
                        >Edit</button>
                        <form action="" onsubmit="deleteData(event)" method="POST">
                        ' . method_field('delete') . csrf_field() . '
                            <input type="hidden" name="id" value="' . $row->id . '">
                            <input type="hidden" name="nama" value="' . $row->nama . '">
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
                'jenis' => 'required',
                'nama' => 'required',
                'bobot' => 'required',
            ]);

            $check = KomponenNilai::where([
                ['tahun_id', $request->tahun_id],
                ['jenis', $request->nama],
                ['nama', $request->nama],
            ])->first();

            if ($check) {
                $data = [
                    "message" => 500,
                    "data" => 'Data sudah ada',
                    "req" => $request->all(),
                ];
                return $data;
            }

            $new = new KomponenNilai();
            $new->tahun_id = $request->tahun_id;
            $new->jenis = $request->jenis;
            $new->nama = $request->nama;
            $new->bobot = $request->bobot;
            $new->save();

            $data = [
                "message" => 200,
                "data" => 'Berhasil menambahkan Komponen Nilai',
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
                'jenis' => 'required',
                'nama' => 'required',
                'bobot' => 'required',
            ]);

            $editData = KomponenNilai::where([
                ['tahun_id', $request->tahun_id],
                ['jenis', $request->jenis],
                ['nama', $request->nama],
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

            $editData = KomponenNilai::findOrFail($request->id);
            $editData->tahun_id = $request->tahun_id;
            $editData->jenis = $request->jenis;
            $editData->nama = $request->nama;
            $editData->bobot = $request->bobot;
            $editData->save();

            $data = [
                'message' => 200,
                'data' => 'Berhasil mengedit Komponen Nilai',
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

            $delete = KomponenNilai::where('id', $dataValidated['id'])->first();

            $delete->destroy($dataValidated['id']);
            $data = [
                "message" => 200,
                "data" => "Berhasil menghapus data",
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "message" => 500,
                "data" => 'Gagal menghapus data, seluruh data yang berkaitan dengan komponen nilai harus dihapus dulu',
            ];
            return $data;
        }
    }
}