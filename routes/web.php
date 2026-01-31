<?php

use App\Http\Controllers\Admin\Absensi\DplController;
use App\Http\Controllers\Admin\Absensi\PamongController as AbsensiPamongController;
use App\Http\Controllers\Admin\Absensi\PengawasController as AbsensiPengawasController;
use App\Http\Controllers\Admin\BiayaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DokumenWajib\DplController as DokumenWajinDplController;
use App\Http\Controllers\Admin\DplController as AdminDplController;
use App\Http\Controllers\Admin\ImportDataController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\Jurnal\PengawasController as JurnalPengawasController;
use App\Http\Controllers\Admin\KegiatanMahasiswaController;
use App\Http\Controllers\Admin\KomponenNilaiController;
use App\Http\Controllers\Admin\KuotaController;
use App\Http\Controllers\Admin\ListDokumenController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\PamongController;
use App\Http\Controllers\Admin\PedomanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PengawasController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\Penilaian\DplController as PenilaianDplController;
use App\Http\Controllers\Admin\Penilaian\PamongController as PenilaianPamongController;
use App\Http\Controllers\Admin\Penilaian\PesertaController as PenilaianPesertaController;
use App\Http\Controllers\Admin\Penugasan\DplController as PenugasanDplController;
use App\Http\Controllers\Admin\Penugasan\PamongController as PenugasanPamongController;
use App\Http\Controllers\Admin\PesertaController as AdminPesertaController;
use App\Http\Controllers\Admin\PoskoController;
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TahunController;
use App\Http\Controllers\Auth\LoginAdminController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Dpl\DashboardController as DplDashboardController;
use App\Http\Controllers\Operasi\DaftarTugasController;
use App\Http\Controllers\Operasi\DokumenController as OperasiDokumenController;
use App\Http\Controllers\Operasi\DosenController;
use App\Http\Controllers\Operasi\KalenderController;
use App\Http\Controllers\Operasi\KuotaController as OperasiKuotaController;
use App\Http\Controllers\Operasi\PengawasController as OperasiPengawasController;
use App\Http\Controllers\Operasi\PesertaController as OperasiPesertaController;
use App\Http\Controllers\Operasi\PesertaKknController;
use App\Http\Controllers\Operasi\ThemeController;
use App\Http\Controllers\Pamong\DashboardController as PamongDashboardController;
use App\Http\Controllers\Pengawas\DashboardController as PengawasDashboardController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboardController;
use App\Http\Controllers\Peserta\FormulirController as PesertaFormulirController;
use App\Http\Controllers\Peserta\PesertaController;
use App\Http\Controllers\RootController;
use App\Http\Controllers\TestingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Auth::routes(
    ['register' => false]
);

// Route::group(['middleware' => ['jadwal']], function () {
//     Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
//     Route::post('/register', [RegisterController::class, 'register']);
// });

Route::get('/', [RootController::class, 'root'])->name('root');
Route::get('/home', [RootController::class, 'home'])->name('home')->middleware('auth');
Route::get('/pengembangan', [RootController::class, 'pengembangan'])->name('pengembangan');

Route::prefix('otp')->group(function () {
    Route::get('/{siswa}', [OtpController::class, 'index'])->name('otp')->middleware('otp:1');
    Route::post('/{siswa}', [OtpController::class, 'process'])->name('otp.process')->middleware('otp:1');
    Route::put('/{siswa}', [OtpController::class, 'savePassword'])->name('otp.savePassword')->middleware('otp:1');
    Route::get('/{siswa}/resend', [OtpController::class, 'resend'])->name('otp.resend')->middleware('otp:1');
    Route::get('/{siswa}/setPassword', [OtpController::class, 'setPassword'])->name('otp.setPassword')->middleware('otp:0');
});

