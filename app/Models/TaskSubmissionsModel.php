<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmissionsModel extends Model
{
    use HasFactory;

    protected $table = 'task_submissions';
    protected $primaryKey = 'ts_id';

    // protected $fillable = [
    //     'ts_task_id',
    //     'ts_user_id',
    //     'ts_submitter',
    //     'ts_content',
    //     'ts_actual_start_time',
    //     'ts_actual_end_time',

    // ];
}
