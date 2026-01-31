<?php
namespace App\Http\Controllers\admin\absensi;

use App\Exports\ExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Services\NilaiPeserta;
use App\Models\AbsensiPsPengawas;
use App\Models\AbsensiPsPengawasDetail;
use App\Models\Pengawas;
use App\Models\Peserta;
use App\Models\Posko;
use App\Models\PoskoPengawas;
use App\Models\PoskoPeserta;
use App\Models\Tahun;
use Carbon\Carbon;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PengawasController extends Controller
{
    public function index()
    {
        $posko = Posko::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();

        // $data = Peserta::join('posko_peserta', 'posko_peserta.peserta_id', '=', 'peserta.id')
        //     ->leftJoin('posko', 'posko.id', '=', 'posko_peserta.posko_id')
        //     ->leftJoin('posko_pengawas', 'posko_pengawas.posko_id', '=', 'posko.id')
        //     ->select('posko_peserta.id as posko_peserta_id', 'posko_pengawas.id as posko_pengawas_id')
        //     ->get();

        // foreach ($data as $key => $value) {
        //     // store absensi nilai
        //     $poskoPengawas = PoskoPengawas::find($value->posko_pengawas_id);
        //     $storeNilai    = NilaiPeserta::storeAbsensiPengawas($poskoPengawas, $value->posko_peserta_id);
        //     if ($storeNilai['status'] == false) {
        //         return abort(500, $storeNilai['error']);
        //     }
        // }
        return view('admin.absensi.pengawas.index', compact('posko', 'tahun'));
    }
    public function data(Request $request)
    {
        $search = request('search.value');
        $data   = PoskoPengawas::join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
            ->join('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->join('users', 'users.id', '=', 'pengawas.user_id')
            ->join('tahun', 'tahun.id', '=', 'posko.tahun_id')
            ->select('posko_pengawas.*', 'posko.nama as nama_posko',
                'pengawas.nama as nama_pengawas', 'tahun.nama as tahun_nama')->orderBy('posko_pengawas.id');
        return DataTables::eloquent($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->when($request->tahun_id != "*", function ($query) use ($request) {
                    $query->where('posko.tahun_id', $request->tahun_id);
                });
                $query->when($request->pengawas_id, function ($query) use ($request) {
                    $query->where('posko_pengawas.pengawas_id', $request->pengawas_id);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('pengawas.nama', 'LIKE', "%$search%");
                    $query->orWhere('posko.nama', 'LIKE', "%$search%");
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
                    <button type="button" class="dropdown-item BtnInput"
                        data-id="' . $row->id . '"
                        data-posko_id="' . $row->posko_id . '"
                        data-pengawas_id="' . $row->pengawas_id . '"
                    >Input Absensi</button>
                </div>
            </div>
            ';
                // <input type="hidden" name="nama" value="' . $row->jenis . '">
                return $btn;
            })->rawColumns(['action'])->make(true);
    }
    public function detail($idPoskoPengawas)
    {
        $data = PoskoPengawas::join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
            ->join('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->where('posko_pengawas.id', $idPoskoPengawas)
            ->select('posko_pengawas.*', 'posko.nama as nama_posko', 'pengawas.nama as nama_pengawas')
            ->first();
        $posko         = Posko::all();
        $pengawas      = Pengawas::all();
        $absensiStatus = \Helper::getEnumValues('absensi_ps_pengawas_detail', 'status');
        return view('admin.absensi.pengawas.detail', compact('idPoskoPengawas', 'posko', 'data', 'pengawas', 'absensiStatus'));
    }
    public function dataDetail($idPoskoPengawas)
    {
        $search = request('search.value');
        $data   = AbsensiPsPengawas::join('posko_pengawas', 'posko_pengawas.id', '=', 'absensi_ps_pengawas.posko_pengawas_id')
            ->join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
            ->join('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->where('absensi_ps_pengawas.posko_pengawas_id', $idPoskoPengawas)
            ->select('absensi_ps_pengawas.*', 'posko.nama as nama_posko', 'pengawas.nama as nama_pengawas');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('absensi_ps_pengawas.nama', 'LIKE', "%$search%");
                    $query->orWhere('posko.nama', 'LIKE', "%$search%");
                    $query->orWhere('pengawas.nama', 'LIKE', "%$search%");
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
                        data-toggle="modal"
                        data-id="' . $row->id . '"
                        data-nama="' . $row->nama . '"
                        data-posko_pengawas_id="' . $row->posko_pengawas_id . '"
                    >Edit</button>
                    <div class="dropdown-divider"></div>
                    <button type="button" class="dropdown-item text-danger BtnDel"
                        data-id="' . $row->id . '"
                        data-nama="' . $row->nama . '"
                        data-posko_pengawas_id="' . $row->posko_pengawas_id . '"
                    >Delete</button>
                </div>
            </div>
            ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }
    public function detailDataPeserta($idPoskoPengawas)
    {
        $search        = request('search.value');
        $poskoPengawas = PoskoPengawas::find($idPoskoPengawas);
        $absensi       = [];
        foreach (\Helper::getEnumValues('absensi_ps_pengawas_detail', 'status') as $key => $value) {
            $absensi["absensi_$value"] = \DB::table('absensi_ps_pengawas_detail')
                ->join('absensi_ps_pengawas', 'absensi_ps_pengawas.id', '=', 'absensi_ps_pengawas_detail.absensi_ps_pengawas_id')
                ->selectRaw('count(*)')
                ->whereColumn('absensi_ps_pengawas_detail.posko_peserta_id', 'posko_peserta.id')
                ->where('absensi_ps_pengawas.posko_pengawas_id', $idPoskoPengawas)
                ->where('absensi_ps_pengawas_detail.status', $value);
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
            ->editColumn('absensi_Belum Absen', function ($row) use ($idPoskoPengawas) {
                $absensi       = AbsensiPsPengawas::where('posko_pengawas_id', $idPoskoPengawas)->get();
                $jumlahAbsensi = $absensi->count();
                if ($jumlahAbsensi > 0) {
                    $absensiId           = $absensi->pluck('id')->toArray();
                    $jumlahAbsensiDetail = AbsensiPsPengawasDetail::whereIn('absensi_ps_pengawas_id', $absensiId)
                        ->where('posko_peserta_id', $row->id)->where('status', '!=', 'Belum Absen')->count();
                    return $jumlahAbsensi - $jumlahAbsensiDetail + $row->absensi_Belum_Absen;
                }
                return 0;
            })
            ->filter(function ($query) use ($search, $poskoPengawas) {
                $query->where('posko_peserta.posko_id', $poskoPengawas->posko_id);
                $query->where(function ($query) use ($search) {
                    $query->orWhere('peserta.nama', 'LIKE', "%$search%");
                    $query->orWhere('peserta.nim', 'LIKE', "%$search%");
                });
            })->rawColumns(['action', 'peserta_nama'])->make(true);
    }
    public function input($idPoskoPengawas)
    {
        $data = PoskoPengawas::join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
            ->join('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->where('posko_pengawas.id', $idPoskoPengawas)
            ->select('posko_pengawas.*', 'pengawas.nama as nama_pengawas', 'pengawas.id as idp', 'posko.nama as nama_posko')
            ->first();
        // dd($data);

        return view('admin.absensi.pengawas.input', compact('data', 'idPoskoPengawas'));
    }
    public function inputDetail($idPoskoPengawas)
    {
        $poskoPengawas = PoskoPengawas::find($idPoskoPengawas);

        $data = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->where('posko_id', $poskoPengawas->posko_id)
            ->select('posko_peserta.*', 'peserta.nama as nama_peserta', 'peserta.nim as nim_peserta');

        return DataTables::eloquent($data)
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
                foreach (\Helper::getEnumValues('absensi_ps_pengawas_detail', 'status') as $key => $value) {
                    $select .= '<option value="' . $value . '">' . strtoupper($value) . '</option>';
                }
                $select .= '
            </select>
        </div>';
                return $select;
            })->rawColumns(['action', 'nama_peserta'])->make(true);
    }
    public function simpanDetail(Request $request, $idPoskoPengawas)
    {

        DB::beginTransaction();
        try {
            $pertemuan      = $request->pertemuan;
            $minta          = $request->minta;
            $poskoPesertaId = $request->poskoPesertaId;

            $absensiPsDpl                    = new AbsensiPsPengawas();
            $absensiPsDpl->posko_pengawas_id = $idPoskoPengawas;
            $absensiPsDpl->nama              = $pertemuan;
            $absensiPsDpl->save();

            $absensiPsPengawasId = $absensiPsDpl->id;
            for ($i = 0; $i < count($poskoPesertaId); $i++) {
                $absensiPsDplDetail                         = new AbsensiPsPengawasDetail();
                $absensiPsDplDetail->absensi_ps_pengawas_id = $absensiPsPengawasId;
                $absensiPsDplDetail->posko_peserta_id       = $poskoPesertaId[$i];
                $absensiPsDplDetail->status                 = $minta[$i] ?? 'Belum Absen';
                $absensiPsDplDetail->waktu_absen            = Carbon::now();
                $absensiPsDplDetail->save();

                // store absensi nilai
                $poskoPengawas = PoskoPengawas::find($idPoskoPengawas);
                $storeNilai    = NilaiPeserta::storeAbsensiPengawas($poskoPengawas, $poskoPesertaId[$i]);
                if ($storeNilai['status'] == false) {
                    return abort(500, $storeNilai['error']);
                }

            }
            DB::commit();
            return response()->json(
                [
                    'message' => 200,
                    'data'    => 'Absensi berhasil disimpann',
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
    public function dataEdit($idPoskoPengawas, $idAbsensiPsPengawas)
    {
        $apd           = AbsensiPsPengawas::find($idAbsensiPsPengawas);
        $absensiDetail = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->leftJoin('absensi_ps_pengawas_detail', function ($join) use ($apd) {
                $join->on('absensi_ps_pengawas_detail.posko_peserta_id', '=', 'posko_peserta.id');
                $join->where('absensi_ps_pengawas_detail.absensi_ps_pengawas_id', $apd->id);
            })
            ->where('posko_id', $apd->poskoPengawas->posko_id)
            ->select('posko_peserta.*', 'peserta.nama as nama_peserta', 'peserta.nim as nim_peserta', 'absensi_ps_pengawas_detail.status as absensi_status');

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
                foreach (\Helper::getEnumValues('absensi_ps_pengawas_detail', 'status') as $key => $value) {
                    $select .= '<option value="' . $value . '" ' . ($value == $row->absensi_status ? 'selected' : '') . '>' . strtoupper($value) . '</option>';
                }
                $select .= '
                    </select>
                </div>';
                return $select;
            })->rawColumns(['action', 'nama_peserta'])->make(true);
    }
    public function formEdit($idPoskoPengawas, $idAbsensiPsPengawas)
    {

        $data = AbsensiPsPengawas::Join('posko_pengawas', 'posko_pengawas.id', 'absensi_ps_pengawas.posko_pengawas_id')
            ->join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
            ->join('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->where('absensi_ps_pengawas.id', $idAbsensiPsPengawas)
            ->select('absensi_ps_pengawas.*', 'pengawas.nama as nama_pengawas', 'posko.nama as nama_posko')
            ->first();

        return View('admin.absensi.pengawas.form', compact('idPoskoPengawas', 'idAbsensiPsPengawas', 'data'));
    }
    public function edit(Request $request, $idPoskoPengawas, $idAbsensiPsPengawas)
    {
        DB::beginTransaction();
        try {
            $pertemuan      = $request->pertemuan;
            $minta          = $request->minta;
            $poskoPesertaId = $request->poskoPesertaId;

            $update                    = AbsensiPsPengawas::find($idAbsensiPsPengawas);
            $update->posko_pengawas_id = $idPoskoPengawas;
            $update->nama              = $pertemuan;
            $update->save();

            $id = $update->id;

            AbsensiPsPengawasDetail::where('absensi_ps_pengawas_id', $id)->delete();

            for ($i = 0; $i < count($poskoPesertaId); $i++) {
                $absensiPsPengawasDetail                         = new AbsensiPsPengawasDetail();
                $absensiPsPengawasDetail->absensi_ps_pengawas_id = $id;
                $absensiPsPengawasDetail->posko_peserta_id       = $poskoPesertaId[$i];
                $absensiPsPengawasDetail->status                 = $minta[$i] ?? 'Belum Absen';
                $absensiPsPengawasDetail->waktu_absen            = Carbon::now();
                $absensiPsPengawasDetail->save();

                // store absensi nilai
                $poskoPengawas = PoskoPengawas::find($idPoskoPengawas);
                $storeNilai    = NilaiPeserta::storeAbsensiPengawas($poskoPengawas, $poskoPesertaId[$i]);
                if ($storeNilai['status'] == false) {
                    return abort(500, $storeNilai['error']);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 200,
                'data'    => 'absensi berhasil diedit',
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
                'error'   => $th->getMessage(),
            ]);
        }
    }
    public function del($idPoskoPengawas, $idAbsensiPsPengawas)
    {
        DB::beginTransaction();
        try {
            $absensiPsPengawasDetail = AbsensiPsPengawasDetail::where('absensi_ps_pengawas_id', $idAbsensiPsPengawas)->get();

            foreach ($absensiPsPengawasDetail as $key => $value) {

                $value->delete();
            }

            $delApd = AbsensiPsPengawas::find($idAbsensiPsPengawas);
            $delApd->delete();

            // store absensi nilai
            foreach ($absensiPsPengawasDetail as $key => $value) {
                $poskoPengawas  = PoskoPengawas::find($idPoskoPengawas);
                $poskoPesertaId = $value->posko_peserta_id;
                $storeNilai     = NilaiPeserta::storeAbsensiPengawas($poskoPengawas, $poskoPesertaId);
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
            ->leftJoin('penilaian_pengawas', 'penilaian_pengawas.posko_peserta_id', '=', 'posko_peserta.id')
            ->leftJoin('posko_pengawas', 'posko_pengawas.posko_id', '=', 'posko.id')
            ->leftJoin('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->leftJoin('prodi as prodi_peserta', 'prodi_peserta.id', '=', 'peserta.prodi_id')
            ->select(
                'peserta.nim',
                'peserta.nama as nama_mahasiswa',
                'prodi_peserta.alias as prodi_mhs',
                'posko.nama as nama_posko',
                'posko.lokasi',
                'pengawas.nama as PENGAWAS',
                'penilaian_pengawas.nilai',
                \DB::raw('(SELECT COUNT(*) FROM absensi_ps_pengawas WHERE absensi_ps_pengawas.posko_pengawas_id = posko_pengawas.id) as jumlah_absensi')

            )
            ->get();

        return Excel::download(new ExcelExport($data), 'Rekap Absensi Pengawas.xlsx');
    }
}
