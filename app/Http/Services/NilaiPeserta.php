<?php
namespace App\Http\Services;

use App\Models\Nilai;
use App\Models\PenilaianPengawas;
use App\Models\PenilaianPengawasDetail;
use App\Models\Tahun;
use App\Models\Peserta;
use App\Models\PoskoDpl;
use App\Models\Pamong;
use App\Models\AbsensiPsDpl;
use App\Models\PenilaianDpl;
use App\Models\PoskoPeserta;
use App\Models\KomponenNilai;
use App\Models\PamongPeserta;
use App\Models\PoskoPengawas;
use App\Http\Services\BulkData;
use App\Models\AbsensiPsPamong;
use App\Models\AbsensiPsPengawas;
use App\Models\PenilaianPamong;
use App\Models\AbsensiPsDplDetail;
use App\Models\PenilaianDplDetail;
use App\Models\AbsensiPsPamongDetail;
use App\Models\PenilaianPamongDetail;
use App\Models\AbsensiPsPengawasDetail;

class NilaiPeserta
{
    /**
     * Store Nilai
     * @param integer $pesertaId
     * @return array
     */
    public static function store($pesertaId)
    {
        try {
            $peserta = Peserta::findOrFail($pesertaId);
            $nilai = [
                'dpl' => 0,
                'pengawas' => 0,
                'pamong' => 0
            ];

            $poskoPeserta = $peserta->poskoPeserta;
            $jumlahPosko = $poskoPeserta->count();

            if ($jumlahPosko > 0) {
                $prosentasePosko = 100 / $jumlahPosko;
                foreach ($poskoPeserta as $key => $poskoPesertaItem) {
                    // Penilaian DPL dari tabel penilaianDpl
                    $poskoDpl = PoskoDpl::where('posko_id', $poskoPesertaItem->posko_id)->get();
                    $jumlahDpl = $poskoDpl->count();
                    if ($jumlahDpl > 0) {
                        $prosentaseDpl = 100 / $jumlahDpl;
                        $nilaiDpl = 0;
                        foreach ($poskoDpl as $poskoDplItem) {
                            $penilaianDpl = PenilaianDpl::where([
                                ['posko_dpl_id', $poskoDplItem->id],
                                ['posko_peserta_id', $poskoPesertaItem->id],
                            ])->first();
                            $nilaiDpl += @$penilaianDpl->nilai * $prosentaseDpl / 100;
                        }
                        $nilai['dpl'] += $nilaiDpl * $prosentasePosko / 100;
                    }

                    // Penilaian Pengawas dari tabel penilaianPengawas
                    $poskoPengawas = PoskoPengawas::where('posko_id', $poskoPesertaItem->posko_id)->get();
                    $jumlahPengawas = $poskoPengawas->count();
                    if ($jumlahPengawas > 0) {
                        $prosentasePengawas = 100 / $jumlahPengawas;
                        $nilaiPengawas = 0;
                        foreach ($poskoPengawas as $poskoPengawasItem) {
                            $penilaianPengawas = PenilaianPengawas::where([
                                ['posko_pengawas_id', $poskoPengawasItem->id],
                                ['posko_peserta_id', $poskoPesertaItem->id],
                            ])->first();
                            $nilaiPengawas += @$penilaianPengawas->nilai * $prosentasePengawas / 100;
                        }
                        $nilai['pengawas'] += $nilaiPengawas * $prosentasePosko / 100;
                    }
                }
            }

            // penilaian pamong dari tabel PenilaianPamong
            $pamongPeserta = $peserta->pamongPeserta;
            $jumlahPamong = $pamongPeserta->count();
            if ($jumlahPamong > 0) {
                $prosentasePamong = 100 / $jumlahPamong;
                foreach ($pamongPeserta as $pamongPesertaItem) {
                    $nilaiPamong = 0;
                    foreach ($pamongPeserta as $pamongPesertaItem) {
                        $penilaianPamong = PenilaianPamong::where([
                            ['pamong_id', $pamongPesertaItem->pamong_id],
                            ['pamong_peserta_id', $pamongPesertaItem->id],
                        ])->first();
                        $nilaiPamong += @$penilaianPamong->nilai * $prosentasePamong / 100;
                    }

                    $nilai['pamong'] += $nilaiPamong * $prosentasePamong / 100;
                }

            }

            $nilaiAkhirPeserta = 0;
            foreach ($nilai as $key => $value) {
                $nilaiAkhirPeserta += $value * BulkData::bobotSupervisor[$key] / 100;
            }

            $storeNilai = Nilai::where('peserta_id', $pesertaId)->first();
            if (!$storeNilai) {
                $storeNilai = new Nilai();
                $storeNilai->peserta_id = $pesertaId;
            }
            $storeNilai->nilai = $nilaiAkhirPeserta;
            $storeNilai->save();

            return [
                'status' => true,
                'message' => 'Data tersimpan',
                'nilai' => $nilai,
                'nilaiAkhirPeserta' => $nilaiAkhirPeserta
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'status' => false,
                'message' => 'Gagal menyimpan data',
                'error' => $th->getMessage(),
            ];
        }
    }

