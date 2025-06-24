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
        if (!Schema::hasColumn('peminjaman_barang', 'deleted_at')) {
            Schema::table('peminjaman_barang', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('peminjaman_barang', 'deleted_at')) {
            Schema::table('peminjaman_barang', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
