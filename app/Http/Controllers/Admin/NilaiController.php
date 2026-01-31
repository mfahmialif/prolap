<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Absensi;
use App\Http\Services\Penugasan;
use App\Models\Nilai;
use App\Models\Peserta;
use App\Models\Prodi;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class NilaiController extends Controller
{
    public function index()
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        $prodi = Prodi::all();
        return view('admin.nilai.index', compact('tahun', 'prodi'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data   = Nilai::rightJoin('peserta', 'peserta.id', '=', 'nilai.peserta_id')
            ->leftJoin('prodi', 'prodi.id', '=', 'peserta.prodi_id')
            ->leftJoin('tahun', 'tahun.id', '=', 'peserta.tahun_id')
            ->leftJoin('users', 'users.id', '=', 'peserta.user_id')
            ->select(
                'nilai.*',
                'peserta.id as peserta_id',
                'peserta.jenis as peserta_jenis',
                'tahun.nama as tahun_nama',
                'prodi.nama as prodi_nama',
                'peserta.nim as peserta_nim',
                'peserta.nama as peserta_nama',
                'users.jenis_kelamin as users_jenis_kelamin',
            );
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->tahun_id != "*", function ($query) use ($request) {
                    $query->where('peserta.tahun_id', $request->tahun_id);
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
                        $query->where('peserta.prodi_id', $request->prodi_id);
                    }
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('peserta.nama', 'LIKE', "%$search%");
                    $query->orWhere('peserta.nim', 'LIKE', "%$search%");
                    $query->orWhere('prodi.nama', 'LIKE', "%$search%");
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
                    $query->orWhere('nilai.nilai', 'LIKE', "%$search%");
                });
            })
            ->editColumn('nilai', function ($row) {
                return $row->nilai ? $row->nilai : '<span class="badge badge-danger">Belum Ada Nilai</span>';
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
                        <a href="'
                . route('admin.nilai.detail', ['peserta' => $row->peserta_id]) .
                    '" type="button" class="dropdown-item">Detail</a>

                    </div>
                </div>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'nilai'])
            ->toJson();

    }
    public function detail(Peserta $peserta)
    {
        $absensiDpl      = Absensi::psDpl($peserta->id);
        $absensiPengawas = Absensi::psPengawas($peserta->id);
        $absensiPamong   = Absensi::psPamong($peserta->id);
        $penugasanDpl    = Penugasan::dpl($peserta->id);
        $penugasanPamong = Penugasan::pamong($peserta->id);

        $nilai = $peserta->nilai;
        return view('admin.nilai.detail', compact(
            'peserta',
            'absensiDpl',
            'absensiPengawas',
            'absensiPamong',
            'penugasanDpl',
            'penugasanPamong',
            'nilai'
        ));
    }

}