Route::prefix('admin')->group(function () {
    Route::get('/', [LoginAdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/', [LoginAdminController::class, 'login'])->name('admin.login.process');
    Route::get('/login', [LoginAdminController::class, 'backToLogin'])->name('admin.login.backToLogin');

    Route::group(['middleware' => ['auth']], function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
            Route::get('/getDaurah', [DashboardController::class, 'getDaurah'])->name('admin.dashboard.getDaurah');
            Route::post('/updateTahunPelajaranAdmin', [DashboardController::class, 'updateTahunPelajaranAdmin'])->name('admin.dashboard.updateTahunPelajaranAdmin');
        });

        Route::prefix('pengguna')->middleware('role:admin')->group(function () {
            Route::get('/', [PenggunaController::class, 'index'])->name('admin.pengguna');
            Route::get('/data', [PenggunaController::class, 'data'])->name('admin.pengguna.data');
            Route::post('/add', [PenggunaController::class, 'add'])->name('admin.pengguna.add');
            Route::post('/edit', [PenggunaController::class, 'edit'])->name('admin.pengguna.edit');
            Route::delete('/delete', [PenggunaController::class, 'delete'])->name('admin.pengguna.delete');
        });

        Route::prefix('komponen-nilai')->middleware('role:admin')->group(function () {
            Route::get('/', [KomponenNilaiController::class, 'index'])->name('admin.komponen-nilai');
            Route::get('/data', [KomponenNilaiController::class, 'data'])->name('admin.komponen-nilai.data');
            Route::post('/add', [KomponenNilaiController::class, 'add'])->name('admin.komponen-nilai.add');
            Route::post('/edit', [KomponenNilaiController::class, 'edit'])->name('admin.komponen-nilai.edit');
            Route::delete('/delete', [KomponenNilaiController::class, 'delete'])->name('admin.komponen-nilai.delete');
        });

        Route::prefix('biaya')->middleware('role:admin')->group(function () {
            Route::get('/', [BiayaController::class, 'index'])->name('admin.biaya');
            Route::get('/data', [BiayaController::class, 'data'])->name('admin.biaya.data');
            Route::post('/add', [BiayaController::class, 'add'])->name('admin.biaya.add');
            Route::post('/edit', [BiayaController::class, 'edit'])->name('admin.biaya.edit');
            Route::delete('/delete', [BiayaController::class, 'delete'])->name('admin.biaya.delete');
        });

        Route::prefix('tahun')->middleware('role:admin')->group(function () {
            Route::get('/', [TahunController::class, 'index'])->name('admin.tahun');
            Route::get('/data', [TahunController::class, 'data'])->name('admin.tahun.data');
            Route::post('/add', [TahunController::class, 'add'])->name('admin.tahun.add');
            Route::post('/edit', [TahunController::class, 'edit'])->name('admin.tahun.edit');
            Route::delete('/delete', [TahunController::class, 'delete'])->name('admin.tahun.delete');
        });

        Route::prefix('prodi')->middleware('role:admin')->group(function () {
            Route::get('/', [ProdiController::class, 'index'])->name('admin.prodi');
            Route::get('/data', [ProdiController::class, 'data'])->name('admin.prodi.data');
            Route::post('/add', [ProdiController::class, 'add'])->name('admin.prodi.add');
            Route::post('/edit', [ProdiController::class, 'edit'])->name('admin.prodi.edit');
            Route::delete('/delete', [ProdiController::class, 'delete'])->name('admin.prodi.delete');
        });

        Route::prefix('pedoman')->middleware('role:admin')->group(function () {
            Route::get('/', [PedomanController::class, 'index'])->name('admin.pedoman');
            Route::get('/data', [PedomanController::class, 'data'])->name('admin.pedoman.data');
            Route::post('/add', [PedomanController::class, 'add'])->name('admin.pedoman.add');
            Route::post('/edit', [PedomanController::class, 'edit'])->name('admin.pedoman.edit');
            Route::delete('/delete', [PedomanController::class, 'delete'])->name('admin.pedoman.delete');
        });

        Route::prefix('kuota')->middleware('role:admin')->group(function () {
            Route::get('/', [KuotaController::class, 'index'])->name('admin.kuota');
            Route::get('/data', [KuotaController::class, 'data'])->name('admin.kuota.data');
            Route::post('/add', [KuotaController::class, 'add'])->name('admin.kuota.add');
            Route::post('/edit', [KuotaController::class, 'edit'])->name('admin.kuota.edit');
            Route::delete('/delete', [KuotaController::class, 'delete'])->name('admin.kuota.delete');
        });

        Route::prefix('posko')->middleware('role:admin,dpl,pengawas')->group(function () {
            Route::get('/', [PoskoController::class, 'index'])->name('admin.posko');
            Route::get('/data', [PoskoController::class, 'data'])->name('admin.posko.data');
            Route::post('/add', [PoskoController::class, 'add'])->name('admin.posko.add');
            Route::post('/edit', [PoskoController::class, 'edit'])->name('admin.posko.edit');
            Route::delete('/delete', [PoskoController::class, 'delete'])->name('admin.posko.delete');
            Route::get('/cetakAbsensi/{posko}/{tipe}', [PoskoController::class, 'cetakAbsensi'])->name('admin.posko.cetakAbsensi');
            Route::post('/import', [PoskoController::class, 'import'])->name('admin.posko.import');

            Route::prefix('/detail/{posko}')->group(function () {
                Route::get('/', [PoskoController::class, 'detail'])->name('admin.posko.detail');
                Route::post('/addPeserta', [PoskoController::class, 'addPeserta'])->name('admin.posko.addPeserta');
                Route::post('/deletePeserta', [PoskoController::class, 'deletePeserta'])->name('admin.posko.deletePeserta');
                
                Route::post('/addDpl', [PoskoController::class, 'addDpl'])->name('admin.posko.addDpl');
                Route::post('/deleteDpl', [PoskoController::class, 'deleteDpl'])->name('admin.posko.deleteDpl');
                Route::post('/addPengawas', [PoskoController::class, 'addPengawas'])->name('admin.posko.addPengawas');
            });
        });

        Route::prefix('jadwal')->middleware('role:admin')->group(function () {
            Route::get('/', [JadwalController::class, 'index'])->name('admin.jadwal');
            Route::get('/data', [JadwalController::class, 'data'])->name('admin.jadwal.data');
            Route::post('/add', [JadwalController::class, 'add'])->name('admin.jadwal.add');
            Route::post('/edit', [JadwalController::class, 'edit'])->name('admin.jadwal.edit');
            Route::delete('/delete', [JadwalController::class, 'delete'])->name('admin.jadwal.delete');
        });

        Route::prefix('list-dokumen')->middleware('role:admin')->group(function () {
            Route::get('/', [ListDokumenController::class, 'index'])->name('admin.list-dokumen');
            Route::get('/data', [ListDokumenController::class, 'data'])->name('admin.list-dokumen.data');
            Route::post('/add', [ListDokumenController::class, 'add'])->name('admin.list-dokumen.add');
            Route::post('/edit', [ListDokumenController::class, 'edit'])->name('admin.list-dokumen.edit');
            Route::delete('/delete', [ListDokumenController::class, 'delete'])->name('admin.list-dokumen.delete');
        });

        Route::prefix('nilai')->middleware('role:admin,peserta')->group(function () {
            Route::get('/', [NilaiController::class, 'index'])->name('admin.nilai')->middleware('role:admin');
            Route::get('/data', [NilaiController::class, 'data'])->name('admin.nilai.data')->middleware('role:admin');
            Route::get('/detail/{peserta}', [NilaiController::class, 'detail'])->name('admin.nilai.detail');
        });

        Route::prefix('pembayaran')->middleware('role:admin,keuangan')->group(function () {
            Route::get('/', [PembayaranController::class, 'index'])->name('admin.pembayaran');
            Route::get('/data', [PembayaranController::class, 'data'])->name('admin.pembayaran.data');
            Route::get('/dataInfo', [PembayaranController::class, 'dataInfo'])->name('admin.pembayaran.dataInfo');
            Route::post('/add', [PembayaranController::class, 'add'])->name('admin.pembayaran.add');
            Route::post('/edit', [PembayaranController::class, 'edit'])->name('admin.pembayaran.edit');
            Route::delete('/delete', [PembayaranController::class, 'delete'])->name('admin.pembayaran.delete');
            Route::get('/kwitansi/{pembayaran}', [PembayaranController::class, 'kwitansi'])->name('admin.pembayaran.kwitansi');
            Route::post('/registasi', [PembayaranController::class, 'registrasi'])->name('admin.pembayaran.registrasi');
            Route::post('/export', [PembayaranController::class, 'export'])->name('admin.pembayaran.export');
        });

        Route::prefix('dpl')->middleware('role:admin,dpl,pengawas')->group(function () {
            Route::get('/', [AdminDplController::class, 'index'])->name('admin.dpl');
            Route::get('/data', [AdminDplController::class, 'data'])->name('admin.dpl.data');
            Route::post('/add', [AdminDplController::class, 'add'])->name('admin.dpl.add');
            Route::post('/edit', [AdminDplController::class, 'edit'])->name('admin.dpl.edit');
            Route::delete('/delete', [AdminDplController::class, 'delete'])->name('admin.dpl.delete');
            Route::post('/export', [AdminDplController::class, 'export'])->name('admin.dpl.export');

            Route::prefix('detail/{dpl}')->group(function () {
                Route::get('/', [AdminDplController::class, 'detail'])->name('admin.dpl.detail');
                Route::get('/posko', [AdminDplController::class, 'detailPosko'])->name('admin.dpl.detailPosko');
                Route::get('/absensi', [AdminDplController::class, 'detailAbsensi'])->name('admin.dpl.detailAbsensi');
                Route::get('/kegiatanMahasiswa', [AdminDplController::class, 'detailKegiatanMahasiswa'])->name('admin.dpl.detailKegiatanMahasiswa');
                Route::get('/penugasan', [AdminDplController::class, 'detailPenugasan'])->name('admin.dpl.detailPenugasan');
                Route::get('/dokumen-wajib', [AdminDplController::class, 'detailDokumenWajib'])->name('admin.dpl.detailDokumenWajib');
                Route::get('/penilaian', [AdminDplController::class, 'detailPenilaian'])->name('admin.dpl.detailPenilaian');
                Route::get('/monitoring', [AdminDplController::class, 'detailMonitoring'])->name('admin.dpl.detailMonitoring');
            });
        });

        Route::prefix('pengawas')->middleware('role:admin,pengawas')->group(function () {
            Route::get('/', [PengawasController::class, 'index'])->name('admin.pengawas');
            Route::get('/data', [PengawasController::class, 'data'])->name('admin.pengawas.data');
            Route::post('/add', [PengawasController::class, 'add'])->name('admin.pengawas.add');
            Route::post('/edit', [PengawasController::class, 'edit'])->name('admin.pengawas.edit');
            Route::delete('/delete', [PengawasController::class, 'delete'])->name('admin.pengawas.delete');
            Route::post('/export', [PengawasController::class, 'export'])->name('admin.pengawas.export');

            Route::prefix('detail/{pengawas}')->group(function () {
                Route::get('/', [PengawasController::class, 'detail'])->name('admin.pengawas.detail');
                Route::get('/posko', [PengawasController::class, 'detailPosko'])->name('admin.pengawas.detailPosko');
                Route::get('/absensi', [PengawasController::class, 'detailAbsensi'])->name('admin.pengawas.detailAbsensi');
                Route::get('/jurnal', [PengawasController::class, 'detailJurnal'])->name('admin.pengawas.detailJurnal');
                Route::get('/penugasan', [PengawasController::class, 'detailPenugasan'])->name('admin.pengawas.detailPenugasan');
                Route::get('/monitoring', [PengawasController::class, 'detailMonitoring'])->name('admin.pengawas.detailMonitoring');
            });
        });

        Route::prefix('pamong')->middleware('role:admin,pamong')->group(function () {
            Route::get('/', [PamongController::class, 'index'])->name('admin.pamong');
            Route::get('/data', [PamongController::class, 'data'])->name('admin.pamong.data');
            Route::post('/add', [PamongController::class, 'add'])->name('admin.pamong.add');
            Route::post('/edit', [PamongController::class, 'edit'])->name('admin.pamong.edit');
            Route::delete('/delete', [PamongController::class, 'delete'])->name('admin.pamong.delete');
            Route::post('/export', [PamongController::class, 'export'])->name('admin.pamong.export');
            Route::get('/cetakAbsensi/{pamong}', [PamongController::class, 'cetakAbsensi'])->name('admin.pamong.cetakAbsensi');
            Route::post('/import', [PamongController::class, 'import'])->name('admin.pamong.import');

            Route::prefix('detail/{pamong}')->group(function () {
                Route::get('/', [PamongController::class, 'detail'])->name('admin.pamong.detail');
                Route::get('/peserta', [PamongController::class, 'detailPeserta'])->name('admin.pamong.detailPeserta');
                Route::get('/absensi', [PamongController::class, 'detailAbsensi'])->name('admin.pamong.detailAbsensi');
                Route::get('/penugasan', [PamongController::class, 'detailPenugasan'])->name('admin.pamong.detailPenugasan');
                Route::get('/penilaian', [PamongController::class, 'detailPenilaian'])->name('admin.pamong.detailPenilaian');
                Route::get('/monitoring', [PamongController::class, 'detailMonitoring'])->name('admin.pamong.detailMonitoring');

                Route::post('/addPeserta', [PamongController::class, 'addPeserta'])->name('admin.pamong.addPeserta');
                Route::post('/deletePeserta', [PamongController::class, 'deletePeserta'])->name('admin.pamong.deletePeserta');
            });
        });

        Route::prefix('peserta')->middleware('role:admin')->group(function () {
            Route::get('/', [AdminPesertaController::class, 'index'])->name('admin.peserta');
            Route::delete('/', [AdminPesertaController::class, 'delete'])->name('admin.peserta.delete');
            Route::get('/data', [AdminPesertaController::class, 'data'])->name('admin.peserta.data');
            Route::get('/detail/{peserta}', [AdminPesertaController::class, 'detail'])->name('admin.peserta.detail');
            Route::put('/detail/{peserta}/', [AdminPesertaController::class, 'update'])->name('admin.peserta.update');
            Route::get('/detail/{peserta}/dataDokumen', [AdminPesertaController::class, 'dataDokumen'])->name('admin.peserta.dataDokumen');
            Route::delete('/detail/{peserta}/deleteDokumen', [AdminPesertaController::class, 'deleteDokumen'])->name('admin.peserta.deleteDokumen');
            Route::post('/detail/{peserta}/saveDokumen', [AdminPesertaController::class, 'saveDokumen'])->name('admin.peserta.saveDokumen');
            Route::post('/upload-file', [AdminPesertaController::class, 'fileUpload'])->name('admin.peserta.fileUpload');
            Route::post('/delete-file', [AdminPesertaController::class, 'fileDelete'])->name('admin.peserta.fileDelete');
            Route::put('/detail/{peserta}/updatePassword', [AdminPesertaController::class, 'updatePassword'])->name('admin.peserta.updatePassword');
            Route::post('/export', [AdminPesertaController::class, 'export'])->name('admin.peserta.export');
            Route::post('/import', [AdminPesertaController::class, 'import'])->name('admin.peserta.import');
            Route::put('/detail/{peserta}/updateStatusTerverifikasi', [AdminPesertaController::class, 'updateStatusTerverifikasi'])->name('admin.peserta.updateStatusTerverifikasi');

            // idCard
            Route::get('/idCard/{idPeserta}/{noUnik}/{download}', [AdminPesertaController::class, 'IdCard'])->name('peserta.peserta.idCard');
        });

        Route::prefix('absensi')->middleware('role:admin,dpl,pengawas,pamong')->group(function () {
            Route::prefix('dpl')->group(function () {
                Route::get('/', [DplController::class, 'index'])->name('admin.absensi.dpl');
                Route::get('/data', [DplController::class, 'data'])->name('admin.absensi.data.dpl');
                Route::get('/downloadExcel', [DplController::class, 'downloadExcel'])->name('admin.absensi.downloadExcel.dpl');
                Route::post('/add', [DplController::class, 'store'])->name('admin.absensi.store');

                Route::prefix('detail/{idPoskoDpl}')->group(function () {
                    Route::get('/', [DplController::class, 'detail'])->name('admin.absensi.detail');
                    Route::get('/data', [DplController::class, 'detailData'])->name('admin.absensi.detail.data');
                    Route::get('/dataPeserta', [DplController::class, 'detailDataPeserta'])->name('admin.absensi.detail.dataPeserta');
                    Route::post('/simpan', [DplController::class, 'simpan'])->name('admin.absensi.detail.simpan');
                    Route::get('/delete/{no}', [DplController::class, 'del'])->name('admin.absensi.detail.delete');

                    Route::prefix('edit/{idAbsensiPsDpl}')->group(function () {
                        Route::get('/', [DplController::class, 'formEdit'])->name('admin.absensi.detail.edit.form');
                        Route::get('/data_edit', [DplController::class, 'dataEdit'])->name('admin.absensi.detail.edit.dedit');
                        Route::post('/edit', [DplController::class, 'edit'])->name('admin.absensi.detail.edit.simpan');
                    });
                });
                Route::prefix('input/{idPoskoDpl}')->group(function () {
                    Route::get('/', [DplController::class, 'input'])->name('admin.absensi.input');
                    Route::get('/dataInput', [DplController::class, 'dataInput'])->name('admin.absensi.input.dataInput');
                    Route::get('/inputDetail', [DplController::class, 'inputDetail'])->name('admin.absensi.input.inputDetail');
                    Route::post('/simpanDetail', [DplController::class, 'simpanDetail'])->name('admin.absensi.input.simpandetail');
                });
            });
            Route::prefix('pengawas')->group(function () {
                Route::get('/', [AbsensiPengawasController::class, 'index'])->name('admin.absensi.pws');
                Route::get('/data', [AbsensiPengawasController::class, 'data'])->name('admin.absensi.pws.data');
                Route::get('/downloadExcel', [AbsensiPengawasController::class, 'downloadExcel'])->name('admin.absensi.pws.downloadExcel');

                Route::prefix('detail/{idPoskoPengawas}')->group(function () {
                    Route::get('/', [AbsensiPengawasController::class, 'detail'])->name('admin.absensi.pws.detail');
                    Route::get('/data', [AbsensiPengawasController::class, 'dataDetail'])->name('admin.absensi.pws.detail.data');
                    Route::get('/dataPeserta', [AbsensiPengawasController::class, 'detailDataPeserta'])->name('admin.absensi.pws.detail.dataPeserta');
                    Route::get('/delete/{no}', [AbsensiPengawasController::class, 'del'])->name('admin.absensi.pws.detail.hapus');

                    Route::prefix('edit/{idAbsensiPsPengawas}')->group(function () {
                        Route::get('/', [AbsensiPengawasController::class, 'formEdit'])->name('admin.absensi.pws.detail.edit.form');
                        Route::get('/data_edit', [AbsensiPengawasController::class, 'dataEdit'])->name('admin.absensi.pws.detail.edit.dedit');
                        Route::post('/edit', [AbsensiPengawasController::class, 'edit'])->name('admin.absensi.pws.detail.edit.simpan');
                    });
                });

                Route::prefix('input/{idPoskoPengawas}')->group(function () {
                    Route::get('/', [AbsensiPengawasController::class, 'input'])->name('admin.absensi.pws.input');
                    Route::get('/inputDetail', [AbsensiPengawasController::class, 'inputDetail'])->name('admin.absensi.pws.input.inputDetail');
                    Route::post('/simpanDetail', [AbsensiPengawasController::class, 'simpanDetail'])->name('admin.absensi.pws.input.simpandetail');
                });
            });
            Route::prefix('pamong')->group(function () {
                Route::get('/', [AbsensiPamongController::class, 'index'])->name('admin.absensi.pamong');
                Route::get('/data', [AbsensiPamongController::class, 'data'])->name('admin.absensi.pamong.data');
                Route::post('/add', [AbsensiPamongController::class, 'store'])->name('admin.absensi.pamong.store');
                Route::get('/delete/{no}', [AbsensiPamongController::class, 'del'])->name('admin.absensi.pamong.delete');

                Route::prefix('detail/{idPamong}')->group(function () {
                    Route::get('/', [AbsensiPamongController::class, 'detail'])->name('admin.absensi.pamong.detail');
                    Route::get('/data', [AbsensiPamongController::class, 'dataDetail'])->name('admin.absensi.pamong.detail.data');
                    Route::get('/dataPeserta', [AbsensiPamongController::class, 'detailDataPeserta'])->name('admin.absensi.pamong.detail.dataPeserta');
                    Route::get('/delete/{no}', [AbsensiPamongController::class, 'del'])->name('admin.absensi.pamong.detail.delete');

                    Route::prefix('edit/{idAbsensiPsPamong}')->group(function () {
                        Route::get('/', [AbsensiPamongController::class, 'formEdit'])->name('admin.absensi.pamong.detail.edit');
                        Route::get('/data', [AbsensiPamongController::class, 'dataEdit'])->name('admin.absensi.pamong.detail.edit.dedit');
                        Route::post('/simpan', [AbsensiPamongController::class, 'simpanEdit'])->name('admin.absensi.pamong.detail.edit.simpan');
                    });
                });
                Route::prefix('input/{idPamong}')->group(function () {
                    Route::get('/', [AbsensiPamongController::class, 'formInput'])->name('admin.absensi.pamong.input');
                    Route::get('/data', [AbsensiPamongController::class, 'dataInput'])->name('admin.absensi.pamong.input.data');
                    Route::post('/simpan', [AbsensiPamongController::class, 'simpan'])->name('admin.absensi.pamong.input.simpan');
                });
            });
        });

        Route::prefix('kegiatan-mahasiswa')->middleware('role:admin,dpl,pengawas')->group(function () {
            Route::get('/', [KegiatanMahasiswaController::class, 'index'])->name('admin.kegiatan-mahasiswa');
            Route::get('/data', [KegiatanMahasiswaController::class, 'data'])->name('admin.kegiatan-mahasiswa.data');
            Route::get('/{posko}/detail', [KegiatanMahasiswaController::class, 'detail'])->name('admin.kegiatan-mahasiswa.detail');
        });

        Route::prefix('jurnal')->middleware('role:admin,pengawas')->group(function () {
            Route::prefix('pengawas')->group(function () {
                Route::get('/', [JurnalPengawasController::class, 'index'])->name('admin.jurnal.pengawas.index');
                Route::get('/data', [JurnalPengawasController::class, 'getData'])->name('admin.jurnal.pengawas.data');

                Route::prefix('detail/{idPoskoPengawas}')->group(function () {
                    Route::get('/', [JurnalPengawasController::class, 'detail'])->name('admin.jurnal.pengawas.detail');
                    Route::get('/data', [JurnalPengawasController::class, 'detailData'])->name('admin.jurnal.pengawas.detail.data');
                    Route::post('/simpan', [JurnalPengawasController::class, 'simpanDetail'])->name('admin.jurnal.pengawas.detail.simpan');
                    Route::post('/edit', [JurnalPengawasController::class, 'editDetail'])->name('admin.jurnal.pengawas.detail.edit');
                    Route::delete('/delete', [JurnalPengawasController::class, 'deleteDetail'])->name('admin.jurnal.pengawas.detail.delete');

                    Route::prefix('input/{idJurnalPengawas}')->group(function () {
                        Route::get('/', [JurnalPengawasController::class, 'input'])->name('admin.jurnal.pengawas.detail.input');
                        // Route::get('/data', [JurnalPengawasController::class, 'inputData'])->name('admin.jurnal.detail.input.data');
                        Route::post('/simpan', [JurnalPengawasController::class, 'simpanInput'])->name('admin.jurnal.pengawas.detail.input.simpan');
                        Route::delete('/delete', [JurnalPengawasController::class, 'deleteInput'])->name('admin.jurnal.pengawas.detail.input.delete');
                    });
                });
            });
        });

        Route::prefix('penugasan')->middleware('role:admin,dpl,pamong,pengawas')->group(function () {
            Route::prefix('dpl')->group(function () {
                Route::get('/', [PenugasanDplController::class, 'index'])->name('admin.penugasan.dpl.index');
                Route::get('/data', [PenugasanDplController::class, 'getData'])->name('admin.penugasan.dpl.data');
                Route::get('/downloadExcel', [PenugasanDplController::class, 'downloadExcel'])->name('admin.penugasan.dpl.downloadExcel');
                Route::get('/createPenugasan', [PenugasanDplController::class, 'createPenugasan'])->name('admin.penugasan.dpl.createPenugasan');
                Route::get('/createPenugasanVideo', [PenugasanDplController::class, 'createPenugasanVideo'])->name('admin.penugasan.dpl.createPenugasanVideo');

                Route::prefix('detail/{idPoskoDpl}')->group(function () {
                    Route::get('/', [PenugasanDplController::class, 'detail'])->name('admin.penugasan.dpl.detail');
                    Route::get('/data', [PenugasanDplController::class, 'detailData'])->name('admin.penugasan.dpl.detail.data');
                    Route::post('/simpan', [PenugasanDplController::class, 'simpanDetail'])->name('admin.penugasan.dpl.detail.simpan');
                    Route::post('/edit', [PenugasanDplController::class, 'editDetail'])->name('admin.penugasan.dpl.detail.edit');
                    Route::delete('/delete', [PenugasanDplController::class, 'deleteDetail'])->name('admin.penugasan.dpl.detail.delete');

                    Route::prefix('input/{idPenugasanDpl}')->group(function () {
                        Route::get('/', [PenugasanDplController::class, 'input'])->name('admin.penugasan.dpl.detail.input');
                        // Route::get('/data', [PenugasanDplController::class, 'inputData'])->name('admin.penugasan.detail.input.data');
                        Route::post('/simpan', [PenugasanDplController::class, 'simpanInput'])->name('admin.penugasan.detail.input.simpan');
                        Route::delete('/delete', [PenugasanDplController::class, 'deleteInput'])->name('admin.penugasan.detail.input.delete');
                    });
                });
            });
            Route::prefix('pamong')->group(function () {
                Route::get('/', [PenugasanPamongController::class, 'index'])->name('admin.penugasan.pamong.index');
                Route::get('/data', [PenugasanPamongController::class, 'getData'])->name('admin.penugasan.pamong.data');

                Route::prefix('detail/{idPamong}')->group(function () {
                    Route::get('/', [PenugasanPamongController::class, 'detail'])->name('admin.penugasan.pamong.detail');
                    Route::get('/data', [PenugasanPamongController::class, 'detailData'])->name('admin.penugasan.pamong.detail.data');
                    Route::post('/simpan', [PenugasanPamongController::class, 'simpanDetail'])->name('admin.penugasan.pamong.detail.simpan');
                    Route::post('/edit', [PenugasanPamongController::class, 'editDetail'])->name('admin.penugasan.pamong.detail.edit');
                    Route::delete('/delete', [PenugasanPamongController::class, 'deleteDetail'])->name('admin.penugasan.pamong.detail.delete');

                    Route::prefix('input/{idPenugasanPamong}')->group(function () {
                        Route::get('/', [PenugasanPamongController::class, 'input'])->name('admin.penugasan.pamong.detail.input');
                        Route::post('/simpan', [PenugasanPamongController::class, 'simpanInput'])->name('admin.penugasan.pamong.detail.input.simpan');
                        Route::delete('/delete', [PenugasanPamongController::class, 'deleteInput'])->name('admin.penugasan.pamong.detail.input.delete');
                    });
                });
            });
        });

        Route::prefix('dokumen-wajib')->middleware('role:admin,dpl')->group(function () {
            Route::prefix('dpl')->group(function () {
                Route::get('/', [DokumenWajinDplController::class, 'index'])->name('admin.dokumen-wajib.dpl.index');
                Route::get('/data', [DokumenWajinDplController::class, 'getData'])->name('admin.dokumen-wajib.dpl.data');

                Route::prefix('input/{idPoskoDpl}')->group(function () {
                    Route::get('/', [DokumenWajinDplController::class, 'input'])->name('admin.dokumen-wajib.dpl.input');
                    Route::get('/data', [DokumenWajinDplController::class, 'inputData'])->name('admin.dokumen-wajib.dpl.input.data');
                    Route::post('/simpan', [DokumenWajinDplController::class, 'simpan'])->name('admin.dokumen-wajib.dpl.input.simpan');
                    Route::delete('/delete', [DokumenWajinDplController::class, 'delete'])->name('admin.dokumen-wajib.dpl.input.delete');
                });
            });
        });

        Route::prefix('penilaian')->middleware('role:admin,dpl,pamong,peserta')->group(function () {
            Route::prefix('dpl')->middleware('role:admin,dpl')->group(function () {
                Route::get('/', [PenilaianDplController::class, 'index'])->name('admin.penilaian.dpl');
                Route::get('/data', [PenilaianDplController::class, 'data'])->name('admin.penilaian.dpl.data');

                Route::prefix('input/{poskoDpl}')->group(function () {
                    Route::get('/', [PenilaianDplController::class, 'input'])->name('admin.penilaian.dpl.input');
                    Route::get('/data', [PenilaianDplController::class, 'dataInput'])->name('admin.penilaian.dpl.input.data');
                    Route::post('/store', [PenilaianDplController::class, 'storeInput'])->name('admin.penilaian.dpl.input.store');
                });
            });
            Route::prefix('pamong')->middleware('role:admin,pamong')->group(function () {
                Route::get('/', [PenilaianPamongController::class, 'index'])->name('admin.penilaian.pamong');
                Route::get('/data', [PenilaianPamongController::class, 'data'])->name('admin.penilaian.pamong.data');

                Route::prefix('input/{pamong}')->group(function () {
                    Route::get('/', [PenilaianPamongController::class, 'input'])->name('admin.penilaian.pamong.input');
                    Route::get('/data', [PenilaianPamongController::class, 'dataInput'])->name('admin.penilaian.pamong.input.data');
                    Route::post('/store', [PenilaianPamongController::class, 'storeInput'])->name('admin.penilaian.pamong.input.store');
                });
            });
            Route::prefix('peserta')->middleware('role:admin,peserta')->group(function () {
                Route::get('/', [PenilaianPesertaController::class, 'index'])->name('admin.penilaian.peserta')->middleware('role:admin');
                Route::get('/data', [PenilaianPesertaController::class, 'data'])->name('admin.penilaian.peserta.data')->middleware('role:admin');

                Route::prefix('input/{peserta}')->group(function () {
                    Route::get('/', [PenilaianPesertaController::class, 'input'])->name('admin.penilaian.peserta.input');
                    Route::get('/data', [PenilaianPesertaController::class, 'dataInput'])->name('admin.penilaian.peserta.input.data');
                    Route::post('/store', [PenilaianPesertaController::class, 'storeInput'])->name('admin.penilaian.peserta.input.store')->middleware('role:admin');
                });
            });
        });
    });

    // Profil
    Route::prefix('profil')->group(function () {
        Route::get('/', [ProfilController::class, 'index'])->name('admin.profil');
        Route::get('/edit', [ProfilController::class, 'edit'])->name('admin.profil.edit');
        Route::post('/edit', [ProfilController::class, 'editProses'])->name('admin.profil.edit.proses');
        Route::get('/upload', [ProfilController::class, 'upload'])->name('admin.profil.upload');
        Route::post('/crop', [ProfilController::class, 'crop'])->name('admin.profil.crop');
    });

    // Setting
    Route::prefix('setting')->middleware('role:admin')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('admin.setting');
        Route::post('/', [SettingController::class, 'save'])->name('admin.setting.save');
        Route::post('/tes', [SettingController::class, 'tes'])->name('admin.setting.tes');
        Route::post('/simkeu', [SettingController::class, 'simkeu'])->name('admin.setting.simkeu');
    });

    Route::prefix('import')->middleware('role:admin')->group(function () {
        Route::get('/', [ImportDataController::class, 'index'])->name('admin.import');
        Route::post('/store', [ImportDataController::class, 'store'])->name('admin.import.store');
    });
});

