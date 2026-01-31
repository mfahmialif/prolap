<?php
namespace App\Http\Controllers\Admin\Jurnal;

use App\Http\Controllers\Controller;
use App\Http\Services\BulkData;
use App\Http\Services\GoogleDrive;
use App\Models\DPL;
use App\Models\JurnalPengawas;
use App\Models\JurnalPengawasDetail;
use App\Models\Posko;
use App\Models\PoskoPengawas;
use App\Models\PoskoPeserta;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PengawasController extends Controller
{
    protected $dir = BulkData::dirGdrive['dokumen'];

    public function index()
    {
        $posko = Posko::all();
        $prodi = Prodi::all();
        return view('admin.jurnal.pengawas.index', compact('posko', 'prodi'));
    }

    public function getData(Request $request)
    {
        $search = request('search.value');

        $d = PoskoPengawas::join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
            ->join('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->join('users', 'users.id', '=', 'pengawas.user_id')
            ->select('posko_pengawas.*', 'posko.nama as nama_posko', 'pengawas.nama as username', 'users.jenis_kelamin as jenis', )->orderBy('posko_pengawas.id');
        return DataTables::of($d)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->pengawas_id, function ($query) use ($request) {
                    $poskoPengawasId = PoskoPengawas::where('pengawas_id', $request->pengawas_id)->get()->pluck('posko_id')->toArray();
                    $query->whereIn('posko_pengawas.posko_id', $poskoPengawasId);
                });
                $query->when($request->pengawas_id, function ($query) use ($request) {
                    $query->where('posko_pengawas.pengawas_id', $request->pengawas_id);
                });
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->when($request->pengawas_id, function ($query) use ($request) {
                    $query->where('posko_pengawas.pengawas_id', $request->pengawas_id);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('pengawas.nama', 'LIKE', "%$search%");
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
                        <a href="' . route('admin.jurnal.pengawas.detail', ['idPoskoPengawas' => $row->id]) . '" class="dropdown-item"
                        >Rekap</a>
                    </div>
                </div>
                ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    public function detail($idPoskoPengawas)
    {
        $posko         = Posko::all();
        $pengawas      = DPL::all();
        $poskoPengawas = PoskoPengawas::join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
            ->join('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')->where('posko_pengawas.id', $idPoskoPengawas)
            ->select('posko_pengawas.*', 'posko.nama as nama_posko', 'pengawas.nama as nama_pengawas')->first();
        return view('admin.jurnal.pengawas.detail', compact('posko', 'idPoskoPengawas', 'pengawas', 'poskoPengawas'));
    }

    public function detailData($idPoskoPengawas)
    {
        $search = request('search.value');

        $data = JurnalPengawas::where('posko_pengawas_id', $idPoskoPengawas)->select('jurnal_pengawas.*')->orderBy('id');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('jurnal_pengawas.nama', 'LIKE', "%$search%");
                    $query->orWhere('jurnal_pengawas.tanggal', 'LIKE', "%$search%");
                });
            })
            ->editColumn('nama', function ($row) use ($idPoskoPengawas) {
                return '<a href="' . route('admin.jurnal.pengawas.detail.input', ['idPoskoPengawas' => $idPoskoPengawas, 'idJurnalPengawas' => $row->id]) . '">' . $row->nama . '</a>';
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
                        data-nama="' . $row->nama . '"
                        data-posko_pengawas_id ="' . $row->posko_pengawas_id . '"
                        data-tanggal="' . $row->tanggal . '"
                    >Input Tugas</button>
                    <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modal_edit"
                        data-id="' . $row->id . '"
                        data-nama="' . $row->nama . '"
                        data-posko_pengawas_id ="' . $row->posko_pengawas_id . '"
                        data-tanggal="' . $row->tanggal . '"
                    >Edit Tugas</button>
                    <div class="dropdown-divider"></div>
                    <form action="" onsubmit="deleteData(event)" method="POST">
                    ' . method_field('delete') . csrf_field() . '
                        <input type="hidden" name="id" value="' . $row->id . '">
                        <input type="hidden" name="nama" value="' . $row->nama . '">
                        <button type="submit" class="dropdown-item text-danger">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            ';
                return $btn;
            })->rawColumns(['action', 'nama'])->make(true);
    }

    public function simpanDetail(Request $request, $idPoskoPengawas)
    {
        try {
            $request->validate([
                'nama'    => 'required',
                'tanggal' => 'required',
            ]);

            $nama    = $request->nama;
            $tanggal = $request->tanggal;

            $JurnalPengawas                    = new JurnalPengawas();
            $JurnalPengawas->posko_pengawas_id = $idPoskoPengawas;
            $JurnalPengawas->nama              = $nama;
            $JurnalPengawas->tanggal           = $tanggal;
            $JurnalPengawas->save();

            return [
                'message' => 200,
                'data'    => 'Data Berhasil ditambahkan',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => 'Data Gagal ditambahkan',
                'req'     => $request->all(),
            ];
        }
    }

    public function editDetail(Request $request, $idPoskoPengawas)
    {
        try {
            $request->validate([
                'id'      => 'required',
                'nama'    => 'required',
                'tanggal' => 'required',
            ]);

            $id      = $request->id;
            $nama    = $request->nama;
            $tanggal = $request->tanggal;

            $JurnalPengawas          = JurnalPengawas::findOrFail($id);
            $JurnalPengawas->nama    = $nama;
            $JurnalPengawas->tanggal = $tanggal;
            $JurnalPengawas->save();

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

    public function deleteDetail(Request $request, $idPoskoPengawas)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);

            $jurnalPengawas = JurnalPengawas::find($request->id);
            if (count($jurnalPengawas->jurnalPengawasDetail) > 0) {
                return abort(500, 'Semua data jurnal mahasiswa harus dihapus dulu');
            }

            $jurnalPengawas->delete();

            return [
                'message' => 200,
                'data'    => 'Penugasan Berhasil dihapus',
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

    public function input($idPoskoPengawas, $idJurnalPengawas)
    {
        $poskoPengawas = PoskoPengawas::find($idPoskoPengawas);
        $data2         = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->where('posko_id', $poskoPengawas->posko_id)
            ->select('posko_peserta.*', 'peserta.nama as nama_peserta')->get();
        $jurnalPengawas = JurnalPengawas::find($idJurnalPengawas);
        $posko          = PoskoPengawas::join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
            ->join('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->where('posko_pengawas.id', $idPoskoPengawas)
            ->select('posko_pengawas.*', 'posko.nama as nama_posko', 'pengawas.nama as nama_pengawas')->first();

        return view('admin.jurnal.pengawas.input', compact('idJurnalPengawas', 'idPoskoPengawas', 'poskoPengawas', 'data2', 'jurnalPengawas', 'posko'));
    }

    public function inputData($idPoskoPengawas, $idJurnalPengawas)
    {
        $poskoPengawas = PoskoPengawas::find($idPoskoPengawas);
        $data2         = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->where('posko_id', $poskoPengawas->posko_id)
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

    public function simpanInput(Request $request, $idPoskoPengawas, $idJurnalPengawas)
    {
        try {
            $request->validate([
                'file'       => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac|max:' . BulkData::maxSizeUpload,
                'no'         => 'required',
                'keterangan' => 'nullable',
            ]);

            $keterangan     = $request->keterangan;
            $poskoPesertaId = $request->no;
            $file           = $request->file('file');

            $jurnalPengawasDetail = JurnalPengawasDetail::where('posko_peserta_id', $poskoPesertaId)->where('jurnal_pengawas_id', $idJurnalPengawas)->first();

            $upload  = GoogleDrive::upload($file, 'JURNAL PENGAWAS', $this->dir);
            $path    = GoogleDrive::getData($upload['name'], $this->dir);
            $getPath = $path['path'];

            if (! $jurnalPengawasDetail) {
                $jurnalPengawasDetail = new JurnalPengawasDetail();
            }

            if (@$jurnalPengawasDetail->file) {
                GoogleDrive::deleteWithPath($jurnalPengawasDetail->path, $this->dir);
            }

            $jurnalPengawasDetail->jurnal_pengawas_id = $idJurnalPengawas;
            $jurnalPengawasDetail->posko_peserta_id   = $poskoPesertaId;
            $jurnalPengawasDetail->file               = $upload['name'];
            $jurnalPengawasDetail->path               = $getPath;
            $jurnalPengawasDetail->keterangan         = $keterangan;
            $jurnalPengawasDetail->save();

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

    public function deleteInput(Request $request, $idPoskoPengawas, $idJurnalPengawas)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);

            $data = JurnalPengawasDetail::where('posko_peserta_id', $request->id)
                ->where('jurnal_pengawas_id', $idJurnalPengawas)
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
                'data'     => 'File Jurnal Kegiatan Berhasil dihapus',
                'req'      => $request->all(),
                'response' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => 'File Jurnal Kegiatan Gagal dihapus',
                'req'     => $request->all(),
            ];
        }
    }

}
