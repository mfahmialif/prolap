<?php
namespace App\Http\Controllers\Admin;

use App\Exports\ExcelExport;
use App\Http\Controllers\Controller;
use App\Models\AbsensiPsDpl;
use App\Models\AbsensiPsDplDetail;
use App\Models\DPL;
use App\Models\PenugasanDpl;
use App\Models\PenugasanDplDetail;
use App\Models\Posko;
use App\Models\PoskoPeserta;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class DplController extends Controller
{
    public function index()
    {
        $prodi = Prodi::orderBy('jenjang', 'asc')->get();
        return view('admin.dpl.index', compact('prodi'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data   = DPL::join('users', 'users.id', '=', 'dpl.user_id')
            ->join('prodi', 'prodi.id', '=', 'dpl.prodi_id')
            ->select(
                'dpl.*',
                'users.jenis_kelamin as users_jenis_kelamin',
                'users.email as users_email',
                'prodi.nama as prodi_nama',
                'users.username as users_username',
            );
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
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
                    $query->orWhere('dpl.hp', 'LIKE', "%$search%");
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
                    $query->orWhere('prodi.nama', 'LIKE', "%$search%");
                    $query->orWhere('prodi.alias', 'LIKE', "%$search%");
                });
            })
            ->editColumn('status', function ($row) {
                if ($row->status == "Aktif") {
                    return '<span class="badge badge-success">' . $row->status . '<span></span>';
                } else {
                    return '<span class="badge badge-danger">' . $row->status . '<span></span>';
                }
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
                        <a href="' . route('admin.dpl.detail', ['dpl' => $row]) . '" class="dropdown-item">Detail</a>
                        <button type="button" class="dropdown-item"
                            data-toggle="modal" data-target="#modal_edit"
                            data-id="' . $row->id . '"
                            data-nama="' . $row->nama . '"
                            data-hp="' . $row->hp . '"
                            data-prodi_id="' . $row->prodi_id . '"
                            data-email="' . $row->users_email . '"
                            data-username="' . $row->users_username . '"
                            data-jenis_kelamin="' . $row->users_jenis_kelamin . '"
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
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function add(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'dpl_id'        => 'nullable',
                'dosen_id'      => 'required',
                'nama'          => 'required',
                'niy'           => 'required',
                'jenis_kelamin' => 'required',
                'hp'            => 'required',
                'email'         => 'nullable|unique:users',
                'prodi_id'      => 'required',
            ]);

            $dpl = DPL::find($request->dpl_id);

            if ($dpl) {
                return abort(500, 'DPL sudah ada');
            }

            // add user
            $password = "123456";
            $user     = User::create([
                'username'      => $request->niy,
                'nama'          => $request->nama,
                'email'         => $request->email,
                'password'      => \Hash::make($password),
                'no_unik'       => $password,
                'jenis_kelamin' => $request->jenis_kelamin,
                'role_id'       => '4',
            ]);
            // add DPL
            $dpl = DPL::create([
                'user_id'  => $user->id,
                'prodi_id' => $request->prodi_id,
                'dosen_id' => $request->dosen_id,
                'nama'     => $request->nama,
                'hp'       => $request->hp,
                'status'   => 'Aktif',
            ]);

            \DB::commit();
            return [
                'message' => 200,
                'data'    => 'Berhasil menambahkan DPL',
                'req'     => $request->all(),
                'dpl'     => $dpl,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
            ];
        }
    }

    public function edit(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id'            => 'required',
                'nama'          => 'required',
                'jenis_kelamin' => 'required',
                'hp'            => 'required',
                'email'         => 'nullable',
                'prodi_id'      => 'required',
            ]);

            $dpl = DPL::find($request->id);

            if (! $dpl) {
                return abort(500, 'DPL tidak ditemukan');
            }

            $dpl->prodi_id = $request->prodi_id;
            $dpl->nama     = $request->nama;
            $dpl->hp       = $request->hp;
            $dpl->save();

            $user                = $dpl->user;
            $user->nama          = $request->nama;
            $user->email         = $request->email;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->save();

            \DB::commit();
            return [
                'message' => 200,
                'data'    => 'Berhasil mengedit data DPL',
                'req'     => $request->all(),
                'dpl'     => $dpl,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
            ];
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);

            $delete = DPL::findOrFail($request->id);
            $delete->delete();
            $delete->user->delete();
            $data = [
                "message" => 200,
                "data"    => "Berhasil menghapus data",
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "message" => 500,
                "data"    => $th->getMessage(),
            ];
            return $data;
        }
    }

    public function export(Request $request)
    {
        $data = DPL::join('users', 'users.id', '=', 'dpl.user_id')
            ->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                $query->where('users.jenis_kelamin', $request->jenis_kelamin);
            })
            ->when($request->prodi_id != "*", function ($query) use ($request) {
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
            })
            ->join('prodi', 'prodi.id', '=', 'dpl.prodi_id')
            ->select(
                'dpl.*',
                'users.jenis_kelamin',
                'users.email',
                'prodi.nama as prodi',
                'users.username',
                'users.no_unik as password'
            )->get();

        $except = [
            'id',
            'user_id',
            'prodi_id',
            'dosen_id',
            'created_at',
            'updated_at',
        ];

        $data = $data->makeHidden($except);

        if ($request->submit == "excel") {
            return Excel::download(new ExcelExport($data), "Data DPL.xlsx");
        }
    }

    public function detail(DPL $dpl)
    {
        return view('admin.dpl.detail', compact('dpl'));
    }

    public function detailPosko(DPL $dpl)
    {
        if (\Helper::roleAccess($dpl, 'dpl') == false) {
            return redirect()->route('home');
        }
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.posko.index', compact('tahun', 'dpl'));
    }

    public function detailAbsensi(DPL $dpl)
    {
        if (\Helper::roleAccess($dpl, 'dpl') == false) {
            return redirect()->route('home');
        }
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.absensi.dpl.indexDpl', compact('prodi', 'dpl', 'tahun'));
    }

    public function detailKegiatanMahasiswa(DPL $dpl)
    {
        if (\Helper::roleAccess($dpl, 'dpl') == false) {
            return redirect()->route('home');
        }
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.kegiatan-mahasiswa.index', compact('tahun', 'dpl'));
    }

    public function detailPenugasan(DPL $dpl)
    {
        if (\Helper::roleAccess($dpl, 'dpl') == false) {
            return redirect()->route('home');
        }
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.tugas.dpl.index', compact('prodi', 'dpl', 'tahun'));
    }

    public function detailDokumenWajib(DPL $dpl)
    {
        if (\Helper::roleAccess($dpl, 'dpl') == false) {
            return redirect()->route('home');
        }
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.dokumen-wajib.dpl.index', compact('prodi', 'dpl', 'tahun'));
    }

    public function detailPenilaian(DPL $dpl)
    {
        if (\Helper::roleAccess($dpl, 'dpl') == false) {
            return redirect()->route('home');
        }
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.penilaian.dpl.index', compact('prodi', 'dpl', 'tahun'));
    }

    public function detailMonitoring(DPl $dpl)
    {
        if (\Helper::roleAccess($dpl, 'dpl') == false) {
            return redirect()->route('home');
        }
        $jumlahAbsensi = Posko::leftJoin('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
            ->leftJoin('absensi_ps_dpl', 'absensi_ps_dpl.posko_dpl_id', '=', 'posko_dpl.id')
            ->leftJoin('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->where('dpl.id', $dpl->id)
            ->select('posko.nama as posko_nama', 'dpl.nama as dpl_nama', 'posko_dpl.id as posko_dpl_id', 'posko.id as posko_id')
            ->addSelect(\DB::raw('COUNT(absensi_ps_dpl.id) as jumlah_absensi'))
            ->groupBy('posko.nama', 'dpl.nama', 'posko_dpl.id', 'posko.id')
            ->get();

        foreach ($jumlahAbsensi as $key => $valueJumlahAbsensi) {
            $absensi = AbsensiPsDpl::where('posko_dpl_id', $valueJumlahAbsensi->posko_dpl_id)->get();

            $monitoring    = [];
            $jumlahPeserta = PoskoPeserta::where('posko_id', $valueJumlahAbsensi->posko_id)->count();
            foreach ($absensi as $key => $valueAbsensi) {
                $cekAbsensi = AbsensiPsDplDetail::where('absensi_ps_dpl_id', $valueAbsensi->id)->where('status', '!=', 'Belum Absen')->count();
                if ($cekAbsensi < $jumlahPeserta) {
                    $monitoring[] = [
                        'jumlahPeserta' => $jumlahPeserta,
                        'totalAbsensi'  => $cekAbsensi,
                        'data'          => $valueAbsensi,
                    ];
                }
            }

            $valueJumlahAbsensi->monitoring = $monitoring;
        }

        $jumlahPenugasan = Posko::leftJoin('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
            ->leftJoin('penugasan_dpl', 'penugasan_dpl.posko_dpl_id', '=', 'posko_dpl.id')
            ->leftJoin('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->where('dpl.id', $dpl->id)
            ->select('posko.nama as posko_nama', 'dpl.nama as dpl_nama', 'posko_dpl.id as posko_dpl_id', 'posko.id as posko_id')
            ->addSelect(\DB::raw('COUNT(penugasan_dpl.id) as jumlah_tugas'))
            ->groupBy('posko.nama', 'dpl.nama', 'posko_dpl.id', 'posko.id')
            ->get();

        foreach ($jumlahPenugasan as $key => $valueJumlahPenugasan) {
            $penugasan = PenugasanDpl::where('posko_dpl_id', $valueJumlahPenugasan->posko_dpl_id)->get();

            $monitoring    = [];
            $jumlahPeserta = PoskoPeserta::where('posko_id', $valueJumlahPenugasan->posko_id)->count();
            foreach ($penugasan as $key => $valuePenugasan) {
                $cekPenugasan = PenugasanDplDetail::where('penugasan_dpl_id', $valuePenugasan->id)->where([
                    ['file', '!=', ''],
                    ['path', '!=', ''],
                ])->count();
                if ($cekPenugasan < $jumlahPeserta) {
                    $monitoring[] = [
                        'jumlahPeserta'  => $jumlahPeserta,
                        'totalPenugasan' => $cekPenugasan,
                        'data'           => $valuePenugasan,
                    ];
                }
            }

            $valueJumlahPenugasan->monitoring = $monitoring;
        }

        $poskoDpl = $dpl->poskoDpl;
        foreach ($poskoDpl as $key => $valuePoskoDpl) {
            $penilaian = PoskoPeserta::leftJoin('penilaian_dpl', 'penilaian_dpl.posko_peserta_id', '=', 'posko_peserta.id')
                ->where('posko_peserta.posko_id', $valuePoskoDpl->posko_id)
                ->select('posko_peserta.*', 'penilaian_dpl.nilai')
                ->get();
            $valuePoskoDpl->penilaian = $penilaian;
        }
        return view('admin.dpl.detail.monitoring', compact('dpl', 'jumlahAbsensi', 'jumlahPenugasan', 'poskoDpl'));
    }
}
