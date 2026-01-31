<?php
namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Services\BulkData;
use App\Http\Services\GoogleDrive;
use App\Http\Services\Helper;
use App\Models\KegiatanMahasiswa;
use App\Models\KegiatanMahasiswaBukti;
use App\Models\ListDokumen;
use App\Models\PenugasanDpl;
use App\Models\PenugasanDplDetail;
use App\Models\Peserta;
use App\Models\PesertaDokumen;
use App\Models\Posko;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    protected $dir = BulkData::dirGdrive['dokumen'];

    public function index()
    {
        $peserta     = Peserta::where('user_id', \Auth::user()->id)->first();
        $listDokumen = ListDokumen::all();

        // $cek = PesertaService::cekKelengkapan($peserta);
        // if (!$cek) {
        //     return redirect()->route("peserta.formulir.edit")->with('warning', 'Mohon lengkapi DATA DIRI dan DOKUMEN terlebih dahulu');
        // }
        $foto     = PesertaDokumen::where('peserta_id', @$peserta->id)->first();
        $showFoto = GoogleDrive::showImage(@$foto->path);

        $poskoPesertaId = $peserta->poskoPeserta->pluck('id');
        $penugasanDplTa = PenugasanDpl::join('posko_dpl', 'posko_dpl.id', 'penugasan_dpl.posko_dpl_id')
            ->join('posko', 'posko.id', 'posko_dpl.posko_id')
            ->join('penugasan_dpl_detail', 'penugasan_dpl_detail.penugasan_dpl_id', 'penugasan_dpl.id')
            ->whereIn('penugasan_dpl_detail.posko_peserta_id', $poskoPesertaId)
            ->where('penugasan_dpl.penugasan', '=', 'Tugas Akhir')
            ->select('penugasan_dpl_detail.*', 'posko.id as posko_id', 'posko.nama as posko_nama', 'penugasan_dpl.penugasan')
            ->get()
            ->keyBy('posko_id');

        foreach ($penugasanDplTa as $item) {
            $item->link = GoogleDrive::link($item->path);
        }

        $penugasanDplVideo = PenugasanDpl::join('posko_dpl', 'posko_dpl.id', 'penugasan_dpl.posko_dpl_id')
            ->join('posko', 'posko.id', 'posko_dpl.posko_id')
            ->join('penugasan_dpl_detail', 'penugasan_dpl_detail.penugasan_dpl_id', 'penugasan_dpl.id')
            ->whereIn('penugasan_dpl_detail.posko_peserta_id', $poskoPesertaId)
            ->where('penugasan_dpl.penugasan', '=', 'Video')
            ->select('penugasan_dpl_detail.*', 'posko.id as posko_id', 'posko.nama as posko_nama', 'penugasan_dpl.penugasan')
            ->get()
            ->keyBy('posko_id');

        foreach ($penugasanDplVideo as $item) {
            $item->link = GoogleDrive::link($item->path);
        }

        return view('peserta.dashboard.index', compact('peserta', 'listDokumen', 'foto', 'showFoto', 'penugasanDplTa', 'penugasanDplVideo'));
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'id_edit'  => 'required',
                'password' => 'required',
            ]);

            $user = User::findOrFail($request->id_edit);

            $user->password = Hash::make($request->password);
            $user->no_unik  = $request->password;
            $user->save();

            return response()->json([
                'status'  => true,
                'message' => 200,
                'data'    => "Berhasil mengupdate password",
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            $data = [
                'status'  => false,
                'message' => 500,
                'data'    => "Gagal mengupdate password",
                'cel'     => $user,
            ];
        }
        return $data;
    }

    public function uploadPenugasanDpl(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'file'       => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac|max:' . BulkData::maxSizeUpload,
                'no'         => 'required',
                'keterangan' => 'nullable',
            ]);

            $no             = explode('|', $request->no);
            $poskoId        = $no[0];
            $poskoPesertaId = $no[1];
            $jenis          = $no[2];
            $keterangan     = $request->keterangan;
            $file           = $request->file('file');

            $posko = Posko::findOrFail($request->no);

            $poskoDpl = $posko->poskoDpl;

            if ($request->hasFile('file')) {
                $lokasi    = trim($posko->lokasi);
                $penugasan = $jenis;
                $kategori  = Str::limit(Helper::changeFormatSymbol("PENUGASAN-$lokasi-$penugasan"), 100, '...');

                $upload  = GoogleDrive::upload($file, $kategori, $this->dir);
                $path    = GoogleDrive::getData($upload['name'], $this->dir);
                $getPath = $path['path'];
            }

            foreach ($poskoDpl as $pdpl) {
                $penugasanDpl = $pdpl->penugasanDpl->where('penugasan', $jenis)->first();
                if ($penugasanDpl == null) {
                    $penugasanDpl = PenugasanDpl::create([
                        'posko_dpl_id' => $pdpl->id,
                        'penugasan'    => $jenis,
                        'mulai'        => null,
                        'selesai'      => null,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now(),
                    ]);
                }
                $idPenugasanDpl = $penugasanDpl->id;

                $penugasanDplDetail = PenugasanDplDetail::where('posko_peserta_id', $poskoPesertaId)->where('penugasan_dpl_id', $idPenugasanDpl)->first();
                if (@$penugasanDplDetail->path) {
                    GoogleDrive::deleteWithPath($penugasanDplDetail->path, $this->dir);
                } else {
                    $penugasanDplDetail = new PenugasanDplDetail();
                }

                if ($request->hasFile('file')) {
                    $penugasanDplDetail->penugasan_dpl_id  = $idPenugasanDpl;
                    $penugasanDplDetail->posko_peserta_id  = $poskoPesertaId;
                    $penugasanDplDetail->file              = $upload['name'];
                    $penugasanDplDetail->path              = $getPath;
                    $penugasanDplDetail->keterangan        = $keterangan;
                    $penugasanDplDetail->waktu_pengumpulan = Carbon::now();
                    $penugasanDplDetail->save();
                }
            }

            \DB::commit();
            return [
                'message' => 200,
                'data'    => 'Data berhasil ditambahkan',
                'req'     => $request->all(),
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 422,
                'error'   => implode(' ', collect($e->errors())->flatten()->toArray()),
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            \DB::rollBack();
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
            ];
        }
    }

    public function deletePenugasanDpl(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);

            $id             = explode('|', $request->id);
            $poskoId        = $id[0];
            $poskoPesertaId = $id[1];
            $jenis          = $id[2];

            $posko = Posko::findOrFail($poskoId);

            $poskoDpl = $posko->poskoDpl;

            foreach ($poskoDpl as $pdpl) {
                $penugasanDpl = $pdpl->penugasanDpl->where('penugasan', $jenis)->first();
                if ($penugasanDpl == null) {
                    $penugasanDpl = PenugasanDpl::create([
                        'posko_dpl_id' => $pdpl->id,
                        'penugasan'    => $jenis,
                        'mulai'        => null,
                        'selesai'      => null,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now(),
                    ]);
                }
                $idPenugasanDpl = $penugasanDpl->id;

                $data = PenugasanDplDetail::where('posko_peserta_id', $poskoPesertaId)
                    ->where('penugasan_dpl_id', $idPenugasanDpl)
                    ->first();

                if ($data) {
                    $delete = GoogleDrive::deleteWithPath($data->path, $this->dir);
                    if (! $delete['status']) {
                        return abort(500, $delete['message']);
                    }

                    $data->delete();
                }
            }
            return [
                'message'  => 200,
                'data'     => 'File Tugas Mahasiswa Berhasil dihapus',
                'req'      => $request->all(),
                'response' => $penugasanDpl,
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => 'File Tugas Mahasiswa Gagal dihapus',
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
            ];
        }
    }

    public function dataKegiatanMahasiswa(Request $request, Posko $posko)
    {
        $search = request('search.value');
        $data   = KegiatanMahasiswa::with('bukti')->select('*');
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $posko) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('nama_kegiatan', 'LIKE', "%$search%");
                    $query->orWhere('tanggal', 'LIKE', "%$search%");
                });
                $query->where('posko_id', $posko->id);
            })
            ->addColumn('bukti_kegiatan', function ($row) {
                $bukti = htmlspecialchars(json_encode($row->bukti), ENT_QUOTES, 'UTF-8');
                return '
                    <a href="javascript:void(0)" id="action_kegiatan_mahasiswa' . $row->id . '"
                            data-toggle="modal" data-target="#modal_edit' . $row->posko_id . '"
                            data-id="' . $row->id . '"
                            data-nama_kegiatan="' . e($row->nama_kegiatan) . '"
                            data-tanggal="' . e($row->tanggal) . '"
                            data-keterangan="' . e($row->keterangan) . '"
                            data-bukti="' . $bukti . '"
                    >' . $row->bukti->count() . ' File' . '</a>
                ';
            })
            ->addColumn('action', function ($row) {
                $bukti     = htmlspecialchars(json_encode($row->bukti), ENT_QUOTES, 'UTF-8');
                $actionBtn = '
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Klik
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <button type="button" class="dropdown-item" id="action_kegiatan_mahasiswa' . $row->id . '"
                            data-toggle="modal" data-target="#modal_edit' . $row->posko_id . '"
                            data-id="' . $row->id . '"
                            data-nama_kegiatan="' . e($row->nama_kegiatan) . '"
                            data-tanggal="' . e($row->tanggal) . '"
                            data-keterangan="' . e($row->keterangan) . '"
                            data-bukti="' . $bukti . '"
                        >Edit</button>
                        <form action="" onsubmit="deleteDataKegiatanMahasiswa' . $row->posko_id. '(event)" method="POST">
                        ' . method_field('delete') . csrf_field() . '
                            <input type="hidden" name="id" value="' . $row->id . '">
                            <input type="hidden" name="nama" value="' . e($row->nama_kegiatan) . '">
                            <button type="submit" class="dropdown-item text-danger">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'bukti_kegiatan'])
            ->toJson();
    }

    public function storeKegiatanMahasiswa(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'posko_id'      => 'required',
                'nama_kegiatan' => 'required|string|max:100',
                'keterangan'    => 'nullable|string|max:500',
                'tanggal'       => 'required|date',
                'dokumen'       => 'required|array',
            ]);

            $kegiatanMahasiswa                = new KegiatanMahasiswa();
            $kegiatanMahasiswa->posko_id      = $request->posko_id;
            $kegiatanMahasiswa->nama_kegiatan = $request->nama_kegiatan;
            $kegiatanMahasiswa->keterangan    = $request->keterangan;
            $kegiatanMahasiswa->tanggal       = $request->tanggal;
            $kegiatanMahasiswa->save();

            foreach ($request->dokumen as $key => $value) {
                $path                           = GoogleDrive::getData($value, $this->dir);
                $getPath                        = $path['path'];
                $dokumen                        = new KegiatanMahasiswaBukti();
                $dokumen->kegiatan_mahasiswa_id = $kegiatanMahasiswa->id;
                $dokumen->path                  = $getPath;
                $dokumen->file                  = $value;
                $dokumen->save();
            }

            \DB::commit();
            return [
                'message' => 200,
                'data'    => 'Kegiatan Mahasiswa Berhasil ditambahkan',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            \DB::rollBack();
            foreach ($request->dokumen as $dokumen) {
                \Helper::deleteFile(public_path('dokumen/' . $dokumen));
            }
            return [
                'message' => 500,
                'data'    => 'Kegiatan Mahasiswa Gagal ditambahkan',
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
            ];
        }
    }
    public function updateKegiatanMahasiswa(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id'            => 'required',
                'nama_kegiatan' => 'required|string|max:100',
                'keterangan'    => 'nullable|string|max:500',
                'tanggal'       => 'required|date',
                'dokumen'       => 'nullable|array',
            ]);

            $kegiatanMahasiswa                = KegiatanMahasiswa::findOrFail($request->id);
            $kegiatanMahasiswa->nama_kegiatan = $request->nama_kegiatan;
            $kegiatanMahasiswa->keterangan    = $request->keterangan;
            $kegiatanMahasiswa->tanggal       = $request->tanggal;
            $kegiatanMahasiswa->save();

            if ($request->filled('dokumen')) {
                foreach ($request->dokumen as $key => $value) {
                    $path                           = GoogleDrive::getData($value, $this->dir);
                    $getPath                        = $path['path'];
                    $dokumen                        = new KegiatanMahasiswaBukti();
                    $dokumen->kegiatan_mahasiswa_id = $kegiatanMahasiswa->id;
                    $dokumen->path                  = $getPath;
                    $dokumen->file                  = $value;
                    $dokumen->save();
                }
            }

            \DB::commit();
            return [
                'message' => 200,
                'data'    => 'Bukti Kegiatan Mahasiswa Berhasil diupdate',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            \DB::rollBack();
            return [
                'message' => 500,
                'data'    => 'Bukti Kegiatan Mahasiswa Gagal diupdate',
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
            ];
        }
    }
    public function deleteKegiatanMahasiswa(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id' => 'required',
            ]);

            $kegiatanMahasiswa = KegiatanMahasiswa::findOrFail($request->id);

            foreach ($kegiatanMahasiswa->bukti as $key => $value) {
                $deleteGdrive = GoogleDrive::deleteWithPath($value->path, $this->dir);
                $value->delete();
            }

            $kegiatanMahasiswa->delete();
            \DB::commit();
            return [
                'message' => 200,
                'data'    => 'Kegiatan Mahasiswa Berhasil dihapus',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            \DB::rollBack();
            return [
                'message' => 500,
                'data'    => 'Kegiatan Mahasiswa Gagal dihapus',
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
                'bukti'   => KegiatanMahasiswaBukti::find($request->id),
            ];
        }
    }
    public function deleteKegiatanMahasiswaBukti(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id' => 'required',
            ]);

            $kegiatanMahasiswaBukti = KegiatanMahasiswaBukti::find($request->id);

            $deleteGdrive = GoogleDrive::deleteWithPath($kegiatanMahasiswaBukti->path, $this->dir);

            $kegiatanMahasiswaBukti->delete();
            \DB::commit();
            return [
                'message' => 200,
                'data'    => 'Kegiatan Mahasiswa Berhasil dihapus',
                'req'     => $request->all(),
            ];
        } catch (\Throwable $th) {
            \DB::rollBack();
            return [
                'message' => 500,
                'data'    => 'Kegiatan Mahasiswa Gagal dihapus',
                'req'     => $request->all(),
                'err'     => $th->getMessage(),
                'bukti'   => KegiatanMahasiswaBukti::find($request->id),
            ];
        }
    }

    public function fileUpload(Request $request)
    {
        $file   = $request->file('file');
        $upload = GoogleDrive::upload($file, $request->nama, $this->dir);

        return response()->json($upload);
    }

    public function fileDelete(Request $request)
    {
        $namaFile = $request->get('dokumen');
        $delete   = GoogleDrive::delete($namaFile, $this->dir);
        return response()->json([
            'name' => $namaFile,
        ]);
    }

}
