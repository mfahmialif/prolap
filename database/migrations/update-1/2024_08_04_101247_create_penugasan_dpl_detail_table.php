<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenugasanDplDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penugasan_dpl_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_dpl_id')->constrained('penugasan_dpl');
            $table->foreignId('posko_peserta_id')->constrained('posko_peserta');
            $table->string('file');
            $table->string('path');
            $table->text('keterangan')->nullable();
            $table->timestamp('waktu_pengumpulan');
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
        Schema::dropIfExists('penugasan_dpl_detail');
    }
}
