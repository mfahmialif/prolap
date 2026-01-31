<?php

namespace App\Http\Controllers\Api\Mobile\Absensi;

use App\Http\Controllers\Controller;
use App\Models\AbsensiPsPamongDetail;
use Illuminate\Http\Request;

class PamongController extends Controller
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

            $data = AbsensiPsPamongDetail::join('absensi_ps_pamong', 'absensi_ps_pamong.id', '=', 'absensi_ps_pamong_detail.absensi_ps_pamong_id')
                ->select('absensi_ps_pamong_detail.*')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->orWhere('absensi_ps_pamong.nama', 'LIKE', "%$search%");
                        $q->orWhere('absensi_ps_pamong_detail.status', 'LIKE', "%$search%");
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
                ->with('absensi.pamong', 'pamongPeserta.peserta')
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

            $data = AbsensiPsPamongDetail::join('absensi_ps_pamong', 'absensi_ps_pamong.id', '=', 'absensi_ps_pamong_detail.absensi_ps_pamong_id')
                ->select('absensi_ps_pamong_detail.*')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->orWhere('absensi_ps_pamong.nama', 'LIKE', "%$search%");
                        $q->orWhere('absensi_ps_pamong_detail.status', 'LIKE', "%$search%");
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

            $data = AbsensiPsPamongDetail::with('absensi.pamong', 'pamongPeserta.peserta')
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
}
