<?php

namespace App\Http\Controllers\Admin\Penilaian;

use App\Http\Services\NilaiPeserta;
use App\Models\Tahun;
use App\Models\Pamong;
use Illuminate\Http\Request;
use App\Models\KomponenNilai;
use App\Models\PamongPeserta;
use App\Models\PenilaianPamong;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PenilaianPamongDetail;
use Yajra\DataTables\Facades\DataTables;

class PamongController extends Controller
{
    function index()
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.penilaian.pamong.index', compact('tahun'));
    }

    function data(Request $request)
    {
        $search = request('search.value');
        $data = Pamong::join('tahun', 'tahun.id', '=', 'pamong.tahun_id')
            ->join('users', 'users.id', '=', 'pamong.user_id')
            ->select(
                'pamong.*',
                'tahun.kode as tahun_kode',
                'users.jenis_kelamin as users_jenis_kelamin'
            );
        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->pamong_id, function ($query) use ($request) {
                    $query->where('pamong.id', $request->pamong_id);
                });
                $query->when($request->tahun_id != "*", function ($query) use ($request) {
                    $query->where('pamong.tahun_id', $request->tahun_id);
                });
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('pamong.nama', 'LIKE', "%$search%");
                    $query->orWhere('pamong.pamong', 'LIKE', "%$search%");
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
                    $query->orWhere('tahun.kode', 'LIKE', "%$search%");
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
                        <a href="' . route('admin.penilaian.pamong.input', ['pamong' => $row]) . '" class="dropdown-item">Input Nilai</a>
                    </div>
                </div>
                ';

                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    function input(Pamong $pamong)
    {
        $komponenNilai = KomponenNilai::where('tahun_id', $pamong->tahun_id)->where('jenis', 'pamong')->get();
        return View('admin.penilaian.pamong.input', compact('pamong', 'komponenNilai'));
    }

    function dataInput(Pamong $pamong)
    {
        $data = PamongPeserta::join('peserta', 'peserta.id', '=', 'pamong_peserta.peserta_id')
            ->select('pamong_peserta.*', 'peserta.nama as peserta_nama', 'peserta.nim as peserta_nim');

        $komponenNilai = KomponenNilai::where('tahun_id', $pamong->tahun_id)->where('jenis', 'pamong')->get();
        foreach ($komponenNilai as $key => $value) {
            $data = $data->addSelect(\DB::raw("(
                SELECT ppd.nilai
                FROM penilaian_pamong pp
                JOIN penilaian_pamong_detail ppd
                    ON pp.id = ppd.penilaian_pamong_id
                WHERE pp.pamong_id=$pamong->id
                AND pp.pamong_peserta_id=pamong_peserta.id
                AND ppd.komponen_nilai_id=$value->id
            ) as nilai_$value->nama"));
        }

        $response = DataTables::eloquent($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($pamong) {
                $query->where('pamong_id', $pamong->id);
            })
            ->editColumn('peserta_nama', function ($row) {
                return $row->peserta_nama .
                    '<input type="hidden" name="pamong_peserta_id[]" value="' . $row->id . '">';
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

    function storeInput(Request $request, Pamong $pamong)
    {
        DB::beginTransaction();
        try {
            $validate = [
                'pamong_peserta_id' => 'required',
                'nilai_akhir' => 'required'
            ];
            $komponenNilai = KomponenNilai::where('tahun_id', $pamong->tahun_id)->where('jenis', 'pamong')->get();
            foreach ($komponenNilai as $key => $value) {
                $validate['nilai_' . $value->nama] = 'required';
            }
            $request->validate($validate);

            // input nilai
            for ($i = 0; $i < count($request->pamong_peserta_id); $i++) {
                $penilaian = PenilaianPamong::where([
                    ['pamong_id', $pamong->id],
                    ['pamong_peserta_id', $request->pamong_peserta_id[$i]],
                ])->first();

                if (!$penilaian) {
                    $penilaian = new PenilaianPamong();
                    $penilaian->pamong_id = $pamong->id;
                    $penilaian->pamong_peserta_id = $request->pamong_peserta_id[$i];
                }

                $penilaian->nilai = $request['nilai_akhir'][$i];
                $penilaian->save();

                foreach ($komponenNilai as $key => $value) {
                    $penilaianDetail = PenilaianPamongDetail::where('penilaian_pamong_id', $penilaian->id)
                        ->where('komponen_nilai_id', $value->id)->first();
                    if (!$penilaianDetail) {
                        $penilaianDetail = new PenilaianPamongDetail();
                        $penilaianDetail->penilaian_pamong_id = $penilaian->id;
                        $penilaianDetail->komponen_nilai_id = $value->id;
                    }

                    $penilaianDetail->nilai = $request['nilai_' . $value->nama][$i] ?? 0;
                    $penilaianDetail->save();

                    // store absensi nilai
                    $pamongPeserta = PamongPeserta::find($request->pamong_peserta_id[$i]);
                    $storeNilai = NilaiPeserta::store($pamongPeserta->peserta_id);
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
            DB::rollBack();
            return response()->json(
                [
                    'message' => 422,
                    'errors' => $e->errors(),
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
