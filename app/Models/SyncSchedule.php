<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['task_name', 'interval_hours', 'last_run_at'];

    protected $dates = ['last_run_at'];
}