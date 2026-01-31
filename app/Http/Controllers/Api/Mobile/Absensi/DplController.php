<?php

namespace App\Http\Controllers\Api\Mobile\Absensi;

use App\Http\Controllers\Controller;
use App\Http\Services\NilaiPeserta;
use App\Models\AbsensiPsDpl;
use App\Models\PoskoDpl;
use App\Models\AbsensiPsDplDetail;
use App\Models\AbsensiPsPamong;
use App\Models\AbsensiPsPamongDetail;
use App\Models\AbsensiPsPengawas;
use App\Models\AbsensiPsPengawasDetail;
use App\Models\Pamong;
use App\Models\PamongPeserta;
use App\Models\Posko;
use App\Models\PoskoPengawas;
use App\Models\PoskoPeserta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DplController extends Controller
{
    public function show(Request $request)
    {
        try {
            $dataValidated = $request->validate([
                'limit' => 'nullable',
                'offset' => 'nullable',
                'order' => 'nullable',
                'dir' => 'nullable',
                'search' => 'nullable',
                'where' => 'nullable',
            ]);

            $offset = isset($dataValidated['offset']) ? $dataValidated['offset'] : null;
            $limit = isset($dataValidated['limit']) ? $dataValidated['limit'] : null;
            $search = isset($dataValidated['search']) ? $dataValidated['search'] : null;
            $order = isset($dataValidated['order']) ? $dataValidated['order'] : null;
            $dir = isset($dataValidated['dir']) ? $dataValidated['dir'] : null;
            $where = isset($dataValidated['where']) ? $dataValidated['where'] : null;

            $data = AbsensiPsDplDetail::join('absensi_ps_dpl', 'absensi_ps_dpl.id', '=', 'absensi_ps_dpl_detail.absensi_ps_dpl_id')
                ->select('absensi_ps_dpl_detail.*')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->orWhere('absensi_ps_dpl.nama', 'LIKE', "%$search%");
                        $q->orWhere('absensi_ps_dpl_detail.status', 'LIKE', "%$search%");
                    });
                })
                ->when($where, function ($q) use ($where) {
                    $where = json_decode($where);
                    $q->where($where);
                })
                ->when($order, function ($q) use ($order, $dir) {
                    $q->orderBy($order, $dir);
                })
                ->when($offset, function ($q) use ($offset) {
                    $q->offset($offset);
                })
                ->when($limit, function ($q) use ($limit) {
                    $q->limit($limit);
                })
                ->with('absensi.poskoDpl.posko', 'absensi.poskoDpl.dpl', 'poskoPeserta.peserta')
                ->get();

            $data = [
                "status" => true,
                "code" => 200,
                "message" => "success",
                'count' => $data->count(),
                "data" => $data,
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "status" => false,
                "data" => [],
                "message" => $th->getMessage(),
                "code" => 500,
            ];
            return $data;
        }
    }

    public function count(Request $request)
    {
        try {
            $dataValidated = $request->validate([
                'limit' => 'nullable',
                'offset' => 'nullable',
                'order' => 'nullable',
                'dir' => 'nullable',
                'search' => 'nullable',
                'where' => 'nullable',
            ]);

            $offset = isset($dataValidated['offset']) ? $dataValidated['offset'] : null;
            $limit = isset($dataValidated['limit']) ? $dataValidated['limit'] : null;
            $search = isset($dataValidated['search']) ? $dataValidated['search'] : null;
            $order = isset($dataValidated['order']) ? $dataValidated['order'] : null;
            $dir = isset($dataValidated['dir']) ? $dataValidated['dir'] : null;
            $where = isset($dataValidated['where']) ? $dataValidated['where'] : null;

            $data = AbsensiPsDplDetail::join('absensi_ps_dpl', 'absensi_ps_dpl.id', '=', 'absensi_ps_dpl_detail.absensi_ps_dpl_id')
                ->select('absensi_ps_dpl_detail.*')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->orWhere('absensi_ps_dpl.nama', 'LIKE', "%$search%");
                        $q->orWhere('absensi_ps_dpl_detail.status', 'LIKE', "%$search%");
                    });
                })
                ->when($where, function ($q) use ($where) {
                    $where = json_decode($where);
                    $q->where($where);
                })
                ->when($order, function ($q) use ($order, $dir) {
                    $q->orderBy($order, $dir);
                })
                ->when($offset, function ($q) use ($offset) {
                    $q->offset($offset);
                })
                ->when($limit, function ($q) use ($limit) {
                    $q->limit($limit);
                })
                ->count();

            $data = [
                "status" => true,
                "code" => 200,
                "message" => "success",
                "data" => $data,
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "status" => false,
                "data" => [],
                "message" => $th->getMessage(),
                "code" => 500,
            ];
            return $data;
        }
    }

    public function find(Request $request)
    {
        try {
            $dataValidated = $request->validate([
                'id' => 'nullable',
            ]);

            $data = AbsensiPsDplDetail::with('absensi.poskoDpl.posko', 'absensi.poskoDpl.dpl', 'poskoPeserta.peserta')
                ->findOrFail($dataValidated['id']);

            $data = [
                "status" => true,
                "code" => 200,
                "message" => "success",
                "data" => $data,
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "status" => false,
                "data" => [],
                "message" => $th->getMessage(),
                "code" => 500,
            ];
            return $data;
        }
    }
    function data(Request $request)
    {
        //absensiPsDpl
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'role' => 'required|string'
            ]);
            if ($validated['role'] == 'dpl') {
                $absensi = AbsensiPsDpl::join('posko_dpl', 'posko_dpl.id', '=', 'absensi_ps_dpl.posko_dpl_id')
                    ->join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
                    ->where('posko_dpl.dpl_id', $request->id)
                    ->select('absensi_ps_dpl.*', 'posko.nama as nama_posko')->orderBy('id', 'desc')->limit(10)->get();
            } elseif ($validated['role'] == 'pengawas') {
                $absensi = AbsensiPsPengawas::join('posko_pengawas', 'posko_pengawas.id', '=', 'absensi_ps_pengawas.posko_pengawas_id')
                    ->join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
                    ->where('posko_pengawas.pengawas_id', $validated['id'])
                    ->select('absensi_ps_pengawas.*', 'posko.nama as nama_posko')->orderBy('id', 'desc')->limit(10)->get();
            } else {
                $absensi = AbsensiPsPamong::join('pamong', 'pamong.id', '=', 'absensi_ps_pamong.pamong_id')
                    ->where('pamong_id', $validated['id'])
                    ->select('absensi_ps_pamong.*', 'posko.nama as nama_posko')
                    ->orderBy('id', 'desc')->limit(10)
                    ->get();
            }

            return [
                'status' => true,
                'data' => $absensi,
                'code' => 200,
                'request' => $request->all(),
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'data' => [],
                'code' => 500,
                'message' => $th->getMessage()
            ];
        }
    }
    function dataAll(Request $request)
    {

        try {
            $validate = $request->validate([
                'id' => 'required|integer',
                'role' => 'required|string'
            ]);
            if ($validate['role'] == 'dpl') {
                $absensi = AbsensiPsDpl::join('posko_dpl', 'posko_dpl.id', '=', 'absensi_ps_dpl.posko_dpl_id')
                    ->join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
                    ->where('posko_dpl.dpl_id', $request->id)
                    ->select('absensi_ps_dpl.*', 'posko.nama as nama_posko')->orderBy('id', 'desc')->get();
            } elseif ($validate['role'] == 'pengawas') {
                $absensi = AbsensiPsPengawas::join('posko_pengawas', 'posko_pengawas.id', '=', 'absensi_ps_pengawas.posko_pengawas_id')
                    ->join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
                    ->where('posko_pengawas.pengawas_id', $validate['id'])
                    ->select('absensi_ps_pengawas.*', 'posko.nama as nama_posko')->orderBy('id', 'desc')
                    ->get();
            } else {
                $absensi = AbsensiPsPamong::join('pamong', 'pamong.id', '=', 'absensi_ps_pamong.pamong_id')
                    ->where('pamong_id', $validate['id'])
                    ->select('absensi_ps_pamong.*', 'posko.nama as nama_posko')
                    ->orderBy('id', 'desc')
                    ->get();
            }

            return [
                'status' => true,
                'data' => $absensi,
                'code' => 200,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
    function poskoPeserta(Request $request)
    {
        try {
            $validation = $request->validate([
                'id' => 'required|integer',
                'role' => 'required|string'
            ]);
            if ($validation['role'] == 'dpl') {
                $peserta = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
                    ->join('posko', 'posko.id', '=', 'posko_peserta.posko_id')
                    ->join('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
                    ->where('posko_dpl.dpl_id', $validation['id'])
                    ->select('posko_peserta.*', 'peserta.nama as nama_peserta', 'peserta.jenis as jenis_kkn', 'peserta.tanggal_lahir as t_lahir', 'peserta.nomor_hp as hp', 'posko.nama as nama_posko')
                    ->with('peserta', 'posko')
                    ->get();
            } else if ($validation['role'] == 'pengawas') {
                $peserta = PoskoPeserta::join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
                    ->join('posko', 'posko.id', '=', 'posko_peserta.posko_id')
                    ->join('posko_pengawas', 'posko_pengawas.posko_id', '=', 'posko.id')
                    ->where('posko_pengawas.pengawas_id', $validation['id'])
                    ->select('posko_peserta.*', 'peserta.nama as nama_peserta', 'peserta.jenis as jenis_kkn', 'peserta.tanggal_lahir as t_lahir', 'peserta.nomor_hp as hp', 'posko.nama as nama_posko')
                    ->with('peserta', 'posko')
                    ->get();
            } else {
                $peserta = PamongPeserta::join('peserta', 'peserta.id', '=', 'pamong_peserta.peserta_id')
                    ->join('pamong', 'pamong.id', '=', 'pamong_peserta.pamong_id')
                    ->where('pamong_id', $validation['id'])
                    ->select('pamong_peserta.*', 'peserta.nama as nama_peserta', 'pamong.nama as nama_pamong')
                    ->get();
            }


            return [
                'status' => true,
                'data' => $peserta,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
    function posko(Request $request)
    {
        try {
            $validasi = $request->validate([
                'id' => 'required|integer',
                'role' => 'required|string'
            ]);
            if ($validasi['role'] == 'dpl') {
                $posko = Poskodpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
                    ->where('dpl_id', $validasi['id'])
                    ->select('posko_dpl.*', 'posko.nama as nama_posko', 'posko.lokasi as nama_lokasi')
                    ->get();
            } elseif ($validasi['role'] == 'pengawas') {
                $posko = PoskoPengawas::join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
                    ->where('pengawas_id', $validasi['id'])
                    ->select('posko_pengawas.*', 'posko.nama as nama_posko', 'posko.lokasi as nama_lokasi')
                    ->get();
            } else {
                $posko = Pamong::where('id', $validasi['id'])->get();
            }


            return [
                'status' => true,
                'data' => $posko,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'data' => [],
                'message' => 'error'
            ];
        }
    }
    function listPertemuan(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'role' => 'required|string'
            ]);
            if ($validated['role'] == 'dpl') {
                $data = AbsensiPsDpl::join('posko_dpl', 'posko_dpl.id', '=', 'absensi_ps_dpl.posko_dpl_id')
                    ->join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
                    ->where('posko_dpl_id', $validated['id'])
                    ->select('absensi_ps_dpl.*', 'posko.nama as nama_posko')
                    ->get();
            } elseif ($validated['role'] == 'pengawas') {
                $data = AbsensiPsPengawas::join('posko_pengawas', 'posko_pengawas.id', '=', 'absensi_ps_pengawas.posko_pengawas_id')
                    ->join('posko', 'posko.id', '=', 'posko_pengawas.posko_id')
                    ->where('absensi_ps_pengawas.posko_pengawas_id', $validated['id'])
                    ->select('absensi_ps_pengawas.*', 'posko.nama as nama_posko')
                    ->get();
            } else {
                $data = AbsensiPsPamong::join('pamong', 'pamong.id', '=', 'absensi_ps_pamong.pamong_id')
                    ->where('pamong_id', $validated['id'])
                    ->select('absensi_ps_pengawas.*', 'pamong.nama as nama_pamong')
                    ->get();
            }
            return [
                'status' => true,
                'data' => $data,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
    function simpanPertemuan(Request $request)
    {
        try {
            $validasi = $request->validate([
                'pertemuan' => 'required|string',
                'posko_dpl_id' => 'required|integer',
                'role' => 'required|string'
            ]);
            if ($validasi['role'] == 'dpl') {
                $pertemuan = new AbsensiPsDpl();
                $pertemuan->posko_dpl_id = $request->posko_dpl_id;
                $pertemuan->nama = $request->pertemuan;
                $pertemuan->save();
            } elseif ($validasi['role'] == 'pengawas') {
                $pertemuan = new AbsensiPsPengawas();
                $pertemuan->posko_pengawas_id = $request->posko_dpl_id;
                $pertemuan->nama = $request->pertemuan;
                $pertemuan->save();
            } else {
                $pertemuan = new AbsensiPsPamong();
                $pertemuan->pamong_id = $request->posko_dpl_id;
                $pertemuan->nama = $request->pertemuan;
                $pertemuan->save();
            }
            return [
                'status' => true,
                'data' => [],
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            Log::error($th);
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
    function daftarPeserta(Request $request)
    {
        Log::info('data', $request->all());
        try {
            $validasi = $request->validate([
                'idPosko' => 'required|integer',
                'role' => 'required|string'
            ]);

            if ($validasi['role'] == 'dpl' || $validasi['role'] == 'pengawas') {
                $data = PoskoPeserta::join('posko', 'posko.id', '=', 'posko_peserta.posko_id')
                    ->join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
                    ->where('posko_id', $validasi['idPosko'])
                    ->select('posko_peserta.*', 'peserta.nama as nama_peserta')
                    ->get();
            } else {
                $data = PamongPeserta::join('pamong', 'pamong.id', '=', 'pamong_peserta.pamong_id')
                    ->join('peserta', 'peserta.id', '=', 'pamong_peserta.peserta_id')
                    ->where('pamong_id', $validasi['idPosko'])
                    ->select('pamong_peserta.*', 'peserta.nama as nama_peserta')
                    ->get();
            }
            return [
                'status' => true,
                'data' => $data,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            Log::error($th);
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
    function simpanPeserta(Request $request)
    {
        \DB::beginTransaction();
        try {
            $validate = $request->validate([
                'absensi_ps_dpl_id' => 'required|integer',
                'posko_peserta_id' => 'required|integer',
                'status' => 'required|string',
                'role' => 'required|string'
            ]);
            if ($validate['role'] == 'dpl') {
                $absensi = AbsensiPsDplDetail::where('absensi_ps_dpl_id', $validate['absensi_ps_dpl_id'])
                    ->where('posko_peserta_id', $validate['posko_peserta_id'])->first();
                if(!$absensi){
                    $absensi = new AbsensiPsDplDetail();
                    $absensi->absensi_ps_dpl_id = $validate['absensi_ps_dpl_id'];
                    $absensi->posko_peserta_id = $validate['posko_peserta_id'];
                }
                $absensi->status = $validate['status'];
                $absensi->waktu_absen = Carbon::now();
                $absensi->save();

                $absensiDpl = AbsensiPsDpl::find($validate['absensi_ps_dpl_id']);
                $poskoDpl = PoskoDpl::find($absensiDpl->posko_dpl_id);
                $nilai = NilaiPeserta::storeAbsensiDpl($poskoDpl, $validate['posko_peserta_id']);
                if ($nilai['status'] == false) {
                    return abort(500, $nilai['error']);
                }
            } elseif ($validate['role'] == 'pengawas') {
                $absensi = AbsensiPsPengawasDetail::where('absensi_ps_pengawas_id', $validate['absensi_ps_dpl_id'])
                    ->where('posko_peserta_id', $validate['posko_peserta_id'])->first();
                if(!$absensi){
                    $absensi = new AbsensiPsPengawasDetail();
                    $absensi->absensi_ps_pengawas_id = $validate['absensi_ps_dpl_id'];
                    $absensi->posko_peserta_id = $validate['posko_peserta_id'];
                }
                $absensi->status = $validate['status'];
                $absensi->waktu_absen = Carbon::now();
                $absensi->save();

                $absensiPengawas = AbsensiPsPengawas::find($validate['absensi_ps_dpl_id']);
                $poskoPengawas = PoskoPengawas::find($absensiPengawas->posko_pengawas_id);
                $nilai = NilaiPeserta::storeAbsensiPengawas($poskoPengawas, $validate['posko_peserta_id']);
                if ($nilai['status'] == false) {
                    return abort(500, $nilai['error']);
                }
            } else {
                $absensi = AbsensiPsPamongDetail::where('absensi_ps_pamong_id', $validate['absensi_ps_dpl_id'])
                    ->where('pamong_peserta_id', $validate['posko_peserta_id'])->first();
                if(!$absensi){
                    $absensi = new AbsensiPsPamongDetail();
                    $absensi->absensi_ps_pamong_id = $validate['absensi_ps_dpl_id'];
                    $absensi->pamong_peserta_id = $validate['posko_peserta_id'];
                }
                $absensi->status = $validate['status'];
                $absensi->waktu_absen = Carbon::now();
                $absensi->save();

                $absensiPamong = AbsensiPsPamong::find($validate['absensi_ps_dpl_id']);
                $pamong = Pamong::find($absensiPamong->pamong_id);
                $nilai = NilaiPeserta::storeAbsensiPamong($pamong, $validate['posko_peserta_id']);
                if ($nilai['status'] == false) {
                    return abort(500, $nilai['error']);
                }
            }

            \DB::commit();
            return [
                'status' => true,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
    function absenSemua(Request $request)
    {
        try {
            \DB::beginTransaction();
            $validate = $request->validate([
                'id_absensi_ps' => 'required|integer',
                'posko_peserta_id' => 'required|array',
                'posko_peserta_id.*' => 'integer',
                'status' => 'required|string',
                'role' => 'required|string'
            ]);
            if ($validate['role'] == 'dpl') {
                for ($i = 0; $i < count($validate['posko_peserta_id']); $i++) {

                    $absensi = AbsensiPsDplDetail::where('absensi_ps_dpl_id', $validate['id_absensi_ps'])
                        ->where('posko_peserta_id', $validate['posko_peserta_id'][$i])->first();
                    
                    if(!$absensi){
                        $absensi = new AbsensiPsDplDetail();
                        $absensi->absensi_ps_dpl_id = $validate['id_absensi_ps'];
                        $absensi->posko_peserta_id = $validate['posko_peserta_id'][$i];
                    }
                    $absensi->status = $validate['status'];
                    $absensi->waktu_absen = Carbon::now();
                    $absensi->save();

                    $absensiPs = AbsensiPsDpl::find($validate['id_absensi_ps']);
                    $poskoDpl = PoskoDpl::find($absensiPs->posko_dpl_id);
                    $nilai = NilaiPeserta::storeAbsensiDpl($poskoDpl, $validate['posko_peserta_id'][$i]);
                    if ($nilai['status'] == false) {
                        return abort(500, $nilai['error']);
                    }
                }
            } elseif ($validate['role'] == 'pengawas') {
                for ($i = 0; $i < count($validate['posko_peserta_id']); $i++) {
                    $absensi = AbsensiPsPengawasDetail::where('absensi_ps_pengawas_id',$validate['id_absensi_ps'])
                        ->where('posko_peserta_id', $validate['posko_peserta_id'][$i])->first();

                    if(!$absensi){
                        $absensi = new AbsensiPsPengawasDetail();
                        $absensi->absensi_ps_pengawas_id = $validate['id_absensi_ps'];
                        $absensi->posko_peserta_id = $validate['posko_peserta_id'][$i];
                    }
                    $absensi->status = $validate['status'];
                    $absensi->waktu_absen = Carbon::now();
                    $absensi->save();

                    $absensiPs = AbsensiPsPengawas::find($validate['id_absensi_ps']);
                    $poskoPengawas = PoskoPengawas::find($absensiPs->posko_pengawas_id);
                    $nilai = NilaiPeserta::storeAbsensiPengawas($poskoPengawas, $validate['posko_peserta_id'][$i]);
                    if ($nilai['status'] == false) {
                        return abort(500, $nilai['error']);
                    }
                }
            } else {
                for ($i = 0; $i < count($validate['posko_peserta_id']); $i++) {
                    $absensi = AbsensiPsPamongDetail::where('absensi_ps_pamong_id', $validate['id_absensi_ps'])
                        ->where('pamong_peserta_id',$validate['posko_peserta_id'] )->first();

                    if(!$absensi){
                        $absensi = new AbsensiPsPamongDetail();
                        $absensi->absensi_ps_pamong_id = $validate['id_absensi_ps'];
                        $absensi->pamong_peserta_id = $validate['posko_peserta_id'];
                    }
                    $absensi->status = $validate['status'];
                    $absensi->waktu_absen = Carbon::now();
                    $absensi->save();

                    $absensiPs = AbsensiPsPamong::find($validate['id_absensi_ps']);
                    $pamong = Pamong::find($absensiPs->pamong_id);
                    $nilai = NilaiPeserta::storeAbsensiPamong($pamong, $validate['posko_peserta_id'][$i]);
                    if ($nilai['status'] == false) {
                        return abort(500, $nilai['error']);
                    }
                }
            }

            \DB::commit();
            return [
                'status' => true,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            Log::error($th);
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
    function dataAbsenEdit(Request $request)
    {
        try {
            $validate = $request->validate([
                'idPs' => 'required|integer',
                'role' => 'required|string'
            ]);
            if ($validate['role'] == 'dpl') {
                $absensi = AbsensiPsDplDetail::join('posko_peserta', 'posko_peserta.id', '=', 'absensi_ps_dpl_detail.posko_peserta_id')
                    ->join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
                    ->where('absensi_ps_dpl_id', $validate['idPs'])
                    ->select('absensi_ps_dpl_detail.*', 'peserta.nama as nama_peserta')
                    ->get();
            } elseif ($validate['role'] == 'pengawas') {
                $absensi = AbsensiPsPengawasDetail::join('posko_peserta', 'posko_peserta.id', '=', 'absensi_ps_pengawas_detail.posko_peserta_id')
                    ->join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
                    ->where('absensi_ps_pengawas_id', $validate['idPs'])
                    ->select('absensi_ps_pengawas_detail.*', 'peserta.nama as nama_peserta')
                    ->get();
            } else {
                $absensi = AbsensiPsPamongDetail::join('pamong_peserta', 'pamong_peserta.id', '=', 'absensi_ps_pamong_detail.pamong_peserta_id')
                    ->join('peserta', 'peserta.id', '=', 'pamong_peserta.peserta_id')
                    ->where('absensi_ps_pamong_id', $validate['idPs'])
                    ->select('absensi_ps_pamong_detail.*', 'peserta.nama as nama_peserta')
                    ->get();
            }
            return [
                'status' => true,
                'data' => $absensi,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage(),
            ];
        }
    }
    function editAbsensi(Request $request)
    {
        \DB::beginTransaction();
        try {
            $validasi = $request->validate([
                'id' => 'required|integer',
                'status' => 'required|string',
                'role' => 'required|string'
            ]);
            if ($validasi['role'] == 'dpl') {
                $absensi = AbsensiPsDplDetail::find($validasi['id']);
                $absensi->status = $validasi['status'];
                $absensi->save();

                $absensiDpl = AbsensiPsDpl::find($absensi->absensi_ps_dpl_id);
                $poskoDpl = PoskoDpl::find($absensiDpl->posko_dpl_id);
                $nilaiDpl = NilaiPeserta::storeAbsensiDpl($poskoDpl, $absensiDpl->posko_peserta_id);
                if ($nilaiDpl['status'] == false) {
                    return abort(500, $nilaiDpl['error']);
                }
            } elseif ($validasi['role'] == 'pengawas') {
                $absensi = AbsensiPsPengawasDetail::find($validasi['id']);
                $absensi->status = $validasi['status'];
                $absensi->save();

                $absensiPengawas = AbsensiPsPengawas::find($absensi->absensi_ps_pengawas_id);
                $poskoPengawas = PoskoPengawas::find($absensiPengawas->posko_pengawas_id);
                $nilaiPengawas = NilaiPeserta::storeAbsensiPengawas($poskoPengawas, $absensi->posko_peserta_id);
                if ($nilaiPengawas['status'] == false) {
                    return abort(500, $nilaiPengawas['error']);
                }
            } else {
                $absensi = AbsensiPsPamongDetail::find($validasi['id']);
                $absensi->status = $validasi['status'];
                $absensi->save();

                $absensiPamong = AbsensiPsPamong::find($absensi->absensi_ps_pamong_id);
                $pamongPeserta = Pamong::find($absensiPamong->pamong_id);
                $nilaiPamong = NilaiPeserta::storeAbsensiPamong($pamongPeserta, $absensi->pamong_peserta_id);
                if ($nilaiPamong['status'] == false) {
                    return abort(500, $nilaiPamong['error']);
                }
            }

            \DB::commit();
            return [
                'status' => true,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
    function hapusAbsen(Request $request)
    {
        try {
            \DB::beginTransaction();

            $validasi = $request->validate([
                'id' => 'required|integer',
                'role' => 'required|string'
            ]);
            if ($validasi['role'] == 'dpl') {
                $absensiPsDplDetail = AbsensiPsDplDetail::where('absensi_ps_dpl_id', $validasi['id'])->get();

                foreach ($absensiPsDplDetail as $key => $value) {
                    $value->delete();
                }

                $delApd = AbsensiPsDpl::find($validasi['id']);
                $poskoDplId = $delApd->posko_dpl_id;
                $delApd->delete();

                // store absensi nilai
                foreach ($absensiPsDplDetail as $key => $value) {
                    $poskoDpl = PoskoDpl::find($poskoDplId);
                    $poskoPesertaId = $value->posko_peserta_id;

                    $value->delete();
                    $storeNilai = NilaiPeserta::storeAbsensiDpl($poskoDpl, $poskoPesertaId);
                    if ($storeNilai['status'] == false) {
                        return abort(500, $storeNilai['error']);
                    }
                }
            } elseif ($validasi['role'] == 'pengawas') {
                $absensiPsPengawasDetail = AbsensiPsPengawasDetail::where('absensi_ps_pengawas_id', $validasi['id'])->get();

                foreach ($absensiPsPengawasDetail as $key => $value) {
                    $value->delete();
                }

                $delApd = AbsensiPsPengawas::find($validasi['id']);
                $idPoskoPengawas = $delApd->posko_pengawas_id;
                $delApd->delete();

                // store absensi nilai
                foreach ($absensiPsPengawasDetail as $key => $value) {
                    $poskoPengawas = PoskoPengawas::find($idPoskoPengawas);
                    $poskoPesertaId = $value->posko_peserta_id;
                    $storeNilai = NilaiPeserta::storeAbsensiPengawas($poskoPengawas, $poskoPesertaId);
                    if ($storeNilai['status'] == false) {
                        return abort(500, $storeNilai['error']);
                    }
                }
            } else {
                $absensiPsPamongDetail = AbsensiPsPamongDetail::where('absensi_ps_pamong_id', $validasi['id'])->get();

                foreach ($absensiPsPamongDetail as $key => $value) {
                    $value->delete();
                }

                $delApd = AbsensiPsPamong::find($validasi['id']);
                $idPamong = $delApd->pamong_id;
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
            }

            \DB::commit();
            return [
                'status' => true,
                'message' => 'success'
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            log::error($th->getMessage());
            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }
    }
    function cariMahasiswa(Request $request)
    {
        try {

            $validasi = $request->validate([
                'nama' => 'nullable|string',
                'id' => 'required|integer',
                'role' => 'required|string'
            ]);
            $nama = $validasi['nama'];
            if ($validasi['role'] == 'dpl') {

                $data = PoskoPeserta::join('posko', 'posko.id', '=', 'posko_peserta.posko_id')
                    ->join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
                    ->join('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
                    ->where('posko_dpl.dpl_id', $validasi['id'])
                    ->when($nama, function ($query, $nama) {
                        return $query->where('peserta.nama', 'LIKE', "%$nama%");
                    })
                    ->select('posko_peserta.*', 'peserta.nama as nama_peserta', 'posko.nama as nama_posko')
                    ->get();
            } elseif ($validasi['role'] == 'pengawas') {
                $data = PoskoPeserta::join('posko', 'posko.id', '=', 'posko_peserta.posko_id')
                    ->join('peserta', 'peserta.id', '=', 'posko_peserta.peserta_id')
                    ->join('posko_pengawas', 'posko_pengawas.posko_id', '=', 'posko.id')
                    ->where('posko_pengawas.pengawas_id', $validasi['id'])
                    ->where('peserta.nama', 'LIKE', "%$nama%")
                    ->select('posko_peserta.*', 'peserta.nama as nama_peserta', 'posko.nama as nama_posko')
                    ->get();
            } else {
                $data = PamongPeserta::join('peserta', 'peserta.id', '=', 'pamong_peserta.pamong_id')
                    ->where('pamong_id', $validasi['id'])
                    ->where('peserta.nama', 'LIKE', "%$nama%")
                    ->select('pamong_peserta.*', 'peserta.nama as nama_peserta', 'pamong.nama as nama_pamong')
                    ->get();
            }
            return [
                'status' => true,
                'data' => $data,
                'message' => 'success',
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return [
                'status' => false,
                'data' => [],
                'message' => $th->getMessage()
            ];
        }
    }
}
