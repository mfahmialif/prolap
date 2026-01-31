<?php
namespace App\Http\Controllers\Admin\Penugasan;

use App\Exports\ExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Services\BulkData;
use App\Http\Services\GoogleDrive;
use App\Http\Services\Helper;
use App\Models\DPL;
use App\Models\PenugasanDpl;
use App\Models\PenugasanDplDetail;
use App\Models\Peserta;
use App\Models\Posko;
use App\Models\PoskoDpl;
use App\Models\PoskoPengawas;
use App\Models\PoskoPeserta;
use App\Models\Prodi;
use App\Models\Tahun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DplController extends Controller
{
    protected $dir = BulkData::dirGdrive['dokumen'];

    public function index()
    {
        $posko = Posko::all();
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();

        return view('admin.tugas.dpl.index', compact('posko', 'prodi', 'tahun'));
    }

    public function getData(Request $request)
    {
        $search = request('search.value');

        $d = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->join('prodi', 'prodi.id', '=', 'dpl.prodi_id')
            ->join('users', 'users.id', '=', 'dpl.user_id')
            ->join('tahun', 'tahun.id', '=', 'posko.tahun_id')
            ->select('posko_dpl.*', 'posko.nama as nama_posko', 'dpl.nama as username', 'users.jenis_kelamin as jenis', 'prodi.nama', 'prodi.jenjang', 'tahun.nama as tahun_nama')->orderBy('posko_dpl.id');
        return DataTables::of($d)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->pengawas_id, function ($query) use ($request) {
                    $poskoPengawasId = PoskoPengawas::where('pengawas_id', $request->pengawas_id)->get()->pluck('posko_id')->toArray();
                    $query->whereIn('posko_dpl.posko_id', $poskoPengawasId);
                });
                $query->when($request->dpl_id, function ($query) use ($request) {
                    $query->where('posko_dpl.dpl_id', $request->dpl_id);
                });
                $query->when($request->tahun_id != "*", function ($query) use ($request) {
                    $query->where('posko.tahun_id', $request->tahun_id);
                });
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->when($request->prodi_id != "*", function ($query) use ($request) {
                    if ($request->prodi_id == "S1") {
                        $query->where('prodi.jenjang', "S1");
                    } else if ($request->prodi_id == "PASCA") {
                        $query->where(function ($query) {
                            $query->orWhere('prodi.jenjang', "S2");
                            $query->orWhere('prodi.jenjang', "S3");
                        });
                    } else {
                        $query->where('dpl.prodi_id', $request->prodi_id);
                    }
                });
                $query->when($request->dpl_id, function ($query) use ($request) {
                    $query->where('posko_dpl.dpl_id', $request->dpl_id);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('dpl.nama', 'LIKE', "%$search%");
                    $query->orWhere('posko.nama', 'LIKE', "%$search%");
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
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
                        <a href="' . route('admin.penugasan.dpl.detail', ['idPoskoDpl' => $row->id]) . '" class="dropdown-item"
                        >Rekap</a>
                    </div>
                </div>
                ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    public function detail($idPoskoDpl)
    {
        $poskoDpl = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')->where('posko_dpl.id', $idPoskoDpl)
            ->select('posko_dpl.*', 'posko.nama as nama_posko', 'dpl.nama as nama_dpl')->first();
        return view('admin.tugas.dpl.detail', compact('idPoskoDpl', 'poskoDpl'));
    }

    public function detailData($idPoskoDpl)
    {
        $search = request('search.value');

        $data = PenugasanDpl::where('posko_dpl_id', $idPoskoDpl)->select('penugasan_dpl.*')->orderBy('id');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('penugasan_dpl.penugasan', 'LIKE', "%$search%");
                    $query->orWhere('penugasan_dpl.mulai', 'LIKE', "%$search%");
                    $query->orWhere('penugasan_dpl.selesai', 'LIKE', "%$search%");
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
                        data-posko_dpl_id ="' . $row->posko_dpl_id . '"
                        data-mulai="' . $row->mulai . '"
                        data-selesai="' . $row->selesai . '"
                    >Input Tugas</button>
                    <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modal_edit"
                        data-id="' . $row->id . '"
                        data-penugasan="' . $row->penugasan . '"
                        data-posko_dpl_id ="' . $row->posko_dpl_id . '"
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

    public function simpanDetail(Request $request, $idPoskoDpl)
    {
        try {
            $request->validate([
                'penugasan' => 'required',
                'mulai'     => 'nullable',
                'selesai'   => 'nullable',
            ]);

            $penugasanDpl = PenugasanDpl::where('posko_dpl_id', $idPoskoDpl)
                ->where('penugasan', $request->penugasan)
                ->first();
            if ($penugasanDpl) {
                return abort(500, 'Penugasan sudah ada di posko DPL ini');
            }

            $penugasan = $request->penugasan;
            $mulai     = $request->mulai;
            $selesai   = $request->selesai;

            $PenugasanDpl               = new PenugasanDpl();
            $PenugasanDpl->posko_dpl_id = $idPoskoDpl;
            $PenugasanDpl->penugasan    = $penugasan;
            $PenugasanDpl->mulai        = $mulai;
            $PenugasanDpl->selesai      = $selesai;
            $PenugasanDpl->save();
            return [
                'message' => 200,
                'data'    => 'Data Berhasil ditambahkan',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
            ];
        }
    }

    public function editDetail(Request $request, $idPoskoDpl)
    {
        try {
            $request->validate([
                'id'        => 'required',
                'penugasan' => 'required',
                'mulai'     => 'nullable',
                'selesai'   => 'nullable',
            ]);

            $penugasanDpl = PenugasanDpl::where('posko_dpl_id', $idPoskoDpl)
                ->where('id', '!=', $request->id)
                ->where('penugasan', $request->penugasan)
                ->first();
            if ($penugasanDpl) {
                return abort(500, 'Penugasan sudah ada di posko DPL ini');
            }

            $id        = $request->id;
            $penugasan = $request->penugasan;
            $mulai     = $request->mulai;
            $selesai   = $request->selesai;

            $PenugasanDpl            = PenugasanDpl::findOrFail($id);
            $PenugasanDpl->penugasan = $penugasan;
            $PenugasanDpl->mulai     = $mulai;
            $PenugasanDpl->selesai   = $selesai;
            $PenugasanDpl->save();

            return [
                'message' => 200,
                'data'    => 'Data Berhasil diedit',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => 'Data Gagal diedit',
                'req'     => $request->all(),
            ];
        }
    }

    public function deleteDetail(Request $request, $idPoskoDpl)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);

            $penugasanDpl = PenugasanDpl::find($request->id);
            if (count($penugasanDpl->penugasanDplDetail) > 0) {
                return abort(500, 'Semua data penugasan mahasiswa harus dihapus dulu');
            }

            $penugasanDpl->delete();

            return [
                'message' => 200,
                'data'    => 'Penugasan Berhasil ditambahkan',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
            ];
        }
    }

    public function input($idPoskoDpl, $idPenugasanDpl)
    {
        $poskoDpl = PoskoDpl::findOrFail($idPoskoDpl);
        if (Helper::roleAccess($poskoDpl->dpl, 'dpl') == false) {
            return abort(403, 'Akses ditolak karena DPL tidak sesuai dengan posko DPL');
        }
        $PenugasanDpl = PenugasanDpl::findOrFail($idPenugasanDpl);
        if ($poskoDpl->id != $PenugasanDpl->posko_dpl_id) {
            return abort(403, 'Akses ditolak karena penugasan tidak sesuai dengan posko DPL');
        }

        $data2 = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->where('posko_id', $poskoDpl->posko_id)
            ->select('posko_peserta.*', 'peserta.nama as nama_peserta')->get();
        $posko = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->where('posko_dpl.id', $idPoskoDpl)
            ->select('posko_dpl.*', 'posko.nama as nama_posko', 'dpl.nama as nama_dpl')->first();

        return view('admin.tugas.dpl.input', compact('idPenugasanDpl', 'idPoskoDpl', 'poskoDpl', 'data2', 'PenugasanDpl', 'posko'));
    }

    public function inputData($idPoskoDpl, $idPenugasanDpl)
    {
        $poskoDpl = PoskoDpl::find($idPoskoDpl);
        $data2    = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->where('posko_id', $poskoDpl->posko_id)
            ->select('posko_peserta.*', 'peserta.nama as nama_peserta');

        return DataTables::eloquent($data2)
            ->addIndexColumn()
            ->editColumn('nama_peserta', function ($row) {
                return $row->nama_peserta .
                '<input type="hidden" name="posko_peserta_id[]" value="' . $row->id . '">';
            })
            ->addColumn('action', function ($row) {
                $select = '<div class="form-group">
                                 <label for="exampleFormControlFile1">Example file input</label>
                                <input type="file" name="tugas[]" class="form-control-file" id="exampleFormControlFile1">
                         </div>';
                return $select;
            })->rawColumns(['action', 'nama_peserta'])->make(true);
    }

    public function simpanInput(Request $request, $idPoskoDpl, $idPenugasanDpl)
    {
        try {
            $request->validate([
                'file'       => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac|max:' . BulkData::maxSizeUpload,
                'no'         => 'required',
                'keterangan' => 'nullable',
            ]);

            $penugasanDpl = PenugasanDpl::findOrFail($idPenugasanDpl);
            // $start        = Carbon::parse($penugasanDpl->mulai);
            // $end          = Carbon::parse($penugasanDpl->selesai);

            // $now = Carbon::now();
            // if ($now->lessThan($start)) {
            //     return abort(500, 'Penugasan belum dimulai');
            // }
            // if (! $now->between($start, $end)) {
            //     return abort(500, 'Penugasan melewati batas deadline');
            // }

            $keterangan         = $request->keterangan;
            $poskoPesertaId     = $request->no;
            $file               = $request->file('file');
            $penugasanDplDetail = PenugasanDplDetail::where('posko_peserta_id', $poskoPesertaId)->where('penugasan_dpl_id', $idPenugasanDpl)->first();
            if ($penugasanDplDetail) {
                GoogleDrive::deleteWithPath($penugasanDplDetail->path, $this->dir);
            } else {
                $penugasanDplDetail = new PenugasanDplDetail();
            }
            $lokasi    = trim($penugasanDpl->poskoDpl->posko->lokasi);
            $penugasan = $penugasanDpl->penugasan;
            $kategori  = Str::limit(Helper::changeFormatSymbol("PENUGASAN-$lokasi-$penugasan"), 100, '...');
            if ($request->hasFile('file')) {
                $upload  = GoogleDrive::upload($file, $kategori, $this->dir);
                $path    = GoogleDrive::getData($upload['name'], $this->dir);
                $getPath = $path['path'];

                $penugasanDplDetail->penugasan_dpl_id  = $idPenugasanDpl;
                $penugasanDplDetail->posko_peserta_id  = $poskoPesertaId;
                $penugasanDplDetail->file              = $upload['name'];
                $penugasanDplDetail->path              = $getPath;
                $penugasanDplDetail->keterangan        = $keterangan;
                $penugasanDplDetail->waktu_pengumpulan = Carbon::now();
                $penugasanDplDetail->save();
            }

            return [
                'message' => 200,
                'data'    => 'Data berhasil ditambahkan',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
            ];
        }
    }

    public function deleteInput(Request $request, $idPoskoDpl, $idPenugasanDpl)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);

            $data = PenugasanDplDetail::where('posko_peserta_id', $request->id)
                ->where('penugasan_dpl_id', $idPenugasanDpl)
                ->first();

            if ($data) {
                $delete = GoogleDrive::deleteWithPath($data->path, $this->dir);
                if (! $delete['status']) {
                    return abort(500, $delete['message']);
                }

                $data->delete();
            }
            return [
                'message'  => 200,
                'data'     => 'File Tugas Mahasiswa Berhasil dihapus',
                'req'      => $request->all(),
                'response' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => 'File Tugas Mahasiswa Gagal dihapus',
                'req'     => $request->all(),
            ];
        }
    }

    public function downloadExcel()
    {

        $data = Peserta::join('posko_peserta', 'posko_peserta.peserta_id', '=', 'peserta.id')
            ->leftJoin('posko', 'posko.id', '=', 'posko_peserta.posko_id')
            ->leftJoin('penugasan_dpl_detail', 'penugasan_dpl_detail.posko_peserta_id', '=', 'posko_peserta.id')
            ->leftJoin('penugasan_dpl', 'penugasan_dpl.id', '=', 'penugasan_dpl_detail.penugasan_dpl_id')
            ->leftJoin('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
            ->leftJoin('dpl', 'posko_dpl.dpl_id', '=', 'dpl.id')
            ->leftJoin('prodi as prodi_peserta', 'prodi_peserta.id', '=', 'peserta.prodi_id')
            ->leftJoin('prodi as prodi_dpl', 'prodi_dpl.id', '=', 'dpl.prodi_id')
        // ->whereNull('penugasan_dpl_detail.id')
            ->select('peserta.nim', 'peserta.nama as nama_mahasiswa', 'prodi_peserta.alias as prodi_mhs', 'posko.nama as nama_posko', 'posko.lokasi', 'dpl.nama as DPL', 'prodi_dpl.alias as prodi_dpl',
                \DB::raw('CASE WHEN penugasan_dpl_detail.id IS NULL THEN "Belum" ELSE "Sudah" END as status'),
                'penugasan_dpl.penugasan as nama_tugas')
            ->get();

        return Excel::download(new ExcelExport($data), 'Rekap Penugasan DPL.xlsx');
    }

    public function createPenugasan()
    {
        try {
            $poskoDpl = PoskoDpl::whereDoesntHave('penugasanDpl', function ($q) {
                $q->where('penugasan', '=', 'Tugas Akhir');
            })->get();

            $insertPenugasanDpl = [];
            foreach ($poskoDpl as $p) {
                $insertPenugasanDpl[] = [
                    'posko_dpl_id' => $p->id,
                    'penugasan'    => 'Tugas Akhir',
                    'mulai'        => null,
                    'selesai'      => null,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ];
            }

            PenugasanDpl::insert($insertPenugasanDpl);
            return [
                'message' => 200,
                'data'    => 'Data Berhasil ditambahkan',
                'insert'     => $insertPenugasanDpl,
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => 'Data Gagal ditambahkan',
                'req'     => $th->getMessage(),
            ];
        }
    }

    public function createPenugasanVideo()
    {
        try {
            $poskoDpl = PoskoDpl::whereDoesntHave('penugasanDpl', function ($q) {
                $q->where('penugasan', '=', 'Video');
            })->get();

            $insertPenugasanDpl = [];
            foreach ($poskoDpl as $p) {
                $insertPenugasanDpl[] = [
                    'posko_dpl_id' => $p->id,
                    'penugasan'    => 'Video',
                    'mulai'        => null,
                    'selesai'      => null,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ];
            }

            PenugasanDpl::insert($insertPenugasanDpl);
            return [
                'message' => 200,
                'data'    => 'Data Berhasil ditambahkan',
                'insert'     => $insertPenugasanDpl,
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => 'Data Gagal ditambahkan',
                'req'     => $th->getMessage(),
            ];
        }
    }
}
