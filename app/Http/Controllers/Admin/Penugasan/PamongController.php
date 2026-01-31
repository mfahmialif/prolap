<?php

namespace App\Http\Controllers\Admin\Penugasan;

use Carbon\Carbon;
use App\Models\Tahun;
use App\Models\Pamong;
use Illuminate\Http\Request;
use App\Models\PamongPeserta;
use App\Http\Services\BulkData;
use App\Models\PenugasanPamong;
use App\Http\Services\GoogleDrive;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PenugasanPamongDetail;
use Yajra\DataTables\Facades\DataTables;

class PamongController extends Controller
{

    protected $dir = BulkData::dirGdrive['dokumen'];
    function index()
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.tugas.pamong.index', compact('tahun'));
    }

    function getData(Request $request)
    {
        $search = request('search.value');

        $data = Pamong::join('users', 'users.id', '=', 'pamong.user_id')
            ->join('tahun', 'tahun.id', '=', 'pamong.tahun_id')
            ->select('pamong.*', 'users.email as email', 'users.jenis_kelamin as jk');
        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->pamong_id, function ($query) use ($request) {
                    $query->where('pamong.id', $request->pamong_id);
                });
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->when($request->tahun_id != "*", function ($query) use ($request) {
                    $query->where('pamong.tahun_id', $request->tahun_id);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
                    $query->orWhere('users.email', 'LIKE', "%$search%");
                    $query->orWhere('pamong.nama', 'LIKE', "%$search%");
                    $query->orWhere('pamong.pamong', 'LIKE', "%$search%");
                });
            })
            ->addColumn('action', function ($row) {
                $btn = '
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Klik
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button type="button" class="dropdown-item BtnRekap"
                            data-id="' . $row->id . '"
                        >Rekap</button>
                    </div>
                </div>
                ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    function detail($idPamong)
    {
        $pamong = Pamong::find($idPamong);

        return view('admin.tugas.pamong.detail', compact('pamong', 'idPamong'));
    }

    function detailData($idPamong)
    {
        $search = request('search.value');
        $data = PenugasanPamong::where('pamong_id', $idPamong)->select('penugasan_pamong.*')->orderBy('id');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('penugasan_pamong.penugasan', 'LIKE', "%$search%");
                    $query->orWhere('penugasan_pamong.mulai', 'LIKE', "%$search%");
                    $query->orWhere('penugasan_pamong.selesai', 'LIKE', "%$search%");
                });
            })
            ->addColumn('action', function ($row) {
                $btn = '
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button"
                    id="dropdownMenuButton" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    Klik
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <button type="button" class="dropdown-item BtnInputTugas"
                        data-id="' . $row->id . '"
                        data-penugasan="' . $row->penugasan . '"
                        data-pamong_id ="' . $row->pamong_id . '"
                        data-mulai="' . $row->mulai . '"
                        data-selesai="' . $row->selesai . '"
                    >Input Tugas</button>
                    <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modal_edit"
                        data-id="' . $row->id . '"
                        data-penugasan="' . $row->penugasan . '"
                        data-pamong_id ="' . $row->pamong_id . '"
                        data-mulai="' . $row->mulai . '"
                        data-selesai="' . $row->selesai . '"
                    >Edit Tugas</button>
                    <div class="dropdown-divider"></div>
                    <form action="" onsubmit="deleteData(event)" method="POST">
                    ' . method_field('delete') . csrf_field() . '
                        <input type="hidden" name="id" value="' . $row->id . '">
                        <input type="hidden" name="nama" value="' . $row->penugasan . '">
                        <button type="submit" class="dropdown-item text-danger">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    function simpanDetail(Request $request, $idPamong)
    {
        try {
            $request->validate([
                'penugasan' => 'required',
                'mulai' => 'required',
                'selesai' => 'required'
            ]);

            $penugasan = $request->penugasan;
            $mulai = $request->mulai;
            $selesai = $request->selesai;

            $PenugasanPamong = new PenugasanPamong();
            $PenugasanPamong->pamong_id = $idPamong;
            $PenugasanPamong->penugasan = $penugasan;
            $PenugasanPamong->mulai = $mulai;
            $PenugasanPamong->selesai = $selesai;
            $PenugasanPamong->save();
            return [
                'message' => 200,
                'data' => 'Data Berhasil ditambahkan',
                'req' => $request->all()
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data' => 'Data Gagal ditambahkan',
                'req' => $request->all()
            ];
        }
    }

    public function editDetail(Request $request, $idPamong)
    {
        try {
            $request->validate([
                'id' => 'required',
                'penugasan' => 'required',
                'mulai' => 'required',
                'selesai' => 'required'
            ]);

            $id = $request->id;
            $penugasan = $request->penugasan;
            $mulai = $request->mulai;
            $selesai = $request->selesai;

            $PenugasanPamong = PenugasanPamong::findOrFail($id);
            $PenugasanPamong->penugasan = $penugasan;
            $PenugasanPamong->mulai = $mulai;
            $PenugasanPamong->selesai = $selesai;
            $PenugasanPamong->save();

            return [
                'message' => 200,
                'data' => 'Data Berhasil diedit',
                'req' => $request->all()
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data' => 'Data Gagal diedit',
                'req' => $request->all()
            ];
        }
    }

    public function deleteDetail(Request $request, $idPamong)
    {
        try {
            $request->validate([
                'id' => 'required'
            ]);

            $penugasanPamong = PenugasanPamong::find($request->id);
            if (count($penugasanPamong->penugasanPamongDetail) > 0) {
                return abort(500, 'Semua data penugasan mahasiswa harus dihapus dulu');
            }

            $penugasanPamong->delete();

            return [
                'message' => 200,
                'data' => 'Penugasan Berhasil dihapus',
                'req' => $request->all()
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data' => $th->getMessage(),
                'req' => $request->all()
            ];
        }
    }

    function input($idPamong, $idPenugasanPamong)
    {
        $pamong = Pamong::find($idPamong);
        $penugasanPamong = PenugasanPamong::find($idPenugasanPamong);
        $pamongPeserta = PamongPeserta::join('peserta', 'peserta.id', '=', 'pamong_peserta.peserta_id')->select('pamong_peserta.*', 'peserta.nama as nama_peserta')->where('pamong_id', $idPamong)->get();
        return view('admin.tugas.pamong.input', compact('idPamong', 'idPenugasanPamong', 'pamongPeserta', 'penugasanPamong', 'pamong'));
    }

    function simpanInput(Request $request, $idPamong, $idPenugasanPamong)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac|max:' . BulkData::maxSizeUpload,
                'no' => 'required',
                'keterangan' => 'nullable'
            ]);

            $penugasanPamong = PenugasanPamong::findOrFail($idPenugasanPamong);
            $start = Carbon::parse($penugasanPamong->mulai);
            $end = Carbon::parse($penugasanPamong->selesai);

            $now = Carbon::now();
            // $now = Carbon::parse('2024-09-29 00:00:00');
            if ($now->lessThan($start)) {
                return abort(500, 'Penugasan belum dimulai');
            }
            if (!$now->between($start, $end)) {
                return abort(500, 'Penugasan melewati batas deadline');
            }

            $pamongPesertaId = $request->no;
            $file = $request->file('file');
            $keterangan = $request->keterangan;

            $penugasanPamongDetail = PenugasanPamongDetail::where('pamong_peserta_id', $pamongPesertaId)
                ->where('penugasan_pamong_id', $idPenugasanPamong)->first();

            if (!$penugasanPamongDetail) {
                $penugasanPamongDetail = new PenugasanPamongDetail();
            } else {
                GoogleDrive::deleteWithPath($penugasanPamongDetail->path, $this->dir);
            }

            if ($request->hasFile('file')) {
                $uploud = GoogleDrive::upload($file, 'PENUGASAN', $this->dir);
                $path = GoogleDrive::getData($uploud['name'], $this->dir);
                $getPath = $path['path'];
            }

            $penugasanPamongDetail->penugasan_pamong_id = $idPenugasanPamong;
            $penugasanPamongDetail->pamong_peserta_id = $pamongPesertaId;
            $penugasanPamongDetail->file = $uploud['name'];
            $penugasanPamongDetail->path = $getPath;
            $penugasanPamongDetail->keterangan = $keterangan;
            $penugasanPamongDetail->waktu_pengumpulan = Carbon::now();
            $penugasanPamongDetail->save();


            return [
                'message' => 200,
                'data' => 'Data berhasil ditambahkan',
                'req' => $request->all()
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data' => $th->getMessage(),
                'req' => $request->all(),
                'err' => $th->getMessage()
            ];
        }
    }

    public function deleteInput(Request $request, $idPamong, $idPenugasanPamong)
    {
        try {
            $request->validate([
                'id' => 'required'
            ]);

            $data = PenugasanPamongDetail::where('pamong_peserta_id', $request->id)
                ->where('penugasan_pamong_id', $idPenugasanPamong)
                ->first();

            if ($data) {
                $delete = GoogleDrive::deleteWithPath($data->path, $this->dir);
                if (!$delete['status']) {
                    return abort(500, $delete['message']);
                }

                $data->delete();
            }
            return [
                'message' => 200,
                'data' => 'File Tugas Mahasiswa Berhasil dihapus',
                'req' => $request->all(),
                'response' => $idPenugasanPamong
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data' => 'File Tugas Mahasiswa Gagal dihapus',
                'req' => $request->all()
            ];
        }
    }
}
