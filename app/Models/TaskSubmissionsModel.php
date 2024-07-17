<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmissionsModel extends Model
{
    use HasFactory;

    protected $table = 'task_submissions';
    protected $primaryKey = 'ts_id';
}
