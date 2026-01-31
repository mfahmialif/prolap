<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Posko;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class KegiatanMahasiswaController extends Controller
{
    public function index()
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.kegiatan-mahasiswa.index', compact('tahun'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data   = Posko::leftJoin('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
            ->leftJoin('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->leftJoin('posko_pengawas', 'posko_pengawas.posko_id', '=', 'posko.id')
            ->leftJoin('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->leftJoin('prodi', 'prodi.id', '=', 'dpl.prodi_id')
            ->leftJoin('tahun', 'tahun.id', '=', 'posko.tahun_id')
            ->select('posko.*', 'dpl.nama as dpl_nama', 'prodi.alias as prodi_alias', 'pengawas.nama as pengawas_nama', 'tahun.kode as tahun_kode');
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('posko.nama', 'LIKE', "%$search%");
                    $query->orWhere('posko.lokasi', 'LIKE', "%$search%");
                    $query->orWhere('dpl.nama', 'LIKE', "%$search%");
                    $query->orWhere('prodi.alias', 'LIKE', "%$search%");
                });
                $query->when($request->tahun_id != '*', function ($query) use ($request) {
                    $query->where('posko.tahun_id', $request->tahun_id);
                });
                $query->when($request->dpl_id, function ($query) use ($request) {
                    $query->where('dpl_id', $request->dpl_id);
                });
                $query->when($request->pengawas_id, function ($query) use ($request) {
                    $query->where('pengawas_id', $request->pengawas_id);
                });
            })
            ->editColumn('dpl_nama', function ($row) {
                return $row->dpl_nama ? $row->dpl_nama : '<span class="badge badge-warning">Belum ada DPL</span>';
            })
            ->editColumn('prodi_alias', function ($row) {
                return $row->prodi_alias ? $row->prodi_alias : '<span class="badge badge-warning">Belum ada DPL</span>';
            })
            ->editColumn('pengawas_nama', function ($row) {
                return $row->pengawas_nama ? $row->pengawas_nama : '<span class="badge badge-warning">Belum ada Pengawas</span>';
            })
            ->addColumn('action', function ($row) use ($request) {
                $actionBtn = '
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Klik
                    </button>';
                $actionBtn .= '
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                $actionBtn .= '
                        <a class="dropdown-item" href="' . route('admin.kegiatan-mahasiswa.detail', ['posko' => $row->id]) . '">Detail</a>';
                $actionBtn .= '
                    </div>
                </div>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'dpl_nama', 'prodi_alias', 'pengawas_nama'])
            ->toJson();
    }

    public function detail(Posko $posko)
    {
        if (\Auth::user()->role->nama == 'dpl') {
            $isPermitted = false;
            foreach ($posko->poskoDpl as $key => $value) {
                if (\Helper::roleAccess($value->dpl, 'dpl') == true) {
                    $isPermitted = true;
                }
            }
            if (! $isPermitted) {
                return redirect()->route('home');
            }
        }
        return view('admin.kegiatan-mahasiswa.detail', compact('posko'));
    }

}
