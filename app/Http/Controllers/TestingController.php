<?php

namespace App\Http\Controllers;

use App\Models\DPL;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Peserta;
use App\Models\PoskoDpl;
use App\Models\Pembayaran;
use App\Models\PoskoPeserta;
use Illuminate\Http\Request;
use App\Http\Services\Absensi;
use App\Http\Services\Message;
use App\Http\Services\WhatsApp;
use App\Http\Services\Mahasiswa;
use App\Http\Services\Penugasan;
use App\Models\AbsensiPsDplDetail;
use App\Models\PenilaianDplDetail;
use App\Http\Services\DosenService;
use App\Http\Services\NilaiPeserta;
use Illuminate\Support\Facades\Hash;
use App\Models\AbsensiPsPengawasDetail;
use App\Models\PenilaianPengawasDetail;
use Yajra\DataTables\Facades\DataTables;

class TestingController extends Controller
{
    public function index()
    {
        // $poskoPeserta = PoskoPeserta::find(51);
        // $penilaianDpl = $poskoPeserta->penilaianDpl;
        // foreach ($penilaianDpl as $key => $value) {
        //     PenilaianDplDetail::where('penilaian_dpl_id', $value->id)->delete();
        //     $value->delete();
        // }
        // $penilaianPengawas = $poskoPeserta->penilaianPengawas;
        // foreach ($penilaianPengawas as $key => $value) {
        //     PenilaianPengawasDetail::where('penilaian_pengawas_id', $value->id)->delete();
        //     $value->delete();
        // }
        
        // if(@$poskoPeserta->peserta->nilai){
        //     $poskoPeserta->peserta->nilai->delete();
        // }

        // AbsensiPsDplDetail::where('posko_peserta_id', $poskoPeserta->id)->delete();
        // AbsensiPsPengawasDetail::where('posko_peserta_id', $poskoPeserta->id)->delete();

        $cek = WhatsApp::wa_satuconnect([
            'type' => 'notif_wa_satuconnect',
            'phone' => '6282229872328',
            'message' => 'test'
        ]);

        dd($cek);

        // $storeAbsensi = NilaiPeserta::storeAbsensiDpl(PoskoDpl::find(1), 1);
        // dd($storeAbsensi);
        // $makanan = ['kebab', 'burger', 'sembarang'];

        // foreach ($makanan as $m) {
        //     echo $m;
        // }

        // for ($i = 0; $i < count($makanan); $i++) {
        //     echo $makanan[$i];
        // }
        // return Absensi::psDpl(4);
        // return Penugasan::dpl(4);
        $dpl = DPL::find(1);
        $poskoPeserta = [];
        foreach ($dpl->poskoDpl as $key => $poskoDpl) {
            $posko = $poskoDpl->posko;
            $poskoPeserta[] = $posko->poskoPeserta;
        }

        $poskoPeserta = PoskoPeserta::join('posko', 'posko.id', '=', 'posko_peserta.posko_id')
            ->join('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
            ->where('posko_dpl.dpl_id', 1)
            ->select('posko_peserta.*')
            ->with('peserta', 'posko')
            ->get();
        // ->toArray();
        dd($poskoPeserta[0]['posko']['nama']);
        return view('testing');
        // Get data Pembayaran dengan where alias prodi
        // $pembayaran = Pembayaran::join('peserta', 'peserta.id', '=', 'pembayaran.peserta_id')
        //     ->join('prodi', 'prodi.id', '=', 'peserta.prodi_id')
        //     ->where('prodi.alias', 'AS-HK')
        //     ->with('peserta', 'peserta.prodi')
        //     ->select('pembayaran.*')
        //     ->get();
        // // dd($pembayaran[0]->peserta->prodi->nama);
        // return response()->json($pembayaran);

        // $peserta = Peserta::query();
        // return DataTables::of($peserta)
        //         ->addIndexColumn()
        //         ->make(true);
        // dd(\Helper::getTheme());
        // $dosen = DosenService::all(false,false, false, false, false, [
        //     ['mst_dosen.id', 20]
        // ]);
        // dd($dosen);
        // \Helper::setTheme('light');
        // $getTheme = \Helper::getTheme();
        // dd($getTheme);
        // \Cookie::queue(\Cookie::forget('theme'));
        // $mahasiswa = Mahasiswa::kkn(null, null, null, null, null, [
        //     ["mst_mhs.nim", "202385320085"]
        // ]);
        // dd($mahasiswa);
        // $cookie = cookie('theme', 'dark', 24*60); // Name, Value, Minutes
        // return response('Cookie has been set')->cookie($cookie);
        // $userName = request()->cookie();
        // dd($userName);
        // return "User Name: $userName";
        // return 'testing';
    }


}