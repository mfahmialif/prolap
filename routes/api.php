<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Mobile\Absensi\DplController;
use App\Http\Controllers\Api\Mobile\Absensi\PamongController;
use App\Http\Controllers\Api\Mobile\Absensi\PengawasController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

    Route::prefix('absensi')->group(function () {

        Route::prefix('dpl')->group(function () {
            Route::get('/show', [DplController::class, 'show'])->name('api.absensi.dpl.show');
            Route::get('/count', [DplController::class, 'count'])->name('api.absensi.dpl.count');
            Route::get('/find', [DplController::class, 'find'])->name('api.absensi.dpl.find');
            Route::get('/data', [DplController::class, 'data'])->name('api.absensi.dpl.data');
            Route::get('/dataAll', [DplController::class, 'dataAll'])->name('api.absensi.dpl.dataAll');
            Route::get('/listPeserta', [DplController::class, 'poskoPeserta'])->name('api.absensi.dpl.list');
            Route::get('/posko', [DplController::class, 'posko'])->name('api.absensi.dpl.posko');
            Route::post('/simpanPertemuan', [DplController::class, 'simpanPertemuan'])->name('api.absensi.dpl.simpan');
            Route::get('/listPertemuan', [DplController::class, 'listPertemuan'])->name('api.absensi.dpl.listPertemuan');
            Route::get('/daftarPeserta', [DplController::class, 'daftarPeserta'])->name('api.absensi.dpl.daftarPeserta');
            Route::post('/simpanPesertaDpl', [DplController::class, 'simpanPeserta'])->name('api.absensi.dpl.simpanPeserta');
            Route::post('/absenSemua', [DplController::class, 'absenSemua'])->name('api.absensi.dpl.absenSemua');
            Route::get('/dataAbsenEdit', [DplController::class, 'dataAbsenEdit'])->name('api.absensi.dpl.dataAbsenEdit');
            Route::post('/simpanEditAbsen', [DplController::class, 'editAbsensi'])->name('api.absensi.dpl.editAbsensi');
            Route::post('/hapusAbsen', [DplController::class, 'hapusAbsen'])->name('api.absensi.dpl.hapusAbsen');
            Route::post('/cari', [DplController::class, 'cariMahasiswa'])->name('api.absensi.dpl.cariMahasiswa');
        });

        Route::prefix('pengawas')->group(function () {
            Route::get('/show', [PengawasController::class, 'show'])->name('api.absensi.pengawas.show');
            Route::get('/count', [PengawasController::class, 'count'])->name('api.absensi.pengawas.count');
            Route::get('/find', [PengawasController::class, 'find'])->name('api.absensi.pengawas.find');
        });
        Route::prefix('pamong')->group(function () {
            Route::get('/show', [PamongController::class, 'show'])->name('api.absensi.pamong.show');
            Route::get('/count', [PamongController::class, 'count'])->name('api.absensi.pamong.count');
            Route::get('/find', [PamongController::class, 'find'])->name('api.absensi.pamong.find');
        });
    });
});


// Testing
Route::prefix('tes')->group(function () {
    Route::prefix('dpl')->group(function () {
        Route::get('/show', [DplController::class, 'show'])->name('api.absensi.dpl.show');
        Route::get('/count', [DplController::class, 'count'])->name('api.absensi.dpl.count');
        Route::get('/find', [DplController::class, 'find'])->name('api.absensi.dpl.find');
        Route::get('/data', [DplController::class, 'data'])->name('api.absensi.dpl.data');
        Route::get('/hapusAbsen', [DplController::class, 'hapusAbsen'])->name('api.absensi.dpl.hapusAbsen');

        Route::get('/simpanPertemuan', [DplController::class, 'simpanPertemuan'])->name('api.absensi.dpl.simpan');
        Route::get('/absenSemua', [DplController::class, 'absenSemua'])->name('api.absensi.dpl.absenSemua');
        Route::get('/simpanPesertaDpl', [DplController::class, 'simpanPeserta'])->name('api.absensi.dpl.simpanPeserta');

        Route::get('/dataAll', [DplController::class, 'dataAll'])->name('api.absensi.dpl.dataAll');
    });
});
