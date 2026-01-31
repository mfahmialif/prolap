<?php
namespace App\Http\Controllers\Admin\DokumenWajib;

use App\Http\Controllers\Controller;
use App\Http\Services\BulkData;
use App\Http\Services\GoogleDrive;
use App\Http\Services\Helper;
use App\Models\BeritaAcaraDpl;
use App\Models\DokumentasiDpl;
use App\Models\DPL;
use App\Models\PenugasanDpl;
use App\Models\PenugasanDplDetail;
use App\Models\Posko;
use App\Models\PoskoDpl;
use App\Models\PoskoPengawas;
use App\Models\Prodi;
use App\Models\RubrikPenilaianDpl;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DplController extends Controller
{
    protected $dir = BulkData::dirGdrive['dokumen'];

    public function index()
    {
        $posko = Posko::all();
        $prodi = Prodi::all();
        $tahun = Tahun::all();
        return view('admin.dokumen-wajib.dpl.index', compact('posko', 'prodi', 'tahun'));
    }

    public function getData(Request $request)
    {
        $search = request('search.value');

        $d = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->join('prodi', 'prodi.id', '=', 'dpl.prodi_id')
            ->join('users', 'users.id', '=', 'dpl.user_id')
            ->join('tahun', 'tahun.id', '=', 'posko.tahun_id')
            ->select('posko_dpl.*', 'posko.nama as nama_posko', 'dpl.nama as username', 'users.jenis_kelamin as jenis',
                'prodi.nama', 'prodi.jenjang', 'tahun.nama as tahun_nama')->orderBy('posko_dpl.id');
        return DataTables::of($d)
            ->addIndexColumn()
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->pengawas_id, function ($query) use ($request) {
                    $poskoPengawasId = PoskoPengawas::where('pengawas_id', $request->pengawas_id)->get()->pluck('posko_id')->toArray();
                    $query->whereIn('posko_dpl.posko_id', $poskoPengawasId);
                });
                $query->when($request->dpl_id, function ($query) use ($request) {
                    $query->where('posko_dpl.dpl_id', $request->dpl_id);
                });
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->when($request->tahun_id != "*", function ($query) use ($request) {
                    $query->where('posko.tahun_id', $request->tahun_id);
                });
                $query->when($request->prodi_id != "*", function ($query) use ($request) {
                    if ($request->prodi_id == "S1") {
                        $query->where('prodi.jenjang', "S1");
                    } else if ($request->prodi_id == "PASCA") {
                        $query->where(function ($query) {
                            $query->orWhere('prodi.jenjang', "S2");
                            $query->orWhere('prodi.jenjang', "S3");
                        });
                    } else {
                        $query->where('dpl.prodi_id', $request->prodi_id);
                    }
                });
                $query->when($request->dpl_id, function ($query) use ($request) {
                    $query->where('posko_dpl.dpl_id', $request->dpl_id);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('dpl.nama', 'LIKE', "%$search%");
                    $query->orWhere('posko.nama', 'LIKE', "%$search%");
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
                        <a href="' . route('admin.dokumen-wajib.dpl.input', ['idPoskoDpl' => $row->id]) . '" class="dropdown-item"
                        >Input Dokumen Wajib</a>
                    </div>
                </div>
                ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    public function input($idPoskoDpl)
    {
        $posko    = Posko::all();
        $dpl      = DPL::all();
        $poskoDpl = PoskoDpl::join('posko', 'posko.id', '=', 'posko_dpl.posko_id')
            ->join('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')->where('posko_dpl.id', $idPoskoDpl)
            ->select('posko_dpl.*', 'posko.nama as nama_posko', 'dpl.nama as nama_dpl')->first();

        $rubrikPenilaianDpl  = RubrikPenilaianDpl::where('posko_dpl_id', $idPoskoDpl)->first();
        $beritaAcaraDpl      = BeritaAcaraDpl::where('posko_dpl_id', $idPoskoDpl)->first();
        $dokumentasiFotoDpl  = DokumentasiDpl::where('posko_dpl_id', $idPoskoDpl)->where('tipe', 'foto')->first();
        $dokumentasiVideoDpl = DokumentasiDpl::where('posko_dpl_id', $idPoskoDpl)->where('tipe', 'video')->first();
        $tugasAkhir          = PenugasanDpl::where('penugasan', 'LIKE', "%Tugas Akhir%")
            ->where('posko_dpl_id', $idPoskoDpl)
            ->first();
        $tugasAkhir = $tugasAkhir ? PenugasanDplDetail::where('penugasan_dpl_id', $tugasAkhir->id)
            ->where('file', '!=', null)
            ->exists() : false;
        $maxSizeUpload = BulkData::maxSizeUpload;
        return view('admin.dokumen-wajib.dpl.input', compact(
            'posko',
            'idPoskoDpl',
            'dpl',
            'poskoDpl',
            'rubrikPenilaianDpl',
            'beritaAcaraDpl',
            'dokumentasiFotoDpl',
            'dokumentasiVideoDpl',
            'tugasAkhir',
            'maxSizeUpload'
        ));
    }

    public function inputData($idPoskoDpl)
    {
        $search = request('search.value');

        $data = PenugasanDpl::where('posko_dpl_id', $idPoskoDpl)->select('penugasan_dpl.*')->orderBy('id');

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('penugasan_dpl.penugasan', 'LIKE', "%$search%");
                    $query->orWhere('penugasan_dpl.mulai', 'LIKE', "%$search%");
                    $query->orWhere('penugasan_dpl.selesai', 'LIKE', "%$search%");
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
                    <button type="button" class="dropdown-item BtnInputTugas"
                        data-id="' . $row->id . '"
                        data-penugasan="' . $row->penugasan . '"
                        data-posko_dpl_id ="' . $row->posko_dpl_id . '"
                        data-mulai="' . $row->mulai . '"
                        data-selesai="' . $row->selesai . '"
                    >Input Tugas</button>
                    <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modal_edit"
                        data-id="' . $row->id . '"
                        data-penugasan="' . $row->penugasan . '"
                        data-posko_dpl_id ="' . $row->posko_dpl_id . '"
                        data-mulai="' . $row->mulai . '"
                        data-selesai="' . $row->selesai . '"
                    >Edit Tugas</button>
                    <div class="dropdown-divider"></div>
                    <form action="" onsubmit="deleteData(event)" method="POST">
                    ' . method_field('delete') . csrf_field() . '
                        <input type="hidden" name="id" value="' . $row->id . '">
                        <input type="hidden" name="nama" value="' . $row->penugasan . '">
                        <button type="submit" class="dropdown-item text-danger">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            ';
                return $btn;
            })->rawColumns(['action'])->make(true);
    }

    public function simpan(Request $request, $idPoskoDpl)
    {
        try {
            // if ($request->no == "dokumentasi_video") {
            //     $request->validate([
            //         'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac|max:' . BulkData::maxSizeUpload * 5,
            //         'no' => 'required',
            //         'keterangan' => 'nullable',
            //     ]);
            // } else {
            //     $request->validate([
            //         'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac|max:' . BulkData::maxSizeUpload,
            //         'no' => 'required',
            //         'keterangan' => 'nullable',
            //     ]);
            // }

            $request->validate([
                'file'       => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mov,avi,mkv,mp3,wav,ogg,aac|max:' . BulkData::maxSizeUpload,
                'no'         => 'required',
                'keterangan' => 'nullable',
            ]);

            $file = $request->file('file');

            $poskoDpl = PoskoDpl::findOrFail($idPoskoDpl);
            $lokasi   = trim($poskoDpl->posko->lokasi);
            if ($request->no == 'rubrik_penilaian') {
                $rubrikPenilaian = RubrikPenilaianDpl::where('posko_dpl_id', $idPoskoDpl)->first();

                $kategori = Str::limit(Helper::changeFormatSymbol("RUBRIK PENILAIAN DPL-$lokasi"), 100, '...');
                $upload   = GoogleDrive::upload($file, $kategori, $this->dir);
                $path     = GoogleDrive::getData($upload['name'], $this->dir);
                $getPath  = $path['path'];

                if (! $rubrikPenilaian) {
                    $rubrikPenilaian = new RubrikPenilaianDpl();
                }

                if (@$rubrikPenilaian->file) {
                    GoogleDrive::deleteWithPath($rubrikPenilaian->path, $this->dir);
                }

                $rubrikPenilaian->posko_dpl_id = $idPoskoDpl;
                $rubrikPenilaian->file         = $upload['name'];
                $rubrikPenilaian->path         = $getPath;
                $rubrikPenilaian->keterangan   = $request->keterangan;
                $rubrikPenilaian->status       = 'Sudah Diisi';
                $rubrikPenilaian->save();
            }

            if ($request->no == 'berita_acara') {
                $beritaAcara = BeritaAcaraDpl::where('posko_dpl_id', $idPoskoDpl)->first();

                $kategori = Str::limit(Helper::changeFormatSymbol("BERITA ACARA DPL-$lokasi"), 100, '...');
                $upload   = GoogleDrive::upload($file, $kategori, $this->dir);
                $path     = GoogleDrive::getData($upload['name'], $this->dir);
                $getPath  = $path['path'];

                if (! $beritaAcara) {
                    $beritaAcara = new BeritaAcaraDpl();
                }

                if (@$beritaAcara->file) {
                    GoogleDrive::deleteWithPath($beritaAcara->path, $this->dir);
                }

                $beritaAcara->posko_dpl_id = $idPoskoDpl;
                $beritaAcara->file         = $upload['name'];
                $beritaAcara->path         = $getPath;
                $beritaAcara->keterangan   = $request->keterangan;
                $beritaAcara->status       = 'Sudah Diisi';
                $beritaAcara->save();
            }

            if ($request->no == 'dokumentasi_foto') {
                $dokumentasiFotoDpl = DokumentasiDpl::where('posko_dpl_id', $idPoskoDpl)->where('tipe', 'foto')->first();

                $kategori = Str::limit(Helper::changeFormatSymbol("DOKUMENTASI FOTO DPL-$lokasi"), 100, '...');
                $upload   = GoogleDrive::upload($file, $kategori, $this->dir);
                $path     = GoogleDrive::getData($upload['name'], $this->dir);
                $getPath  = $path['path'];

                if (! $dokumentasiFotoDpl) {
                    $dokumentasiFotoDpl = new DokumentasiDpl();
                }

                if (@$dokumentasiFotoDpl->file) {
                    GoogleDrive::deleteWithPath($dokumentasiFotoDpl->path, $this->dir);
                }

                $dokumentasiFotoDpl->posko_dpl_id = $idPoskoDpl;
                $dokumentasiFotoDpl->tipe         = 'foto';
                $dokumentasiFotoDpl->file         = $upload['name'];
                $dokumentasiFotoDpl->path         = $getPath;
                $dokumentasiFotoDpl->keterangan   = $request->keterangan;
                $dokumentasiFotoDpl->status       = 'Sudah Diisi';
                $dokumentasiFotoDpl->save();
            }

            if ($request->no == 'dokumentasi_video') {

                // $file = $request->file('file');

                // $folderPath = public_path('upload/video/dpl/');

                // if (!file_exists($folderPath)) {
                //     mkdir($folderPath, 0777, true);
                // }

                // $originalName = $file->getClientOriginalName();
                // $fileName = 'DOKUMENTASI_VIDEO_DPL_' . '-' . date('YmdHis') . '-' . uniqid() . '-' . $originalName;
                // $file->move($folderPath, $fileName);

                // $dokumentasiVideoDpl = DokumentasiDpl::where('posko_dpl_id', $idPoskoDpl)
                //     ->where('tipe', 'video')
                //     ->first();

                // if ($dokumentasiVideoDpl && $dokumentasiVideoDpl->file) {
                //     $oldFilePath = public_path($dokumentasiVideoDpl->path);
                //     if (file_exists($oldFilePath)) {
                //         unlink($oldFilePath);
                //     }
                // }

                // if (!$dokumentasiVideoDpl) {
                //     $dokumentasiVideoDpl = new DokumentasiDpl();
                // }

                // $dokumentasiVideoDpl->posko_dpl_id = $idPoskoDpl;
                // $dokumentasiVideoDpl->tipe = 'video';
                // $dokumentasiVideoDpl->file = $fileName;
                // $dokumentasiVideoDpl->path = 'upload/video/dpl/' . $fileName; // Simpan path relatif
                // $dokumentasiVideoDpl->keterangan = $request->keterangan;
                // $dokumentasiVideoDpl->status = 'Sudah Diisi';
                // $dokumentasiVideoDpl->save();

                $dokumentasiVideoDpl = DokumentasiDpl::where('posko_dpl_id', $idPoskoDpl)->where('tipe', 'video')->first();

                $kategori = Str::limit(Helper::changeFormatSymbol("DOKUMENTASI VIDEO DPL-$lokasi"), 100, '...');
                $upload   = GoogleDrive::upload($file, $kategori, $this->dir);
                $path     = GoogleDrive::getData($upload['name'], $this->dir);
                $getPath  = $path['path'];

                if (! $dokumentasiVideoDpl) {
                    $dokumentasiVideoDpl = new DokumentasiDpl();
                }

                if (@$dokumentasiVideoDpl->file) {
                    GoogleDrive::deleteWithPath($dokumentasiVideoDpl->path, $this->dir);
                }

                $dokumentasiVideoDpl->posko_dpl_id = $idPoskoDpl;
                $dokumentasiVideoDpl->tipe         = 'video';
                $dokumentasiVideoDpl->file         = $upload['name'];
                $dokumentasiVideoDpl->path         = $getPath;
                $dokumentasiVideoDpl->keterangan   = $request->keterangan;
                $dokumentasiVideoDpl->status       = 'Sudah Diisi';
                $dokumentasiVideoDpl->save();
            }

            return [
                'message' => 200,
                'data'    => 'Data Berhasil ditambahkan',
                'req'     => $request->all(),
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 500,
                'data'    => implode(', ', array_map(function ($messages) {
                    return implode(', ', $messages);
                }, $e->errors())),
                'req'     => $request->all(),
            ]);
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
                'error'   => $th->getMessage(),
            ];
        }
    }

    public function delete(Request $request, $idPoskoDpl)
    {
        try {

            $request->validate([
                'id' => 'required',
            ]);

            if ($request->id == 'rubrik_penilaian') {
                $rubrikPenilaian = RubrikPenilaianDpl::where('posko_dpl_id', $idPoskoDpl)->first();
                if ($rubrikPenilaian->file) {
                    GoogleDrive::deleteWithPath($rubrikPenilaian->path, $this->dir);
                }
                $rubrikPenilaian->delete();
            }

            if ($request->id == 'berita_acara') {
                $beritaAcara = BeritaAcaraDpl::where('posko_dpl_id', $idPoskoDpl)->first();
                if ($beritaAcara->file) {
                    GoogleDrive::deleteWithPath($beritaAcara->path, $this->dir);
                }
                $beritaAcara->delete();
            }

            if ($request->id == 'dokumentasi_foto') {
                $dokumentasiFotoDpl = DokumentasiDpl::where('posko_dpl_id', $idPoskoDpl)->where('tipe', 'foto')->first();
                if ($dokumentasiFotoDpl->file) {
                    GoogleDrive::deleteWithPath($dokumentasiFotoDpl->path, $this->dir);
                }
                $dokumentasiFotoDpl->delete();
            }

            if ($request->id == 'dokumentasi_video') {
                $dokumentasiVideoDpl = DokumentasiDpl::where('posko_dpl_id', $idPoskoDpl)->where('tipe', 'video')->first();
                // $file = public_path($dokumentasiVideoDpl->path);
                // if (file_exists($file)) {
                //     unlink($file);
                // }
                if ($dokumentasiVideoDpl->file) {
                    GoogleDrive::deleteWithPath($dokumentasiVideoDpl->path, $this->dir);
                }
                $dokumentasiVideoDpl->delete();
            }

            return [
                'message'    => 200,
                'data'       => $request->nama . ' Berhasil dihapus',
                'req'        => $request->all(),
                'idPoskoDpl' => $idPoskoDpl,
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data'    => $th->getMessage(),
                'req'     => $request->all(),
            ];
        }
    }

}
