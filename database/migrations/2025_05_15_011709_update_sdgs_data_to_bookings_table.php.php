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
        Schema::table('booking', function (Blueprint $table) {
            if (!Schema::hasColumn('booking', 'sdgs_data')) {
                $table->longText('sdgs_data')->nullable()->after('jumlah_peserta');
            }
        });
    }

    public function down()
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropColumn('sdgs_data');
        });
    }
};
