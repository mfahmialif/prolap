<?php

namespace App\Http\Controllers\Admin;

use App\Models\AbsensiPsDplDetail;
use App\Models\AbsensiPsPengawasDetail;
use App\Models\DPL;
use App\Models\PenilaianDplDetail;
use App\Models\PenilaianPengawasDetail;
use App\Models\User;
use App\Models\Posko;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\Peserta;
use App\Models\Pengawas;
use App\Models\PoskoDpl;
use App\Imports\PoskoImport;
use App\Models\PoskoPeserta;
use Illuminate\Http\Request;
use App\Models\PoskoPengawas;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;


class PoskoController extends Controller
{
    public function index()
    {
        $tahun = Tahun::orderBy('id', 'desc')->get();
        return view('admin.posko.index', compact('tahun'));
    }

    public function data(Request $request)
    {
        $search = request('search.value');
        $data = Posko::leftJoin('posko_dpl', 'posko_dpl.posko_id', '=', 'posko.id')
            ->leftJoin('dpl', 'dpl.id', '=', 'posko_dpl.dpl_id')
            ->leftJoin('posko_pengawas', 'posko_pengawas.posko_id', '=', 'posko.id')
            ->leftJoin('pengawas', 'pengawas.id', '=', 'posko_pengawas.pengawas_id')
            ->leftJoin('prodi', 'prodi.id', '=', 'dpl.prodi_id')
            ->leftJoin('tahun', 'tahun.id', '=', 'posko.tahun_id')
            ->select('posko.*', 'dpl.nama as dpl_nama', 'prodi.alias as prodi_alias', 'pengawas.nama as pengawas_nama', 'tahun.kode as tahun_kode');
        return DataTables::of($data)
            ->filter(function ($query) use ($search, $request) {
                $query->where(function ($query) use ($search) {
                    $query->orWhere('posko.nama', 'LIKE', "%$search%");
                    $query->orWhere('posko.lokasi', 'LIKE', "%$search%");
                    $query->orWhere('dpl.nama', 'LIKE', "%$search%");
                    $query->orWhere('prodi.alias', 'LIKE', "%$search%");
                });
                $query->when($request->tahun_id != '*', function ($query) use ($request) {
                    $query->where('posko.tahun_id', $request->tahun_id);
                });
                $query->when($request->dpl_id, function ($query) use ($request) {
                    $query->where('dpl_id', $request->dpl_id);
                });
                $query->when($request->pengawas_id, function ($query) use ($request) {
                    $query->where('pengawas_id', $request->pengawas_id);
                });
            })
            ->editColumn('dpl_nama', function ($row) {
                return $row->dpl_nama ? $row->dpl_nama : '<span class="badge badge-warning">Belum ada DPL</span>';
            })
            ->editColumn('prodi_alias', function ($row) {
                return $row->prodi_alias ? $row->prodi_alias : '<span class="badge badge-warning">Belum ada DPL</span>';
            })
            ->editColumn('pengawas_nama', function ($row) {
                return $row->pengawas_nama ? $row->pengawas_nama : '<span class="badge badge-warning">Belum ada Pengawas</span>';
            })
            ->addColumn('action', function ($row) use ($request) {
                $actionBtn = '
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Klik
                    </button>';
                $actionBtn .= '
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                $actionBtn .= '
                        <a class="dropdown-item" href="' . route('admin.posko.detail', ['posko' => $row->id]) . '">Detail</a>';
                if ($request->pengawas_id == null) {
                    $actionBtn .= '
                                    <div class="dropdown-divider"></div>
                                    <a href="' . route('admin.posko.cetakAbsensi', ['posko' => $row, 'tipe' => 'DPL']) . '" target="_blank" class="dropdown-item" >Cetak Absensi DPL</a>';
                }
                if ($request->dpl_id == null) {
                    $actionBtn .= '
                                    <a href="' . route('admin.posko.cetakAbsensi', ['posko' => $row, 'tipe' => 'pengawas']) . '" target="_blank" class="dropdown-item" >Cetak Absensi Pengawas</a>';
                }
                if ($request->dpl_id == null && $request->pengawas_id == null) {
                    $actionBtn .= '
                            <div class="dropdown-divider"></div>
                            <button type="button" class="dropdown-item"
                                data-toggle="modal" data-target="#modal_import"
                                data-id="' . $row->id . '"
                                data-extension="xls,xlsx"
                            >Import</button>';
                    $actionBtn .= '
                            <div class="dropdown-divider"></div>
                            <button type="button" class="dropdown-item"
                                data-toggle="modal" data-target="#modal_edit"
                                data-id="' . $row->id . '"
                                data-dpl_id="' . $row->dpl_id . '"
                                data-nama="' . $row->nama . '"
                                data-tahun_id="' . $row->tahun_id . '"
                                data-lokasi="' . $row->lokasi . '"
                                data-keterangan="' . $row->keterangan . '"
                            >Edit</button>';
                    $actionBtn .= '
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
            ->rawColumns(['action', 'dpl_nama', 'prodi_alias', 'pengawas_nama'])
            ->toJson();
    }

    public function add(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required',
                'tahun_id' => 'required',
                'lokasi' => 'required',
                'keterangan' => 'nullable',
            ]);

            $cek = Posko::where([
                ['nama', $request->nama],
                ['tahun_id', $request->tahun_id]
            ])->first();

            if ($cek) {
                $data = [
                    "message" => 500,
                    "data" => 'Data sudah ada, bisa diedit',
                    "req" => $request->all(),
                ];
                return $data;
            }

            $new = new Posko();
            $new->nama = $request->nama;
            $new->tahun_id = $request->tahun_id;
            $new->lokasi = $request->lokasi;
            $new->keterangan = $request->keterangan;
            $new->save();

            $data = [
                "message" => 200,
                "data" => 'Berhasil menambahkan Posko',
                "req" => $request->all(),
            ];
        } catch (\Throwable $th) {
            $data = [
                "message" => 500,
                "data" => $th->getMessage(),
                "req" => $request->all(),
            ];
        }
        return $data;
    }

