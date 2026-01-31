<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaianPamongDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaian_pamong_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_pamong_id')->constrained('penilaian_pamong');
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
        Schema::dropIfExists('penilaian_pamong_detail');
    }
}
