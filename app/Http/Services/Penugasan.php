<?php
namespace App\Http\Services;

use App\Models\PenugasanDpl;
use App\Models\PenugasanPamong;
use App\Models\Peserta;
use App\Models\PenugasanDplDetail;
use App\Models\PenugasanPamongDetail;

class Penugasan
{
    /**
     * get data penugasan peserta yang diabsensikan oleh DPL
     * @param integer $pesertaId
     * @return object
     */
    public static function dpl($pesertaId)
    {
        try {
            $peserta = Peserta::findOrFail($pesertaId);
            $poskoPeserta = $peserta->poskoPeserta;
            $response = [];

            for ($i = 0; $i < count($poskoPeserta); $i++) {
                $penugasan = PenugasanDpl::join('posko_dpl', 'posko_dpl.id', '=', 'penugasan_dpl.posko_dpl_id')
                    ->where('posko_dpl.posko_id', $poskoPeserta[$i]->posko_id)
                    ->select('penugasan_dpl.*')
                    ->get();

                $penugasanDetail = [];
                $rekap = [
                    'sudah' => 0,
                    'belum' => 0,
                ];
                foreach ($penugasan as $key => $penugasanItem) {
                    $pd = PenugasanDplDetail::where('posko_peserta_id', $poskoPeserta[$i]->id)
                        ->where('penugasan_dpl_id', $penugasanItem->id)
                        ->orderBy('id', 'desc')
                        ->with('penugasanDpl', 'poskoPeserta', 'penugasanDpl.poskoDpl.dpl')
                        ->first();
                    $penugasanDetail[] = [
                        'data' => $pd,
                        'penugasan' => $penugasanItem
                    ];

                    if ($pd) {
                        $rekap['sudah']++;
                    } else {
                        $rekap['belum']++;
                    }
                }

                $response[] = (object) [
                    'posko' => $poskoPeserta[$i]->posko,
                    'penugasan' => $penugasanDetail,
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
     * get data penugasan peserta yang diabsensikan oleh Pamong
     * @param integer $pesertaId
     * @return object
     */
    public static function pamong($pesertaId)
    {
        try {
            $peserta = Peserta::findOrFail($pesertaId);
            $pamongPeserta = $peserta->pamongPeserta;
            $response = [];

            for ($i = 0; $i < count($pamongPeserta); $i++) {

                $penugasan = PenugasanPamong::where('pamong_id', $pamongPeserta[$i]->pamong_id)
                    ->get();

                $penugasanDetail = [];
                $rekap = [
                    'sudah' => 0,
                    'belum' => 0,
                ];
                foreach ($penugasan as $key => $penugasanItem) {
                    $pd = PenugasanPamongDetail::where('pamong_peserta_id', $pamongPeserta[$i]->id)
                        ->where('penugasan_pamong_id', $penugasanItem->id)
                        ->orderBy('id', 'desc')
                        ->with('penugasanPamong', 'pamongPeserta', 'penugasanPamong.pamong')
                        ->first();
                    $penugasanDetail[] = [
                        'data' => $pd,
                        'penugasan' => $penugasanItem
                    ];

                    if ($pd) {
                        $rekap['sudah']++;
                    } else {
                        $rekap['belum']++;
                    }
                }

                $response[] = (object) [
                    'pamong' => $pamongPeserta[$i]->pamong,
                    'penugasan' => $penugasanDetail,
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