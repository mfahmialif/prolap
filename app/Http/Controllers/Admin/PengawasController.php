<?php

namespace App\Http\Controllers\Admin;

use App\Models\AbsensiPsPengawas;
use App\Models\AbsensiPsPengawasDetail;
use App\Models\Posko;
use App\Models\PoskoPeserta;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\Pengawas;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;

class PengawasController extends Controller
{
    public function index()
    {
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        return view('admin.pengawas.index', compact('tahun'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Pengawas::join('users', 'users.id', '=', 'pengawas.user_id')
            ->select(
                'pengawas.*',
                'users.jenis_kelamin as users_jenis_kelamin',
                'users.email as users_email',
            );
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('pengawas.nama', 'LIKE', "%$search%");
                    $query->orWhere('pengawas.hp', 'LIKE', "%$search%");
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
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
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                $actionBtn .= '
                    <a class="dropdown-item" href="' . route('admin.pengawas.detail', ['pengawas' => $row]) . '">Detail</a>
                    <div class="dropdown-divider"></div>
                ';
                $actionBtn .= '
                        <button type="button" class="dropdown-item"
                            data-toggle="modal" data-target="#modal_edit"
                            data-id="' . $row->id . '"
                            data-nama="' . $row->nama . '"
                            data-hp="' . $row->hp . '"
                            data-email="' . $row->users_email . '"
                            data-jenis_kelamin="' . $row->users_jenis_kelamin . '"
                        >Edit</button>';
                $actionBtn .= '
                        <form action="" onsubmit="deleteData(event)" method="POST">
                        ' . method_field('delete') . csrf_field() . '
                            <input type="hidden" name="id" value="' . $row->id . '">
                            <input type="hidden" name="nama" value="' . $row->jenjang . '">
                            <button type="submit" class="dropdown-item text-danger">
                                Delete
                            </button>
                        </form>';
                $actionBtn .= '
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
                'nama' => 'required',
                'email' => 'nullable|unique:users',
                'hp' => 'nullable',
                'jenis_kelamin' => 'required',
            ]);

            $generateUsername = \Helper::generateRandomString();
            $user = new User();
            $user->username = $generateUsername;
            $user->nama = $request->nama;
            $user->password = \Hash::make($generateUsername);
            $user->no_unik = $generateUsername;
            $user->email = $request->email;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->role_id = 5; //pengawas
            $user->save();

            $new = new Pengawas();
            $new->user_id = $user->id;
            $new->nama = $request->nama;
            $new->hp = $request->hp;
            $new->status = 'Tidak Aktif';
            $new->save();

            $data = [
                "message" => 200,
                "data" => 'Berhasil menambahkan Pengawas',
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
                'nama' => 'required',
                'email' => 'nullable|unique:users',
                'hp' => 'nullable',
                'jenis_kelamin' => 'required',
            ]);

            $update = Pengawas::findOrFail($request->id);
            $update->nama = $request->nama;
            $update->hp = $request->hp;
            $update->save();

            $user = $update->user;
            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->save();

            $data = [
                'message' => 200,
                'data' => 'Berhasil mengedit Pengawas',
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
            $request->validate([
                'id' => 'required',
            ]);

            $delete = Pengawas::findOrFail($request->id);
            $delete->delete();
            $delete->user->delete();
            $data = [
                "message" => 200,
                "data" => "Berhasil menghapus data",
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "message" => 500,
                "data" => $th->getMessage(),
            ];
            return $data;
        }
    }

    public function detail(Pengawas $pengawas)
    {
        return view('admin.pengawas.detail', compact('pengawas'));
    }

    public function export(Request $request)
    {
        $data = Pengawas::join('users', 'users.id', '=', 'pengawas.user_id')
            ->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                $query->where('users.jenis_kelamin', $request->jenis_kelamin);
            })
            ->select(
                'pengawas.*',
                'users.jenis_kelamin as jenis_kelamin',
                'users.email as email',
                'users.username as username',
                'users.no_unik as password',
            )->get();

        $except = [
            'id',
            'user_id',
            'created_at',
            'updated_at',
        ];

        $data = $data->makeHidden($except);

        if ($request->submit == "excel") {
            return Excel::download(new ExcelExport($data), "Data Pengawas.xlsx");
        }
    }

    public function detailPosko(Pengawas $pengawas)
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.posko.index', compact('tahun', 'pengawas'));
    }

    function detailAbsensi(Pengawas $pengawas)
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.absensi.pengawas.index', compact('pengawas', 'tahun'));
    }

    function detailJurnal(Pengawas $pengawas)
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.jurnal.pengawas.index', compact('pengawas', 'tahun'));
    }

    function detailPenugasan(Pengawas $pengawas)
    {
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.tugas.dpl.index', compact('prodi', 'pengawas', 'tahun'));
    }
    function detailMonitoring(Pengawas $pengawas)
    {
        $jumlahAbsensi = Posko::leftJoin('posko_pengawas', 'posko_pengawas.posko_id', '=', 'posko.id')
            ->leftJoin('absensi_ps_pengawas', 'absensi_ps_pengawas.posko_pengawas_id', '=', 'posko_pengawas.id')
            ->leftJoin('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->where('pengawas.id', $pengawas->id)
            ->select('posko.nama as posko_nama', 'pengawas.nama as pengawas_nama', 'posko_pengawas.id as posko_pengawas_id', 'posko.id as posko_id')
            ->addSelect(\DB::raw('COUNT(absensi_ps_pengawas.id) as jumlah_absensi'))
            ->groupBy('posko.nama', 'pengawas.nama', 'posko_pengawas.id', 'posko.id')
            ->get();

        foreach ($jumlahAbsensi as $key => $valueJumlahAbsensi) {
            $absensi = AbsensiPsPengawas::where('posko_pengawas_id', $valueJumlahAbsensi->posko_pengawas_id)->get();

            $monitoring = [];
            $jumlahPeserta = PoskoPeserta::where('posko_id', $valueJumlahAbsensi->posko_id)->count();
            foreach ($absensi as $key => $valueAbsensi) {
                $cekAbsensi = AbsensiPsPengawasDetail::where('absensi_ps_pengawas_id', $valueAbsensi->id)->where('status', '!=', 'Belum Absen')->count();
                if ($cekAbsensi < $jumlahPeserta) {
                    $monitoring[] = [
                        'jumlahPeserta' => $jumlahPeserta,
                        'totalAbsensi' => $cekAbsensi,
                        'data' => $valueAbsensi
                    ];
                }
            }

            $valueJumlahAbsensi->monitoring = $monitoring;
        }
        return view('admin.pengawas.detail.monitoring', compact('pengawas', 'jumlahAbsensi'));
    }
}
