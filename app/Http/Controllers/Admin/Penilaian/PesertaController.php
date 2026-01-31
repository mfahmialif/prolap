<?php

namespace App\Http\Controllers\Admin\Penilaian;

use Carbon\Carbon;
use App\Models\DPL;
use App\Models\User;
use App\Models\Nilai;
use App\Models\Posko;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\Peserta;
use App\Models\AbsensiPsDpl;
use App\Models\PenilaianDpl;
use App\Models\PoskoPeserta;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use App\Models\KomponenNilai;
use App\Models\PenilaianPamong;
use App\Models\AbsensiPsDplDetail;
use App\Models\PenilaianDplDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Services\NilaiPeserta;
use App\Http\Controllers\Controller;
use App\Models\PenilaianPamongDetail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PesertaController extends Controller
{
    function index()
    {
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.penilaian.peserta.index', compact('prodi', 'tahun'));
    }

    function data(Request $request)
    {
        $search = request('search.value');
        $data = Peserta::join('prodi', 'prodi.id', '=', 'peserta.prodi_id')
            ->join('users', 'users.id', '=', 'peserta.user_id')
            ->join('tahun', 'tahun.id', '=', 'peserta.tahun_id')
            ->select(
                'peserta.*',
                'users.jenis_kelamin as users_jenis_kelamin',
                'prodi.nama as prodi_nama',
                'tahun.nama as tahun_nama',
            );
        return DataTables::of($data)
            ->addIndexColumn()
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
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
                    $query->orWhere('prodi.nama', 'LIKE', "%$search%");
                    $query->orWhere('prodi.alias', 'LIKE', "%$search%");
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
                        <a href="' . route('admin.penilaian.peserta.input', ['peserta' => $row]) . '" class="dropdown-item">Input Nilai</a>
                    </div>
                </div>
                ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    function input(Peserta $peserta)
    {
        $poskoPeserta = $peserta->poskoPeserta;
        $pamongPeserta = $peserta->pamongPeserta;
        $komponenNilai = KomponenNilai::all();
        $nilai = Nilai::where('peserta_id', $peserta->id)->first();
        return View('admin.penilaian.peserta.input', compact('peserta', 'komponenNilai', 'poskoPeserta', 'pamongPeserta', 'nilai'));
    }

    public function storeInput(Request $request, Peserta $peserta)
    {
        DB::beginTransaction();
        try {
            $validate = [
                'peserta_id' => 'required',
                'nilai_akhir' => 'required',
                'supervisor_id' => 'required',
                'tipe_db' => 'required',
            ];
            $komponenNilai = KomponenNilai::where('tahun_id', Tahun::aktif()->id)->get();
            $komponenNilai = $komponenNilai->where('jenis', $request->tipe_db);
            foreach ($komponenNilai as $key => $value) {
                $validate['nilai_' . $value->nama] = 'required';
            }
            $request->validate($validate);

            if ($request->tipe_db == 'dpl') {
                self::saveDpl($request, $komponenNilai);
            } else if ($request->tipe_db == 'pamong') {
                self::savePamong($request, $komponenNilai);
            } else {
                return abort(500, 'Hanya bisa simpan dpl dan pamong');
            }

            $nilaiPeserta = NilaiPeserta::store($peserta->id);
            if (!$nilaiPeserta['status']) {
                return abort(500, $nilaiPeserta['error']);
            }

            DB::commit();
            return response()->json(
                [
                    'message' => 200,
                    'data' => 'Nilai berhasil disimpan',
                    'req' => $request->all()
                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors
            DB::rollBack();
            return response()->json(
                [
                    'message' => 422,
                    'errors' => $e->errors(),  // Validation errors
                    'req' => $request->all()
                ]
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'message' => 500,
                'data' => 'Nilai gagal disimpan',
                'req' => $request->all(),
                'err' => $th->getMessage()
            ];
        }
    }

    public static function saveDpl($request, $komponenNilai)
    {
        $poskoPesertaId = $request->peserta_id;
        $penilaian = PenilaianDpl::where([
            ['posko_dpl_id', $request->supervisor_id],
            ['posko_peserta_id', $poskoPesertaId],
        ])->first();

        if (!$penilaian) {
            $penilaian = new PenilaianDpl();
            $penilaian->posko_dpl_id = $request->supervisor_id;
            $penilaian->posko_peserta_id = $poskoPesertaId;
        }

        $penilaian->nilai = $request['nilai_akhir'];
        $penilaian->save();

        foreach ($komponenNilai as $key => $value) {
            $penilaianDetail = PenilaianDplDetail::where('penilaian_dpl_id', $penilaian->id)
                ->where('komponen_nilai_id', $value->id)->first();
            if (!$penilaianDetail) {
                $penilaianDetail = new PenilaianDplDetail();
                $penilaianDetail->penilaian_dpl_id = $penilaian->id;
                $penilaianDetail->komponen_nilai_id = $value->id;
            }

            $penilaianDetail->nilai = $request['nilai_' . $value->nama] ?? 0;
            $penilaianDetail->save();
        }
    }

    public static function savePamong($request, $komponenNilai)
    {
        $pamongPesertaId = $request->peserta_id;
        $penilaian = PenilaianPamong::where([
            ['pamong_id', $request->supervisor_id],
            ['pamong_peserta_id', $pamongPesertaId],
        ])->first();

        if (!$penilaian) {
            $penilaian = new PenilaianPamong();
            $penilaian->pamong_id = $request->supervisor_id;
            $penilaian->pamong_peserta_id = $pamongPesertaId;
        }

        $penilaian->nilai = $request['nilai_akhir'];
        $penilaian->save();

        foreach ($komponenNilai as $key => $value) {
            $penilaianDetail = PenilaianPamongDetail::where('penilaian_pamong_id', $penilaian->id)
                ->where('komponen_nilai_id', $value->id)->first();
            if (!$penilaianDetail) {
                $penilaianDetail = new PenilaianPamongDetail();
                $penilaianDetail->penilaian_pamong_id = $penilaian->id;
                $penilaianDetail->komponen_nilai_id = $value->id;
            }

            $penilaianDetail->nilai = $request['nilai_' . $value->nama] ?? 0;
            $penilaianDetail->save();
        }
    }
}
