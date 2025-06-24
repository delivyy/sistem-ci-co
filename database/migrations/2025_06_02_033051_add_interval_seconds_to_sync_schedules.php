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
        Schema::table('sync_schedules', function (Blueprint $table) {
            $table->integer('interval_seconds')->nullable()->default(3600); // default 1 jam
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_schedules', function (Blueprint $table) {
            //
        });
    }
};
