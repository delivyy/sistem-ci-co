<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking')->unique();
            $table->string('nama_event');
            $table->string('nama_organisasi');
            $table->date('tanggal');
            $table->string('waktu_mulai');
            $table->string('waktu_selesai');
            $table->string('nama_pic');
            $table->string('status');
            $table->string('bidang');
            $table->string('kegiatan');
            $table->string('komersial/non');
            $table->string('lantai');
            $table->string('ruangan');
            $table->integer('jumlah_peserta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
