<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\Status;
use App\Models\Peserta;
use App\Models\ListDokumen;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use App\Http\Services\Helper;
use App\Imports\PesertaImport;
use App\Models\PesertaDokumen;
use App\Http\Services\BulkData;
use Yajra\DataTables\DataTables;
use App\Http\Services\GoogleDrive;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PesertaController extends Controller
{
    protected $dir = BulkData::dirGdrive['dokumen'];

    public function store(Request $request) {
        try {
            \DB::beginTransaction();
            
            $request->validate([
                'nim' => 'required',
                'nama' => 'required',
                'prodi_id' => 'required',
                'tahun_id' => 'required',
                'tanggal_daftar' => 'required',
                'jenis_kelamin' => 'required',
                'jenis' => 'required',
                'status_id' => 'required',
            ]);

            // Check if User exists by NIM/Username
            $user = \App\Models\User::where('username', $request->nim)->first();
            
            if (!$user) {
                // Create new User if not exists
                $user = \App\Models\User::create([
                    'username' => $request->nim,
                    'nama' => $request->nama,
                    'role_id' => 2, // Role Peserta
                    'password' => \Hash::make($request->nim), // Password default NIM
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'no_unik' => $request->nim,
                ]);
            } else {
                // Ensure user role is compatible or just proceed to link
                // Ideally prompt if user exists but has different role, but for now we assume correct usage or link to existing user
            }

            // Check if Peserta already exists
            $cekPeserta = Peserta::where('user_id', $user->id)
                            ->where('tahun_id', $request->tahun_id)
                            ->first();

            if ($cekPeserta) {
                 return response()->json([
                    'status' => false,
                    'message' => 500,
                    'data' => "Peserta dengan NIM $request->nim sudah terdaftar di tahun ini.",
                ]);
            }

            // Create Peserta
            $peserta = Peserta::create([
                'user_id' => $user->id,
                'nim' => $request->nim,
                'nama' => $request->nama,
                'prodi_id' => $request->prodi_id,
                'tahun_id' => $request->tahun_id,
                'tanggal_daftar' => $request->tanggal_daftar,
                'jenis' => $request->jenis,
                'status_id' => $request->status_id,
                'nik' => $request->nik,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'nomor_hp' => $request->nomor_hp,
                'alamat' => $request->alamat,
                'nama_pondok' => $request->nama_pondok,
                'keterangan' => $request->keterangan,
            ]);

            \DB::commit();

            return response()->json([
                'status' => true,
                'message' => 200,
                'data' => "Berhasil menambahkan peserta baru",
            ]);

        } catch (\Throwable $th) {
            \DB::rollback();
            return response()->json([
                 'status' => false,
                'message' => 500,
                'data' => "Gagal menambahkan data: " . $th->getMessage()
            ]);
        }
    }

    public function index()
    {
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        $status = Status::all();
        $prodi = Prodi::all();

        return view('admin.peserta.index', compact('tahun', 'status', 'prodi'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Peserta::join('users', 'users.id', '=', 'peserta.user_id')
            ->join('status', 'status.id', '=', 'peserta.status_id')
            ->join('tahun', 'tahun.id', '=', 'peserta.tahun_id')
            ->join('prodi', 'prodi.id', '=', 'peserta.prodi_id')
            ->select(
                'peserta.*',
                'status.nama as status_nama',
                'users.jenis_kelamin as jenis_kelamin',
                'tahun.nama as tahun_nama',
                'prodi.nama as prodi_nama'
            );


        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->when($request->tahun_id != "*", function ($query) use ($request) {
                    $query->where('peserta.tahun_id', $request->tahun_id);
                });
                $query->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                    $query->where('users.jenis_kelamin', $request->jenis_kelamin);
                });
                $query->when($request->status_id != "*", function ($query) use ($request) {
                    $query->where('peserta.status_id', $request->status_id);
                });
                $query->when($request->jenis != "*", function ($query) use ($request) {
                    $query->where('peserta.jenis', $request->jenis);
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
                        $query->where('peserta.prodi_id', $request->prodi_id);
                    }
                });
                $query->when($request->tanggal != "*", function ($query) use ($request) {
                    list($tanggalMulai, $tanggalSelesai) = explode(" - ", $request->range_tanggal);
                    $tanggalMulai = explode("/", $tanggalMulai);
                    $tanggalMulai = Carbon::parse($tanggalMulai[2] . '-' . $tanggalMulai[1] . '-' . $tanggalMulai[0]);
                    $tanggalSelesai = explode("/", $tanggalSelesai);
                    $tanggalSelesai = Carbon::parse($tanggalSelesai[2] . '-' . $tanggalSelesai[1] . '-' . $tanggalSelesai[0]);
                    $query->whereBetween('peserta.tanggal_daftar', [$tanggalMulai->startOfDay(), $tanggalSelesai->endOfDay()]);
                });
                $query->where(function ($query) use ($search) {
                    $query->orWhere('peserta.tanggal_daftar', 'LIKE', "%$search%");
                    $query->orWhere('peserta.nama', 'LIKE', "%$search%");
                    $query->orWhere('peserta.nim', 'LIKE', "%$search%");
                    $query->orWhere('users.jenis_kelamin', 'LIKE', "%$search%");
                    $query->orWhere('peserta.tahun_id', 'LIKE', "%$search%");
                });
            })
            ->editColumn('nama', function ($row) {
                return "<b>$row->nama</b>";
            })
            ->editColumn('status_nama', function ($row) {
                return '<span class="badge badge-' . $row->status->warna . '">' . strtoupper($row->status->nama) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Klik
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                $actionBtn .= '
                        <a target="_blank" href="'
                    . route(
                        'peserta.formulir.cetak',
                        ['idPeserta' => @$row->id, 'noUnik' => @$row->user->no_unik]
                    ) .
                    '" type="button" class="dropdown-item">Cetak Formulir</a>';
                $actionBtn .= '
                            <a href="'
                    . route('admin.peserta.detail', ['peserta' => $row->id]) .
                    '" type="button" class="dropdown-item">Detail</a>';
                if (\Auth::user()->role_id == 1) {
                    $actionBtn .= '
                            <div class="dropdown-divider"></div>
                            <form action="" onsubmit="deleteData(event)" method="POST">
                            ' . method_field('delete') . csrf_field() . '
                                <input type="hidden" name="id" value="' . $row->id . '">
                                <input type="hidden" name="nama" value="' . $row->nama . '">
                                <button type="submit" class="dropdown-item text-danger">
                                    Delete
                                </button>
                            </form>';
                }
                $actionBtn .= '
                    </div>
                </div>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'status_nama', 'nama'])
            ->toJson();
    }

    public function delete(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id' => 'required'
            ]);

            $peserta = Peserta::find($request->id);
            $dataDokumen = PesertaDokumen::where('peserta_id', $peserta->id)->get();
            foreach ($dataDokumen as $dokumen) {
                if ($dokumen->file && $dokumen->path) {
                    $deleteGdrive = GoogleDrive::delete($dokumen->file, $this->dir);
                    if ($deleteGdrive['status'] == false) {
                        return abort(500, "Delete file di gdrive gagal");
                    }
                }
                $dokumen->delete();
            }

            $peserta->delete();
            $peserta->user->delete();
            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 200,
                'data' => 'Berhasil menghapus data'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 500,
                'data' => 'Gagal menghapus data',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function detail(Peserta $peserta)
    {
        $status = Status::all();
        $listDokumen = ListDokumen::all();
        $prodi = Prodi::all();

        $foto = PesertaDokumen::where('peserta_id', $peserta->id)->where('list_dokumen_id', 1)->first();
        $tahun = Tahun::orderBy('nama', 'desc')->get();
        return view('admin.peserta.detail', compact('peserta', 'status', 'listDokumen', 'prodi', 'foto', 'tahun'));
    }

    public function update(Request $request, Peserta $peserta)
    {
        try {
            \DB::beginTransaction();
            $dataValidated = $request->validate([
                'username' => 'nullable',
                'nama' => 'nullable',
                'nama_pondok' => 'nullable',
                'nim' => 'nullable',
                'nik' => 'nullable',
                'prodi_id' => 'nullable',
                'tanggal_daftar' => 'nullable',
                'tempat_lahir' => 'nullable',
                'tanggal_lahir' => 'nullable',
                'jenis' => 'nullable',
                'tahun_id' => 'nullable',
                'nomor_hp' => 'nullable',
                'nomor_hp_orang_tua' => 'nullable',
                'jenis_kelamin' => 'nullable',
                'alamat' => 'nullable',
                'kamar' => 'nullable',
                'kelas_pondok' => 'nullable',
                'qism_pondok' => 'nullable',
                'mahir_bahasa_lokal' => 'nullable',
                'keahlian' => 'nullable',
                'ukuran_baju' => 'nullable',
                'mursal' => 'nullable',
                'status_id' => 'nullable',
                'keterangan' => 'nullable'
            ]);

            $dataUpdate = [];
            $dataUpdateUser = [];
            $dataKolomUser = ['jenis_kelamin', 'username'];
            foreach ($dataValidated as $key => $value) {
                if ($value != null) {
                    if (in_array($key, $dataKolomUser)) {
                        $dataUpdateUser[$key] = $value;
                    } else {
                        $dataUpdate[$key] = $value;
                    }
                }
            }

            $peserta->update($dataUpdate);
            $peserta->user->update($dataUpdateUser);

            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 200,
                'data' => "Berhasil update biodata diri",
                'request' => $request->all(),
            ]);
        } catch (\Throwable $th) {
            \DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 500,
                'data' => $th->getMessage()
            ]);
        }
    }

    public function dataDokumen(Request $request, Peserta $peserta)
    {
        $search = request('search.value');
        $data = PesertaDokumen::join('list_dokumen', 'list_dokumen.id', '=', 'peserta_dokumen.list_dokumen_id')
            ->where('peserta_dokumen.peserta_id', $peserta->id)
            ->select('peserta_dokumen.*', 'list_dokumen.tipe as list_dokumen_tipe');

        return DataTables::of($data)
            ->filter(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('tanggal', 'LIKE', "%$search%");
                });
            })
            ->editColumn('file', function ($row) {
                $linkFoto = GoogleDrive::showImage($row->path);
                return '
                <a data-fancybox="gallery"
                    data-src="' . $linkFoto . '">
                    <img src="' . $linkFoto . '" class="profile-user-img cursor-pointer " alt="" />
                </a>
                ';
            })
            ->addColumn('download', function ($row) {
                $customName = $row->peserta->nama;
                $url = GoogleDrive::directDownload($row->file, $this->dir, $customName);
                return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-dark">Download</a>';
            })
            ->addColumn('delete', function ($row) {
                return '
                <form action="" onsubmit="deleteDataDokumen(event)" method="POST">
                ' . method_field('delete') . csrf_field() . '
                    <input type="hidden" name="id" value="' . $row->id . '">
                    <input type="hidden" name="tipe" value="' . $row->list_dokumen_tipe . '">
                    <button type="submit" class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </form>
                ';
            })
            ->rawColumns(['file', 'download', 'delete'])
            ->toJson();
    }

    public function saveDokumen(Request $request, Peserta $peserta)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'list_dokumen_id' => 'required',
                'dokumen' => 'nullable'
            ]);

            $dokumen = PesertaDokumen::where([
                ['peserta_id', $peserta->id],
                ['list_dokumen_id', $request->list_dokumen_id]
            ])->first();

            if (!$dokumen) {
                $dokumen = new PesertaDokumen;
            } else {
                // hapus dokumen lama
                if ($request->has('dokumen')) {
                    $getFileLama = $dokumen->file;
                    if ($getFileLama != null) {
                        GoogleDrive::delete($getFileLama, $this->dir);
                    }

                }
            }

            $dokumen->list_dokumen_id = $request->list_dokumen_id;
            $dokumen->peserta_id = $peserta->id;
            $dokumen->tanggal = now();

            // ambil path
            $path = GoogleDrive::getData($request->dokumen, $this->dir);
            $getPath = $path['path'];
            $dokumen->path = $getPath;
            $dokumen->file = $request->dokumen;

            $dokumen->save();

            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 200,
                'data' => "Berhasil menyimpan dokumen",
                'req' => $request->all(),
                'dok' => $dokumen,
                'path' => $path
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 500,
                'data' => "Gagal menambahkan dokumen",
                'req' => $request->all(),
                'error' => $th->getMessage()
            ]);
        }
    }

    public function deleteDokumen(Request $request, Peserta $peserta)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'id' => 'required',
                'tipe' => 'required'
            ]);

            $delete = PesertaDokumen::find($request->id);

            $deleteGdrive = GoogleDrive::delete($delete->file, $this->dir);
            if ($deleteGdrive['status'] == false) {
                return abort(500, "Delete file di gdrive gagal");
            }

            $delete->delete();

            \DB::commit();
            return response()->json([
                'status' => true,
                'message' => 200,
                'data' => "Berhasil menghapus dokumen " . $request->tipe,
                'req' => $request->all(),
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 500,
                'data' => "Gagal menghapus dokumen | " . $th->getMessage(),
                'req' => $request->all(),
                'error' => $th->getMessage()
            ]);
        }
    }

    public function fileUpload(Request $request)
    {
        $file = $request->file('file');
        $upload = GoogleDrive::upload($file, 'DOK', $this->dir);

        return response()->json($upload);
    }
    public function fileDelete(Request $request)
    {
        $namaFile = $request->get('dokumen');
        $delete = GoogleDrive::delete($namaFile, $this->dir);
        return response()->json([
            'name' => $namaFile,
        ]);
    }

    public function updatePassword(Request $request, Peserta $peserta)
    {
        try {
            $request->validate([
                'password' => 'required'
            ]);

            $user = $peserta->user;
            $user->password = Hash::make($request->password);
            $user->no_unik = $request->password;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 200,
                'data' => "Berhasil mengupdate password",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 500,
                'data' => "Gagal mengupdate password",
                'error' => $th->getMessage()
            ]);
        }
    }

    public function export(Request $request)
    {
        $data = Peserta::join('users', 'users.id', '=', 'peserta.user_id')
            ->join('status', 'status.id', '=', 'peserta.status_id')
            ->join('tahun', 'tahun.id', '=', 'peserta.tahun_id')
            ->join('prodi', 'prodi.id', '=', 'peserta.prodi_id')
            ->leftJoin('posko_peserta', 'posko_peserta.peserta_id', '=', 'peserta.id')
            ->leftJoin('posko', 'posko.id', '=', 'posko_peserta.posko_id')
            ->when($request->tahun_id != "*", function ($query) use ($request) {
                $query->where('peserta.tahun_id', $request->tahun_id);
            })
            ->when($request->jenis_kelamin != "*", function ($query) use ($request) {
                $query->where('users.jenis_kelamin', $request->jenis_kelamin);
            })
            ->when($request->status_id != "*", function ($query) use ($request) {
                $query->where('peserta.status_id', $request->status_id);
            })
            ->when($request->prodi_id != "*", function ($query) use ($request) {
                $query->where('peserta.prodi_id', $request->prodi_id);
            })
            ->when($request->tanggal != "*", function ($query) use ($request) {
                list($tanggalMulai, $tanggalSelesai) = explode(" - ", $request->range_tanggal);
                $tanggalMulai = explode("/", $tanggalMulai);
                $tanggalMulai = Carbon::parse($tanggalMulai[2] . '-' . $tanggalMulai[1] . '-' . $tanggalMulai[0]);
                $tanggalSelesai = explode("/", $tanggalSelesai);
                $tanggalSelesai = Carbon::parse($tanggalSelesai[2] . '-' . $tanggalSelesai[1] . '-' . $tanggalSelesai[0]);
                $query->whereBetween('peserta.tanggal_daftar', [$tanggalMulai->startOfDay(), $tanggalSelesai->endOfDay()]);
            })
            ->select(
                'peserta.*',
                'status.nama as status_nama',
                'users.jenis_kelamin as jenis_kelamin',
                'tahun.nama as tahun_nama',
                'prodi.nama as prodi_nama',
                'posko.nama as posko_nama',
                'posko.lokasi as posko_lokasi'
            )
            ->orderBy('posko.id', 'asc')
            ->get();

        $except = [
            'id',
            'user_id',
            'tahun_id',
            'prodi_id',
            'status_id',
        ];

        $data = $data->makeHidden($except);

        $tahun = Tahun::find($request->tahun_id);
        $tahun = $tahun ? '-' . "$tahun->kode" : "";
        $prodi = Prodi::find($request->prodi_id);
        $prodi = $prodi ? '-' . Helper::changeName($prodi->nama) : "";
        $jenis_kelamin = $request->jenis_kelamin != "*" ? '-' . substr($request->jenis_kelamin, 0, 1) : "";
        if ($request->tanggal != '*') {
            list($tanggalMulai, $tanggalSelesai) = explode(" - ", $request->range_tanggal);
            $tanggal = '-' . Helper::changeName($tanggalMulai) . '-' . Helper::changeName($tanggalSelesai);
        } else {
            $tanggal = '';
        }

        $nama = $tanggal . $tahun . $prodi . $jenis_kelamin;
        if ($request->submit == "excel") {
            return Excel::download(new ExcelExport($data), "Rekap Peserta$nama.xlsx");
        }

        if ($request->submit == "idcard") {
            return $this->createZip($data, $nama, $request->mulai, $request->sampai);
        }
    }

    public function updateStatusTerverifikasi(Peserta $peserta)
    {
        try {
            $peserta->status_id = 2;
            $peserta->save();
            return [
                'message' => 200,
                'data' => 'Berhasil update status verifikasi',
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 500,
                'data' => $th->getMessage(),
            ];
        }
    }

    public function import(Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'file' => 'required|mimes:xls,xlsx'
            ]);

            $file = $request->file('file');

            $import = new PesertaImport();
            \Excel::import($import, $file);

            \DB::commit();
            return [
                'message' => 200,
                'data' => "Berhasil mengimport data " . $import->getResponse(),
                'req' => $request->all(),
            ];
        } catch (ValidationException $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 500,
                'data' => collect($e->errors())->flatten()->implode(' '),
                'req' => $request->all()
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'message' => 500,
                'data' => 'Gagal mengimport data',
                'err' => $th->getMessage(),
                'req' => $request->all()
            ];
        }
    }
}
