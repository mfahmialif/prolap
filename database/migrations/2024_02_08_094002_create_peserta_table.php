<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesertaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ["Internasional", "Nasional"])->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('tahun_id')->constrained('tahun');
            $table->date('tanggal_daftar');
            $table->string('nama');
            $table->string('nama_pondok')->nullable();
            $table->string('nim')->nullable();
            $table->string('nik')->nullable();
            $table->foreignId('prodi_id')->constrained('prodi');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('nomor_hp_orang_tua')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kamar')->nullable();
            $table->string('kelas_pondok')->nullable();
            $table->string('qism_pondok')->nullable();
            $table->string('keahlian')->nullable();
            $table->enum('mahir_bahasa_lokal', ['Bahasa Jawa', 'Bahasa Madura', 'Bahasa Jawa dan Bahasa Madura', 'Tidak Ada'])->nullable();
            $table->enum('ukuran_baju', ['S', 'M', 'L', 'XL', 'XXL', 'XXXL'])->nullable();
            $table->enum('mursal', ['iya', 'tidak'])->nullable();
            $table->foreignId('status_id')->constrained('status');
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('peserta');
    }
}
