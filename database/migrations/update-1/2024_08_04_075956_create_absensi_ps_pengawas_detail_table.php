<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiPsPengawasDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi_ps_pengawas_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_ps_pengawas_id')->constrained('absensi_ps_pengawas');
            $table->foreignId('posko_peserta_id')->constrained('posko_peserta');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha', 'Belum Absen']);
            $table->timestamp('waktu_absen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absensi_ps_pengawas_detail');
    }
}
