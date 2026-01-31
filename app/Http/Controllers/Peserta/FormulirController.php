<?php

namespace App\Http\Controllers\Peserta;

use App\Models\User;
use App\Models\Prodi;
use App\Models\Status;
use App\Models\Peserta;
use App\Models\ListDokumen;
use Illuminate\Http\Request;
use App\Models\PesertaDokumen;
use App\Http\Services\BulkData;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Services\GoogleDrive;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class FormulirController extends Controller
{
    protected $dir = BulkData::dirGdrive['dokumen'];
    public function edit()
    {
        $peserta = Peserta::where('user_id', \Auth::user()->id)->first();

        $prodi = Prodi::all();
        $listDokumen = ListDokumen::all();

        return view('peserta.formulir.edit', compact('peserta', 'prodi', 'listDokumen'));
    }

    public function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'nama' => 'required',
                'nama_pondok' => 'required',
                'nik' => 'required',
                'jenis' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'nullable',
                'nomor_hp' => 'required',
                'nomor_hp_orang_tua' => 'required',
                'alamat' => 'required',
                'ukuran_baju' => 'required',
                'kamar' => 'required',
                'kelas_pondok' => 'required',
                'qism_pondok' => 'required',
                'mahir_bahasa_lokal' => 'required',
                'keahlian' => 'required',
                'mursal' => 'required',
            ]);

            $peserta = Peserta::where('user_id', \Auth::user()->id)->first();
            $peserta->nama = strtoupper($request->nama);
            $peserta->nama_pondok = strtoupper($request->nama_pondok);
            $peserta->nik = strtoupper($request->nik);
            $peserta->jenis = strtoupper($request->jenis);
            $peserta->tempat_lahir = strtoupper($request->tempat_lahir);
            $peserta->tanggal_lahir = strtoupper($request->tanggal_lahir);
            $peserta->nomor_hp = $request->nomor_hp;
            $peserta->nomor_hp_orang_tua = $request->nomor_hp_orang_tua;
            $peserta->alamat = strtoupper($request->alamat);
            $peserta->ukuran_baju = strtoupper($request->ukuran_baju);
            $peserta->kamar = strtoupper($request->kamar);
            $peserta->kelas_pondok = strtoupper($request->kelas_pondok);
            $peserta->qism_pondok = strtoupper($request->qism_pondok);
            $peserta->mahir_bahasa_lokal = strtoupper($request->mahir_bahasa_lokal);
            $peserta->keahlian = strtoupper($request->keahlian);
            $peserta->mursal = $request->mursal;
            $peserta->save();

            \DB::commit();

            return redirect()->route('peserta.dashboard')->with('success', 'Data peserta berhasil diperbarui');

        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function dokumen(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id_dokumen' => 'required',
                'tipe' => 'required',
                'status' => 'nullable'
            ]);

            $listDokumen = ListDokumen::findOrFail($request->id_dokumen);
            $fileValidator = Validator::make($request->only('file'), [
                'file' => "required|file|mimes:$listDokumen->upload|max:".BulkData::maxSizeUpload,
            ], [
                'file.mimes' => "File harus bertipe $listDokumen->upload",
            ]);

            if ($fileValidator->fails()) {
                throw new ValidationException($fileValidator);
            }

            $file = $request->file('file');
            $peserta = Peserta::where('user_id', \Auth::user()->id)->first();
            // abort(500, $peserta);

            $dokumen = PesertaDokumen::where([
                ['peserta_id', $peserta->id],
                ['list_dokumen_id', $request->id_dokumen]
            ])->first();

            if (!$dokumen) {
                $dokumen = new PesertaDokumen;
            } else {
                // hapus dokumen lama
                if ($request->has('file')) {
                    $getFileLama = $dokumen->file;
                    if ($getFileLama != null) {
                        GoogleDrive::delete($getFileLama, $this->dir);
                    }

                }
            }

            $dokumen->peserta_id = $peserta->id;
            $dokumen->tanggal = now();
            $dokumen->list_dokumen_id = $request->id_dokumen;
            $upload = GoogleDrive::upload($file, strtoupper($request->tipe), $this->dir);
            // ambil path
            $path = GoogleDrive::getData($upload['name'], $this->dir);
            $getPath = $path['path'];
            $dokumen->path = $getPath;
            $dokumen->file = $upload['name'];

            $dokumen->save();

            $data = [
                'message' => 200,
                'data' => 'Berhasil mengupload dokumen',
            ];
            \DB::commit();
        } catch (ValidationException $e) {
            \DB::rollback();
            $data = [
                'message' => 500,
                'data' => $e->validator->errors()->getMessages()['file']
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            $data = [
                'message' => 500,
                'data' => 'Gagal mengupload dokumen, format harus jpg/jpeg/png dan ukuran file maksimal 2mb',
            ];
        }

        return $data;
    }

    public function cetak($idPeserta, $noUnik)
    {
        $peserta = Peserta::find($idPeserta);
        if (!$peserta) {
            return abort(404);
        }

        $check = $peserta->user->no_unik == $noUnik ? true : false;
        if (!$check) {
            return abort(404);
        }

        $user = $peserta->user;
        $tipeIdentitas = \Helper::getEnumValues('peserta', 'tipe_identitas');
        $prodi = Prodi::all();

        $foto = PesertaDokumen::where('list_dokumen_id', 1)->where('peserta_id', $peserta->id)->first();
        // dd(GoogleDrive::showImage($foto->path));
        $pdf = Pdf::loadView(
            'peserta.formulir.cetak',
            compact('peserta', 'tipeIdentitas', 'prodi', 'foto')
        );

        return $pdf->setPaper('a4')
            ->stream('Cetak Formulir.pdf');
    }
}