<?php
namespace App\Http\Services;

use App\Models\AbsensiPsDpl;
use App\Models\AbsensiPsPamong;
use App\Models\AbsensiPsPengawas;
use App\Models\Peserta;
use App\Models\AbsensiPsDplDetail;
use App\Models\AbsensiPsPamongDetail;
use App\Models\AbsensiPsPengawasDetail;

class Absensi
{
    /**
     * get data absensi peserta yang diabsensikan oleh DPL
     * @param integer $pesertaId
     * @return object
     */
    public static function psDpl($pesertaId)
    {
        try {
            $peserta = Peserta::findOrFail($pesertaId);
            $poskoPeserta = $peserta->poskoPeserta;
            $response = [];

            $absensiDetail = [];
            for ($i = 0; $i < count($poskoPeserta); $i++) {
                $belumAbsen = 0;
                $absensi = AbsensiPsDpl::join('posko_dpl', 'posko_dpl.id', '=', 'absensi_ps_dpl.posko_dpl_id')
                    ->where('posko_dpl.posko_id', $poskoPeserta[$i]->posko_id)
                    ->select('absensi_ps_dpl.*')
                    ->get();

                foreach ($absensi as $key => $absensiItem) {
                    $pd = AbsensiPsDplDetail::where('posko_peserta_id', $poskoPeserta[$i]->id)
                        ->where('absensi_ps_dpl_id', $absensiItem->id)
                        ->orderBy('id', 'desc')
                        ->with('absensi', 'poskoPeserta', 'absensi.poskoDpl.dpl')
                        ->first();
                    if (!$pd) {
                        $belumAbsen++;
                    }
                    $absensiDetail[] = [
                        'data' => $pd,
                        'absensi' => $absensiItem
                    ];
                }

                $rekap = [];
                foreach (\Helper::getEnumValues('absensi_ps_dpl_detail', 'status') as $status) {
                    $rekap[$status] = AbsensiPsDplDetail::where('posko_peserta_id', $poskoPeserta[$i]->id)
                        ->where('status', $status)
                        ->count();
                }

                $rekap['Belum Absen'] += $belumAbsen;
                $response[] = (object) [
                    'posko' => $poskoPeserta[$i]->posko,
                    'absensi' => $absensiDetail,
                    'rekap' => $rekap
                ];
            }

            return (object) [
                'status' => true,
                'code' => 200,
                'message' => 'Success',
                'data' => $response
            ];
        } catch (\Throwable $th) {
            return (object) [
                'status' => false,
                'code' => 500,
                'message' => 'Failed',
                'data' => $th->getMessage()
            ];
        }
    }

    /**
     * get data absensi peserta yang diabsensikan oleh Pengawas
     * @param integer $pesertaId
     * @return object
     */
    public static function psPengawas($pesertaId)
    {
        try {
            $peserta = Peserta::findOrFail($pesertaId);
            $poskoPeserta = $peserta->poskoPeserta;
            $response = [];

            $absensiDetail = [];
            for ($i = 0; $i < count($poskoPeserta); $i++) {
                $belumAbsen = 0;
                $absensi = AbsensiPsPengawas::join('posko_pengawas', 'posko_pengawas.id', '=', 'absensi_ps_pengawas.posko_pengawas_id')
                    ->where('posko_pengawas.posko_id', $poskoPeserta[$i]->posko_id)
                    ->select('absensi_ps_pengawas.*')
                    ->get();

                foreach ($absensi as $key => $absensiItem) {
                    $pd = AbsensiPsPengawasDetail::where('posko_peserta_id', $poskoPeserta[$i]->id)
                        ->where('absensi_ps_pengawas_id', $absensiItem->id)
                        ->orderBy('id', 'desc')
                        ->with('absensi', 'poskoPeserta', 'absensi.poskoPengawas.pengawas')
                        ->first();

                    if (!$pd) {
                        $belumAbsen++;
                    }
                    $absensiDetail[] = [
                        'data' => $pd,
                        'absensi' => $absensiItem
                    ];
                }

                $rekap = [];
                foreach (\Helper::getEnumValues('absensi_ps_pengawas_detail', 'status') as $status) {
                    $rekap[$status] = AbsensiPsPengawasDetail::where('posko_peserta_id', $poskoPeserta[$i]->id)
                        ->where('status', $status)
                        ->count();
                }
                $rekap['Belum Absen'] += $belumAbsen;
                $response[] = (object) [
                    'posko' => $poskoPeserta[$i]->posko,
                    'absensi' => $absensiDetail,
                    'rekap' => $rekap
                ];
            }

            return (object) [
                'status' => true,
                'code' => 200,
                'message' => 'Success',
                'data' => $response
            ];
        } catch (\Throwable $th) {
            return (object) [
                'status' => false,
                'code' => 500,
                'message' => 'Failed',
                'data' => $th->getMessage()
            ];
        }
    }

    /**
     * get data absensi peserta yang diabsensikan oleh Pamong
     * @param integer $pesertaId
     * @return object
     */
    public static function psPamong($pesertaId)
    {
        try {
            $peserta = Peserta::findOrFail($pesertaId);
            $pamongPeserta = $peserta->pamongPeserta;
            $response = [];

            $absensiDetail = [];
            for ($i = 0; $i < count($pamongPeserta); $i++) {
                $belumAbsen = 0;
                $absensi = AbsensiPsPamong::where('pamong_id', $pamongPeserta[$i]->pamong_id)
                    ->get();

                foreach ($absensi as $key => $absensiItem) {
                    $pd = AbsensiPsPamongDetail::where('pamong_peserta_id', $pamongPeserta[$i]->id)
                        ->where('absensi_ps_pamong_id', $absensiItem->id)
                        ->orderBy('id', 'desc')
                        ->with('absensi', 'pamongPeserta', 'absensi.pamong')
                        ->first();

                    if (!$pd) {
                        $belumAbsen++;
                    }
                    $absensiDetail[] = [
                        'data' => $pd,
                        'absensi' => $absensiItem
                    ];
                }

                $rekap = [];
                foreach (\Helper::getEnumValues('absensi_ps_pamong_detail', 'status') as $status) {
                    $rekap[$status] = AbsensiPsPamongDetail::where('pamong_peserta_id', $pamongPeserta[$i]->id)
                        ->where('status', $status)
                        ->count();
                }
                $rekap['Belum Absen'] += $belumAbsen;
                $response[] = (object) [
                    'pamong' => $pamongPeserta[$i]->pamong,
                    'absensi' => $absensiDetail,
                    'rekap' => $rekap
                ];
            }

            return (object) [
                'status' => true,
                'code' => 200,
                'message' => 'Success',
                'data' => $response
            ];
        } catch (\Throwable $th) {
            return (object) [
                'status' => false,
                'code' => 500,
                'message' => 'Failed',
                'data' => $th->getMessage()
            ];
        }
    }
}