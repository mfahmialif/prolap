<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRubrikPenilaianDplTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rubrik_penilaian_dpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posko_dpl_id')->constrained('posko_dpl');
            $table->string('file');
            $table->string('path');
            $table->string('keterangan')->nullable();
            $table->enum('status', ['Belum Diisi', 'Sudah Diisi'])->default('Belum Diisi');
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
        Schema::dropIfExists('rubrik_penilaian_dpl');
    }
}