    /**
     * storeAbsensi
     * @param PoskoDpl $poskoDpl
     * @param mixed $poskoPesertaId
     * @return array
     */
    public static function storeAbsensiDpl($poskoDpl, $poskoPesertaId)
    {
        try {
            $poskoPeserta = PoskoPeserta::find($poskoPesertaId);

            $jumlahAbsensi = AbsensiPsDpl::where('posko_dpl_id', $poskoDpl->id)->count();
            $jumlahHadir = AbsensiPsDplDetail::join('absensi_ps_dpl', 'absensi_ps_dpl_detail.absensi_ps_dpl_id', 'absensi_ps_dpl.id')
                ->where('absensi_ps_dpl.posko_dpl_id', $poskoDpl->id)
                ->where('absensi_ps_dpl_detail.status', 'Hadir')
                ->where('absensi_ps_dpl_detail.posko_peserta_id', $poskoPeserta->id)
                ->count();
            $nilaiAbsensi = $jumlahAbsensi > 0 ? round(($jumlahHadir / $jumlahAbsensi * 100), 2) : 0;

            $penilaian = PenilaianDpl::where([
                ['posko_dpl_id', $poskoDpl->id],
                ['posko_peserta_id', $poskoPeserta->id],
            ])->first();

            if (!$penilaian) {
                $penilaian = new PenilaianDpl();
                $penilaian->posko_dpl_id = $poskoDpl->id;
                $penilaian->posko_peserta_id = $poskoPeserta->id;
                $penilaian->nilai = 0;
                $penilaian->save();
            }

            $nilaiAkhir = 0;
            $komponenNilai = KomponenNilai::where('tahun_id', $poskoDpl->posko->tahun->id)->get()->where('jenis', 'dpl');
            foreach ($komponenNilai as $key => $value) {
                $penilaianDetail = PenilaianDplDetail::where('penilaian_dpl_id', $penilaian->id)
                    ->where('komponen_nilai_id', $value->id)->first();
                if (strtolower($value->nama) == "absensi") {
                    if (!$penilaianDetail) {
                        $penilaianDetail = new PenilaianDplDetail();
                        $penilaianDetail->penilaian_dpl_id = $penilaian->id;
                        $penilaianDetail->komponen_nilai_id = $value->id;
                    }

                    $penilaianDetail->nilai = $nilaiAbsensi;
                    $penilaianDetail->save();
                }

                $nilaiAkhir += @$penilaianDetail->nilai * $value->bobot / 100;
            }

            $penilaian->nilai = $nilaiAkhir;
            $penilaian->save();

            // store to sinkron with all nilai
            $store = self::store($poskoPeserta->peserta_id);
            if ($store['status'] == false) {
                abort(500, $store['error']);
            }
            return [
                'status' => true,
                'message' => 'Data tersimpan',
                'penilaian' => $penilaian,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * storeAbsensi
     * @param PoskoPengawas $poskoPengawas
     * @param mixed $poskoPesertaId
     * @return array
     */
    public static function storeAbsensiPengawas($poskoPengawas, $poskoPesertaId)
    {
        try {
            $poskoPeserta = PoskoPeserta::find($poskoPesertaId);

            $jumlahAbsensi = AbsensiPsPengawas::where('posko_pengawas_id', $poskoPengawas->id)->count();
            $jumlahHadir = AbsensiPsPengawasDetail::join('absensi_ps_pengawas', 'absensi_ps_pengawas_detail.absensi_ps_pengawas_id', 'absensi_ps_pengawas.id')
                ->where('absensi_ps_pengawas.posko_pengawas_id', $poskoPengawas->id)
                ->where('absensi_ps_pengawas_detail.status', 'Hadir')
                ->where('absensi_ps_pengawas_detail.posko_peserta_id', $poskoPeserta->id)
                ->count();
            $nilaiAbsensi = $jumlahAbsensi > 0 ? round(($jumlahHadir / $jumlahAbsensi * 100), 2) : 0;

            $penilaian = PenilaianPengawas::where([
                ['posko_pengawas_id', $poskoPengawas->id],
                ['posko_peserta_id', $poskoPeserta->id],
            ])->first();

            if (!$penilaian) {
                $penilaian = new PenilaianPengawas();
                $penilaian->posko_pengawas_id = $poskoPengawas->id;
                $penilaian->posko_peserta_id = $poskoPeserta->id;
                $penilaian->nilai = 0;
                $penilaian->save();
            }

            $nilaiAkhir = 0;
            $komponenNilai = KomponenNilai::where('tahun_id', $poskoPengawas->posko->tahun_id)->get()->where('jenis', 'pengawas');
            foreach ($komponenNilai as $key => $value) {
                $penilaianDetail = PenilaianPengawasDetail::where('penilaian_pengawas_id', $penilaian->id)
                    ->where('komponen_nilai_id', $value->id)->first();
                if (strtolower($value->nama) == "absensi") {
                    if (!$penilaianDetail) {
                        $penilaianDetail = new PenilaianPengawasDetail();
                        $penilaianDetail->penilaian_pengawas_id = $penilaian->id;
                        $penilaianDetail->komponen_nilai_id = $value->id;
                    }

                    $penilaianDetail->nilai = $nilaiAbsensi;
                    $penilaianDetail->save();
                }

                $nilaiAkhir += @$penilaianDetail->nilai * $value->bobot / 100;
            }

            $penilaian->nilai = $nilaiAkhir;
            $penilaian->save();

            // // store to sinkron with all nilai
            $store = self::store($poskoPeserta->peserta_id);
            if ($store['status'] == false) {
                abort(500, $store['error']);
            }
            return [
                'status' => true,
                'message' => 'Data tersimpan',
                'penilaian' => $penilaian,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    /**
     * storeAbsensi
     * @param Pamong $pamong
     * @param mixed $pamongPesertaId
     * @return array
     */
    public static function storeAbsensiPamong($pamong, $pamongPesertaId)
    {
        try {
            $pamongPeserta = PamongPeserta::find($pamongPesertaId);

            $jumlahAbsensi = AbsensiPsPamong::where('pamong_id', $pamong->id)->count();
            $jumlahHadir = AbsensiPsPamongDetail::join('absensi_ps_pamong', 'absensi_ps_pamong_detail.absensi_ps_pamong_id', 'absensi_ps_pamong.id')
                ->where('absensi_ps_pamong.pamong_id', $pamong->id)
                ->where('absensi_ps_pamong_detail.status', 'Hadir')
                ->where('absensi_ps_pamong_detail.pamong_peserta_id', $pamongPeserta->id)
                ->count();
            $nilaiAbsensi = $jumlahAbsensi > 0 ? round(($jumlahHadir / $jumlahAbsensi * 100), 2) : 0;

            $penilaian = PenilaianPamong::where([
                ['pamong_id', $pamong->id],
                ['pamong_peserta_id', $pamongPeserta->id],
            ])->first();

            if (!$penilaian) {
                $penilaian = new PenilaianPamong();
                $penilaian->pamong_id = $pamong->id;
                $penilaian->pamong_peserta_id = $pamongPeserta->id;
                $penilaian->nilai = 0;
                $penilaian->save();
            }

            $nilaiAkhir = 0;
            $komponenNilai = KomponenNilai::where('tahun_id', $pamong->tahun_id)->get()->where('jenis', 'pamong');
            ;
            foreach ($komponenNilai as $key => $value) {
                $penilaianDetail = PenilaianPamongDetail::where('penilaian_pamong_id', $penilaian->id)
                    ->where('komponen_nilai_id', $value->id)->first();

                if (strtolower($value->nama) == "absensi") {
                    if (!$penilaianDetail) {
                        $penilaianDetail = new PenilaianPamongDetail();
                        $penilaianDetail->penilaian_pamong_id = $penilaian->id;
                        $penilaianDetail->komponen_nilai_id = $value->id;
                    }

                    $penilaianDetail->nilai = $nilaiAbsensi;
                    $penilaianDetail->save();
                }

                $nilaiAkhir += @$penilaianDetail->nilai * $value->bobot / 100;
            }

            $penilaian->nilai = $nilaiAkhir;
            $penilaian->save();

            // store to sinkron with all nilai
            $store = self::store($pamongPeserta->peserta_id);
            if ($store['status'] == false) {
                abort(500, $store['error']);
            }
            return [
                'status' => true,
                'message' => 'Data tersimpan',
                'penilaian' => $penilaian,
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }
}

// Penilaian Pengawas dari kalkulasi absensi
// $nilaiPengawas = 0;
// $poskoPengawas = PoskoPengawas::where('posko_id', $poskoPesertaItem->posko_id)->get();
// $jumlahPengawas = $poskoPengawas->count();

// if ($jumlahPengawas > 0) {
//     $prosentasePengawas = 100 / $jumlahPengawas;
//     foreach ($poskoPengawas as $poskoPengawasItem) {
//         $jumlahAbsensi = $poskoPengawasItem->absensi->count();
//         $jumlahHadir = AbsensiPsPengawasDetail::join('absensi_ps_pengawas', 'absensi_ps_pengawas_detail.absensi_ps_pengawas_id', 'absensi_ps_pengawas.id')
//             ->where('absensi_ps_pengawas.posko_pengawas_id', $poskoPengawasItem->id)
//             ->where('absensi_ps_pengawas_detail.status', 'Hadir')
//             ->where('absensi_ps_pengawas_detail.posko_peserta_id', $poskoPesertaItem->id)
//             ->count();

//         if ($jumlahAbsensi > 0) {
//             $nilaiPengawas += (($jumlahHadir / $jumlahAbsensi) * 100) * $prosentasePengawas / 100;
//         }
//     }
//     $nilai['pengawas'] += $nilaiPengawas * $prosentasePosko / 100;
// }