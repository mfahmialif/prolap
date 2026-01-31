<?php

namespace App\Http\Controllers\admin\absensi;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Posko;
use App\Models\Tahun;
use App\Models\Pamong;
use Facade\FlareClient\View;
use Illuminate\Http\Request;
use App\Models\PamongPeserta;
use App\Models\AbsensiPsPamong;
use Illuminate\Support\Facades\DB;
use App\Http\Services\NilaiPeserta;
use App\Http\Controllers\Controller;
use App\Models\AbsensiPsPamongDetail;
use Yajra\DataTables\Facades\DataTables;

class PamongController extends Controller
{
    public function index()
    {
        $tahun = Tahun::all();
        $users = User::all();
        return view('admin.absensi.pamong.index', compact('tahun'));
    }
    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Pamong::join('users', 'users.id', '=', 'pamong.user_id')
            ->join('tahun', 'tahun.id', '=', 'pamong.tahun_id')
            ->select('pamong.*', 'users.jenis_kelamin as jk', 'users.email as email', 'tahun.nama as tahun_nama');
        return DataTables::eloquent($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->when($request->pamong_id, function ($query) use ($request) {
                    $query->where('pamong.id', $request->pamong_id);
                });
                $query->when($request->tahun_id != '*', function ($query) use ($request) {
                    $query->where('pamong.tahun_id', $request->tahun_id);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('pamong.nama', 'LIKE', "%$search%");
                    $query->orWhere('users.email', 'LIKE', "%$search%");
                    $query->orWhere('pamong.hp', 'LIKE', "%$search%");
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
                        <button type="button" class="dropdown-item BtnInput"
                           data-id="' . $row->id . '"
                       >Input Absensi</button>
                   </div>
               </div>
               ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }
    public function detail($idPamong)
    {
        $pamong = Pamong::find($idPamong);
        $absensiStatus = \Helper::getEnumValues('absensi_ps_pamong_detail', 'status');
        return view('admin.absensi.pamong.detail', compact('idPamong', 'pamong', 'absensiStatus'));
    }
    public function dataDetail(Request $request, $idPamong)
    {
        $search = request('search.value');
        $data = AbsensiPsPamong::join('pamong', 'pamong.id', '=', 'absensi_ps_pamong.pamong_id')
            ->join('users', 'users.id', '=', 'pamong.user_id')
            ->join('tahun', 'tahun.id', '=', 'pamong.tahun_id')
            ->where('pamong_id', $idPamong)
            ->select('absensi_ps_pamong.*', 'pamong.nama as nama_pamong');
        return DataTables::eloquent($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {

                $query->where(function ($query) use ($search) {
                    $query->orWhere('absensi_ps_pamong.nama', 'LIKE', "%$search%");
                    $query->orWhere('pamong.nama', 'LIKE', "%$search%");
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
                        <button type="button" class="dropdown-item BtnEdit"
                           data-id="' . $row->id . '"
                            data-pamong_id ="' . $row->pamong_id . '"
                       >Edit</button>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item text-danger BtnDel"
                           data-id="' . $row->id . '"
                           data-nama="' . $row->nama . '"
                            data-pamong_id ="' . $row->pamong_id . '"
                       >Delete</button>
                   </div>
               </div>
               ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }
    public function detailDataPeserta($idPamong)
    {
        $search = request('search.value');
        $pamong = Pamong::find($idPamong);
        $absensi = [];
        foreach (\Helper::getEnumValues('absensi_ps_pamong_detail', 'status') as $key => $value) {
            $absensi["absensi_$value"] = \DB::table('absensi_ps_pamong_detail')
                ->join('absensi_ps_pamong', 'absensi_ps_pamong.id', '=', 'absensi_ps_pamong_detail.absensi_ps_pamong_id')
                ->selectRaw('count(*)')
                ->whereColumn('absensi_ps_pamong_detail.pamong_peserta_id', 'pamong_peserta.id')
                ->where('absensi_ps_pamong.id', $idPamong)
                ->where('absensi_ps_pamong_detail.status', $value);
        }
        $data = PamongPeserta::join('peserta', 'peserta.id', '=', 'pamong_peserta.peserta_id')
            ->select('pamong_peserta.*', 'peserta.nama as peserta_nama', 'peserta.nim as peserta_nim')
            ->addSelect($absensi);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('peserta_nama', function ($row) {
                $content = "<div>$row->peserta_nama</div>";
                $content .= "<div class='text-bold'>$row->peserta_nim</div>";
                $content .= "<div class='text-bold'>" . $row->peserta->prodi->alias . "</div>";
                return $content;
            })
            ->editColumn('absensi_Belum Absen', function ($row) use ($idPamong) {
                $absensi = AbsensiPsPamong::where('pamong_id', $idPamong)->get();
                $jumlahAbsensi = $absensi->count();
                if ($jumlahAbsensi > 0) {
                    $absensiId = $absensi->pluck('id')->toArray();
                    $jumlahAbsensiDetail = AbsensiPsPamongDetail::whereIn('absensi_ps_pamong_id', $absensiId)
                        ->where('pamong_peserta_id', $row->id)->count();
                    return $jumlahAbsensi - $jumlahAbsensiDetail + $row->absensi_Belum_Absen;
                }
                return 0;
            })
            ->filter(function ($query) use ($search, $pamong) {
                $query->where('pamong_peserta.pamong_id', $pamong->id);
                $query->where(function ($query) use ($search) {
                    $query->orWhere('peserta.nama', 'LIKE', "%$search%");
                    $query->orWhere('peserta.nim', 'LIKE', "%$search%");
                });
            })->rawColumns(['action', 'peserta_nama'])->make(true);
    }
    public function store(Request $request)
    {
        try {

            $request->validate([
                'pertemuan' => 'required',
                'pamong' => 'required'
            ]);
            $absensiPsPamong = new AbsensiPsPamong();
            $absensiPsPamong->nama = $request->pertemuan;
            $absensiPsPamong->pamong_id = $request->pamong;
            $absensiPsPamong->save();
            return [
                'message' => 200,
                'data' => 'Data Berhasil Ditambahkan',
                'req' => $request->all()
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data' => 'Data Gagal Ditambahkan',
                'req' => $request->all()
            ];
        }
    }
    public function formInput($idPamong)
    {
        $pamong = Pamong::find($idPamong);
        // $pamongPeserta = PamongPeserta::where('pamong_id', $idPamong)->get();
        return View('admin.absensi.pamong.input', compact('idPamong', 'pamong'));
    }
    public function dataInput($idPamong)
    {
        $data = PamongPeserta::join('peserta', 'peserta.id', 'pamong_peserta.peserta_id')
            ->where('pamong_id', $idPamong)
            ->select('pamong_peserta.*', 'peserta.nama as nama_peserta', 'peserta.nim as nim_peserta');
        return DataTables::eloquent($data)
            ->addIndexColumn()
            ->editColumn('nama_peserta', function ($row) {
                $content = "<div>$row->nama_peserta</div>";
                $content .= "<div class='text-bold'>$row->nim_peserta</div>";
                $content .= "<div class='text-bold'>" . $row->peserta->prodi->alias . "</div>";
                $content .= '<input type="hidden" name="pamongPesertaId[]" value="' . $row->id . '">';
                return $content;
            })
            ->addColumn('action', function ($row) {
                $select = '<div class="form-group">
                            <select class="form-control minta" name="minta[]">
                            <option value="">-- PILIH --</option>';
                foreach (\Helper::getEnumValues('absensi_ps_pamong_detail', 'status') as $key => $value) {
                    $select .= '<option value="' . $value . '" ' . ($value == $row->absensi_status ? 'selected' : '') . '>' . strtoupper($value) . '</option>';
                }
                $select .= '
                        </select>
                    </div>';
                return $select;
            })
            ->rawColumns(['action', 'nama_peserta'])
            ->make(true);
    }
    public function simpan(Request $request, $idPamong)
    {
        DB::beginTransaction();
        try {
            $pertemuan = $request->pertemuan;
            $minta = $request->minta;
            $pamongPesertaId = $request->pamongPesertaId;
            $absensiPsPamong = new AbsensiPsPamong();
            $absensiPsPamong->nama = $pertemuan;
            $absensiPsPamong->pamong_id = $idPamong;
            $absensiPsPamong->save();

            $id = $absensiPsPamong->id;
            for ($i = 0; $i < count($pamongPesertaId); $i++) {
                $absensiPsPamongDetail = new AbsensiPsPamongDetail();
                $absensiPsPamongDetail->absensi_ps_pamong_id = $id;
                $absensiPsPamongDetail->pamong_peserta_id = $pamongPesertaId[$i];
                $absensiPsPamongDetail->status = $minta[$i];
                $absensiPsPamongDetail->waktu_absen = Carbon::now();
                $absensiPsPamongDetail->save();

                // store absensi nilai
                $pamong = Pamong::find($idPamong);
                $storeNilai = NilaiPeserta::storeAbsensiPamong($pamong, $pamongPesertaId[$i]);
                if ($storeNilai['status'] == false) {
                    return abort(500, $storeNilai['error']);
                }
            }
            DB::commit();
            return [
                'message' => 200,
                'data' => 'Data Berhasil Disimpan',
                'req' => $request->all()
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'message' => 500,
                'data' => 'Data Gagal Disimpan',
                'req' => $request->all(),
                'error' => $th->getMessage()
            ];
        }
    }
    public function formEdit($idPamong, $idAbsensiPsPamong)
    {
        $data = AbsensiPsPamong::Join('pamong', 'pamong.id', 'absensi_ps_pamong.pamong_id')
            ->where('absensi_ps_pamong.id', $idAbsensiPsPamong)
            ->select('absensi_ps_pamong.*')
            ->first();
        $pamong = Pamong::find($idPamong);
        return View('admin.absensi.pamong.form', compact('idPamong', 'idAbsensiPsPamong', 'data', 'pamong'));
    }
    public function dataEdit(Request $request, $idPamong, $idAbsensiPsPamong)
    {
        $apd = AbsensiPsPamong::find($idAbsensiPsPamong);
        $absensiDetail = PamongPeserta::join('peserta', 'peserta.id', 'pamong_peserta.peserta_id')
            ->leftJoin('absensi_ps_pamong_detail', function ($join) use ($apd) {
                $join->on('absensi_ps_pamong_detail.pamong_peserta_id', '=', 'pamong_peserta.id');
                $join->where('absensi_ps_pamong_detail.absensi_ps_pamong_id', $apd->id);
            })
            ->where('pamong_id', $idPamong)
            ->select('pamong_peserta.*', 'peserta.nama as nama_peserta', 'peserta.nim as nim_peserta', 'absensi_ps_pamong_detail.status as absensi_status');

        return DataTables::eloquent($absensiDetail)
            ->addIndexColumn()
            ->editColumn('nama_peserta', function ($row) {
                $content = "<div>$row->nama_peserta</div>";
                $content .= "<div class='text-bold'>$row->nim_peserta</div>";
                $content .= "<div class='text-bold'>" . $row->peserta->prodi->alias . "</div>";
                $content .= '<input type="hidden" name="pamongPesertaId[]" value="' . $row->id . '">';
                return $content;
            })
            ->addColumn('action', function ($row) {
                $select = '<div class="form-group">
                            <select class="form-control mintaku" name="minta[]">
                            <option value="">-- PILIH --</option>';
                foreach (\Helper::getEnumValues('absensi_ps_pamong_detail', 'status') as $key => $value) {
                    $select .= '<option value="' . $value . '" ' . ($value == $row->absensi_status ? 'selected' : '') . '>' . strtoupper($value) . '</option>';
                }
                $select .= '
                        </select>
                    </div>';
                return $select;
            })->rawColumns(['action', 'nama_peserta'])->make(true);
    }
    public function simpanEdit(Request $request, $idPamong, $idAbsensiPsPamong)
    {
        DB::beginTransaction();
        try {
            $pertemuan = $request->pertemuan;
            $minta = $request->minta;
            $pamongPesertaId = $request->pamongPesertaId;

            $update = AbsensiPsPamong::find($idAbsensiPsPamong);
            $update->nama = $pertemuan;
            $update->save();

            $id = $update->id;

            AbsensiPsPamongDetail::where('absensi_ps_pamong_id', $id)->delete();

            for ($i = 0; $i < count($pamongPesertaId); $i++) {
                $absensiPsPamongDetail = new AbsensiPsPamongDetail();
                $absensiPsPamongDetail->absensi_ps_pamong_id = $id;
                $absensiPsPamongDetail->pamong_peserta_id = $pamongPesertaId[$i];
                $absensiPsPamongDetail->status = $minta[$i];
                $absensiPsPamongDetail->save();

                // store absensi nilai
                $pamong = Pamong::find($idPamong);
                $storeNilai = NilaiPeserta::storeAbsensiPamong($pamong, $pamongPesertaId[$i]);
                if ($storeNilai['status'] == false) {
                    return abort(500, $storeNilai['error']);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 200,
                'data' => true,
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 500,
                'data' => $th->getMessage(),
                'req' => $request->all()
            ]);
        }
    }
    public function del($idPamong, $idAbsensiPsPamong)
    {
        DB::beginTransaction();
        try {
            $absensiPsPamongDetail = AbsensiPsPamongDetail::where('absensi_ps_pamong_id', $idAbsensiPsPamong)->get();

            foreach ($absensiPsPamongDetail as $key => $value) {
                $value->delete();
            }

            $delApd = AbsensiPsPamong::find($idAbsensiPsPamong);
            $delApd->delete();

            // store absensi nilai
            foreach ($absensiPsPamongDetail as $key => $value) {
                $pamong = Pamong::find($idPamong);
                $pamongPesertaId = $value->pamong_peserta_id;
                $storeNilai = NilaiPeserta::storeAbsensiPamong($pamong, $pamongPesertaId);
                if ($storeNilai['status'] == false) {
                    return abort(500, $storeNilai['error']);
                }

            }

            DB::commit();
            return [
                'message' => 200,
                'data' => 'Data Berhasil dihapus',
                'status' => true
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'message' => 500,
                'data' => 'Gagal Menghapus Data',
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }
}
