<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaianPengawasDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaian_pengawas_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_pengawas_id')->constrained('penilaian_pengawas');
            $table->foreignId('komponen_nilai_id')->constrained('komponen_nilai');
            $table->double('nilai');
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
        Schema::dropIfExists('penilaian_pengawas_detail');
    }
}