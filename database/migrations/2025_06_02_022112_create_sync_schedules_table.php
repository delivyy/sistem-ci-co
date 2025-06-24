<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sync_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('task_name')->unique();
            $table->integer('interval_hours')->default(1);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sync_schedules');
    }
};
