<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiPsPamongDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi_ps_pamong_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_ps_pamong_id')->constrained('absensi_ps_pamong');
            $table->foreignId('pamong_peserta_id')->constrained('pamong_peserta');
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
        Schema::dropIfExists('absensi_ps_pamong_detail');
    }
}
