<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenugasanPamongDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penugasan_pamong_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_pamong_id')->constrained('penugasan_pamong');
            $table->foreignId('pamong_peserta_id')->constrained('pamong_peserta');
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
        Schema::dropIfExists('penugasan_pamong_detail');
    }
}
