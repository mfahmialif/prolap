<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalPengawasDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal_pengawas_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_pengawas_id')->constrained('jurnal_pengawas');
            $table->foreignId('posko_peserta_id')->constrained('posko_peserta');
            $table->string('file');
            $table->string('path');
            $table->text('keterangan')->nullable();
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
        Schema::dropIfExists('jurnal_pengawas_detail');
    }
}
