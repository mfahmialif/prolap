<?php
namespace App\Http\Controllers\Admin;

use App\Exports\ExcelExport;
use App\Http\Controllers\Controller;
use App\Imports\PamongImport;
use App\Models\Pamong;
use App\Models\PamongPeserta;
use App\Models\PenugasanPamong;
use App\Models\PenugasanPamongDetail;
use App\Models\Tahun;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class PamongController extends Controller
{
    public function index()
    {
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        return view('admin.pamong.index', compact('tahun'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data   = Pamong::join('users', 'users.id', '=', 'pamong.user_id')
            ->select(
                'pamong.*',
                'users.jenis_kelamin as users_jenis_kelamin',
                'users.email as users_email',
            );
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->when($request->tahun_id != "*", function ($query) use ($request) {
                    $query->where('pamong.tahun_id', $request->tahun_id);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('pamong.nama', 'LIKE', "%$search%");
                    $query->orWhere('pamong.hp', 'LIKE', "%$search%");
                    $query->orWhere('pamong.pamong', 'LIKE', "%$search%");
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
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="' . route('admin.pamong.detail', ['pamong' => $row]) . '">Detail</a>
                        <div class="dropdown-divider"></div>
                        <a href="' . route('admin.pamong.cetakAbsensi', ['pamong' => $row]) . '" target="_blank" class="dropdown-item" >Cetak Absensi</a>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item"
                        data-toggle="modal" data-target="#modal_import"
                        data-id="' . $row->id . '"
                        data-extension="xls,xlsx"
                        >Import</button>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item"
                            data-toggle="modal" data-target="#modal_edit"
                            data-id="' . $row->id . '"
                            data-tahun_id="' . $row->tahun_id . '"
                            data-nama="' . $row->nama . '"
                            data-pamong="' . $row->pamong . '"
                            data-hp="' . $row->hp . '"
                            data-email="' . $row->users_email . '"
                            data-jenis_kelamin="' . $row->users_jenis_kelamin . '"
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
                'nama'          => 'required',
                'pamong'        => 'required',
                'email'         => 'nullable|unique:users',
                'hp'            => 'nullable',
                'jenis_kelamin' => 'required',
                'tahun_id'      => 'required',
            ]);

            $generateUsername    = \Helper::generateRandomString();
            $user                = new User();
            $user->username      = $generateUsername;
            $user->password      = \Hash::make($generateUsername);
            $user->no_unik       = $generateUsername;
            $user->email         = $request->email;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->role_id       = 6; //pamong
            $user->save();

            $new           = new Pamong();
            $new->user_id  = $user->id;
            $new->nama     = $request->nama;
            $new->pamong   = $request->pamong;
            $new->hp       = $request->hp;
            $new->tahun_id = $request->tahun_id;
            $new->save();

            $data = [
                "message" => 200,
                "data"    => 'Berhasil menambahkan Pamong',
                "req"     => $request->all(),
            ];
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            $data = [
                "message" => 500,
                "data"    => $th->getMessage(),
                "req"     => $request->all(),
            ];
        }
        return $data;
    }

    public function edit(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id'            => 'required',
                'nama'          => 'required',
                'pamong'        => 'required',
                'email'         => 'nullable|unique:users',
                'hp'            => 'nullable',
                'jenis_kelamin' => 'required',
                'tahun_id'      => 'required',
            ]);

            $update           = Pamong::findOrFail($request->id);
            $update->nama     = $request->nama;
            $update->pamong   = $request->pamong;
            $update->hp       = $request->hp;
            $update->tahun_id = $request->tahun_id;
            $update->save();

            $user                = $update->user;
            $user->nama          = $request->nama;
            $user->email         = $request->email;
            $user->jenis_kelamin = $request->jenis_kelamin;
            $user->save();

            $data = [
                'message' => 200,
                'data'    => 'Berhasil mengedit Pamong',
                'req'     => $request->all(),
            ];
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            $data = [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
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

            $delete = Pamong::findOrFail($request->id);
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
        $data = Pamong::join('users', 'users.id', '=', 'pamong.user_id')
            ->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                $query->where('users.jenis_kelamin', $request->jenis_kelamin);
            })
            ->when($request->tahun_id != "*", function ($query) use ($request) {
                $query->where('pamong.tahun_id', $request->tahun_id);
            })
            ->select(
                'pamong.*',
                'users.jenis_kelamin as jenis_kelamin',
                'users.email as email',
                'users.username as username',
                'users.no_unik as password',
            )->get();

        $except = [
            'id',
            'created_at',
            'updated_at',
        ];

        $data = $data->makeHidden($except);

        if ($request->submit == "excel") {
            return Excel::download(new ExcelExport($data), "Data Pamong.xlsx");
        }
    }

    public function detail(Pamong $pamong)
    {
        return view('admin.pamong.detail', compact('pamong'));
    }

    public function detailPeserta(Pamong $pamong)
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.pamong.detail.peserta.index', compact('tahun', 'pamong'));
    }

    public function addPeserta(Pamong $pamong, Request $request)
    {
        try {
            $request->validate([
                'peserta_id' => 'required',
            ]);

            $pamongPeserta = PamongPeserta::where([
                ['pamong_id', $pamong->id],
                ['peserta_id', $request->peserta_id],
            ])->first();

            if ($pamongPeserta) {
                return abort(500, 'Peserta KKN sudah ada di Pamong Ini');
            }

            PamongPeserta::create([
                'pamong_id'  => $pamong->id,
                'peserta_id' => $request->peserta_id,
            ]);

            return [
                "message" => 200,
                "data"    => "Berhasil menambahkan Peserta di Pamong Ini",
                "req"     => $request->all(),
            ];
        } catch (ValidationException $e) {
            return [
                'message' => 500,
                'data'    => 'Peserta KKN tidak ditemukan',
            ];
        } catch (\Throwable $th) {
            return [
                "message" => 500,
                "data"    => $th->getMessage(),
                "req"     => $request->all(),
            ];
        }
    }

    public function deletePeserta(Pamong $pamong, Request $request)
    {
        try {
            $request->validate([
                'pamong_peserta_id' => 'required',
            ]);

            Pamongpeserta::destroy($request->pamong_peserta_id);
            return [
                "message" => 200,
                "data"    => "Berhasil menghapus Peserta di Pamong Ini",
                "req"     => $request->all(),
            ];
        } catch (ValidationException $e) {
            return [
                'message' => 500,
                'data'    => 'Peserta KKN tidak ditemukan',
            ];
        } catch (\Throwable $th) {
            return [
                "message" => 500,
                "data"    => "Gagal menghapus Peserta di Pamong Ini",
                "req"     => $request->all(),
                "error"   => $th->getMessage(),
            ];
        }
    }

    public function detailAbsensi(Pamong $pamong)
    {
        if (\Helper::roleAccess($pamong, 'pamong') == false) {
            return redirect()->route('home');
        }
        $tahun = Tahun::all();
        return view('admin.absensi.pamong.index', compact('tahun', 'pamong'));
    }

    public function detailPenugasan(Pamong $pamong)
    {
        if (\Helper::roleAccess($pamong, 'pamong') == false) {
            return redirect()->route('home');
        }
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.tugas.pamong.index', compact('tahun', 'pamong'));
    }

    public function detailPenilaian(Pamong $pamong)
    {
        if (\Helper::roleAccess($pamong, 'pamong') == false) {
            return redirect()->route('home');
        }
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.penilaian.pamong.index', compact('tahun', 'pamong'));
    }

    public function detailMonitoring(Pamong $pamong)
    {
        if (\Helper::roleAccess($pamong, 'pamong') == false) {
            return redirect()->route('home');
        }

        $jumlahPenugasan = Pamong::leftJoin('penugasan_pamong', 'penugasan_pamong.pamong_id', '=', 'pamong.id')
            ->where('pamong.id', $pamong->id)
            ->select('pamong.pamong as pamong', 'pamong.nama as nama', 'pamong.id as id')
            ->addSelect(\DB::raw('COUNT(penugasan_pamong.id) as jumlah_tugas'))
            ->groupBy('pamong.pamong', 'pamong.nama', 'pamong.id')
            ->get();

        foreach ($jumlahPenugasan as $key => $valueJumlahPenugasan) {
            $penugasan = PenugasanPamong::where('pamong_id', $valueJumlahPenugasan->id)->get();

            $monitoring    = [];
            $jumlahPeserta = PamongPeserta::where('id', $valueJumlahPenugasan->id)->count();
            foreach ($penugasan as $key => $valuePenugasan) {
                $cekPenugasan = PenugasanPamongDetail::where('penugasan_pamong_id', $valuePenugasan->id)->where([
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

        $penilaian = PamongPeserta::leftJoin('penilaian_pamong', 'penilaian_pamong.pamong_peserta_id', '=', 'pamong_peserta.id')
            ->where('pamong_peserta.pamong_id', $pamong->id)
            ->select('pamong_peserta.*', 'penilaian_pamong.nilai')
            ->get();
        $pamong->penilaian = $penilaian;

        return view('admin.pamong.detail.monitoring', compact('pamong', 'jumlahPenugasan'));
    }

    public function cetakAbsensi(Pamong $pamong)
    {
        $pamongPeserta = $pamong->pamongPeserta;

        $jumlahPertemuan = 25;
        $tahun           = $pamong->tahun;

        $pdf = Pdf::loadView('admin.pamong.cetak', compact('pamongPeserta', 'jumlahPertemuan', 'pamong', 'tahun'));
        return $pdf->setPaper('a4', 'landscape')->stream('absensi.pdf');
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'id'   => 'required',
                'file' => 'required|mimes:xls,xlsx',
            ]);

            $file = $request->file('file');

            $pamong = Pamong::find($request->id);
            $import = new PamongImport($pamong);
            \Excel::import($import, $file);
            return [
                'message' => 200,
                'data'    => "Berhasil mengimport data " . $import->getResponse(),
                'req'     => $request->all(),
            ];
        } catch (ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 500,
                'data'    => collect($e->errors())->flatten()->implode(' '),
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'message' => 500,
                'data'    => 'Gagal mengimport data',
                'err'     => $th->getMessage(),
                'req'     => $request->all(),
            ];
        }
    }
}
