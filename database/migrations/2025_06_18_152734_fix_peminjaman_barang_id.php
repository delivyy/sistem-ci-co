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
        // Drop tabel lama jika ada (dengan hati-hati, pastikan backup sudah dilakukan)
        Schema::dropIfExists('peminjaman_barang');

        // Buat tabel baru dengan struktur yang benar
        Schema::create('peminjaman_barang', function (Blueprint $table) {
            $table->id(); // bigint unsigned, auto_increment, primary key
            $table->string('kode_booking', 255)->nullable(); // Hapus dari primary key
            $table->string('nama_item', 255)->nullable();
            $table->integer('jumlah')->unsigned()->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->string('marketing', 255)->nullable();
            $table->string('FO', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Tambahkan index jika diperlukan (misalnya untuk kode_booking)
            $table->index('kode_booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_barang');
    }
};