Route::prefix('peserta')->middleware(['auth', 'role:peserta,admin,dpl'])->group(function () {
    Route::get('/', [PesertaController::class, 'index'])->name('peserta')->middleware('role:peserta');
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [PesertaDashboardController::class, 'index'])->name('peserta.dashboard')->middleware('role:peserta');
        Route::put('/change-password', [PesertaDashboardController::class, 'changePassword'])->name('peserta.dashboard.changePassword')->middleware('role:peserta');
        Route::post('/uploadPenugasanDpl', [PesertaDashboardController::class, 'uploadPenugasanDpl'])->name('peserta.dashboard.uploadPenugasanDpl')->middleware('role:peserta');
        Route::delete('/deletePenugasanDpl', [PesertaDashboardController::class, 'deletePenugasanDpl'])->name('peserta.dashboard.deletePenugasanDpl')->middleware('role:peserta');

        Route::get('/dataKegiatanMahasiswa/{posko}', [PesertaDashboardController::class, 'dataKegiatanMahasiswa'])->name('peserta.dashboard.dataKegiatanMahasiswa');
        Route::post('/storeKegiatanMahasiswa', [PesertaDashboardController::class, 'storeKegiatanMahasiswa'])->name('peserta.dashboard.storeKegiatanMahasiswa');
        Route::put('/updateKegiatanMahasiswa', [PesertaDashboardController::class, 'updateKegiatanMahasiswa'])->name('peserta.dashboard.updateKegiatanMahasiswa');
        Route::delete('/deleteKegiatanMahasiswa', [PesertaDashboardController::class, 'deleteKegiatanMahasiswa'])->name('peserta.dashboard.deleteKegiatanMahasiswa');
        Route::delete('/deleteKegiatanMahasiswaBukti', [PesertaDashboardController::class, 'deleteKegiatanMahasiswaBukti'])->name('peserta.dashboard.deleteKegiatanMahasiswaBukti');
        Route::post('/fileUpload', [PesertaDashboardController::class, 'fileUpload'])->name('peserta.dashboard.fileUpload');
        Route::post('/fileDelete', [PesertaDashboardController::class, 'fileDelete'])->name('peserta.dashboard.fileDelete');

    });
    Route::prefix('formulir')->middleware('role:peserta')->group(function () {
        Route::get('/edit', [PesertaFormulirController::class, 'edit'])->name('peserta.formulir.edit');
        Route::put('/update', [PesertaFormulirController::class, 'update'])->name('peserta.formulir.update');
        Route::post('/dokumen', [PesertaFormulirController::class, 'dokumen'])->name('peserta.formulir.dokumen');
    });
});