    public function edit(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'nama' => 'required',
                'tahun_id' => 'required',
                'lokasi' => 'required',
                'keterangan' => 'nullable',
            ]);


            $cek = Posko::where([
                ['nama', $request->nama],
                ['tahun_id', $request->tahun_id],
                ['id', '!=', $request->id]
            ])->first();

            if ($cek) {
                $data = [
                    "message" => 500,
                    "data" => 'Data sudah ada, bisa diedit',
                    "req" => $request->all(),
                ];
                return $data;
            }

            $edit = Posko::findOrFail($request->id);
            $edit->nama = $request->nama;
            $edit->tahun_id = $request->tahun_id;
            $edit->lokasi = $request->lokasi;
            $edit->keterangan = $request->keterangan;
            $edit->save();

            $data = [
                'message' => 200,
                'data' => 'Berhasil mengedit Posko',
                'req' => $request->all(),
            ];
        } catch (\Throwable $th) {
            $data = [
                'message' => 500,
                'data' => $th->getMessage(),
                'req' => $request->all(),
            ];
        }

        return $data;
    }

    public function delete(Request $request)
    {
        try {
            $dataValidated = $request->validate([
                'id' => 'required',
            ]);

            $delete = Posko::where('id', $dataValidated['id'])->first();

            $delete->destroy($dataValidated['id']);
            $data = [
                "message" => 200,
                "data" => "Berhasil menghapus data",
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                "message" => 500,
                "data" => 'Gagal menghapus data',
            ];
            return $data;
        }
    }

    public function detail(Posko $posko)
    {
        $prodi = Prodi::orderBy('jenjang', 'asc')->get();
        $dpl = count($posko->poskoDpl) > 0 ? $posko->poskoDpl[0]->dpl : null;

        $poskoDpl = null;
        if ($dpl) {
            $username = $dpl->user->username;
            $prodiDpl = $dpl->prodi->nama;
            $poskoDpl = "$dpl->dosen_id - $username - $dpl->nama - $prodiDpl";
        }
        $pengawas = count($posko->poskoPengawas) > 0 ? $posko->poskoPengawas[0]->pengawas : null;
        $poskoPengawas = null;
        if ($pengawas) {
            $username = $pengawas->user->username;
            $poskoPengawas = [
                'label' => "$username - $pengawas->nama",
                'value' => $pengawas->id
            ];
        }

        $dpl = \Auth::user()->role->nama == 'dpl' ? \Auth::user()->dpl : null;
        $pengawas = \Auth::user()->role->nama == 'pengawas' ? \Auth::user()->pengawas : null;

        return view('admin.posko.detail', compact('posko', 'prodi', 'poskoDpl', 'poskoPengawas', 'dpl', 'pengawas'));
    }

    public function addPeserta(Posko $posko, Request $request)
    {
        try {
            $request->validate([
                'peserta_id' => 'required',
            ]);

            $peserta = Peserta::findOrFail($request->peserta_id);

            // $checkPosko = PoskoPeserta::where('peserta_id', $peserta->id)->first();
            // if ($checkPosko) {
            //     return abort(500, 'Peserta PROLAP sudah masuk di posko lain yaitu di ' . $checkPosko->posko->nama);
            // }

            $poskoPeserta = PoskoPeserta::where([
                ['posko_id', $posko->id],
                ['peserta_id', $peserta->id]
            ])->first();

            if ($poskoPeserta) {
                return abort(500, 'Peserta PROLAP sudah masuk di Posko ini');
            }

            PoskoPeserta::create([
                'posko_id' => $posko->id,
                'peserta_id' => $peserta->id
            ]);

            return [
                "message" => 200,
                "data" => "Berhasil menambahkan Peserta di Posko Ini",
                "req" => $request->all(),
            ];
        } catch (ValidationException $e) {
            return [
                'message' => 500,
                'data' => 'Peserta PROLAP tidak ditemukan',
            ];
        } catch (\Throwable $th) {
            return [
                "message" => 500,
                "data" => $th->getMessage(),
                "req" => $request->all(),
            ];
        }
    }

    public function deletePeserta(Posko $posko, Request $request)
    {
        try {
            \DB::beginTransaction();

            $request->validate([
                'posko_peserta_id' => 'required'
            ]);

            $poskoPeserta = PoskoPeserta::find($request->posko_peserta_id);
            $penilaianDpl = @$poskoPeserta->penilaianDpl ?? [];
            foreach ($penilaianDpl as $key => $value) {
                PenilaianDplDetail::where('penilaian_dpl_id', $value->id)->delete();
                $value->delete();
            }
            $penilaianPengawas = @$poskoPeserta->penilaianPengawas ?? [];
            foreach ($penilaianPengawas as $key => $value) {
                PenilaianPengawasDetail::where('penilaian_pengawas_id', $value->id)->delete();
                $value->delete();
            }

            if (@$poskoPeserta->peserta->nilai) {
                $poskoPeserta->peserta->nilai->delete();
            }

            AbsensiPsDplDetail::where('posko_peserta_id', $poskoPeserta->id)->delete();
            AbsensiPsPengawasDetail::where('posko_peserta_id', $poskoPeserta->id)->delete();

            PoskoPeserta::destroy($request->posko_peserta_id);

            \DB::commit();
            return [
                "message" => 200,
                "data" => "Berhasil menghapus Peserta di Posko Ini",
                "req" => $request->all(),
            ];
        } catch (ValidationException $e) {
            \DB::rollback();
            return [
                'message' => 500,
                'data' => 'Peserta PROLAP tidak ditemukan',
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                "message" => 500,
                "data" => "Gagal menghapus Peserta di Posko Ini",
                "req" => $request->all(),
                "err" => $th->getMessage(),
            ];
        }
    }

    public function deleteDpl(Posko $posko, Request $request)
    {
        try {
            \DB::beginTransaction();

            $request->validate([
                'posko_dpl_id' => 'required'
            ]);

            PoskoDpl::destroy($request->posko_dpl_id);

            \DB::commit();
            return [
                "message" => 200,
                "data" => "Berhasil menghapus DPL di Posko Ini",
                "req" => $request->all(),
            ];
        } catch (ValidationException $e) {
            \DB::rollback();
            return [
                'message' => 500,
                'data' => 'DPL PROLAP tidak ditemukan',
            ];
        } catch (\Throwable $th) {
            \DB::rollback();
            return [
                "message" => 500,
                "data" => "Gagal menghapus DPL di Posko Ini",
                "req" => $request->all(),
                "err" => $th->getMessage(),
            ];
        }
    }

    public function addDpl(Posko $posko, Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'dpl_id' => 'nullable',
                'dosen_id' => 'required',
                'nama' => 'required',
                'niy' => 'required',
                'jenis_kelamin' => 'required',
                'hp' => 'nullable',
                'prodi_id' => 'required',
                'email' => 'nullable',
            ]);

            $dpl = DPL::find($request->dpl_id);
            // add DPL when not exist
            if (!$dpl) {
                // add user
                $password = "123456";
                $user = User::where('username', $request->niy)->first();
                if (!$user) {
                    $user = new User();
                }
                $user->username = $request->niy;
                $user->nama = $request->nama;
                $user->email = $request->email;
                $user->password = \Hash::make($password);
                $user->no_unik = $password;
                $user->jenis_kelamin = $request->jenis_kelamin;
                $user->role_id = 4;
                $user->save();
                // add DPL
                $dpl = DPL::create([
                    'user_id' => $user->id,
                    'prodi_id' => $request->prodi_id,
                    'dosen_id' => $request->dosen_id,
                    'nama' => $request->nama,
                    'hp' => $request->hp,
                    'status' => 'Aktif'
                ]);
            }

            // edit DPL when exist
            if ($dpl) {
                // edit user
                $user = $dpl->user;
                $user->username = $request->niy;
                $user->nama = $request->nama;
                $user->email = $request->email;
                $user->jenis_kelamin = $request->jenis_kelamin;
                $user->save();

                // edit DPL
                $dpl->prodi_id = $request->prodi_id;
                $dpl->dosen_id = $request->dosen_id;
                $dpl->nama = $request->nama;
                $dpl->hp = $request->hp;
                $dpl->status = 'Aktif';
                $dpl->save();
            }

            // add PoskoDPL

            $poskoDpl = PoskoDpl::where('posko_id', $posko->id)
                ->where('dpl_id', $dpl->id)->exists();

            if ($poskoDpl) {
                abort(500, 'DPL PROLAP sudah masuk di Posko ini');
            }

            $poskoDpl = new PoskoDpl();
            $poskoDpl->posko_id = $posko->id;
            $poskoDpl->dpl_id = $dpl->id;
            $poskoDpl->save();

            // $poskoDpl = PoskoDpl::where('posko_id', $posko->id)
            //     ->first();

            // $oldDpl = false;
            // if (! $poskoDpl){
            //     $poskoDpl = new PoskoDpl();
            // } else {
            //     $oldDpl = $poskoDpl->dpl;
            // }

            // $poskoDpl->posko_id = $posko->id;
            // $poskoDpl->dpl_id = $dpl->id;
            // $poskoDpl->save();

            // if ($oldDpl) {
            //     // $poskoDpl->delete();
            //     $nPosko = count($oldDpl->poskoDpl);
            //     if ($nPosko == 0 && $oldDpl->id != $dpl->id) {
            //         $oldDpl->update([
            //             'status' => 'Tidak Aktif'
            //         ]);
            //     }
            // }

            \DB::commit();
            return [
                'message' => 200,
                'data' => 'Berhasil menambahkan DPL di Posko Ini',
                'req' => $request->all(),
                'dpl' => $dpl
            ];
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            return [
                'message' => 500,
                'data' => 'Gagal menambahkan DPL di Posko Ini',
                'req' => $request->all(),
                'err' => $th->getMessage()
            ];
        }
    }

    public function addPengawas(Posko $posko, Request $request)
    {
        try {
            \DB::beginTransaction();
            $request->validate([
                'pengawas_id' => 'nullable',
                'nama' => 'required',
                'jenis_kelamin' => 'required',
                'hp' => 'nullable',
                'email' => 'nullable',
            ]);

            $pengawas = Pengawas::find($request->pengawas_id);
            // add Pengawas when not exist
            if (!$pengawas) {
                // add user
                $generateUsername = \Helper::generateRandomString();
                $user = new User();
                $user->username = $generateUsername;
                $user->nama = $request->nama;
                $user->password = \Hash::make($generateUsername);
                $user->no_unik = $generateUsername;
                $user->email = $request->email;
                $user->jenis_kelamin = $request->jenis_kelamin;
                $user->role_id = 5; //pengawas
                $user->save();

                // add pengawas
                $pengawas = new Pengawas();
                $pengawas->user_id = $user->id;
                $pengawas->nama = $request->nama;
                $pengawas->hp = $request->hp;
                $pengawas->status = 'Aktif';
                $pengawas->save();
            }

            // edit Pengawas when exist
            if ($pengawas) {
                // edit user
                $user = $pengawas->user;
                $user->nama = $request->nama;
                $user->email = $request->email;
                $user->jenis_kelamin = $request->jenis_kelamin;
                $user->save();

                // edit Pengawas
                $pengawas->user_id = $user->id;
                $pengawas->nama = $request->nama;
                $pengawas->hp = $request->hp;
                $pengawas->status = 'Aktif';
                $pengawas->save();
            }

            // add PoskoPengawas
            $poskoPengawas = PoskoPengawas::where('posko_id', $posko->id)
                ->first();

            if ($poskoPengawas) {
                $oldPengawas = $poskoPengawas->pengawas;
                // $poskoPengawas->delete();
                $nPosko = count($oldPengawas->poskoPengawas);
                if ($nPosko == 0 && $oldPengawas->id != $pengawas->id) {
                    $oldPengawas->update([
                        'status' => 'Tidak Aktif'
                    ]);
                }
            } else {
                $poskoPengawas = new PoskoPengawas();
            }

            $poskoPengawas->posko_id = $posko->id;
            $poskoPengawas->pengawas_id = $pengawas->id;
            $poskoPengawas->save();

            \DB::commit();
            return [
                'status' => true,
                'message' => 200,
                'data' => 'Berhasil menambahkan Pengawas di Posko Ini',
                'req' => $request->all(),
                'pengawas' => $pengawas
            ];
        } catch (\Throwable $th) {
            //throw $th;
            \DB::rollback();
            return [
                'status' => false,
                'message' => 500,
                'data' => 'Gagal menambahkan Pengawas di Posko Ini',
                'req' => $request->all(),
                'err' => $th->getMessage()
            ];
        }
    }

    public function cetakAbsensi(Posko $posko, $tipe)
    {
        $poskoPeserta = $posko->poskoPeserta;
        $poskoDpl = $posko->poskoDpl->load('posko', 'dpl');
        $poskoPengawas = $posko->poskoPengawas->load('posko', 'pengawas');

        $jumlahPertemuan = 25;
        $tahun = $posko->tahun;

        if (strtolower($tipe) == 'dpl') {
            $petugas = [
                'jabatan' => 'Dosen Pembimbing Lapangan',
                'db' => 'dpl',
                'data' => $poskoDpl,
            ];
        }
        if (strtolower($tipe) == 'pengawas') {
            $petugas = [
                'jabatan' => 'Pengawas',
                'db' => 'pengawas',
                'data' => $poskoPengawas,
            ];
        }

        $pdf = Pdf::loadView('admin.posko.cetak', compact('poskoPeserta', 'jumlahPertemuan', 'posko', 'poskoDpl', 'poskoPengawas', 'tahun', 'tipe', 'petugas'));
        return $pdf->setPaper('a4', 'landscape')->stream('absensi.pdf');
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'file' => 'required|mimes:xls,xlsx'
            ]);

            $file = $request->file('file');

            $posko = Posko::find($request->id);
            $import = new PoskoImport($posko);
            \Excel::import($import, $file);
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
