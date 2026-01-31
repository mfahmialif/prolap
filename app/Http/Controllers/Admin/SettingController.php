<?php

namespace App\Http\Controllers\Admin;

use App\Http\Services\Tagihan;
use App\Models\Api;
use App\Models\Setting;
use App\Http\Services\Otp;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Services\Message;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::get()->pluck('value', 'slug');
        $fonnte = Api::where('type', 'notif_wa_fonnte')->first();
        $zenziva = Api::where('type', 'notif_wa_zenziva')->first();
        return view('admin.setting.index', compact('setting', 'fonnte', 'zenziva'));
    }

    public function save(Request $request)
    {
        try {
            \DB::beginTransaction();
            $dataValidated = $request->validate([
                'otp' => 'nullable',
                'isi_pesan_wa' => 'nullable',
                'vendor_notifikasi' => 'nullable',
                'userkey_zenziva' => 'nullable',
                'passkey_zenziva' => 'nullable',
                'passkey_fonnte' => 'nullable',
            ]);

            $setting = $request->only('vendor_notifikasi', 'isi_pesan_wa');
            foreach ($setting as $slug => $value) {
                Setting::where('slug', $slug)
                    ->update([
                        'value' => $value
                    ]);
            }

            Api::where('type', 'notif_wa_fonnte')
                ->update([
                    'token' => $request->passkey_fonnte
                ]);

            Api::where('type', 'notif_wa_zenziva')
                ->update([
                    'userkey' => $request->userkey_zenziva,
                    'token' => $request->passkey_zenziva
                ]);


            \DB::commit();
            return [
                "status" => true,
                "message" => 200,
                "data" => 'Berhasil update setting whatsapp',
                "req" => $request->all()
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                "status" => false,
                "message" => 500,
                "data" => $th->getMessage(),
            ];
        }
    }

    public function tes(Request $request)
    {
        try {
            //code...
            $request->validate([
                'nomor_hp' => 'required'
            ]);

            $send = Message::send([
                'nomor_hp' => $request->nomor_hp,
                'peserta_id' => null,
                'password' => 'prodidalwa',
            ]);

            if (!$send) {
                abort(500, 'Gagal kirim pesan');
            }

            return [
                "status" => true,
                "message" => 200,
                "data" => 'Berhasil',
                "req" => $request->all()
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                "status" => true,
                "message" => 500,
                "data" => $th->getMessage(),
                "req" => $request->all()
            ];
        }
    }

    public function simkeu(Request $request)
    {
        \DB::beginTransaction();
        try {
            $pembayaran = Pembayaran::all();
            foreach ($pembayaran as $key => $value) {
                $tagihan = Tagihan::kkn($value->peserta->nim, $value->peserta->tahun->kode, $value->jenis_pembayaran, $value->jumlah);
                if (!$tagihan->status) {
                    continue;
                }
                $value->simkeu_pembayaran_id = $tagihan->pembayaran->id;
                $value->save();
            }
            \DB::commit();
            return [
                "status" => true,
                "message" => 200,
                "data" => 'Berhasil Sinkron SIMKEU',
                "req" => $request->all(),
            ];
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            return [
                "status" => true,
                "message" => 500,
                "data" => $th->getMessage(),
                "req" => $request->all()
            ];
        }
    }
}