Route::prefix('dpl')->middleware(['auth', 'role:dpl'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DplDashboardController::class, 'index'])->name('dpl.dashboard');
    });
});

Route::prefix('pengawas')->middleware(['auth', 'role:pengawas'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [PengawasDashboardController::class, 'index'])->name('pengawas.dashboard');
    });
});

Route::prefix('pamong')->middleware(['auth', 'role:pamong'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [PamongDashboardController::class, 'index'])->name('pamong.dashboard');
    });
});

Route::prefix('operasi')->group(function () {
    // admin
    Route::prefix('daftar-tugas')->group(function () {
        Route::get('/', [DaftarTugasController::class, 'show'])->name('operasi.daftarTugas.show');
        Route::post('/tambah', [DaftarTugasController::class, 'tambah'])->name('operasi.daftarTugas.tambah');
        Route::post('/edit', [DaftarTugasController::class, 'edit'])->name('operasi.daftarTugas.edit');
        Route::get('/jumlah-halaman', [DaftarTugasController::class, 'jumlahHalaman'])->name('operasi.daftarTugas.jumlahHalaman');
        Route::get('/{offset}', [DaftarTugasController::class, 'daftarTugas'])->name('operasi.daftarTugas');
        Route::get('/{id}/edit/{status}', [DaftarTugasController::class, 'check'])->name('operasi.daftarTugas.check');
        Route::post('/{id}/hapus', [DaftarTugasController::class, 'hapus'])->name('operasi.daftarTugas.hapus');
    });

    Route::prefix('kalender')->group(function () {
        Route::get('/', [KalenderController::class, 'show'])->name('operasi.kalender');
        Route::post('/tambah', [KalenderController::class, 'tambah'])->name('operasi.kalender.tambah');
        Route::post('/{id}/edit', [KalenderController::class, 'edit'])->name('operasi.kalender.edit');
        Route::delete('/{id}/hapus', [KalenderController::class, 'hapus'])->name('operasi.kalender.hapus');
    });

    Route::prefix('dokumen')->group(function () {
        Route::post('/delete-image', [OperasiDokumenController::class, 'imageDelete'])->name('operasi.dokumen.imageDelete');
        Route::get('/download', [OperasiDokumenController::class, 'download'])->name('operasi.dokumen.download');
        Route::get('/downloadFile', [OperasiDokumenController::class, 'downloadFile'])->name('operasi.dokumen.downloadFile');
    });

    Route::prefix('peserta')->group(function () {
        Route::get('/autocomplete', [OperasiPesertaController::class, 'autocomplete'])->name('operasi.peserta.autocomplete');
        Route::post('/getData', [OperasiPesertaController::class, 'getData'])->name('operasi.peserta.getData');
    });

    Route::prefix('dosen')->group(function () {
        Route::get('/autocomplete', [DosenController::class, 'autocomplete'])->name('operasi.dosen.autocomplete');
        Route::get('/getData', [DosenController::class, 'getData'])->name('operasi.dosen.getData');
    });

    Route::prefix('pengawas')->group(function () {
        Route::get('/autocomplete', [OperasiPengawasController::class, 'autocomplete'])->name('operasi.pengawas.autocomplete');
        Route::get('/getData', [OperasiPengawasController::class, 'getData'])->name('operasi.pengawas.getData');
    });

    Route::prefix('pesertaKkn')->group(function () {
        Route::get('/autocomplete', [PesertaKknController::class, 'autocomplete'])->name('operasi.pesertaKkn.autocomplete');
        Route::get('/getData', [PesertaKknController::class, 'getData'])->name('operasi.pesertaKkn.getData');
    });

    Route::prefix('kuota')->group(function () {
        Route::get('/', [OperasiKuotaController::class, 'show'])->name('operasi.kuota');
        Route::get('/getData', [OperasiKuotaController::class, 'getData'])->name('operasi.kuota.getData');
    });

    Route::prefix('theme')->group(function () {
        Route::get('/', [ThemeController::class, 'index'])->name('operasi.theme');
    });
});

Route::prefix('peserta')->group(function () {
    Route::prefix('formulir')->group(function () {
        Route::get('/cetak/{idPeserta}/{noUnik}', [PesertaFormulirController::class, 'cetak'])->name('peserta.formulir.cetak');
    });
});
Route::get('/testing', [TestingController::class, 'index'])->name('testing');
// Route::get('/coba', [TestingController::class, 'cobaDosen'])->name('coba');
