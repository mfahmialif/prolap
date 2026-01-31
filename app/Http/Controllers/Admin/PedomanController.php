<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\BulkData;
use App\Http\Services\GoogleDrive;
use App\Http\Services\Summernote;
use App\Models\Pedoman;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;


class PedomanController extends Controller
{
    public $dir = BulkData::dirGdrive['dokumen'];

    public function index()
    {
        $prodi = Prodi::orderBy('jenjang', 'asc')->get();
        return view('admin.pedoman.index', compact('prodi'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Pedoman::join('prodi', 'prodi.id', '=', 'pedoman.prodi_id')
            ->select('pedoman.*', 'prodi.nama as prodi_nama');
        return DataTables::of($data)
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('pedoman.nama', 'LIKE', "%$search%");
                    $query->orWhere('prodi.nama', 'LIKE', "%$search%");
                    $query->orWhere('pedoman.keterangan', 'LIKE', "%$search%");
                });
            })
            ->editColumn('file', function ($row) {
                $button = $row->file ? "<a class='btn btn-primary w-100' href='" . GoogleDrive::directDownload(
                    $row->file,
                    $this->dir,
                    "$row->keterangan - " . $row->prodi_nama
                ) .
                    "' target='_blank'>Download <i class='fas fa-download'></i></a>" : "<button class='btn btn-danger w-100'>Tidak ada file <i class='fas fa-times'></i></button>";
                return $button;
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
                            data-prodi_id="' . $row->prodi_id . '"
                            data-nama="' . $row->nama . '"
                            data-keterangan="' . $row->keterangan . '"
                        >Edit</button>
                        <form action="" onsubmit="deleteData(event)" method="POST">
                        ' . method_field('delete') . csrf_field() . '
                            <input type="hidden" name="id" value="' . $row->id . '">
                            <input type="hidden" name="nama" value="' . $row->prodi_nama . '">
                            <button type="submit" class="dropdown-item text-danger">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'file'])
            ->toJson();
    }

    public function add(Request $request)
    {
        try {
            $request->validate([
                'prodi_id' => 'nullable',
                'keterangan' => 'nullable|max:15',
            ]);

            $prodi_id = $request->prodi_id;
            $keterangan = $request->keterangan;

            $edit = Pedoman::where([
                ['prodi_id', $prodi_id],
                ['keterangan', $keterangan]
            ])->first();

            if ($edit) {
                $data = [
                    "message" => 500,
                    "data" => 'Data sudah ada, silahkan update jika ada perubahan',
                    "req" => $request->all(),
                ];
                return $data;
            }

            $new = new Pedoman();
            $new->prodi_id = $prodi_id;
            $new->keterangan = $keterangan;

            if ($request->has('file')) {
                $upload = GoogleDrive::upload($request->file('file'), 'Pedoman', $this->dir);
                if ($upload['status'] == false) {
                    return abort(500, "Upload file di gdrive gagal");
                }
                $getPath = GoogleDrive::getData($upload['name'], $this->dir);
                $new->file = $upload['name'];
                $new->path = $getPath['path'];
            }
            $new->save();

            $data = [
                "message" => 200,
                "data" => 'Berhasil menambahkan data pedoman prodi',
                "req" => $request->all(),
            ];
        } catch (\Throwable $th) {
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
            $request->validate([
                'id' => 'required',
                'keterangan' => 'nullable|max:15',
            ]);

            $id = $request->id;
            $prodi_id = $request->prodi_id;
            $keterangan = $request->keterangan;

            $edit = Pedoman::where([
                ['prodi_id', $prodi_id],
                ['keterangan', $keterangan],
                ['id', '!=', $id]
            ])->first();

            if ($edit) {
                $data = [
                    "message" => 500,
                    "data" => 'Data sudah ada',
                    "req" => $request->all(),
                ];
                return $data;
            }

            $edit = Pedoman::findOrFail($id);
            $edit->prodi_id = $prodi_id;
            $edit->keterangan = $keterangan;

            if ($request->has('file')) {
                $upload = GoogleDrive::upload($request->file, 'Pedoman', $this->dir);
                if ($upload['status'] == false) {
                    return abort(500, "Upload file di gdrive gagal");
                }
                $getPath = GoogleDrive::getData($upload['name'], $this->dir);

                $deleteOldFile = GoogleDrive::deleteWithPath($edit->path, $this->dir);
                if ($deleteOldFile['status'] == false) {
                    return abort(500, "Delete file di gdrive gagal");
                }

                $edit->file = $upload['name'];
                $edit->path = $getPath['path'];
            }

            $edit->save();

            $data = [
                'message' => 200,
                'data' => 'Berhasil mengedit data pedoman prodi',
                'req' => $request->all(),
            ];
        } catch (\Throwable $th) {
            if (@$upload['status'] == true) {
                $deleteOldFile = GoogleDrive::deleteWithPath($getPath->path, $this->dir);
                if ($deleteOldFile['status'] == false) {
                    return abort(500, "Delete file di gdrive gagal");
                }
            }
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

            $delete = Pedoman::where('id', $dataValidated['id'])->first();

            if ($delete->path) {
                $deleteGdrive = GoogleDrive::deleteWithPath($delete->path, $this->dir);
                if ($deleteGdrive['status'] == false) {
                    return abort(500, "Delete file di gdrive gagal");
                }
            }
            $delete->delete();
            $data = [
                "message" => 200,
                "data" => "Berhasil menghapus data",
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "message" => 500,
                "data" => 'Gagal menghapus data',
            ];
            return $data;
        }
    }
}
