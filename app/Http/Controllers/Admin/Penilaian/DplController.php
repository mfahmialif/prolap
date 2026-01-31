<?php

namespace App\Http\Controllers\Admin\Penilaian;

use App\Http\Services\NilaiPeserta;
use Carbon\Carbon;
use App\Models\DPL;
use App\Models\User;
use App\Models\Nilai;
use App\Models\Posko;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\PoskoDpl;
use App\Models\AbsensiPsDpl;
use App\Models\PenilaianDpl;
use App\Models\PoskoPeserta;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use App\Models\KomponenNilai;
use App\Models\AbsensiPsDplDetail;
use App\Models\PenilaianDplDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DplController extends Controller
{
    function index()
    {
        $prodi = Prodi::all();
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.penilaian.dpl.index', compact('prodi', 'tahun'));
    }

    function data(Request $request)
    {
        $search = request('search.value');
        $data = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->join('prodi', 'prodi.id', '=', 'dpl.prodi_id')
            ->join('users', 'users.id', '=', 'dpl.user_id')
            ->join('tahun', 'tahun.id', '=', 'posko.tahun_id')
            ->select(
                'posko_dpl.*',
                'posko.nama as nama_posko',
                'dpl.nama as username',
                'users.jenis_kelamin as jenis',
                'prodi.nama as prodi_nama',
                'prodi.jenjang as prodi_jenjanga',
                'tahun.nama as tahun_nama'
            );
        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->dpl_id, function ($query) use ($request) {
                    $query->where('posko_dpl.dpl_id', $request->dpl_id);
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
                        <a href="' . route('admin.penilaian.dpl.input', ['poskoDpl' => $row]) . '" class="dropdown-item">Input Nilai</a>
                    </div>
                </div>
                ';

                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    function input(PoskoDpl $poskoDpl)
    {
        if (\Helper::roleAccess($poskoDpl->dpl, 'dpl') == false) {
            return redirect()->route('home');
        }
        $komponenNilai = KomponenNilai::where('tahun_id', $poskoDpl->posko->tahun_id)->where('jenis', 'dpl')->get();
        $poskoPeserta = $poskoDpl->posko->poskoPeserta;

        // store absensi nilai
        for ($i=0; $i < count($poskoPeserta); $i++) {
            $storeNilai = NilaiPeserta::storeAbsensiDpl($poskoDpl, $poskoPeserta[$i]->id);
            if ($storeNilai['status'] == false) {
                return abort(500, $storeNilai['error']);
            }
        }
        return View('admin.penilaian.dpl.input', compact('poskoDpl', 'komponenNilai'));
    }

    function dataInput(PoskoDpl $poskoDpl)
    {
        $data = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
            ->select('posko_peserta.*', 'peserta.nama as peserta_nama', 'peserta.nim as peserta_nim');

        $komponenNilai = KomponenNilai::where('tahun_id', $poskoDpl->posko->tahun_id)->where('jenis', 'dpl')->get();
        foreach ($komponenNilai as $key => $value) {
            $data = $data->addSelect(\DB::raw("(
                        SELECT pdd.nilai
                        FROM penilaian_dpl pd
                        JOIN penilaian_dpl_detail pdd
                            ON pd.id = pdd.penilaian_dpl_id
                        WHERE pd.posko_dpl_id=$poskoDpl->id
                        AND pd.posko_peserta_id=posko_peserta.id
                        AND pdd.komponen_nilai_id=$value->id
                    ) as nilai_$value->nama"));
        }

        $response = DataTables::eloquent($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($poskoDpl) {
                $query->where('posko_id', $poskoDpl->posko_id);
            })
            ->editColumn('peserta_nama', function ($row) {
                return $row->peserta_nama .
                    '<input type="hidden" name="posko_peserta_id[]" value="' . $row->id . '">';
            });

        $rawColumns = ['peserta_nama'];
        foreach ($komponenNilai as $key => $value) {
            $response = $response->editColumn("nilai_$value->nama", function ($row) use ($value) {
                $return = '
                    <input type="number" name="nilai_' . $value->nama . '[]" class="form-control text-center"
                    min="0" max="100"
                    onkeyup="setNilaiAkhir(' . $row->id . ', event)"
                    id="nilai_' . $value->nama . '_' . $row->id . '"
                    value="' . $row['nilai_' . $value->nama] . '"' .
                    (strtolower($value->nama) == 'absensi' ? ' readonly tabindex="-1"' : '') .
                    '>';
                return $return;
            });
            $rawColumns[] = "nilai_" . $value->nama;
        }

        $response = $response->addColumn('nilai_akhir', function ($row) use ($komponenNilai) {
            $nilaiAkhir = 0;
            foreach ($komponenNilai as $key => $value) {
                $nilaiAkhir += $row['nilai_' . $value->nama] * $value->bobot / 100;
            }
            $response = '
                        <input type="number" name="nilai_akhir[]" class="form-control text-center"
                        id="nilai_akhir_' . $row->id . '"
                        tabindex="-1"
                        value="' . $nilaiAkhir . '" readonly>';
            return $response;
        });

        $rawColumns[] = 'nilai_akhir';
        $response = $response->rawColumns($rawColumns)->make(true);
        return $response;
    }

    function storeInput(Request $request, PoskoDpl $poskoDpl)
    {
        DB::beginTransaction();
        try {
            $validate = [
                'posko_peserta_id' => 'required',
                'nilai_akhir' => 'required'
            ];
            $komponenNilai = KomponenNilai::where('tahun_id', $poskoDpl->posko->tahun_id)->where('jenis', 'dpl')->get();
            foreach ($komponenNilai as $key => $value) {
                $validate['nilai_' . $value->nama] = 'required';
            }
            $request->validate($validate);

            // input nilai
            for ($i = 0; $i < count($request->posko_peserta_id); $i++) {
                $penilaian = PenilaianDpl::where([
                    ['posko_dpl_id', $poskoDpl->id],
                    ['posko_peserta_id', $request->posko_peserta_id[$i]],
                ])->first();

                if (!$penilaian) {
                    $penilaian = new PenilaianDpl();
                    $penilaian->posko_dpl_id = $poskoDpl->id;
                    $penilaian->posko_peserta_id = $request->posko_peserta_id[$i];
                }

                $penilaian->nilai = $request['nilai_akhir'][$i];
                $penilaian->save();

                foreach ($komponenNilai as $key => $value) {
                    $penilaianDetail = PenilaianDplDetail::where('penilaian_dpl_id', $penilaian->id)
                        ->where('komponen_nilai_id', $value->id)->first();
                    if (!$penilaianDetail) {
                        $penilaianDetail = new PenilaianDplDetail();
                        $penilaianDetail->penilaian_dpl_id = $penilaian->id;
                        $penilaianDetail->komponen_nilai_id = $value->id;
                    }

                    $penilaianDetail->nilai = $request['nilai_' . $value->nama][$i] ?? 0;
                    $penilaianDetail->save();

                    // store absensi nilai
                    $poskoPeserta = PoskoPeserta::find($request->posko_peserta_id[$i]);
                    $storeNilai = NilaiPeserta::store($poskoPeserta->peserta_id);
                    if ($storeNilai['status'] == false) {
                        return abort(500, $storeNilai['error']);
                    }
                }
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
                'data' => $th->getMessage(),
                'req' => $request->all(),
                'err' => $th->getMessage()
            ];
        }
    }
}
