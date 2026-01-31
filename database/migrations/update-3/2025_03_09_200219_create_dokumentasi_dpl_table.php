<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDokumentasiDplTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dokumentasi_dpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posko_dpl_id')->constrained('posko_dpl');
            $table->string('file');
            $table->string('path');
            $table->enum('tipe', ['foto', 'video', 'lain-lain']);
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
        Schema::dropIfExists('dokumentasi_dpl');
    }
}
