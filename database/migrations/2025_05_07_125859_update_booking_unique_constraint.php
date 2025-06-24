<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropUnique(['kode_booking']); // Hapus unique lama
            $table->unique(['kode_booking', 'tanggal']); // Tambahkan unique gabungan
        });
    }

    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropUnique(['kode_booking', 'tanggal']); // Hapus unique baru
            $table->unique('kode_booking'); // Kembalikan jika perlu
        });
    }
};
