<?php
namespace App\Http\Controllers\Admin\Absensi;

use App\Exports\ExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Services\NilaiPeserta;
use App\Models\AbsensiPsDpl;
use App\Models\AbsensiPsDplDetail;
use App\Models\DPL;
use App\Models\Peserta;
use App\Models\Posko;
use App\Models\PoskoDpl;
use App\Models\PoskoPeserta;
use App\Models\Prodi;
use App\Models\Tahun;
use Carbon\Carbon;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DplController extends Controller
{
    public function index()
    {
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();
        // $data  = Peserta::join('posko_peserta', 'posko_peserta.peserta_id', '=', 'peserta.id')
        //     ->leftJoin('posko', 'posko.id', '=', 'posko_peserta.posko_id')
        //     ->leftJoin('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
        //     ->select('posko_peserta.id as posko_peserta_id', 'posko_dpl.id as posko_dpl_id')
        //     ->get();

        // // dd($data);
        // foreach ($data as $key => $value) {
        //     // store absensi nilai
        //     $poskoDpl   = PoskoDpl::find($value->posko_dpl_id);
        //     $storeNilai = NilaiPeserta::storeAbsensiDpl($poskoDpl, $value->posko_peserta_id);
        //     if ($storeNilai['status'] == false) {
        //         return abort(500, $storeNilai['error']);
        //     }
        // }
        return view('admin.absensi.dpl.indexDpl', compact('prodi', 'tahun'));
    }
    public function data(Request $request)
    {
        $search = request('search.value');

        $data = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->join('prodi', 'prodi.id', '=', 'dpl.prodi_id')
            ->join('users', 'users.id', '=', 'dpl.user_id')
            ->join('tahun', 'tahun.id', '=', 'posko.tahun_id')
            ->select('posko_dpl.*', 'posko.nama as nama_posko', 'dpl.nama as username', 'users.jenis_kelamin as jenis',
                'prodi.nama', 'prodi.jenjang', 'tahun.nama as tahun_nama')
            ->orderBy('posko_dpl.id');
        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
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
                        <button type="button" class="dropdown-item BtnRekap"
                            data-id="' . $row->id . '"
                        >Rekap</button>
                        <button type="button" class="dropdown-item BtnInput" id="BtnInput"
                            data-id="' . $row->id . '"
                            data-posko_id="' . $row->posko_id . '"
                            data-dpl_id="' . $row->dpl_id . '"
                        >Input Absensi</button>
                    </div>
                </div>
                ';

                return $btn;
            })->rawColumns(['action'])->make(true);
    }
    public function store(Request $request)
    {
        $rule = [
            'posko' => 'required',
            'dpl'   => 'required',
        ];
        $message = [
            'posko.required' => 'Posko harus dipilih',
            'dpl.required'   => 'Dpl harus dipilih',
        ];
        $errors = Validator::make($request->all(), $rule, $message);
        if ($errors->fails()) {
            return response()->json(['salah' => $errors->errors()->all()]);
        }

        try {
            $data = [
                'posko_id' => $request->posko,
                'dpl_id'   => $request->dpl,
            ];
            PoskoDpl::create($data);

            return [
                "message" => 200,
                "data"    => 'Berhasil menambahkan DPL Absens',
                "req"     => $request->all(),
            ];
        } catch (\Throwable $th) {
            return [
                "message" => 500,
                "data"    => $th->getMessage(),
                "req"     => $request->all(),
            ];
        }
    }
    public function detailData($idPoskoDpl)
    {
        $search = request('search.value');
        $data   = AbsensiPsDpl::join('posko_dpl', 'posko_dpl.id', '=', 'absensi_ps_dpl.posko_dpl_id')
            ->join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->select('absensi_ps_dpl.*', 'posko.id as posko_id', 'dpl.id as dpl_id', 'posko.nama as nama_posko', 'dpl.nama as nama_dpl')->where('posko_dpl_id', $idPoskoDpl)->orderBy('id');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('absensi_ps_dpl.nama', 'LIKE', "%$search%");
                    $query->orWhere('dpl.nama', 'LIKE', "%$search%");
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
                        data-nama="' . $row->nama . '"
                        data-posko_dpl_id ="' . $row->posko_dpl_id . '"
                        data-posko_id="' . $row->posko_id . '"
                        data-dpl_id="' . $row->dpl_id . '"
                    >Edit</button>
                     <div class="dropdown-divider"></div>
                    <button type="button" class="dropdown-item text-danger BtnDel"
                        data-id="' . $row->id . '"
                        data-nama="' . $row->nama . '"
                        data-posko_dpl_id="' . $row->posko_dpl_id . '"
                        data-posko_id="' . $row->posko_id . '"
                        data-dpl_id="' . $row->dpl_id . '"
                    >Delete</button>
                </div>
            </div>
            ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }
    public function detailDataPeserta($idPoskoDpl)
    {
        $search   = request('search.value');
        $poskoDpl = PoskoDpl::find($idPoskoDpl);
        $absensi  = [];
        foreach (\Helper::getEnumValues('absensi_ps_dpl_detail', 'status') as $key => $value) {
            $absensi["absensi_$value"] = \DB::table('absensi_ps_dpl_detail')
                ->join('absensi_ps_dpl', 'absensi_ps_dpl.id', '=', 'absensi_ps_dpl_detail.absensi_ps_dpl_id')
                ->selectRaw('count(*)')
                ->whereColumn('absensi_ps_dpl_detail.posko_peserta_id', 'posko_peserta.id')
                ->where('absensi_ps_dpl.posko_dpl_id', $idPoskoDpl)
                ->where('absensi_ps_dpl_detail.status', $value);
        }
        $data = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->select('posko_peserta.*', 'peserta.nama as peserta_nama', 'peserta.nim as peserta_nim')
            ->addSelect($absensi);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('peserta_nama', function ($row) {
                $content = "<div>$row->peserta_nama</div>";
                $content .= "<div class='text-bold'>$row->peserta_nim</div>";
                $content .= "<div class='text-bold'>" . $row->peserta->prodi->alias . "</div>";
                return $content;
            })
            ->editColumn('absensi_Belum Absen', function ($row) use ($idPoskoDpl) {
                $absensi       = AbsensiPsDpl::where('posko_dpl_id', $idPoskoDpl)->get();
                $jumlahAbsensi = $absensi->count();
                if ($jumlahAbsensi > 0) {
                    $absensiId           = $absensi->pluck('id')->toArray();
                    $jumlahAbsensiDetail = AbsensiPsDplDetail::whereIn('absensi_ps_dpl_id', $absensiId)
                        ->where('posko_peserta_id', $row->id)->where('status', '!=', 'Belum Absen')->count();
                    return $jumlahAbsensi - $jumlahAbsensiDetail + $row->absensi_Belum_Absen;
                }
                return 0;
            })
            ->filter(function ($query) use ($search, $poskoDpl) {
                $query->where('posko_peserta.posko_id', $poskoDpl->posko_id);
                $query->where(function ($query) use ($search) {
                    $query->orWhere('peserta.nama', 'LIKE', "%$search%");
                    $query->orWhere('peserta.nim', 'LIKE', "%$search%");
                });
            })->rawColumns(['action', 'peserta_nama'])->make(true);
    }
    public function detail($idPoskoDpl)
    {
        $posko    = Posko::all();
        $dataDpl  = DPL::all();
        $poskoDpl = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')->where('posko_dpl.id', $idPoskoDpl)
            ->select('posko_dpl.*', 'posko.*', 'dpl.nama as nama_dpl')->first();
        $dpl           = Dpl::find($poskoDpl->dpl_id);
        $absensiStatus = \Helper::getEnumValues('absensi_ps_dpl_detail', 'status');
        return View('admin.absensi.dpl.detail', compact('idPoskoDpl', 'posko', 'dataDpl', 'poskoDpl', 'dpl', 'absensiStatus'));
    }
    public function dataEdit($idPoskoDpl, $idAbsensiPsDpl)
    {
        $absensiPsDpl  = AbsensiPsDpl::find($idAbsensiPsDpl);
        $absensiDetail = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->leftJoin('absensi_ps_dpl_detail', function ($join) use ($absensiPsDpl) {
                $join->on('absensi_ps_dpl_detail.posko_peserta_id', '=', 'posko_peserta.id');
                $join->where('absensi_ps_dpl_detail.absensi_ps_dpl_id', $absensiPsDpl->id);
            })
            ->where('posko_id', $absensiPsDpl->poskoDpl->posko_id)
            ->select('posko_peserta.*', 'peserta.nama as nama_peserta', 'peserta.nim as nim_peserta', 'absensi_ps_dpl_detail.status as absensi_status');

        return DataTables::eloquent($absensiDetail)
            ->addIndexColumn()
            ->editColumn('nama_peserta', function ($row) {
                $content = "<div>$row->nama_peserta</div>";
                $content .= "<div class='text-bold'>$row->nim_peserta</div>";
                $content .= "<div class='text-bold'>" . $row->peserta->prodi->alias . "</div>";
                $content .= '<input type="hidden" name="poskoPesertaId[]" value="' . $row->id . '">';
                return $content;
            })
            ->addColumn('action', function ($row) {
                $select = '<div class="form-group">
                            <select class="form-control mintaku" name="minta[]">
                            <option value="">-- PILIH --</option>';
                foreach (\Helper::getEnumValues('absensi_ps_dpl_detail', 'status') as $key => $value) {
                    $select .= '<option value="' . $value . '" ' . ($value == $row->absensi_status ? 'selected' : '') . '>' . strtoupper($value) . '</option>';
                }
                $select .= '
                        </select>
                    </div>';
                return $select;
            })->rawColumns(['action', 'nama_peserta'])->make(true);
    }
    public function formEdit($idPoskoDpl, $idAbsensiPsDpl)
    {
        $data = AbsensiPsDpl::Join('posko_dpl', 'posko_dpl.id', 'absensi_ps_dpl.posko_dpl_id')
            ->join('posko', 'posko.id', '=', 'posko_dpl.posko_id')->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')->where('absensi_ps_dpl.id', $idAbsensiPsDpl)
            ->select('absensi_ps_dpl.*', 'posko.nama as nama_posko', 'dpl.nama as nama_dpl')
            ->first();

        return View('admin.absensi.dpl.form', compact('idPoskoDpl', 'idAbsensiPsDpl', 'data'));
    }
    public function edit(Request $request, $idPoskoDpl, $idAbsensiPsDpl)
    {
        DB::beginTransaction();
        try {
            $pertemuan      = $request->pertemuan;
            $minta          = $request->minta;
            $poskoPesertaId = $request->poskoPesertaId;

            $update               = AbsensiPsDpl::find($idAbsensiPsDpl);
            $update->posko_dpl_id = $idPoskoDpl;
            $update->nama         = $pertemuan;
            $update->save();

            $id = $update->id;

            AbsensiPsDplDetail::where('absensi_ps_dpl_id', $id)->delete();

            for ($i = 0; $i < count($poskoPesertaId); $i++) {
                $absensiPsDplDetail                    = new AbsensiPsDplDetail();
                $absensiPsDplDetail->absensi_ps_dpl_id = $id;
                $absensiPsDplDetail->posko_peserta_id  = $poskoPesertaId[$i];
                $absensiPsDplDetail->status            = $minta[$i] ?? 'Belum Absen';
                $absensiPsDplDetail->save();

                // store absensi nilai
                $poskoDpl   = PoskoDpl::find($idPoskoDpl);
                $storeNilai = NilaiPeserta::storeAbsensiDpl($poskoDpl, $poskoPesertaId[$i]);
                if ($storeNilai['status'] == false) {
                    return abort(500, $storeNilai['error']);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 200,
                'data'    => 'Absensi berhasil diedit',
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
            ]);
        }
    }
    public function input($idPoskoDpl)
    {
        $data = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->select('posko_dpl.*', 'dpl.id as idd', 'posko.nama as nama_posko', 'dpl.nama as nama_dpl')->where('posko_dpl.id', $idPoskoDpl)
            ->first();
        // $posko = Posko::find($poskoId);

        return View('admin.absensi.dpl.input', compact('data', 'idPoskoDpl'));
    }
    public function inputDetail($idPoskoDpl)
    {
        $posko = PoskoDpl::find($idPoskoDpl);
        $data2 = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->where('posko_id', $posko->posko_id)
            ->select('posko_peserta.*', 'peserta.nama as nama_peserta', 'peserta.nim as nim_peserta');

        return DataTables::eloquent($data2)
            ->addIndexColumn()
            ->editColumn('nama_peserta', function ($row) {
                $content = "<div>$row->nama_peserta</div>";
                $content .= "<div class='text-bold'>$row->nim_peserta</div>";
                $content .= "<div class='text-bold'>" . $row->peserta->prodi->alias . "</div>";
                $content .= '<input type="hidden" name="poskoPesertaId[]" value="' . $row->id . '">';
                return $content;
            })
            ->addColumn('action', function ($row) {
                $select = '<div class="form-group">
                            <select class="form-control minta" name="minta[]">
                            <option value="">-- PILIH --</option>';
                foreach (\Helper::getEnumValues('absensi_ps_dpl_detail', 'status') as $key => $value) {
                    $select .= '<option value="' . $value . '">' . strtoupper($value) . '</option>';
                }
                $select .= '
                        </select>
                    </div>';
                return $select;
            })->rawColumns(['action', 'nama_peserta'])->make(true);
    }
    public function simpanDetail(Request $request, $idPoskoDpl)
    {
        DB::beginTransaction();
        try {
            $pertemuan      = $request->pertemuan;
            $minta          = $request->minta;
            $poskoPesertaId = $request->poskoPesertaId;

            $absensiPsDpl               = new AbsensiPsDpl();
            $absensiPsDpl->posko_dpl_id = $idPoskoDpl;
            $absensiPsDpl->nama         = $pertemuan;
            $absensiPsDpl->save();

            $absensiPsDplId = $absensiPsDpl->id;
            for ($i = 0; $i < count($poskoPesertaId); $i++) {
                $absensiPsDplDetail                    = new AbsensiPsDplDetail;
                $absensiPsDplDetail->absensi_ps_dpl_id = $absensiPsDplId;
                $absensiPsDplDetail->posko_peserta_id  = $poskoPesertaId[$i];
                $absensiPsDplDetail->status            = $minta[$i] ?? 'Belum Absen';
                $absensiPsDplDetail->waktu_absen       = Carbon::now();
                $absensiPsDplDetail->save();

                // store absensi nilai
                $poskoDpl   = PoskoDpl::find($idPoskoDpl);
                $storeNilai = NilaiPeserta::storeAbsensiDpl($poskoDpl, $poskoPesertaId[$i]);
                if ($storeNilai['status'] == false) {
                    return abort(500, $storeNilai['error']);
                }
            }
            DB::commit();
            return response()->json(
                [
                    'message' => 200,
                    'data'    => 'Absensi berhasil disimpan',
                    'req'     => $request->all(),
                ]
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
            ];
        }
    }
    public function del($idPoskoDpl, $idAbsensiPsDpl)
    {
        DB::beginTransaction();
        try {
            $absensiPsDplDetail = AbsensiPsDplDetail::where('absensi_ps_dpl_id', $idAbsensiPsDpl)->get();

            foreach ($absensiPsDplDetail as $key => $value) {
                $value->delete();
            }

            $delApd = AbsensiPsDpl::find($idAbsensiPsDpl);
            $delApd->delete();

            // store absensi nilai
            foreach ($absensiPsDplDetail as $key => $value) {
                $poskoDpl       = PoskoDpl::find($idPoskoDpl);
                $poskoPesertaId = $value->posko_peserta_id;

                $value->delete();
                $storeNilai = NilaiPeserta::storeAbsensiDpl($poskoDpl, $poskoPesertaId);
                if ($storeNilai['status'] == false) {
                    return abort(500, $storeNilai['error']);
                }
            }
            DB::commit();
            return [
                'message' => 200,
                'data'    => 'Data Berhasil dihapus',
                'status'  => true,
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'message' => 500,
                'data'    => 'Gagal Menghapus Data',
                'status'  => false,
                'error'   => $th->getMessage(),
            ];
        }
    }

    public function downloadExcel()
    {

        $data = Peserta::join('posko_peserta', 'posko_peserta.peserta_id', '=', 'peserta.id')
            ->leftJoin('posko', 'posko.id', '=', 'posko_peserta.posko_id')
            ->leftJoin('penilaian_dpl', 'penilaian_dpl.posko_peserta_id', '=', 'posko_peserta.id')
            ->leftJoin('penilaian_dpl_detail', function ($join) {
                $join->on('penilaian_dpl_detail.penilaian_dpl_id', '=', 'penilaian_dpl.id')
                    ->where('penilaian_dpl_detail.komponen_nilai_id', '=', 1);
            })
            ->leftJoin('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
            ->leftJoin('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->leftJoin('prodi as prodi_peserta', 'prodi_peserta.id', '=', 'peserta.prodi_id')
            ->select(
                'peserta.nim',
                'peserta.nama as nama_mahasiswa',
                'prodi_peserta.alias as prodi_mhs',
                'posko.nama as nama_posko',
                'posko.lokasi',
                'dpl.nama as PENGAWAS',
                'penilaian_dpl_detail.nilai',
                \DB::raw('(SELECT COUNT(*) FROM absensi_ps_dpl WHERE absensi_ps_dpl.posko_dpl_id = posko_dpl.id) as jumlah_absensi')
            )
            ->get();

        return Excel::download(new ExcelExport($data), 'Rekap Absensi DPL.xlsx');
    }
}
